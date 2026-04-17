<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CasemixDashboardController;
use App\Http\Controllers\CasemixExportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('casemix.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::prefix('casemix')->group(function () {
        Route::get('/', [CasemixDashboardController::class, 'index'])->name('casemix.index');
        Route::get('/{id}', [CasemixDashboardController::class, 'show'])
            ->whereNumber('id')
            ->name('casemix.show');

        Route::post('/sync-mock-discharge', [CasemixDashboardController::class, 'syncMockDischarge'])
            ->middleware('role:admin,casemix')
            ->name('casemix.sync-mock');

        Route::post('/{id}/run-mock-ai', [CasemixDashboardController::class, 'runMockAi'])
            ->middleware('role:admin,casemix')
            ->whereNumber('id')
            ->name('casemix.run-mock-ai');

        Route::post('/{id}/run-mock-audit', [CasemixDashboardController::class, 'runMockAudit'])
            ->middleware('role:admin,casemix,verifier')
            ->whereNumber('id')
            ->name('casemix.run-mock-audit');

        Route::post('/{id}/update-review', [CasemixDashboardController::class, 'updateToReview'])
            ->middleware('role:admin,casemix,verifier,manager')
            ->whereNumber('id')
            ->name('casemix.update-review');

        Route::post('/{id}/verification/{verificationKey}', [CasemixDashboardController::class, 'saveVerification'])
            ->middleware('role:admin,casemix,verifier')
            ->whereNumber('id')
            ->name('casemix.save-verification');

        Route::post('/{id}/follow-up', [CasemixDashboardController::class, 'createFollowUp'])
            ->middleware('role:admin,casemix,verifier')
            ->whereNumber('id')
            ->name('casemix.create-follow-up');

        Route::post('/{id}/follow-up/{followUpId}', [CasemixDashboardController::class, 'updateFollowUp'])
            ->middleware('role:admin,casemix,verifier')
            ->whereNumber('id')
            ->whereNumber('followUpId')
            ->name('casemix.update-follow-up');

        Route::post('/{id}/import-operational', [CasemixDashboardController::class, 'importOperational'])
            ->middleware('role:admin,casemix,verifier')
            ->whereNumber('id')
            ->name('casemix.import-operational');

        Route::get('/export/episodes', [CasemixExportController::class, 'exportEpisodes'])
            ->middleware('role:admin,casemix,manager')
            ->name('casemix.export.episodes');

        Route::get('/export/follow-ups-active', [CasemixExportController::class, 'exportActiveFollowUps'])
            ->middleware('role:admin,casemix,manager')
            ->name('casemix.export.followups');

        Route::get('/export/verification-summary', [CasemixExportController::class, 'exportVerificationSummary'])
            ->middleware('role:admin,casemix,manager')
            ->name('casemix.export.verification-summary');
    });

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->middleware('role:admin,casemix,manager')
        ->name('activity.index');

    Route::prefix('users')
        ->middleware('role:admin')
        ->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('users.index');
            Route::get('/create', [UserManagementController::class, 'create'])->name('users.create');
            Route::post('/', [UserManagementController::class, 'store'])->name('users.store');
            Route::get('/{id}/edit', [UserManagementController::class, 'edit'])
                ->whereNumber('id')
                ->name('users.edit');
            Route::put('/{id}', [UserManagementController::class, 'update'])
                ->whereNumber('id')
                ->name('users.update');
            Route::put('/{id}/password', [UserManagementController::class, 'updatePassword'])
                ->whereNumber('id')
                ->name('users.update-password');
            Route::post('/{id}/toggle-active', [UserManagementController::class, 'toggleActive'])
                ->whereNumber('id')
                ->name('users.toggle-active');
        });
});