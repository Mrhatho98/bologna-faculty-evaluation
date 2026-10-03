<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'college_id',
        'name_ar',
        'name_en',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function teachingStaff()
    {
        return $this->hasMany(TeachingStaff::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
