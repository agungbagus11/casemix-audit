<?php

namespace App\Http\Controllers\InternalApi;

use App\Http\Controllers\Controller;
use App\Services\ClaimVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClaimVerificationController extends Controller
{
    public function __construct(
        protected ClaimVerificationService $claimVerificationService
    ) {
    }

    public function ensureDefaults(int $id): JsonResponse
    {
        $items = $this->claimVerificationService->ensureDefaultItems($id);

        return response()->json([
            'success' => true,
            'message' => 'Default verification items berhasil dibuat.',
            'data' => $items,
        ]);
    }

    public function update(Request $request, int $id, string $verificationKey): JsonResponse
    {
        $validated = $request->validate([
            'verification_label' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'finding_notes' => ['nullable', 'string'],
            'follow_up_notes' => ['nullable', 'string'],
            'source_reference' => ['nullable', 'string', 'max:255'],
            'reviewer_name' => ['nullable', 'string', 'max:255'],
            'reviewer_role' => ['nullable', 'string', 'max:100'],
        ]);

        $item = $this->claimVerificationService->updateItem($id, $verificationKey, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Verification item berhasil diperbarui.',
            'data' => $item,
        ]);
    }
}