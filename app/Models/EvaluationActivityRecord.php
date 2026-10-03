<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationActivityRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'evaluation_item_id',
        'evaluation_item_option_id',
        'title',
        'description',
        'evidence_url',
        'activity_date',
        'score_awarded',
        'metadata_json',
        'created_by_id',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'score_awarded' => 'float',
        'metadata_json' => 'array',
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function item()
    {
        return $this->belongsTo(EvaluationItem::class, 'evaluation_item_id');
    }

    public function option()
    {
        return $this->belongsTo(EvaluationItemOption::class, 'evaluation_item_option_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
