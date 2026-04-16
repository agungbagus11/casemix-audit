<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_verification_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('claim_episode_id')
                ->constrained('claim_episodes')
                ->cascadeOnDelete();

            $table->string('verification_key', 100)->index();
            $table->string('verification_label', 255);
            $table->string('status', 50)->default('not_checked')->index();

            $table->text('finding_notes')->nullable();
            $table->text('follow_up_notes')->nullable();

            $table->string('source_reference', 255)->nullable();
            $table->string('reviewer_name', 255)->nullable();
            $table->string('reviewer_role', 100)->nullable();
            $table->dateTime('checked_at')->nullable();

            $table->timestamps();

            $table->unique(['claim_episode_id', 'verification_key'], 'uniq_episode_verification_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_verification_items');
    }
};