<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationAxis extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'axis_number',
        'title_ar',
        'title_en',
        'max_score',
        'weight_percent',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'max_score' => 'float',
        'weight_percent' => 'float',
        'is_active' => 'boolean',
    ];

    public function sections()
    {
        return $this->hasMany(EvaluationSection::class)->orderBy('display_order');
    }
}
