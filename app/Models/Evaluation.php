<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teaching_staff_id',
        'academic_year_id',
        'semester_id',
        'department_id',
        'college_id',
        'status',
        'score_axis_1',
        'score_axis_2',
        'score_axis_3',
        'score_axis_4',
        'total_score',
        'final_classification',
        'axes_details_json',
        'submitted_at',
        'accepted_at',
        'rejected_at',
        'current_reviewer_id',
    ];

    protected $casts = [
        'score_axis_1' => 'float',
        'score_axis_2' => 'float',
        'score_axis_3' => 'float',
        'score_axis_4' => 'float',
        'total_score' => 'float',
        'axes_details_json' => 'array',
        'submitted_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function teachingStaff()
    {
        return $this->belongsTo(TeachingStaff::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function evidenceRecords()
    {
        return $this->hasMany(EvidenceRecord::class);
    }

    public function activityRecords()
    {
        return $this->hasMany(EvaluationActivityRecord::class);
    }

    public function administrativePenalties()
    {
        return $this->hasMany(AdministrativePenalty::class);
    }

    public function workflowHistories()
    {
        return $this->hasMany(WorkflowHistory::class);
    }

    public function currentReviewer()
    {
        return $this->belongsTo(User::class, 'current_reviewer_id');
    }

    public function isLocked(): bool
    {
        return in_array($this->status, [
            'SUBMITTED_TO_COLLEGE_QA',
            'UNDER_COLLEGE_QA_REVIEW',
            'SUBMITTED_TO_UNIVERSITY_QA',
            'UNDER_UNIVERSITY_QA_REVIEW',
            'ACCEPTED',
        ]);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'DRAFT' => 'مسودة',
            'SUBMITTED_TO_COLLEGE_QA' => 'مقدم لجودة الكلية',
            'UNDER_COLLEGE_QA_REVIEW' => 'قيد مراجعة جودة الكلية',
            'SUBMITTED_TO_UNIVERSITY_QA' => 'مقدم لجودة الجامعة',
            'UNDER_UNIVERSITY_QA_REVIEW' => 'قيد مراجعة جودة الجامعة',
            'ACCEPTED' => 'مقبول ومعتمد',
            'REJECTED' => 'معاد للتصحيح',
            default => $this->status,
        };
    }

    public function getClassificationLabelAttribute(): string
    {
        return match ($this->final_classification) {
            'Excellent' => 'امتياز (90 فأكثر)',
            'Very Good' => 'جيد جداً (80-89)',
            'Good' => 'جيد (70-79)',
            'Weak' => 'ضعيف (أقل من 70)',
            default => 'غير محدد',
        };
    }
}
