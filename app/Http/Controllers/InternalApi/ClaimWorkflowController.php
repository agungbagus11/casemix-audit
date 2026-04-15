<?php

namespace App\Http\Controllers\InternalApi;

use App\Http\Controllers\Controller;
use App\Services\AuditResultService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClaimWorkflowController extends Controller
{
    public function __construct(
        protected AuditResultService $auditResultService
    ) {
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'claim_status' => ['nullable', 'string', 'max:50'],
            'audit_status' => ['nullable', 'string', 'max:50'],
            'processing_stage' => ['nullable', 'string', 'max:50'],
            'risk_level' => ['nullable', 'string', 'max:50'],
            'risk_score' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
            'reviewer_name' => ['nullable', 'string', 'max:255'],
            'reviewer_role' => ['nullable', 'string', 'max:100'],
            'action_type' => ['nullable', 'string', 'max:100'],
            'review_notes' => ['nullable', 'string'],
        ]);

        $episode = $this->auditResultService->updateEpisodeWorkflow($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Workflow episode berhasil diperbarui.',
            'data' => $episode,
        ]);
    }
}