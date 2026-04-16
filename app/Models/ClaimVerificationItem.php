<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimVerificationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_episode_id',
        'verification_key',
        'verification_label',
        'status',
        'finding_notes',
        'follow_up_notes',
        'source_reference',
        'reviewer_name',
        'reviewer_role',
        'checked_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function episode()
    {
        return $this->belongsTo(ClaimEpisode::class, 'claim_episode_id');
    }
}