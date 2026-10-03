<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_section_id',
        'code',
        'title_ar',
        'title_en',
        'max_score',
        'input_type',
        'requires_evidence',
        'is_computed',
        'official_notes_ar',
        'metadata_schema_json',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'max_score' => 'float',
        'requires_evidence' => 'boolean',
        'is_computed' => 'boolean',
        'metadata_schema_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(EvaluationSection::class, 'evaluation_section_id');
    }

    public function options()
    {
        return $this->hasMany(EvaluationItemOption::class)->orderBy('display_order');
    }
}
