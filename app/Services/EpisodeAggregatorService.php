<?php

namespace App\Services;

use Carbon\Carbon;
use Throwable;

class EpisodeAggregatorService
{
    public function __construct(
        protected SimrsApiService $simrsApiService
    ) {
    }

    public function buildEpisodePayload(string $encounterId): array
    {
        $detail          = $this->safeCall(fn () => $this->simrsApiService->fetchEncounterDetail($encounterId));
        $resume          = $this->safeCall(fn () => $this->simrsApiService->fetchResume($encounterId));
        $diagnoses       = $this->safeCall(fn () => $this->simrsApiService->fetchDiagnoses($encounterId));
        $procedures      = $this->safeCall(fn () => $this->simrsApiService->fetchProcedures($encounterId));
        $billing         = $this->safeCall(fn () => $this->simrsApiService->fetchBilling($encounterId));
        $sep             = $this->safeCall(fn () => $this->simrsApiService->fetchSep($encounterId));
        $documents       = $this->safeCall(fn () => $this->simrsApiService->fetchDocuments($encounterId));
        $labs            = $this->safeCall(fn () => $this->simrsApiService->fetchLabs($encounterId));
        $radiology       = $this->safeCall(fn () => $this->simrsApiService->fetchRadiology($encounterId));
        $operationReport = $this->safeCall(fn () => $this->simrsApiService->fetchOperationReport($encounterId));
        $cppt            = $this->safeCall(fn () => $this->simrsApiService->fetchCppt($encounterId));

        $admissionDate = $this->value($detail, [
            'data.admission_date',
            'data.tanggal_masuk',
            'admission_date',
            'tanggal_masuk',
        ]);

        $dischargeDate = $this->value($detail, [
            'data.discharge_date',
            'data.tanggal_pulang',
            'discharge_date',
            'tanggal_pulang',
        ]);

        return [
            'episode_id' => $this->makeEpisodeNo($detail, $encounterId),
            'encounter_id' => $encounterId,
            'sep_no' => $this->value($sep, [
                'data.sep_no',
                'data.no_sep',
                'sep_no',
                'no_sep',
            ]),
            'patient' => [
                'mrn' => $this->value($detail, [
                    'data.patient.mrn',
                    'data.patient.no_rm',
                    'data.mrn',
                    'data.no_rm',
                    'patient.mrn',
                    'patient.no_rm',
                    'mrn',
                    'no_rm',
                ]),
                'name' => $this->value($detail, [
                    'data.patient.name',
                    'data.patient.nama',
                    'data.patient_name',
                    'data.nama_pasien',
                    'patient.name',
                    'patient.nama',
                    'patient_name',
                    'nama_pasien',
                ]),
                'gender' => $this->value($detail, [
                    'data.patient.gender',
                    'data.patient.jenis_kelamin',
                    'patient.gender',
                    'patient.jenis_kelamin',
                    'gender',
                    'jenis_kelamin',
                ]),
                'dob' => $this->value($detail, [
                    'data.patient.dob',
                    'data.patient.tanggal_lahir',
                    'patient.dob',
                    'patient.tanggal_lahir',
                    'dob',
                    'tanggal_lahir',
                ]),
            ],
            'service' => [
                'care_type' => $this->value($detail, [
                    'data.care_type',
                    'data.jenis_rawat',
                    'care_type',
                    'jenis_rawat',
                ]),
                'unit' => $this->value($detail, [
                    'data.unit',
                    'data.unit_name',
                    'data.ruangan',
                    'unit',
                    'unit_name',
                    'ruangan',
                ]),
                'doctor' => $this->value($detail, [
                    'data.doctor',
                    'data.doctor_name',
                    'data.dpjp',
                    'doctor',
                    'doctor_name',
                    'dpjp',
                ]),
                'admission_date' => $admissionDate,
                'discharge_date' => $dischargeDate,
                'length_of_stay_days' => $this->calculateLos($admissionDate, $dischargeDate),
            ],
            'clinical_data' => [
                'chief_complaint' => $this->value($resume, [
                    'data.chief_complaint',
                    'data.keluhan_utama',
                    'chief_complaint',
                    'keluhan_utama',
                ]),
                'resume_text' => $this->extractTextBlock($resume),
                'diagnoses_text' => $this->extractListText($diagnoses),
                'procedures_text' => $this->extractListText($procedures),
                'operation_report_text' => $this->extractTextBlock($operationReport),
                'cppt_text' => $this->extractTextBlock($cppt),
            ],
            'supporting_results' => [
                'labs' => $this->extractListText($labs),
                'radiology' => $this->extractListText($radiology),
            ],
            'administrative_data' => [
                'payer' => $this->value($detail, [
                    'data.payer',
                    'data.penjamin',
                    'payer',
                    'penjamin',
                ]),
                'class' => $this->value($detail, [
                    'data.class',
                    'data.kelas',
                    'class',
                    'kelas',
                ]),
                'billing_items' => $this->extractListText($billing),
            ],
            'documents' => $this->extractDocuments($documents),
            'raw_sources' => [
                'detail' => $detail,
                'resume' => $resume,
                'diagnoses' => $diagnoses,
                'procedures' => $procedures,
                'billing' => $billing,
                'sep' => $sep,
                'documents' => $documents,
                'labs' => $labs,
                'radiology' => $radiology,
                'operation_report' => $operationReport,
                'cppt' => $cppt,
            ],
        ];
    }

    protected function safeCall(callable $callback): array
    {
        try {
            $result = $callback();
            return is_array($result) ? $result : [];
        } catch (Throwable $e) {
            return [
                '_error' => true,
                '_message' => $e->getMessage(),
            ];
        }
    }

    protected function value(array $source, array $paths, mixed $default = null): mixed
    {
        foreach ($paths as $path) {
            $value = data_get($source, $path);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return $default;
    }

    protected function extractTextBlock(array $payload): string
    {
        $candidates = [
            'data.text',
            'data.resume_text',
            'data.content',
            'data.description',
            'data.report_text',
            'data.cppt_text',
            'text',
            'resume_text',
            'content',
            'description',
            'report_text',
            'cppt_text',
        ];

        foreach ($candidates as $path) {
            $value = data_get($payload, $path);
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        $data = data_get($payload, 'data', $payload);

        if (is_array($data)) {
            $flattened = [];

            array_walk_recursive($data, function ($item) use (&$flattened) {
                if (is_scalar($item) && trim((string) $item) !== '') {
                    $flattened[] = trim((string) $item);
                }
            });

            return implode("\n", array_slice(array_unique($flattened), 0, 30));
        }

        return '';
    }

    protected function extractListText(array $payload): array
    {
        $data = data_get($payload, 'data', $payload);

        if (! is_array($data)) {
            return [];
        }

        $items = [];

        foreach ($data as $row) {
            if (is_string($row) && trim($row) !== '') {
                $items[] = trim($row);
                continue;
            }

            if (is_array($row)) {
                $text = $this->firstFilled($row, [
                    'text',
                    'name',
                    'nama',
                    'description',
                    'keterangan',
                    'diagnosis',
                    'procedure',
                    'result',
                    'item_name',
                    'label',
                ]);

                if ($text !== null) {
                    $items[] = trim($text);
                    continue;
                }

                $joined = collect($row)
                    ->filter(fn ($v) => is_scalar($v) && trim((string) $v) !== '')
                    ->map(fn ($v) => trim((string) $v))
                    ->implode(' | ');

                if ($joined !== '') {
                    $items[] = $joined;
                }
            }
        }

        return array_values(array_unique(array_filter($items)));
    }

    protected function extractDocuments(array $payload): array
    {
        $data = data_get($payload, 'data', $payload);

        if (! is_array($data)) {
            return [];
        }

        $documents = [];

        foreach ($data as $row) {
            if (! is_array($row)) {
                continue;
            }

            $documents[] = [
                'type' => $this->firstFilled($row, [
                    'type',
                    'document_type',
                    'jenis',
                    'jenis_dokumen',
                    'name',
                    'nama',
                ]) ?? 'unknown',
                'available' => (bool) ($this->firstFilled($row, [
                    'available',
                    'is_available',
                    'exists',
                    'is_exist',
                ]) ?? true),
                'file_url' => $this->firstFilled($row, [
                    'file_url',
                    'url',
                    'link',
                    'path',
                ]),
                'file_name' => $this->firstFilled($row, [
                    'file_name',
                    'filename',
                    'name',
                    'nama',
                ]),
                'raw' => $row,
            ];
        }

        return $documents;
    }

    protected function firstFilled(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
                return (string) $row[$key];
            }
        }

        return null;
    }

    protected function calculateLos(?string $admissionDate, ?string $dischargeDate): ?int
    {
        if (empty($admissionDate) || empty($dischargeDate)) {
            return null;
        }

        try {
            $start = Carbon::parse($admissionDate);
            $end = Carbon::parse($dischargeDate);

            return max(0, $start->startOfDay()->diffInDays($end->startOfDay()));
        } catch (Throwable $e) {
            return null;
        }
    }

    protected function makeEpisodeNo(array $detail, string $encounterId): string
    {
        $existing = $this->value($detail, [
            'data.episode_no',
            'data.no_episode',
            'episode_no',
            'no_episode',
        ]);

        if ($existing) {
            return (string) $existing;
        }

        return 'EP-' . preg_replace('/[^A-Za-z0-9\-]/', '', $encounterId);
    }
}