<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simrs_api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint', 255)->index();
            $table->string('method', 20)->index();
            $table->longText('request_payload')->nullable();
            $table->longText('response_payload')->nullable();
            $table->integer('http_status')->nullable()->index();
            $table->boolean('is_success')->default(true)->index();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simrs_api_logs');
    }
};