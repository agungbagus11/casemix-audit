<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_episode_id',
        'document_type',
        'file_url',
        'file_name',
        'is_required',
        'is_available',
        'notes',
    ];

    protected $casts = [
        'is_required'  => 'boolean',
        'is_available' => 'boolean',
    ];

    public function episode()
    {
        return $this->belongsTo(ClaimEpisode::class, 'claim_episode_id');
    }
}