<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'department_id',
        'student_id_number',
        'stage',
        'class_group',
        'academic_year_id',
        'semester_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function evaluationAssignments()
    {
        return $this->hasMany(StudentEvaluationAssignment::class);
    }

    public function evaluations()
    {
        return $this->hasMany(StudentEvaluation::class);
    }
}
