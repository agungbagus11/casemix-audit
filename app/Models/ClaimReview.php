<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_episode_id',
        'reviewer_name',
        'reviewer_role',
        'action_type',
        'notes',
        'old_data_json',
        'new_data_json',
    ];

    protected $casts = [
        'old_data_json' => 'array',
        'new_data_json' => 'array',
    ];

    public function episode()
    {
        return $this->belongsTo(ClaimEpisode::class, 'claim_episode_id');
    }
}