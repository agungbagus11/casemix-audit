<?php

namespace App\Services;

use App\Models\ClaimEpisode;
use App\Models\ClaimFollowUp;
use App\Models\ClaimReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClaimFollowUpService
{
    public function create(int $claimEpisodeId, array $payload): ClaimFollowUp
    {
        $episode = ClaimEpisode::find($claimEpisodeId);

        if (! $episode) {
            throw ValidationException::withMessages([
                'claim_episode_id' => 'Claim episode tidak ditemukan.',
            ]);
        }

        return DB::transaction(function () use ($episode, $payload) {
            $followUp = ClaimFollowUp::create([
                'claim_episode_id' => $episode->id,
                'category' => $payload['category'] ?? 'pending',
                'title' => $payload['title'] ?? 'Follow up baru',
                'target_unit' => $payload['target_unit'] ?? null,
                'priority' => $payload['priority'] ?? 'medium',
                'status' => $payload['status'] ?? 'open',
                'issue_summary' => $payload['issue_summary'] ?? null,
                'action_needed' => $payload['action_needed'] ?? null,
                'resolution_notes' => $payload['resolution_notes'] ?? null,
                'created_by_name' => $payload['created_by_name'] ?? 'Casemix',
                'assigned_to_name' => $payload['assigned_to_name'] ?? null,
                'due_at' => $payload['due_at'] ?? null,
                'resolved_at' => in_array(($payload['status'] ?? 'open'), ['resolved', 'closed'], true) ? now() : null,
            ]);

            ClaimReview::create([
                'claim_episode_id' => $episode->id,
                'reviewer_name' => $payload['created_by_name'] ?? 'Casemix',
                'reviewer_role' => 'follow_up',
                'action_type' => 'follow_up_created',
                'notes' => 'Follow up baru dibuat: ' . $followUp->title,
                'old_data_json' => null,
                'new_data_json' => $followUp->toArray(),
            ]);

            if (in_array($followUp->status, ['open', 'waiting'], true)) {
                $episode->processing_stage = 'review';
                $episode->claim_status = 'ready_review';
                $episode->save();
            }

            return $followUp;
        });
    }

    public function update(int $followUpId, array $payload): ClaimFollowUp
    {
        $followUp = ClaimFollowUp::find($followUpId);

        if (! $followUp) {
            throw ValidationException::withMessages([
                'follow_up_id' => 'Follow up tidak ditemukan.',
            ]);
        }

        return DB::transaction(function () use ($followUp, $payload) {
            $oldData = $followUp->toArray();

            $followUp->category = $payload['category'] ?? $followUp->category;
            $followUp->title = $payload['title'] ?? $followUp->title;
            $followUp->target_unit = $payload['target_unit'] ?? $followUp->target_unit;
            $followUp->priority = $payload['priority'] ?? $followUp->priority;
            $followUp->status = $payload['status'] ?? $followUp->status;
            $followUp->issue_summary = $payload['issue_summary'] ?? $followUp->issue_summary;
            $followUp->action_needed = $payload['action_needed'] ?? $followUp->action_needed;
            $followUp->resolution_notes = $payload['resolution_notes'] ?? $followUp->resolution_notes;
            $followUp->assigned_to_name = $payload['assigned_to_name'] ?? $followUp->assigned_to_name;
            $followUp->due_at = $payload['due_at'] ?? $followUp->due_at;

            if (in_array($followUp->status, ['resolved', 'closed'], true) && empty($followUp->resolved_at)) {
                $followUp->resolved_at = now();
            }

            $followUp->save();

            ClaimReview::create([
                'claim_episode_id' => $followUp->claim_episode_id,
                'reviewer_name' => $payload['updated_by_name'] ?? 'Casemix',
                'reviewer_role' => 'follow_up',
                'action_type' => 'follow_up_updated',
                'notes' => 'Follow up diperbarui: ' . $followUp->title,
                'old_data_json' => $oldData,
                'new_data_json' => $followUp->toArray(),
            ]);

            return $followUp;
        });
    }
}