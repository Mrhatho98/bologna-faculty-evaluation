<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $periods = DB::table('evaluation_periods')
            ->join('academic_years', 'academic_years.id', '=', 'evaluation_periods.academic_year_id')
            ->select('evaluation_periods.id', 'academic_years.name')
            ->get();

        foreach ($periods as $period) {
            if (!preg_match('/(19|20)\d{2}/', $period->name, $matches)) {
                continue;
            }

            $startYear = (int)$matches[0];

            DB::table('evaluation_periods')
                ->where('id', $period->id)
                ->update([
                    'start_date' => sprintf('%04d-09-01', $startYear),
                    'end_date' => sprintf('%04d-08-31', $startYear + 1),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        //
    }
};
