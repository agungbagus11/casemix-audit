<?php

use App\Http\Controllers\InternalApi\ClaimEpisodeController;
use App\Http\Controllers\InternalApi\SyncController;
use App\Http\Controllers\Mock\MockSimrsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InternalApi\ClaimAiResultController;
use App\Http\Controllers\InternalApi\ClaimAuditController;
use App\Http\Controllers\InternalApi\ClaimWorkflowController;

/*
|--------------------------------------------------------------------------
| Internal API
|--------------------------------------------------------------------------
*/

Route::prefix('internal-api')->group(function () {
    Route::post('/sync-discharge', [SyncController::class, 'syncDischarge']);
    Route::post('/sync-episode/{encounterId}', [SyncController::class, 'syncSingleEpisode']);

    Route::get('/claim-episodes', [ClaimEpisodeController::class, 'index']);
    Route::get('/claim-episodes/pending', [ClaimEpisodeController::class, 'pending']);
    Route::get('/claim-episodes/{id}', [ClaimEpisodeController::class, 'show'])
        ->whereNumber('id');
    Route::get('/claim-episodes/by-episode/{episodeNo}', [ClaimEpisodeController::class, 'showByEpisodeNo']);
    Route::post('/claim-episodes/{id}/ai-result', [ClaimAiResultController::class, 'store'])
    ->whereNumber('id');
    Route::post('/claim-episodes/{id}/audit-flags', [ClaimAuditController::class, 'storeFlags'])
        ->whereNumber('id');
    Route::post('/claim-episodes/{id}/status', [ClaimWorkflowController::class, 'updateStatus'])
        ->whereNumber('id');
});


/*
|--------------------------------------------------------------------------
| Mock SIMRS API
|--------------------------------------------------------------------------
*/

Route::prefix('mock-simrs')->group(function () {
    Route::get('/discharge', [MockSimrsController::class, 'discharge']);
    Route::get('/encounters/{encounterId}', [MockSimrsController::class, 'encounterDetail']);
    Route::get('/encounters/{encounterId}/resume', [MockSimrsController::class, 'resume']);
    Route::get('/encounters/{encounterId}/diagnoses', [MockSimrsController::class, 'diagnoses']);
    Route::get('/encounters/{encounterId}/procedures', [MockSimrsController::class, 'procedures']);
    Route::get('/encounters/{encounterId}/billing', [MockSimrsController::class, 'billing']);
    Route::get('/encounters/{encounterId}/sep', [MockSimrsController::class, 'sep']);
    Route::get('/encounters/{encounterId}/documents', [MockSimrsController::class, 'documents']);
    Route::get('/encounters/{encounterId}/labs', [MockSimrsController::class, 'labs']);
    Route::get('/encounters/{encounterId}/radiology', [MockSimrsController::class, 'radiology']);
    Route::get('/encounters/{encounterId}/operation-report', [MockSimrsController::class, 'operationReport']);
    Route::get('/encounters/{encounterId}/cppt', [MockSimrsController::class, 'cppt']);

});

Route::get('/internal-api/test-sync-discharge', function () {
    return response()->json(
        app(\App\Services\ClaimSyncService::class)->syncDischargesByDate('2026-04-15')
    );
});
Route::get('/internal-api/test-ai-result/{id}', function (int $id) {
    return response()->json(
        app(\App\Services\AuditResultService::class)->saveAiResult($id, [
            'model_name' => 'mock-gpt',
            'prompt_version' => 'v1',
            'primary_diagnosis_text' => 'Sepsis ec pneumonia berat',
            'primary_icd10_json' => [
                ['code' => 'A41.9', 'label' => 'Sepsis, unspecified organism', 'confidence' => 0.88],
            ],
            'secondary_icd10_json' => [
                ['code' => 'J18.9', 'label' => 'Pneumonia, unspecified organism', 'confidence' => 0.84],
            ],
            'procedure_json' => [
                ['code' => '96.72', 'label' => 'Continuous mechanical ventilation', 'confidence' => 0.82],
            ],
            'confidence_score' => 88.5,
            'missing_data_json' => [],
            'ai_notes' => 'Mock AI result berhasil disimpan.',
            'raw_response_json' => ['source' => 'test-route'],
        ])
    );
});

Route::get('/internal-api/test-audit-flags/{id}', function (int $id) {
    return response()->json(
        app(\App\Services\AuditResultService::class)->saveAuditFlags($id, [
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
        ])
    );
});

Route::get('/internal-api/test-status/{id}', function (int $id) {
    return response()->json(
        app(\App\Services\AuditResultService::class)->updateEpisodeWorkflow($id, [
            'claim_status' => 'ready_review',
            'audit_status' => 'reviewed',
            'processing_stage' => 'review',
            'notes' => 'Episode siap direview coder.',
            'reviewer_name' => 'SYSTEM TEST',
            'reviewer_role' => 'system',
            'action_type' => 'test_status_update',
            'review_notes' => 'Status diperbarui lewat route test.',
        ])
    );
});
