<?php

namespace App\Services;

use App\Models\AdministrativePenalty;
use App\Models\Evaluation;
use App\Models\EvidenceRecord;
use App\Models\StudentEvaluation;
use App\Models\SystemSetting;
use App\Models\TeachingStaff;
use Throwable;

class EvaluationScoringService
{
    /**
     * Calculate all 4 axes for an evaluation form and return full breakdown + final scores.
     */
    public function calculateEvaluationScores(Evaluation $evaluation): array
    {
        $staff = $evaluation->teachingStaff;

        // Axis 1: Student Feedback (max 10 points)
        $axis1Score = $this->calculateAxis1($evaluation);

        // Axis 2: Teaching Staff Portfolio (max 60 points)
        $axis2Breakdown = $this->calculateAxis2($evaluation, $staff);
        $axis2Score = $axis2Breakdown['total'];

        // Axis 3: Department Head Evaluation (max 15 points)
        $axis3Breakdown = $this->calculateAxis3($evaluation);
        $axis3Score = $axis3Breakdown['total'];

        // Axis 4: Educational and Guidance Aspect (max 15 points)
        $axis4Breakdown = $this->calculateAxis4($evaluation);
        $axis4Score = $axis4Breakdown['total'];

        // Total weighted score out of 100
        $totalScore = round($axis1Score + $axis2Score + $axis3Score + $axis4Score, 2);
        if ($totalScore > 100) {
            $totalScore = 100.0;
        }

        // Determine final classification
        $classification = $this->determineClassification($totalScore);

        $axesDetails = [
            'axis_1' => [
                'score' => $axis1Score,
                'max' => 10.0,
            ],
            'axis_2' => $axis2Breakdown,
            'axis_3' => $axis3Breakdown,
            'axis_4' => $axis4Breakdown,
        ];

        return [
            'score_axis_1' => $axis1Score,
            'score_axis_2' => $axis2Score,
            'score_axis_3' => $axis3Score,
            'score_axis_4' => $axis4Score,
            'total_score' => $totalScore,
            'final_classification' => $classification,
            'axes_details' => $axesDetails,
        ];
    }

    /**
     * Axis 1: Student Feedback on Course & Teaching Method (Max 10 points)
     * Configurable aggregation across all completed student evaluations for teaching staff assignments.
     */
    public function calculateAxis1(Evaluation $evaluation): float
    {
        $staff = $evaluation->teachingStaff;
        if (!$staff) {
            return 0.0;
        }

        $assignmentIds = $staff->teachingAssignments()
            ->where('academic_year_id', $evaluation->academic_year_id)
            ->where('semester_id', $evaluation->semester_id)
            ->pluck('id');

        if ($assignmentIds->isEmpty()) {
            return 0.0;
        }

        $studentEvaluations = StudentEvaluation::whereIn('teaching_assignment_id', $assignmentIds)->get();
        if ($studentEvaluations->isEmpty()) {
            return 0.0;
        }

        // Configurable aggregation formula setting (default: arithmetic mean)
        $formula = SystemSetting::get('student_evaluation_aggregation_formula', 'mean');

        if ($formula === 'mean') {
            $averageScore = $studentEvaluations->avg('total_score');
            return round(min(10.0, max(0.0, (float)$averageScore)), 2);
        }

        // Fallback to average score
        return round(min(10.0, max(0.0, (float)$studentEvaluations->avg('total_score'))), 2);
    }

    /**
     * Axis 2: Teaching Staff Portfolio (Max 60 points)
     */
    public function calculateAxis2(Evaluation $evaluation, TeachingStaff $staff): array
    {
        $evidenceRecords = $evaluation->evidenceRecords()->where('axis_number', 2)->get();

        $dutiesScore = $this->sumSectionScore($evidenceRecords, 'duties', $this->form39SectionMax('duties', 8.0));
        $skillsScore = $this->sumSectionScore($evidenceRecords, 'skills', $this->form39SectionMax('skills', 5.0));
        $trainingScore = $this->sumSectionScore($evidenceRecords, 'training', $this->form39SectionMax('training', 20.0));

        // 4. Research, Books & Patents (max 20 points after rank multiplier)
        $researchBreakdown = $this->calculateResearchScore($evidenceRecords->where('section_key', 'research'), $staff);
        $researchScore = $researchBreakdown['final_score'];

        $intlScore = $this->sumSectionScore($evidenceRecords, 'international', $this->form39SectionMax('international', 5.0));
        $revenueScore = $this->sumSectionScore($evidenceRecords, 'revenue', $this->form39SectionMax('revenue', 2.0));

        $totalAxis2 = min(60.0, round(
            $dutiesScore +
            $skillsScore +
            $trainingScore +
            $researchScore +
            $intlScore +
            $revenueScore,
            2
        ));

        return [
            'duties_score' => $dutiesScore,
            'skills_score' => $skillsScore,
            'training_score' => $trainingScore,
            'research' => $researchBreakdown,
            'international_score' => $intlScore,
            'revenue_score' => $revenueScore,
            'total' => $totalAxis2,
            'max' => 60.0,
        ];
    }

    /**
     * Calculate Research, Books & Patents with Official Form 39 scoring rules + Academic Rank Multiplier.
     */
    public function calculateResearchScore($researchEvidences, TeachingStaff $staff): array
    {
        $rawScore = 0.0;
        $excludedCount = 0;

        foreach ($researchEvidences as $ev) {
            $baseScore = $ev->score_awarded;
            $meta = $ev->metadata_json ?? [];

            $hasFirstAffiliation = (bool)($meta['has_first_university_affiliation'] ?? true);
            $hasPhdException = (bool)($meta['has_phd_student_exception'] ?? false);
            $isBlacklistedJournal = (bool)($meta['is_blacklisted_journal'] ?? false);

            if ($isBlacklistedJournal || (!$hasFirstAffiliation && !$hasPhdException)) {
                $excludedCount++;
                continue;
            }

            // Author position rules
            $isFirstAuthor = $meta['is_first_author'] ?? false;
            $isCorrespondingAuthor = $meta['is_corresponding_author'] ?? false;
            $authorPosition = (int)($meta['author_position'] ?? 1);

            // 1st author +1
            if ($isFirstAuthor) {
                $baseScore += 1.0;
            }

            // Corresponding author +1
            if ($isCorrespondingAuthor) {
                $baseScore += 1.0;
            }

            // Position 5 to 10: deduct 2 points
            if ($authorPosition >= 5 && $authorPosition <= 10) {
                $baseScore = max(0.0, $baseScore - 2.0);
            }
            // Position > 10: reduce by 50%
            elseif ($authorPosition > 10) {
                $baseScore = $baseScore * 0.5;
            }

            $rawScore += $baseScore;
        }

        // Cap raw research accumulated score at 20 points before multiplier
        $cappedRawScore = min(20.0, $rawScore);

        // Academic Rank Multiplier (Configurable from SystemSetting or baseline defaults)
        $rankMultipliers = $this->rankMultipliers();

        $multiplier = $rankMultipliers[$staff->academic_rank] ?? 1.0;
        $finalResearchScore = min(20.0, round($cappedRawScore * $multiplier, 2));

        return [
            'raw_score' => $rawScore,
            'capped_raw_score' => $cappedRawScore,
            'academic_rank' => $staff->academic_rank,
            'multiplier' => $multiplier,
            'final_score' => $finalResearchScore,
            'excluded_count' => $excludedCount,
        ];
    }

    /**
     * Axis 3: Department Head Evaluation (Max 15 points)
     * Commitment score (max 15) minus Administrative Penalties deductions.
     */
    public function calculateAxis3(Evaluation $evaluation): array
    {
        $baseScore = 15.0;

        // Check evidence records for Axis 3 base score override if entered by Dept Head
        $deptEvidence = $evaluation->evidenceRecords()->where('axis_number', 3)->first();
        if ($deptEvidence && $deptEvidence->score_awarded !== null) {
            $baseScore = min(15.0, max(0.0, (float)$deptEvidence->score_awarded));
        }

        // Total penalties deductions
        $totalDeduction = 0.0;
        $penalties = $evaluation->administrativePenalties;
        foreach ($penalties as $penalty) {
            $totalDeduction += $penalty->deduction_points;
        }

        $finalAxis3 = max(0.0, min(15.0, round($baseScore - $totalDeduction, 2)));

        return [
            'base_score' => $baseScore,
            'total_deduction' => $totalDeduction,
            'penalties_count' => $penalties->count(),
            'total' => $finalAxis3,
            'max' => 15.0,
        ];
    }

    /**
     * Axis 4: Educational and Guidance Aspect (Max 15 points)
     * - Continuing education and quality participation (max 5)
     * - Field visits, voluntary work and external services (max 10)
     */
    public function calculateAxis4(Evaluation $evaluation): array
    {
        $evidenceRecords = $evaluation->evidenceRecords()->where('axis_number', 4)->get();

        $contEdScore = $this->sumSectionScore($evidenceRecords, 'continuing_education', $this->form39SectionMax('continuing_education', 5.0));
        $voluntaryScore = $this->sumSectionScore($evidenceRecords, 'voluntary', $this->form39SectionMax('voluntary', 10.0));

        $totalAxis4 = min(15.0, round($contEdScore + $voluntaryScore, 2));

        return [
            'continuing_education_score' => $contEdScore,
            'voluntary_score' => $voluntaryScore,
            'total' => $totalAxis4,
            'max' => 15.0,
        ];
    }

    /**
     * Determine final classification based on total score.
     */
    public function determineClassification(float $score): string
    {
        if ($score >= 90.0) {
            return 'Excellent'; // ممتاز
        }
        if ($score >= 80.0) {
            return 'Very Good'; // جيد جداً
        }
        if ($score >= 70.0) {
            return 'Good';      // جيد
        }
        return 'Weak';          // ضعيف
    }

    private function sumSectionScore($evidenceRecords, string $sectionKey, float $maxScore): float
    {
        $score = 0.0;

        foreach ($evidenceRecords->where('section_key', $sectionKey)->groupBy('item_key') as $itemKey => $records) {
            $itemScore = 0.0;
            foreach ($records as $record) {
                $itemScore += $record->score_awarded;
            }

            $score += min($this->form39ItemMax((string)$itemKey, $maxScore), $itemScore);
        }

        return min($maxScore, round($score, 2));
    }

    private function form39SectionMax(string $sectionKey, float $default): float
    {
        foreach (Form39DefinitionService::items() as $item) {
            if (($item['section_key'] ?? null) === $sectionKey && isset($item['section_max'])) {
                return (float)$item['section_max'];
            }
        }

        return $default;
    }

    private function form39ItemMax(string $itemKey, float $default): float
    {
        foreach (Form39DefinitionService::items() as $item) {
            if (($item['key'] ?? null) === $itemKey) {
                return (float)($item['max'] ?? $default);
            }
        }

        return $default;
    }

    private function rankMultipliers(): array
    {
        $defaults = [
            'professor' => 1.0,
            'assistant_professor' => 1.4,
            'lecturer' => 1.6,
            'assistant_lecturer' => 1.8,
        ];

        try {
            return SystemSetting::get('academic_rank_multipliers', $defaults);
        } catch (Throwable) {
            return $defaults;
        }
    }
}
