<?php

namespace App\Services;

use App\Models\ClaimDocument;
use App\Models\ClaimEpisode;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class ClaimSyncService
{
    public function __construct(
        protected SimrsApiService $simrsApiService,
        protected EpisodeAggregatorService $episodeAggregatorService
    ) {
    }

    public function syncDischargesByDate(?string $date = null): array
    {
        $date = $date ?: now()->toDateString();

        $response = $this->simrsApiService->fetchDischargesByDate($date);
        $rows = data_get($response, 'data', $response);

        if (! is_array($rows)) {
            $rows = [];
        }

        $processed = 0;
        $created = 0;
        $updated = 0;
        $failed = 0;
        $results = [];

        foreach ($rows as $row) {
            $encounterId = $this->extractEncounterId($row);

            if (! $encounterId) {
                $failed++;
                $results[] = [
                    'encounter_id' => null,
                    'status' => 'failed',
                    'message' => 'Encounter ID tidak ditemukan pada data discharge.',
                    'row' => $row,
                ];
                continue;
            }

            try {
                $syncResult = $this->syncSingleEpisode($encounterId);
                $processed++;

                if (($syncResult['action'] ?? '') === 'created') {
                    $created++;
                } else {
                    $updated++;
                }

                $results[] = [
                    'encounter_id' => $encounterId,
                    'status' => 'success',
                    'action' => $syncResult['action'] ?? 'updated',
                    'claim_episode_id' => $syncResult['claim_episode_id'] ?? null,
                    'episode_no' => $syncResult['episode_no'] ?? null,
                ];
            } catch (Throwable $e) {
                $failed++;
                $results[] = [
                    'encounter_id' => $encounterId,
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                ];
            }
        }

        return [
            'date' => $date,
            'processed' => $processed,
            'created' => $created,
            'updated' => $updated,
            'failed' => $failed,
            'results' => $results,
        ];
    }

    public function syncSingleEpisode(string $encounterId): array
    {
        $payload = $this->episodeAggregatorService->buildEpisodePayload($encounterId);

        return DB::transaction(function () use ($payload, $encounterId) {
            $episodeNo = (string) ($payload['episode_id'] ?? ('EP-' . $encounterId));

            $episodeData = [
                'simrs_encounter_id' => $encounterId,
                'sep_no' => data_get($payload, 'sep_no'),
                'mrn' => (string) data_get($payload, 'patient.mrn', ''),
                'patient_name' => (string) data_get($payload, 'patient.name', 'Tanpa Nama'),
                'care_type' => data_get($payload, 'service.care_type'),
                'service_unit' => data_get($payload, 'service.unit'),
                'doctor_name' => data_get($payload, 'service.doctor'),
                'admission_at' => $this->normalizeDateTime(data_get($payload, 'service.admission_date')),
                'discharge_at' => $this->normalizeDateTime(data_get($payload, 'service.discharge_date')),
                'payer_name' => data_get($payload, 'administrative_data.payer'),
                'snapshot_json' => $payload,
            ];

            $existing = ClaimEpisode::query()
                ->where('episode_no', $episodeNo)
                ->orWhere('simrs_encounter_id', $encounterId)
                ->first();

            $action = $existing ? 'updated' : 'created';

            if ($existing) {
                $existing->fill($episodeData);

                if (blank($existing->processing_stage)) {
                    $existing->processing_stage = 'new';
                }

                if (blank($existing->claim_status)) {
                    $existing->claim_status = 'draft';
                }

                if (blank($existing->audit_status)) {
                    $existing->audit_status = 'pending';
                }

                $existing->save();
                $episode = $existing;
            } else {
                $episode = ClaimEpisode::create(array_merge($episodeData, [
                    'episode_no' => $episodeNo,
                    'claim_status' => 'draft',
                    'audit_status' => 'pending',
                    'processing_stage' => 'new',
                    'risk_level' => 'unknown',
                    'risk_score' => 0,
                ]));
            }

            $this->syncDocuments($episode, data_get($payload, 'documents', []));

            return [
                'action' => $action,
                'claim_episode_id' => $episode->id,
                'episode_no' => $episode->episode_no,
                'encounter_id' => $episode->simrs_encounter_id,
            ];
        });
    }

    protected function syncDocuments(ClaimEpisode $episode, array $documents): void
    {
        if (! is_array($documents)) {
            return;
        }

        $existingDocs = $episode->documents()->get()->keyBy(function (ClaimDocument $doc) {
            return $this->documentKey($doc->document_type, $doc->file_name);
        });

        $seenKeys = [];

        foreach ($documents as $doc) {
            if (! is_array($doc)) {
                continue;
            }

            $documentType = (string) ($doc['type'] ?? 'unknown');
            $fileName = $doc['file_name'] ?? Arr::get($doc, 'raw.file_name') ?? Arr::get($doc, 'raw.filename');
            $key = $this->documentKey($documentType, $fileName);
            $seenKeys[] = $key;

            $attributes = [
                'file_url' => $doc['file_url'] ?? null,
                'file_name' => $fileName,
                'is_required' => (bool) ($doc['is_required'] ?? false),
                'is_available' => (bool) ($doc['available'] ?? false),
                'notes' => isset($doc['raw']) ? json_encode($doc['raw'], JSON_UNESCAPED_UNICODE) : null,
            ];

            if ($existingDocs->has($key)) {
                $existingDocs[$key]->update($attributes);
            } else {
                $episode->documents()->create(array_merge([
                    'document_type' => $documentType,
                ], $attributes));
            }
        }

        // Dokumen lama yang tidak ikut di payload terbaru tidak dihapus dulu.
        // Lebih aman untuk audit trail dan mencegah kehilangan data.
    }

    protected function documentKey(?string $type, ?string $fileName): string
    {
        return mb_strtolower(trim((string) $type)) . '|' . mb_strtolower(trim((string) $fileName));
    }

    protected function extractEncounterId(mixed $row): ?string
    {
        if (! is_array($row)) {
            return null;
        }

        $candidates = [
            'encounter_id',
            'id_kunjungan',
            'visit_id',
            'registration_id',
            'no_register',
            'no_reg',
            'id',
        ];

        foreach ($candidates as $key) {
            $value = data_get($row, $key);
            if (! empty($value)) {
                return (string) $value;
            }
        }

        return null;
    }

    protected function normalizeDateTime(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::parse((string) $value)->format('Y-m-d H:i:s');
        } catch (Throwable $e) {
            return null;
        }
    }
}