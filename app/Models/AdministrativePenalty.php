<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrativePenalty extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'penalty_type',
        'penalty_date',
        'order_number',
        'description',
        'evidence_url',
        'evidence_description',
        'deduction_points',
    ];

    protected $casts = [
        'penalty_date' => 'date',
        'deduction_points' => 'float',
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public static function getDeductionForType(string $type): float
    {
        return match ($type) {
            'notice_of_attention' => 3.0,
            'warning' => 4.0,
            'salary_suspension' => 5.0,
            'reprimand' => 6.0,
            'salary_reduction' => 7.0,
            'rank_reduction' => 8.0,
            default => 0.0,
        };
    }

    public static function getPenaltyTypeLabel(string $type): string
    {
        return match ($type) {
            'notice_of_attention' => 'لفت نظر (-3 درجات)',
            'warning' => 'إنذار (-4 درجات)',
            'salary_suspension' => 'قطع الراتب (-5 درجات)',
            'reprimand' => 'التوبيخ (-6 درجات)',
            'salary_reduction' => 'إنقاص الراتب (-7 درجات)',
            'rank_reduction' => 'تنزيل الدرجة (-8 درجات)',
            default => $type,
        };
    }
}
