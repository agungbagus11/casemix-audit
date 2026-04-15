<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimEpisode extends Model
{
    use HasFactory;

    protected $fillable = [
        'episode_no',
        'simrs_encounter_id',
        'sep_no',
        'mrn',
        'patient_name',
        'care_type',
        'service_unit',
        'doctor_name',
        'admission_at',
        'discharge_at',
        'payer_name',
        'claim_status',
        'audit_status',
        'processing_stage',
        'risk_level',
        'risk_score',
        'snapshot_json',
        'notes',
    ];

    protected $casts = [
        'admission_at' => 'datetime',
        'discharge_at' => 'datetime',
        'snapshot_json' => 'array',
        'risk_score' => 'integer',
    ];

    public function documents()
    {
        return $this->hasMany(ClaimDocument::class);
    }

    public function aiResults()
    {
        return $this->hasMany(ClaimAiResult::class);
    }

    public function latestAiResult()
    {
        return $this->hasOne(ClaimAiResult::class)->latestOfMany();
    }

    public function auditFlags()
    {
        return $this->hasMany(ClaimAuditFlag::class);
    }

    public function reviews()
    {
        return $this->hasMany(ClaimReview::class);
    }
}