<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class College extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function teachingStaff()
    {
        return $this->hasMany(TeachingStaff::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
}
