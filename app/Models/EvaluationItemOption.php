<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationItemOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_item_id',
        'code',
        'label_ar',
        'label_en',
        'score_value',
        'metadata_json',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'score_value' => 'float',
        'metadata_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function item()
    {
        return $this->belongsTo(EvaluationItem::class, 'evaluation_item_id');
    }
}
