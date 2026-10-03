<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEvaluationAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teaching_assignment_id',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teachingAssignment()
    {
        return $this->belongsTo(TeachingAssignment::class);
    }
}
