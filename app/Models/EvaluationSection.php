<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_axis_id',
        'code',
        'title_ar',
        'title_en',
        'max_score',
        'aggregation_method',
        'description_ar',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'max_score' => 'float',
        'is_active' => 'boolean',
    ];

    public function axis()
    {
        return $this->belongsTo(EvaluationAxis::class, 'evaluation_axis_id');
    }

    public function items()
    {
        return $this->hasMany(EvaluationItem::class)->orderBy('display_order');
    }
}
