<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimAuditFlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_episode_id',
        'flag_type',
        'severity',
        'flag_code',
        'flag_title',
        'flag_description',
        'evidence_json',
        'source_type',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'evidence_json' => 'array',
        'reviewed_at'   => 'datetime',
    ];

    public function episode()
    {
        return $this->belongsTo(ClaimEpisode::class, 'claim_episode_id');
    }
}