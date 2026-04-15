<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_ai_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_episode_id')
                ->constrained('claim_episodes')
                ->cascadeOnDelete();

            $table->string('model_name', 100)->nullable();
            $table->string('prompt_version', 50)->nullable();

            $table->text('primary_diagnosis_text')->nullable();
            $table->json('primary_icd10_json')->nullable();
            $table->json('secondary_icd10_json')->nullable();
            $table->json('procedure_json')->nullable();

            $table->decimal('confidence_score', 5, 2)->default(0);
            $table->json('missing_data_json')->nullable();
            $table->text('ai_notes')->nullable();
            $table->json('raw_response_json')->nullable();
            $table->timestamps();

            $table->index(['claim_episode_id', 'confidence_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_ai_results');
    }
};