<?php

namespace App\Http\Controllers;

use App\Models\ClaimEpisode;
use App\Services\AuditResultService;
use App\Services\ClaimSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CasemixDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $stage = trim((string) $request->get('stage', ''));
        $risk = trim((string) $request->get('risk', ''));

        $query = ClaimEpisode::query()
            ->with('latestAiResult')
            ->withCount([
                'documents',
                'auditFlags',
                'reviews',
            ])
            ->orderByRaw("
                CASE
                    WHEN risk_level = 'high' THEN 1
                    WHEN risk_level = 'medium' THEN 2
                    WHEN risk_level = 'low' THEN 3
                    ELSE 4
                END
            ")
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

        $episodes = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => ClaimEpisode::count(),
            'new' => ClaimEpisode::where('processing_stage', 'new')->count(),
            'review' => ClaimEpisode::where('processing_stage', 'review')->count(),
            'done' => ClaimEpisode::where('processing_stage', 'done')->count(),
            'high_risk' => ClaimEpisode::where('risk_level', 'high')->count(),
            'medium_risk' => ClaimEpisode::where('risk_level', 'medium')->count(),
            'flagged' => ClaimEpisode::where('audit_status', 'flagged')->count(),
            'ready_review' => ClaimEpisode::where('claim_status', 'ready_review')->count(),
        ];

        return view('casemix.index', compact('episodes', 'stats', 'q', 'stage', 'risk'));
    }

    public function show(int $id): View
    {
        $episode = ClaimEpisode::query()
            ->with([
                'documents',
                'aiResults' => fn ($q) => $q->latest(),
                'auditFlags' => fn ($q) => $q->latest(),
                'reviews' => fn ($q) => $q->latest(),
            ])
            ->findOrFail($id);

        $snapshot = $episode->snapshot_json ?? [];

        $cpptText = (string) data_get($snapshot, 'clinical_data.cppt_text', '');
        $resumeText = (string) data_get($snapshot, 'clinical_data.resume_text', '');
        $billingItems = (array) data_get($snapshot, 'administrative_data.billing_items', []);
        $documents = $episode->documents;

        $verificationPanels = [
            [
                'key' => 'billing_cppt',
                'title' => 'Kelengkapan Billing vs CPPT',
                'desc' => 'Membandingkan item billing dengan aktivitas/tindakan yang tertulis di CPPT dan resume.',
                'status' => count($billingItems) > 0 && $cpptText !== '' ? 'ready_check' : 'incomplete',
                'points' => [
                    'Apakah tindakan/alat/visit yang muncul di billing punya jejak di CPPT?',
                    'Apakah ada billing besar tanpa dukungan narasi klinis?',
                    'Apakah item penunjang sudah sejalan dengan kronologi perawatan?',
                ],
            ],
            [
                'key' => 'chronology_cppt',
                'title' => 'Kesesuaian Form Kronologis vs CPPT',
                'desc' => 'Panel untuk telekonfirmasi admisi bila ada kronologis yang tidak sinkron dengan CPPT.',
                'status' => $cpptText !== '' ? 'ready_check' : 'incomplete',
                'points' => [
                    'Apakah tanggal/jam kejadian sesuai urutan di CPPT?',
                    'Apakah alasan masuk rawat inap / IGD konsisten?',
                    'Apakah narasi kronologis mendukung klaim yang diajukan?',
                ],
            ],
            [
                'key' => 'documents_rm',
                'title' => 'Kelengkapan Berkas vs Rekam Medis',
                'desc' => 'Panel koordinasi dengan RM untuk memastikan bundling berkas lengkap.',
                'status' => $documents->count() > 0 ? 'ready_check' : 'incomplete',
                'points' => [
                    'Resume medis oleh DPJP',
                    'Laporan operasi / tindakan bila ada',
                    'Hasil penunjang utama yang menjadi dasar klaim',
                    'Berkas lain sesuai kebutuhan verifikasi',
                ],
            ],
        ];

        return view('casemix.show', compact(
            'episode',
            'snapshot',
            'verificationPanels',
            'cpptText',
            'resumeText',
            'billingItems'
        ));
    }

    public function syncMockDischarge(): RedirectResponse
    {
        app(ClaimSyncService::class)->syncDischargesByDate('2026-04-15');

        return redirect()
            ->route('casemix.index')
            ->with('success', 'Mock discharge berhasil disinkronkan.');
    }

    public function runMockAi(int $id): RedirectResponse
    {
        app(AuditResultService::class)->saveAiResult($id, [
            'model_name' => 'mock-gpt',
            'prompt_version' => 'v1',
            'primary_diagnosis_text' => 'Sepsis ec pneumonia berat',
            'primary_icd10_json' => [
                [
                    'code' => 'A41.9',
                    'label' => 'Sepsis, unspecified organism',
                    'confidence' => 0.88,
                ],
            ],
            'secondary_icd10_json' => [
                [
                    'code' => 'J18.9',
                    'label' => 'Pneumonia, unspecified organism',
                    'confidence' => 0.84,
                ],
            ],
            'procedure_json' => [
                [
                    'code' => '96.72',
                    'label' => 'Continuous mechanical ventilation',
                    'confidence' => 0.82,
                ],
            ],
            'confidence_score' => 88.5,
            'missing_data_json' => [],
            'ai_notes' => 'Mock AI result berhasil disimpan dari dashboard.',
            'raw_response_json' => [
                'source' => 'dashboard',
            ],
        ]);

        return redirect()
            ->route('casemix.index')
            ->with('success', "Mock AI result untuk episode ID {$id} berhasil disimpan.");
    }

    public function runMockAudit(int $id): RedirectResponse
    {
        app(AuditResultService::class)->saveAuditFlags($id, [
            [
                'flag_type' => 'clinical_mismatch',
                'severity' => 'high',
                'flag_code' => 'DX_PROC_MISMATCH',
                'flag_title' => 'Diagnosis dan prosedur perlu review',
                'flag_description' => 'Perlu verifikasi kesesuaian diagnosis dengan tindakan.',
                'evidence_json' => [
                    'resume' => 'Pasien sepsis pneumonia',
                    'procedure' => 'Ventilator mekanik',
                ],
                'source_type' => 'rule',
                'status' => 'open',
            ],
            [
                'flag_type' => 'document_missing',
                'severity' => 'medium',
                'flag_code' => 'DOC_MISSING_OPREPORT',
                'flag_title' => 'Laporan operasi belum tersedia',
                'flag_description' => 'Dokumen operasi belum terlampir.',
                'evidence_json' => [
                    'document_type' => 'laporan_operasi',
                ],
                'source_type' => 'rule',
                'status' => 'open',
            ],
        ]);

        return redirect()
            ->route('casemix.index')
            ->with('success', "Mock audit flags untuk episode ID {$id} berhasil disimpan.");
    }

    public function updateToReview(int $id): RedirectResponse
    {
        app(AuditResultService::class)->updateEpisodeWorkflow($id, [
            'claim_status' => 'ready_review',
            'audit_status' => 'reviewed',
            'processing_stage' => 'review',
            'notes' => 'Episode siap direview coder.',
            'reviewer_name' => 'DASHBOARD SYSTEM',
            'reviewer_role' => 'system',
            'action_type' => 'dashboard_status_update',
            'review_notes' => 'Status diperbarui dari dashboard.',
        ]);

        return redirect()
            ->route('casemix.index')
            ->with('success', "Status episode ID {$id} berhasil diperbarui ke review.");
    }
}