<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_audit_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_episode_id')
                ->constrained('claim_episodes')
                ->cascadeOnDelete();

            $table->string('flag_type', 100)->index();
            $table->string('severity', 50)->index();
            $table->string('flag_code', 100)->index();
            $table->string('flag_title', 255);
            $table->text('flag_description')->nullable();
            $table->json('evidence_json')->nullable();
            $table->string('source_type', 50)->default('rule')->index();
            $table->string('status', 50)->default('open')->index();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index(['claim_episode_id', 'status']);
            $table->index(['flag_code', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_audit_flags');
    }
};