<?php

namespace App\Http\Controllers\InternalApi;

use App\Http\Controllers\Controller;
use App\Services\ClaimSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SyncController extends Controller
{
    public function __construct(
        protected ClaimSyncService $claimSyncService
    ) {
    }

    public function syncDischarge(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date'],
        ]);

        $result = $this->claimSyncService->syncDischargesByDate(
            $validated['date'] ?? now()->toDateString()
        );

        return response()->json([
            'success' => true,
            'message' => 'Sinkronisasi discharge selesai.',
            'data' => $result,
        ]);
    }

    public function syncSingleEpisode(Request $request, string $encounterId): JsonResponse
    {
        if (blank($encounterId)) {
            throw ValidationException::withMessages([
                'encounter_id' => 'Encounter ID wajib diisi.',
            ]);
        }

        $result = $this->claimSyncService->syncSingleEpisode($encounterId);

        return response()->json([
            'success' => true,
            'message' => 'Sinkronisasi episode berhasil.',
            'data' => $result,
        ]);
    }
}