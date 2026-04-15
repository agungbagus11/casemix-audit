<?php

use App\Http\Controllers\CasemixDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('casemix.index');
});

Route::prefix('casemix')->group(function () {
    Route::get('/', [CasemixDashboardController::class, 'index'])->name('casemix.index');
    Route::get('/{id}', [CasemixDashboardController::class, 'show'])
        ->whereNumber('id')
        ->name('casemix.show');

    Route::post('/sync-mock-discharge', [CasemixDashboardController::class, 'syncMockDischarge'])
        ->name('casemix.sync-mock');

    Route::post('/{id}/run-mock-ai', [CasemixDashboardController::class, 'runMockAi'])
        ->whereNumber('id')
        ->name('casemix.run-mock-ai');

    Route::post('/{id}/run-mock-audit', [CasemixDashboardController::class, 'runMockAudit'])
        ->whereNumber('id')
        ->name('casemix.run-mock-audit');

    Route::post('/{id}/update-review', [CasemixDashboardController::class, 'updateToReview'])
        ->whereNumber('id')
        ->name('casemix.update-review');
});