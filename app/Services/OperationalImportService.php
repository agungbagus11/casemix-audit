<?php

namespace App\Services;

use App\Models\ClaimEpisode;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OperationalImportService
{
    public function __construct(
        protected ClaimVerificationService $claimVerificationService,
        protected ClaimFollowUpService $claimFollowUpService
    ) {
    }

    public function importFromText(int $claimEpisodeId, string $rawText, string $reviewerName = 'Importer', string $reviewerRole = 'casemix'): array
    {
        $episode = ClaimEpisode::find($claimEpisodeId);

        if (! $episode) {
            throw ValidationException::withMessages([
                'claim_episode_id' => 'Claim episode tidak ditemukan.',
            ]);
        }

        $rawText = trim($rawText);

        if ($rawText === '') {
            throw ValidationException::withMessages([
                'raw_text' => 'Data import kosong.',
            ]);
        }

        $lines = preg_split('/\r\n|\r|\n/', $rawText) ?: [];
        $lines = array_values(array_filter(array_map('trim', $lines), fn ($line) => $line !== ''));

        if (count($lines) < 2) {
            throw ValidationException::withMessages([
                'raw_text' => 'Minimal harus ada header dan 1 baris data.',
            ]);
        }

        $headerLine = array_shift($lines);
        $delimiter = $this->detectDelimiter($headerLine);

        $headers = $this->normalizeHeaders(str_getcsv($headerLine, $delimiter));

        $requiredHeaders = [
            'verification_key',
            'status',
            'finding_notes',
            'follow_up_notes',
            'source_reference',
            'target_unit',
            'priority',
            'title',
        ];

        foreach ($requiredHeaders as $requiredHeader) {
            if (! in_array($requiredHeader, $headers, true)) {
                throw ValidationException::withMessages([
                    'raw_text' => "Header {$requiredHeader} tidak ditemukan.",
                ]);
            }
        }

        $processed = 0;
        $followUpsCreated = 0;
        $results = [];

        DB::transaction(function () use (
            $lines,
            $headers,
            $delimiter,
            $claimEpisodeId,
            $reviewerName,
            $reviewerRole,
            &$processed,
            &$followUpsCreated,
            &$results
        ) {
            foreach ($lines as $index => $line) {
                $row = str_getcsv($line, $delimiter);

                if (! is_array($row) || count(array_filter($row, fn ($v) => trim((string) $v) !== '')) === 0) {
                    continue;
                }

                $mapped = $this->mapRow($headers, $row);

                $verificationKey = trim((string) ($mapped['verification_key'] ?? ''));
                $status = trim((string) ($mapped['status'] ?? ''));

                if ($verificationKey === '' || $status === '') {
                    $results[] = [
                        'line' => $index + 2,
                        'status' => 'skipped',
                        'message' => 'verification_key atau status kosong.',
                    ];
                    continue;
                }

                $verificationLabel = $this->resolveVerificationLabel($verificationKey);

                $this->claimVerificationService->updateItem($claimEpisodeId, $verificationKey, [
                    'verification_label' => $verificationLabel,
                    'status' => $status,
                    'finding_notes' => $mapped['finding_notes'] ?? null,
                    'follow_up_notes' => $mapped['follow_up_notes'] ?? null,
                    'source_reference' => $mapped['source_reference'] ?? null,
                    'reviewer_name' => $reviewerName,
                    'reviewer_role' => $reviewerRole,
                ]);

                $processed++;

                $followUpCreated = false;

                if (in_array($status, ['mismatch', 'need_confirmation'], true)) {
                    $this->claimFollowUpService->create($claimEpisodeId, [
                        'category' => $this->mapCategoryFromVerificationKey($verificationKey),
                        'title' => $mapped['title'] ?: ('Follow up ' . $verificationLabel),
                        'target_unit' => $mapped['target_unit'] ?: $this->defaultTargetUnit($verificationKey),
                        'priority' => $mapped['priority'] ?: 'medium',
                        'status' => 'open',
                        'issue_summary' => $mapped['finding_notes'] ?? null,
                        'action_needed' => $mapped['follow_up_notes'] ?? null,
                        'resolution_notes' => null,
                        'created_by_name' => $reviewerName,
                        'assigned_to_name' => $mapped['target_unit'] ?? null,
                    ]);

                    $followUpsCreated++;
                    $followUpCreated = true;
                }

                $results[] = [
                    'line' => $index + 2,
                    'status' => 'imported',
                    'verification_key' => $verificationKey,
                    'verification_status' => $status,
                    'follow_up_created' => $followUpCreated,
                ];
            }
        });

        return [
            'processed' => $processed,
            'follow_ups_created' => $followUpsCreated,
            'results' => $results,
        ];
    }

    protected function detectDelimiter(string $line): string
    {
        if (substr_count($line, "\t") > 0) {
            return "\t";
        }

        if (substr_count($line, ";") > substr_count($line, ",")) {
            return ";";
        }

        return ",";
    }

    protected function normalizeHeaders(array $headers): array
    {
        return array_map(function ($header) {
            $header = strtolower(trim((string) $header));
            $header = str_replace([' ', '-'], '_', $header);
            return $header;
        }, $headers);
    }

    protected function mapRow(array $headers, array $row): array
    {
        $mapped = [];

        foreach ($headers as $index => $header) {
            $mapped[$header] = isset($row[$index]) ? trim((string) $row[$index]) : null;
        }

        return $mapped;
    }

    protected function resolveVerificationLabel(string $verificationKey): string
    {
        return match ($verificationKey) {
            'billing_vs_cppt' => 'Kelengkapan Billing vs CPPT',
            'chronology_vs_cppt' => 'Kesesuaian Form Kronologis vs CPPT',
            'documents_vs_rm' => 'Kelengkapan Berkas vs Rekam Medis',
            default => ucfirst(str_replace('_', ' ', $verificationKey)),
        };
    }

    protected function mapCategoryFromVerificationKey(string $verificationKey): string
    {
        return match ($verificationKey) {
            'billing_vs_cppt' => 'billing',
            'chronology_vs_cppt' => 'chronology',
            'documents_vs_rm' => 'documents',
            default => 'pending',
        };
    }

    protected function defaultTargetUnit(string $verificationKey): string
    {
        return match ($verificationKey) {
            'billing_vs_cppt' => 'billing',
            'chronology_vs_cppt' => 'admisi',
            'documents_vs_rm' => 'rm',
            default => 'casemix',
        };
    }
}