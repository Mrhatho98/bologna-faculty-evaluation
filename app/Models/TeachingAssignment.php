<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeachingAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teaching_staff_id',
        'course_id',
        'department_id',
        'academic_year_id',
        'semester_id',
        'stage',
        'class_group',
        'min_evaluators',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function teachingStaff()
    {
        return $this->belongsTo(TeachingStaff::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function evaluatorAssignments()
    {
        return $this->hasMany(StudentEvaluationAssignment::class);
    }

    public function studentEvaluations()
    {
        return $this->hasMany(StudentEvaluation::class);
    }

    public function hasMinimumEvaluators(): bool
    {
        return $this->evaluatorAssignments()->count() >= $this->min_evaluators;
    }
}
