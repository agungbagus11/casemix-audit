<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimrsApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'endpoint',
        'method',
        'request_payload',
        'response_payload',
        'http_status',
        'is_success',
        'error_message',
    ];

    protected $casts = [
        'is_success' => 'boolean',
        'http_status' => 'integer',
    ];
}