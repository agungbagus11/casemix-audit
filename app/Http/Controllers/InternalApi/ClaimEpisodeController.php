<?php

namespace App\Http\Controllers\InternalApi;

use App\Http\Controllers\Controller;
use App\Models\ClaimEpisode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClaimEpisodeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->integer('per_page', 10);
        $stage = trim((string) $request->get('stage', ''));
        $claimStatus = trim((string) $request->get('claim_status', ''));
        $auditStatus = trim((string) $request->get('audit_status', ''));
        $riskLevel = trim((string) $request->get('risk_level', ''));
        $q = trim((string) $request->get('q', ''));

        $query = ClaimEpisode::query()
            ->withCount([
                'documents',
                'auditFlags',
                'reviews',
            ])
            ->with([
                'latestAiResult:id,claim_episode_id,confidence_score,created_at',
            ])
            ->orderByDesc('discharge_at')
            ->orderByDesc('id');

        if ($stage !== '') {
            $query->where('processing_stage', $stage);
        }

        if ($claimStatus !== '') {
            $query->where('claim_status', $claimStatus);
        }

        if ($auditStatus !== '') {
            $query->where('audit_status', $auditStatus);
        }

        if ($riskLevel !== '') {
            $query->where('risk_level', $riskLevel);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('episode_no', 'like', "%{$q}%")
                    ->orWhere('simrs_encounter_id', 'like', "%{$q}%")
                    ->orWhere('sep_no', 'like', "%{$q}%")
                    ->orWhere('mrn', 'like', "%{$q}%")
                    ->orWhere('patient_name', 'like', "%{$q}%")
                    ->orWhere('service_unit', 'like', "%{$q}%")
                    ->orWhere('doctor_name', 'like', "%{$q}%");
            });
        }

        $episodes = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Daftar claim episode berhasil diambil.',
            'data' => $episodes,
        ]);
    }

    public function pending(Request $request): JsonResponse
    {
        $stage = trim((string) $request->get('stage', 'new'));
        $limit = (int) $request->integer('limit', 20);

        $episodes = ClaimEpisode::query()
            ->with([
                'latestAiResult:id,claim_episode_id,confidence_score,created_at',
            ])
            ->where('processing_stage', $stage)
            ->orderBy('discharge_at')
            ->limit($limit)
            ->get([
                'id',
                'episode_no',
                'simrs_encounter_id',
                'sep_no',
                'mrn',
                'patient_name',
                'service_unit',
                'doctor_name',
                'discharge_at',
                'claim_status',
                'audit_status',
                'processing_stage',
                'risk_level',
                'risk_score',
                'created_at',
                'updated_at',
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Daftar episode pending berhasil diambil.',
            'data' => $episodes,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $episode = ClaimEpisode::query()
            ->with([
                'documents',
                'aiResults' => fn ($q) => $q->latest(),
                'auditFlags' => fn ($q) => $q->latest(),
                'reviews' => fn ($q) => $q->latest(),
            ])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail claim episode berhasil diambil.',
            'data' => $episode,
        ]);
    }

    public function showByEpisodeNo(string $episodeNo): JsonResponse
    {
        $episode = ClaimEpisode::query()
            ->with([
                'documents',
                'aiResults' => fn ($q) => $q->latest(),
                'auditFlags' => fn ($q) => $q->latest(),
                'reviews' => fn ($q) => $q->latest(),
            ])
            ->where('episode_no', $episodeNo)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Detail claim episode berhasil diambil.',
            'data' => $episode,
        ]);
    }
}