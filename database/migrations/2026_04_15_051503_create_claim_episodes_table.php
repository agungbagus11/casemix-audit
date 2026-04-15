<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_episodes', function (Blueprint $table) {
            $table->id();
            $table->string('episode_no', 100)->unique();
            $table->string('simrs_encounter_id', 100)->index();
            $table->string('sep_no', 100)->nullable()->index();
            $table->string('mrn', 100)->index();
            $table->string('patient_name', 255);
            $table->string('care_type', 50)->nullable()->index();
            $table->string('service_unit', 100)->nullable()->index();
            $table->string('doctor_name', 255)->nullable();
            $table->dateTime('admission_at')->nullable()->index();
            $table->dateTime('discharge_at')->nullable()->index();
            $table->string('payer_name', 100)->nullable()->index();
            $table->string('claim_status', 50)->default('draft')->index();
            $table->string('audit_status', 50)->default('pending')->index();
            $table->string('processing_stage', 50)->default('new')->index();
            $table->string('risk_level', 50)->default('unknown')->index();
            $table->unsignedInteger('risk_score')->default(0);
            $table->json('snapshot_json')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['claim_status', 'audit_status']);
            $table->index(['processing_stage', 'risk_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_episodes');
    }
};