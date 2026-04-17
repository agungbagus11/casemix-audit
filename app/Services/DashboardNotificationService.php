<?php

namespace App\Services;

use App\Models\ClaimEpisode;
use App\Models\ClaimFollowUp;
use App\Models\ClaimVerificationItem;
use Illuminate\Contracts\Auth\Authenticatable;

class DashboardNotificationService
{
    public function getForUser(?Authenticatable $user): array
    {
        if (! $user) {
            return [
                'total_alerts' => 0,
                'cards' => [],
                'items' => [],
            ];
        }

        $highPriorityFollowUps = ClaimFollowUp::query()
            ->with('episode:id,episode_no,patient_name')
            ->where('priority', 'high')
            ->whereIn('status', ['open', 'waiting'])
            ->latest()
            ->limit(5)
            ->get();

        $needConfirmationItems = ClaimVerificationItem::query()
            ->with('episode:id,episode_no,patient_name')
            ->whereIn('status', ['mismatch', 'need_confirmation'])
            ->latest()
            ->limit(5)
            ->get();

        $notCheckedItems = ClaimVerificationItem::query()
            ->with('episode:id,episode_no,patient_name')
            ->where('status', 'not_checked')
            ->latest()
            ->limit(5)
            ->get();

        $highRiskEpisodes = ClaimEpisode::query()
            ->where('risk_level', 'high')
            ->where('processing_stage', '!=', 'done')
            ->latest()
            ->limit(5)
            ->get();

        $cards = [
            [
                'key' => 'high_priority_followups',
                'label' => 'Follow-up Prioritas Tinggi',
                'count' => ClaimFollowUp::where('priority', 'high')->whereIn('status', ['open', 'waiting'])->count(),
                'color' => 'rose',
            ],
            [
                'key' => 'verification_attention',
                'label' => 'Mismatch / Need Confirmation',
                'count' => ClaimVerificationItem::whereIn('status', ['mismatch', 'need_confirmation'])->count(),
                'color' => 'amber',
            ],
            [
                'key' => 'not_checked',
                'label' => 'Checklist Belum Dicek',
                'count' => ClaimVerificationItem::where('status', 'not_checked')->count(),
                'color' => 'blue',
            ],
            [
                'key' => 'high_risk_open',
                'label' => 'Episode High Risk Belum Selesai',
                'count' => ClaimEpisode::where('risk_level', 'high')->where('processing_stage', '!=', 'done')->count(),
                'color' => 'violet',
            ],
        ];

        $items = [];

        if ($user->hasRole(['admin', 'casemix', 'verifier'])) {
            foreach ($highPriorityFollowUps as $row) {
                $items[] = [
                    'type' => 'follow_up',
                    'severity' => 'high',
                    'title' => 'Follow-up prioritas tinggi',
                    'description' => ($row->title ?: '-') . ' · ' . (optional($row->episode)->patient_name ?: '-'),
                    'link' => optional($row->episode)->id ? route('casemix.show', $row->episode->id) : null,
                    'meta' => optional($row->episode)->episode_no,
                ];
            }

            foreach ($needConfirmationItems as $row) {
                $items[] = [
                    'type' => 'verification',
                    'severity' => $row->status === 'mismatch' ? 'high' : 'medium',
                    'title' => 'Checklist perlu perhatian',
                    'description' => ($row->verification_label ?: $row->verification_key) . ' · ' . (optional($row->episode)->patient_name ?: '-'),
                    'link' => optional($row->episode)->id ? route('casemix.show', $row->episode->id) : null,
                    'meta' => strtoupper((string) $row->status),
                ];
            }
        }

        if ($user->hasRole(['admin', 'casemix', 'manager'])) {
            foreach ($highRiskEpisodes as $row) {
                $items[] = [
                    'type' => 'episode',
                    'severity' => 'high',
                    'title' => 'Episode high risk belum selesai',
                    'description' => ($row->episode_no ?: '-') . ' · ' . ($row->patient_name ?: '-'),
                    'link' => route('casemix.show', $row->id),
                    'meta' => 'Risk ' . $row->risk_score,
                ];
            }
        }

        if ($user->hasRole(['admin', 'casemix', 'verifier'])) {
            foreach ($notCheckedItems as $row) {
                $items[] = [
                    'type' => 'not_checked',
                    'severity' => 'low',
                    'title' => 'Checklist belum dicek',
                    'description' => ($row->verification_label ?: $row->verification_key) . ' · ' . (optional($row->episode)->patient_name ?: '-'),
                    'link' => optional($row->episode)->id ? route('casemix.show', $row->episode->id) : null,
                    'meta' => 'NOT CHECKED',
                ];
            }
        }

        $items = array_slice($items, 0, 10);

        return [
            'total_alerts' => collect($cards)->sum('count'),
            'cards' => $cards,
            'items' => $items,
        ];
    }
}