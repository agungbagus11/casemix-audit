<?php

namespace App\Http\Controllers;

use App\Models\ClaimEpisode;
use App\Models\ClaimFollowUp;
use App\Models\ClaimVerificationItem;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CasemixExportController extends Controller
{
    public function exportEpisodes(Request $request): StreamedResponse
    {
        $q = trim((string) $request->get('q', ''));
        $stage = trim((string) $request->get('stage', ''));
        $risk = trim((string) $request->get('risk', ''));

        $query = ClaimEpisode::query()
            ->withCount([
                'documents',
                'auditFlags',
                'reviews',
                'verificationItems',
                'followUps',
            ])
            ->with('latestAiResult')
            ->orderByDesc('updated_at')
            ->orderByDesc('id');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('episode_no', 'like', "%{$q}%")
                    ->orWhere('simrs_encounter_id', 'like', "%{$q}%")
                    ->orWhere('mrn', 'like', "%{$q}%")
                    ->orWhere('patient_name', 'like', "%{$q}%")
                    ->orWhere('service_unit', 'like', "%{$q}%")
                    ->orWhere('doctor_name', 'like', "%{$q}%");
            });
        }

        if ($stage !== '') {
            $query->where('processing_stage', $stage);
        }

        if ($risk !== '') {
            $query->where('risk_level', $risk);
        }

        $filename = 'casemix_episodes_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Episode No',
                'Encounter ID',
                'SEP',
                'MRN',
                'Patient Name',
                'Service Unit',
                'Doctor Name',
                'Care Type',
                'Admission At',
                'Discharge At',
                'Claim Status',
                'Audit Status',
                'Processing Stage',
                'Risk Level',
                'Risk Score',
                'AI Confidence',
                'Documents Count',
                'Audit Flags Count',
                'Verification Items Count',
                'Follow Ups Count',
                'Updated At',
            ]);

            $query->chunk(200, function ($episodes) use ($handle) {
                foreach ($episodes as $episode) {
                    fputcsv($handle, [
                        $episode->episode_no,
                        $episode->simrs_encounter_id,
                        $episode->sep_no,
                        $episode->mrn,
                        $episode->patient_name,
                        $episode->service_unit,
                        $episode->doctor_name,
                        $episode->care_type,
                        optional($episode->admission_at)?->format('Y-m-d H:i:s'),
                        optional($episode->discharge_at)?->format('Y-m-d H:i:s'),
                        $episode->claim_status,
                        $episode->audit_status,
                        $episode->processing_stage,
                        $episode->risk_level,
                        $episode->risk_score,
                        optional($episode->latestAiResult)->confidence_score,
                        $episode->documents_count,
                        $episode->audit_flags_count,
                        $episode->verification_items_count,
                        $episode->follow_ups_count,
                        optional($episode->updated_at)?->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportActiveFollowUps(): StreamedResponse
    {
        $filename = 'casemix_followups_active_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Episode No',
                'Patient Name',
                'MRN',
                'Category',
                'Title',
                'Target Unit',
                'Priority',
                'Status',
                'Issue Summary',
                'Action Needed',
                'Resolution Notes',
                'Created By',
                'Assigned To',
                'Due At',
                'Resolved At',
                'Created At',
            ]);

            ClaimFollowUp::query()
                ->with('episode')
                ->whereIn('status', ['open', 'waiting'])
                ->orderByRaw("
                    CASE
                        WHEN priority = 'high' THEN 1
                        WHEN priority = 'medium' THEN 2
                        ELSE 3
                    END
                ")
                ->latest()
                ->chunk(200, function ($followUps) use ($handle) {
                    foreach ($followUps as $followUp) {
                        fputcsv($handle, [
                            optional($followUp->episode)->episode_no,
                            optional($followUp->episode)->patient_name,
                            optional($followUp->episode)->mrn,
                            $followUp->category,
                            $followUp->title,
                            $followUp->target_unit,
                            $followUp->priority,
                            $followUp->status,
                            $followUp->issue_summary,
                            $followUp->action_needed,
                            $followUp->resolution_notes,
                            $followUp->created_by_name,
                            $followUp->assigned_to_name,
                            optional($followUp->due_at)?->format('Y-m-d H:i:s'),
                            optional($followUp->resolved_at)?->format('Y-m-d H:i:s'),
                            optional($followUp->created_at)?->format('Y-m-d H:i:s'),
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportVerificationSummary(): StreamedResponse
    {
        $filename = 'casemix_verification_summary_' . now()->format('Ymd_His') . '.csv';

        $keys = [
            'billing_vs_cppt' => 'Kelengkapan Billing vs CPPT',
            'chronology_vs_cppt' => 'Kesesuaian Form Kronologis vs CPPT',
            'documents_vs_rm' => 'Kelengkapan Berkas vs Rekam Medis',
        ];

        $rows = [];

        foreach ($keys as $key => $label) {
            $rows[] = [
                'verification_key' => $key,
                'verification_label' => $label,
                'not_checked' => ClaimVerificationItem::where('verification_key', $key)->where('status', 'not_checked')->count(),
                'match' => ClaimVerificationItem::where('verification_key', $key)->where('status', 'match')->count(),
                'mismatch' => ClaimVerificationItem::where('verification_key', $key)->where('status', 'mismatch')->count(),
                'need_confirmation' => ClaimVerificationItem::where('verification_key', $key)->where('status', 'need_confirmation')->count(),
            ];
        }

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Verification Key',
                'Verification Label',
                'Not Checked',
                'Match',
                'Mismatch',
                'Need Confirmation',
            ]);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['verification_key'],
                    $row['verification_label'],
                    $row['not_checked'],
                    $row['match'],
                    $row['mismatch'],
                    $row['need_confirmation'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}