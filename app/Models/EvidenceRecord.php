<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvidenceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'axis_number',
        'section_key',
        'item_key',
        'title',
        'description',
        'evidence_url',
        'score_awarded',
        'metadata_json',
        'created_by_id',
    ];

    protected $casts = [
        'score_awarded' => 'float',
        'metadata_json' => 'array',
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
