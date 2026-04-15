<?php

namespace App\Http\Controllers\InternalApi;

use App\Http\Controllers\Controller;
use App\Services\AuditResultService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClaimAiResultController extends Controller
{
    public function __construct(
        protected AuditResultService $auditResultService
    ) {
    }

    public function store(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'model_name' => ['nullable', 'string', 'max:100'],
            'prompt_version' => ['nullable', 'string', 'max:50'],
            'primary_diagnosis_text' => ['nullable', 'string'],
            'primary_icd10_json' => ['nullable', 'array'],
            'secondary_icd10_json' => ['nullable', 'array'],
            'procedure_json' => ['nullable', 'array'],
            'confidence_score' => ['nullable', 'numeric'],
            'missing_data_json' => ['nullable', 'array'],
            'ai_notes' => ['nullable', 'string'],
            'raw_response_json' => ['nullable', 'array'],
        ]);

        $result = $this->auditResultService->saveAiResult($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Hasil AI berhasil disimpan.',
            'data' => $result,
        ]);
    }
}