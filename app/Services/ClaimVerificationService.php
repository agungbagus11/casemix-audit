<?php

namespace App\Services;

use App\Models\ClaimEpisode;
use App\Models\ClaimReview;
use App\Models\ClaimVerificationItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClaimVerificationService
{
    public function ensureDefaultItems(int $claimEpisodeId): array
    {
        $episode = ClaimEpisode::find($claimEpisodeId);

        if (! $episode) {
            throw ValidationException::withMessages([
                'claim_episode_id' => 'Claim episode tidak ditemukan.',
            ]);
        }

        $defaults = [
            'billing_vs_cppt' => 'Kelengkapan Billing vs CPPT',
            'chronology_vs_cppt' => 'Kesesuaian Form Kronologis vs CPPT',
            'documents_vs_rm' => 'Kelengkapan Berkas vs Rekam Medis',
        ];

        $items = [];

        foreach ($defaults as $key => $label) {
            $items[] = ClaimVerificationItem::firstOrCreate(
                [
                    'claim_episode_id' => $episode->id,
                    'verification_key' => $key,
                ],
                [
                    'verification_label' => $label,
                    'status' => 'not_checked',
                ]
            );
        }

        return $items;
    }

    public function updateItem(int $claimEpisodeId, string $verificationKey, array $payload): ClaimVerificationItem
    {
        $episode = ClaimEpisode::find($claimEpisodeId);

        if (! $episode) {
            throw ValidationException::withMessages([
                'claim_episode_id' => 'Claim episode tidak ditemukan.',
            ]);
        }

        $allowedStatuses = ['not_checked', 'match', 'mismatch', 'need_confirmation'];

        if (! in_array($payload['status'] ?? '', $allowedStatuses, true)) {
            throw ValidationException::withMessages([
                'status' => 'Status verifikasi tidak valid.',
            ]);
        }

        return DB::transaction(function () use ($episode, $verificationKey, $payload) {
            $item = ClaimVerificationItem::firstOrCreate(
                [
                    'claim_episode_id' => $episode->id,
                    'verification_key' => $verificationKey,
                ],
                [
                    'verification_label' => $payload['verification_label'] ?? $verificationKey,
                    'status' => 'not_checked',
                ]
            );

            $oldData = [
                'status' => $item->status,
                'finding_notes' => $item->finding_notes,
                'follow_up_notes' => $item->follow_up_notes,
                'source_reference' => $item->source_reference,
                'reviewer_name' => $item->reviewer_name,
                'reviewer_role' => $item->reviewer_role,
                'checked_at' => optional($item->checked_at)?->format('Y-m-d H:i:s'),
            ];

            $item->verification_label = $payload['verification_label'] ?? $item->verification_label;
            $item->status = $payload['status'];
            $item->finding_notes = $payload['finding_notes'] ?? null;
            $item->follow_up_notes = $payload['follow_up_notes'] ?? null;
            $item->source_reference = $payload['source_reference'] ?? null;
            $item->reviewer_name = $payload['reviewer_name'] ?? 'Verifier';
            $item->reviewer_role = $payload['reviewer_role'] ?? 'casemix';
            $item->checked_at = now();
            $item->save();

            ClaimReview::create([
                'claim_episode_id' => $episode->id,
                'reviewer_name' => $item->reviewer_name,
                'reviewer_role' => $item->reviewer_role,
                'action_type' => 'verification_item_updated',
                'notes' => 'Checklist verifikasi diperbarui: ' . $item->verification_label,
                'old_data_json' => $oldData,
                'new_data_json' => [
                    'verification_key' => $item->verification_key,
                    'verification_label' => $item->verification_label,
                    'status' => $item->status,
                    'finding_notes' => $item->finding_notes,
                    'follow_up_notes' => $item->follow_up_notes,
                    'source_reference' => $item->source_reference,
                    'reviewer_name' => $item->reviewer_name,
                    'reviewer_role' => $item->reviewer_role,
                    'checked_at' => optional($item->checked_at)?->format('Y-m-d H:i:s'),
                ],
            ]);

            $this->syncEpisodeStatusByVerification($episode->fresh());

            return $item;
        });
    }

    protected function syncEpisodeStatusByVerification(ClaimEpisode $episode): void
    {
        $items = $episode->verificationItems()->get();

        if ($items->isEmpty()) {
            return;
        }

        $hasMismatch = $items->contains(fn ($item) => $item->status === 'mismatch');
        $hasNeedConfirmation = $items->contains(fn ($item) => $item->status === 'need_confirmation');
        $allMatched = $items->every(fn ($item) => $item->status === 'match');

        if ($hasMismatch || $hasNeedConfirmation) {
            $episode->processing_stage = 'review';
            $episode->audit_status = 'flagged';
            $episode->claim_status = 'ready_review';
        } elseif ($allMatched) {
            $episode->processing_stage = 'review';
            $episode->audit_status = 'reviewed';
            $episode->claim_status = 'ready_review';
        }

        $episode->save();
    }
}