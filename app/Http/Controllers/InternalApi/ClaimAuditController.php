<?php

namespace App\Http\Controllers\InternalApi;

use App\Http\Controllers\Controller;
use App\Services\AuditResultService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClaimAuditController extends Controller
{
    public function __construct(
        protected AuditResultService $auditResultService
    ) {
    }

    public function storeFlags(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'flags' => ['required', 'array'],
            'flags.*.flag_type' => ['nullable', 'string', 'max:100'],
            'flags.*.severity' => ['nullable', 'string', 'max:50'],
            'flags.*.flag_code' => ['nullable', 'string', 'max:100'],
            'flags.*.flag_title' => ['nullable', 'string', 'max:255'],
            'flags.*.flag_description' => ['nullable', 'string'],
            'flags.*.evidence_json' => ['nullable', 'array'],
            'flags.*.source_type' => ['nullable', 'string', 'max:50'],
            'flags.*.status' => ['nullable', 'string', 'max:50'],
            'flags.*.review_notes' => ['nullable', 'string'],
        ]);

        $result = $this->auditResultService->saveAuditFlags($id, $validated['flags']);

        return response()->json([
            'success' => true,
            'message' => 'Audit flags berhasil disimpan.',
            'data' => $result,
        ]);
    }
}