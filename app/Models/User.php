<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'college_id',
        'department_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function studentProfile()
    {
        return $this->hasOne(Student::class);
    }

    public function teachingStaffProfile()
    {
        return $this->hasOne(TeachingStaff::class);
    }

    // Role helper checks
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isDepartmentHead(): bool
    {
        return $this->role === 'department_head';
    }

    public function isCollegeQA(): bool
    {
        return $this->role === 'college_qa';
    }

    public function isUniversityQADirector(): bool
    {
        return $this->role === 'university_qa_director';
    }
}
