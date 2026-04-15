<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_episode_id')
                ->constrained('claim_episodes')
                ->cascadeOnDelete();

            $table->string('document_type', 100)->index();
            $table->text('file_url')->nullable();
            $table->string('file_name', 255)->nullable();
            $table->boolean('is_required')->default(false)->index();
            $table->boolean('is_available')->default(false)->index();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['claim_episode_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_documents');
    }
};