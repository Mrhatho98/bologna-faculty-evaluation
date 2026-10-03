<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teaching_assignment_id',
        'evaluation_period_id',
        'answers_json',
        'score_section_a',
        'score_section_b',
        'total_score',
        'submitted_at',
    ];

    protected $casts = [
        'answers_json' => 'array',
        'score_section_a' => 'float',
        'score_section_b' => 'float',
        'total_score' => 'float',
        'submitted_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teachingAssignment()
    {
        return $this->belongsTo(TeachingAssignment::class);
    }

    public function evaluationPeriod()
    {
        return $this->belongsTo(EvaluationPeriod::class);
    }
}
