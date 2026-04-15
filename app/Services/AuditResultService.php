<?php

namespace App\Services;

use App\Models\ClaimAiResult;
use App\Models\ClaimAuditFlag;
use App\Models\ClaimEpisode;
use App\Models\ClaimReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuditResultService
{
    public function saveAiResult(int $claimEpisodeId, array $payload): ClaimAiResult
    {
        $episode = ClaimEpisode::find($claimEpisodeId);

        if (! $episode) {
            throw ValidationException::withMessages([
                'claim_episode_id' => 'Claim episode tidak ditemukan.',
            ]);
        }

        return DB::transaction(function () use ($episode, $payload) {
            $result = ClaimAiResult::create([
                'claim_episode_id' => $episode->id,
                'model_name' => $payload['model_name'] ?? null,
                'prompt_version' => $payload['prompt_version'] ?? null,
                'primary_diagnosis_text' => $payload['primary_diagnosis_text'] ?? null,
                'primary_icd10_json' => $payload['primary_icd10_json'] ?? [],
                'secondary_icd10_json' => $payload['secondary_icd10_json'] ?? [],
                'procedure_json' => $payload['procedure_json'] ?? [],
                'confidence_score' => (float) ($payload['confidence_score'] ?? 0),
                'missing_data_json' => $payload['missing_data_json'] ?? [],
                'ai_notes' => $payload['ai_notes'] ?? null,
                'raw_response_json' => $payload['raw_response_json'] ?? [],
            ]);

            $episode->processing_stage = 'auditing';
            $episode->audit_status = 'ai_completed';
            $episode->save();

            ClaimReview::create([
                'claim_episode_id' => $episode->id,
                'reviewer_name' => 'SYSTEM',
                'reviewer_role' => 'system',
                'action_type' => 'ai_result_saved',
                'notes' => 'Hasil AI coding disimpan.',
                'old_data_json' => null,
                'new_data_json' => [
                    'claim_ai_result_id' => $result->id,
                    'confidence_score' => $result->confidence_score,
                    'processing_stage' => $episode->processing_stage,
                ],
            ]);

            return $result;
        });
    }

    public function saveAuditFlags(int $claimEpisodeId, array $flags): array
    {
        $episode = ClaimEpisode::find($claimEpisodeId);

        if (! $episode) {
            throw ValidationException::withMessages([
                'claim_episode_id' => 'Claim episode tidak ditemukan.',
            ]);
        }

        return DB::transaction(function () use ($episode, $flags) {
            $createdFlags = [];

            foreach ($flags as $flag) {
                if (! is_array($flag)) {
                    continue;
                }

                $createdFlags[] = ClaimAuditFlag::create([
                    'claim_episode_id' => $episode->id,
                    'flag_type' => $flag['flag_type'] ?? 'general',
                    'severity' => $flag['severity'] ?? 'low',
                    'flag_code' => $flag['flag_code'] ?? 'UNSPECIFIED',
                    'flag_title' => $flag['flag_title'] ?? 'Tanpa Judul Flag',
                    'flag_description' => $flag['flag_description'] ?? null,
                    'evidence_json' => $flag['evidence_json'] ?? [],
                    'source_type' => $flag['source_type'] ?? 'rule',
                    'status' => $flag['status'] ?? 'open',
                    'review_notes' => $flag['review_notes'] ?? null,
                ]);
            }

            $riskScore = $this->calculateRiskScore($episode->id);
            $riskLevel = $this->resolveRiskLevel($riskScore);

            $episode->risk_score = $riskScore;
            $episode->risk_level = $riskLevel;
            $episode->processing_stage = 'document_check';
            $episode->audit_status = count($createdFlags) > 0 ? 'flagged' : 'clear';
            $episode->save();

            ClaimReview::create([
                'claim_episode_id' => $episode->id,
                'reviewer_name' => 'SYSTEM',
                'reviewer_role' => 'system',
                'action_type' => 'audit_flags_saved',
                'notes' => 'Audit flags disimpan.',
                'old_data_json' => null,
                'new_data_json' => [
                    'flags_count' => count($createdFlags),
                    'risk_score' => $riskScore,
                    'risk_level' => $riskLevel,
                    'processing_stage' => $episode->processing_stage,
                ],
            ]);

            return [
                'flags_count' => count($createdFlags),
                'risk_score' => $riskScore,
                'risk_level' => $riskLevel,
            ];
        });
    }

    public function updateEpisodeWorkflow(int $claimEpisodeId, array $payload): ClaimEpisode
    {
        $episode = ClaimEpisode::find($claimEpisodeId);

        if (! $episode) {
            throw ValidationException::withMessages([
                'claim_episode_id' => 'Claim episode tidak ditemukan.',
            ]);
        }

        return DB::transaction(function () use ($episode, $payload) {
            $oldData = [
                'claim_status' => $episode->claim_status,
                'audit_status' => $episode->audit_status,
                'processing_stage' => $episode->processing_stage,
                'risk_level' => $episode->risk_level,
                'risk_score' => $episode->risk_score,
                'notes' => $episode->notes,
            ];

            $allowedStages = ['new', 'ai_coding', 'auditing', 'document_check', 'review', 'done'];
            $allowedClaimStatuses = ['draft', 'ready_review', 'approved', 'rejected', 'submitted'];
            $allowedAuditStatuses = ['pending', 'ai_completed', 'clear', 'flagged', 'reviewed'];

            if (isset($payload['processing_stage']) && in_array($payload['processing_stage'], $allowedStages, true)) {
                $episode->processing_stage = $payload['processing_stage'];
            }

            if (isset($payload['claim_status']) && in_array($payload['claim_status'], $allowedClaimStatuses, true)) {
                $episode->claim_status = $payload['claim_status'];
            }

            if (isset($payload['audit_status']) && in_array($payload['audit_status'], $allowedAuditStatuses, true)) {
                $episode->audit_status = $payload['audit_status'];
            }

            if (isset($payload['risk_level'])) {
                $episode->risk_level = (string) $payload['risk_level'];
            }

            if (isset($payload['risk_score'])) {
                $episode->risk_score = (int) $payload['risk_score'];
            }

            if (array_key_exists('notes', $payload)) {
                $episode->notes = $payload['notes'];
            }

            $episode->save();

            ClaimReview::create([
                'claim_episode_id' => $episode->id,
                'reviewer_name' => $payload['reviewer_name'] ?? 'SYSTEM',
                'reviewer_role' => $payload['reviewer_role'] ?? 'system',
                'action_type' => $payload['action_type'] ?? 'workflow_updated',
                'notes' => $payload['review_notes'] ?? 'Workflow episode diperbarui.',
                'old_data_json' => $oldData,
                'new_data_json' => [
                    'claim_status' => $episode->claim_status,
                    'audit_status' => $episode->audit_status,
                    'processing_stage' => $episode->processing_stage,
                    'risk_level' => $episode->risk_level,
                    'risk_score' => $episode->risk_score,
                    'notes' => $episode->notes,
                ],
            ]);

            return $episode;
        });
    }

    protected function calculateRiskScore(int $claimEpisodeId): int
    {
        $flags = ClaimAuditFlag::where('claim_episode_id', $claimEpisodeId)->get();

        $score = 0;

        foreach ($flags as $flag) {
            $score += match ($flag->severity) {
                'critical' => 40,
                'high' => 25,
                'medium' => 15,
                'low' => 5,
                default => 5,
            };
        }

        return min($score, 100);
    }

    protected function resolveRiskLevel(int $score): string
    {
        return match (true) {
            $score >= 70 => 'high',
            $score >= 40 => 'medium',
            $score >= 1 => 'low',
            default => 'clear',
        };
    }
}