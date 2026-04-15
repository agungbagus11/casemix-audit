<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_episode_id')
                ->constrained('claim_episodes')
                ->cascadeOnDelete();

            $table->string('reviewer_name', 255);
            $table->string('reviewer_role', 100)->index();
            $table->string('action_type', 100)->index();
            $table->text('notes')->nullable();
            $table->json('old_data_json')->nullable();
            $table->json('new_data_json')->nullable();
            $table->timestamps();

            $table->index(['claim_episode_id', 'action_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_reviews');
    }
};