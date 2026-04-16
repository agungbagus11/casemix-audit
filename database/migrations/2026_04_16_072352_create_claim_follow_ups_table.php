<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_follow_ups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('claim_episode_id')
                ->constrained('claim_episodes')
                ->cascadeOnDelete();

            $table->string('category', 100)->index(); // billing, chronology, documents, coding, pending, dispute
            $table->string('title', 255);
            $table->string('target_unit', 100)->nullable()->index(); // admisi, rm, dpjp, casemix
            $table->string('priority', 50)->default('medium')->index(); // low, medium, high
            $table->string('status', 50)->default('open')->index(); // open, waiting, resolved, closed

            $table->text('issue_summary')->nullable();
            $table->text('action_needed')->nullable();
            $table->text('resolution_notes')->nullable();

            $table->string('created_by_name', 255)->nullable();
            $table->string('assigned_to_name', 255)->nullable();
            $table->dateTime('due_at')->nullable();
            $table->dateTime('resolved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_follow_ups');
    }
};