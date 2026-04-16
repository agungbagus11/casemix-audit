<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimFollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_episode_id',
        'category',
        'title',
        'target_unit',
        'priority',
        'status',
        'issue_summary',
        'action_needed',
        'resolution_notes',
        'created_by_name',
        'assigned_to_name',
        'due_at',
        'resolved_at',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function episode()
    {
        return $this->belongsTo(ClaimEpisode::class, 'claim_episode_id');
    }
}