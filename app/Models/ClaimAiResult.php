<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimAiResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_episode_id',
        'model_name',
        'prompt_version',
        'primary_diagnosis_text',
        'primary_icd10_json',
        'secondary_icd10_json',
        'procedure_json',
        'confidence_score',
        'missing_data_json',
        'ai_notes',
        'raw_response_json',
    ];

    protected $casts = [
        'primary_icd10_json'   => 'array',
        'secondary_icd10_json' => 'array',
        'procedure_json'       => 'array',
        'missing_data_json'    => 'array',
        'raw_response_json'    => 'array',
        'confidence_score'     => 'decimal:2',
    ];

    public function episode()
    {
        return $this->belongsTo(ClaimEpisode::class, 'claim_episode_id');
    }
}