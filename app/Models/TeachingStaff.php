<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeachingStaff extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'teaching_staff';

    protected $fillable = [
        'user_id',
        'college_id',
        'department_id',
        'first_name',
        'father_name',
        'grandfather_name',
        'great_grandfather_name',
        'surname',
        'mother_name',
        'mother_father_name',
        'mother_grandfather_name',
        'national_id',
        'record_number',
        'page_number',
        'issue_date',
        'degree',
        'ministerial_order_no',
        'degree_date',
        'degree_country',
        'degree_university',
        'degree_college',
        'degree_department',
        'general_specialization',
        'specific_specialization',
        'academic_rank',
        'rank_awarding_body',
        'rank_date',
        'mobile',
        'email',
        'is_active',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'degree_date' => 'date',
        'rank_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function teachingAssignments()
    {
        return $this->hasMany(TeachingAssignment::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function getFullNameAttribute(): string
    {
        return implode(' ', array_filter([
            $this->first_name,
            $this->father_name,
            $this->grandfather_name,
            $this->great_grandfather_name,
            $this->surname,
        ]));
    }

    public function getRankLabelAttribute(): string
    {
        return match ($this->academic_rank) {
            'professor' => 'أستاذ',
            'assistant_professor' => 'أستاذ مساعد',
            'lecturer' => 'مدرس',
            'assistant_lecturer' => 'مدرس مساعد',
            default => $this->academic_rank,
        };
    }
}
