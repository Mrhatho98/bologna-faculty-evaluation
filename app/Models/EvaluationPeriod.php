<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'semester_id',
        'name_ar',
        'name_en',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function scopeOpen($query)
    {
        $today = now()->toDateString();

        return $query
            ->where('is_active', true)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);
    }

    public function isOpen(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        $today = now()->startOfDay();
        return $today->gte($this->start_date) && $today->lte($this->end_date);
    }

    public static function annualDatesForAcademicYearName(string $academicYearName): ?array
    {
        if (!preg_match('/(19|20)\d{2}/', $academicYearName, $matches)) {
            return null;
        }

        $startYear = (int)$matches[0];

        return [
            'start_date' => sprintf('%04d-09-01', $startYear),
            'end_date' => sprintf('%04d-08-31', $startYear + 1),
        ];
    }
}
