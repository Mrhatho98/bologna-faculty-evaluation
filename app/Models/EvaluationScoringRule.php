<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationScoringRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'scope_type',
        'scope_code',
        'rule_key',
        'title_ar',
        'description_ar',
        'parameters_json',
        'is_configurable',
        'is_active',
    ];

    protected $casts = [
        'parameters_json' => 'array',
        'is_configurable' => 'boolean',
        'is_active' => 'boolean',
    ];
}
