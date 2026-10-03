<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Services\EvaluationScoringService;
use App\Services\Form39DefinitionService;

class EvaluationReportController extends Controller
{
    protected EvaluationScoringService $scoringService;

    public function __construct(EvaluationScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    /**
     * Display printable Form No. (39) official layout.
     */
    public function printForm39(Evaluation $evaluation)
    {
        $user = auth()->user();
        abort_unless($this->canPrintEvaluation($user, $evaluation), 403, 'غير مصرح لك بطباعة هذه الاستمارة.');

        [$scores, $evidenceByItem] = $this->prepareEvaluationReportData($evaluation);

        return view('reports.form39', [
            'evaluation' => $evaluation,
            'scores' => $scores,
            'form39Axes' => Form39DefinitionService::axes(),
            'evidenceByItem' => $evidenceByItem,
        ]);
    }

    /**
     * Export Form 39 as an Excel-compatible XLS document.
     */
    public function exportExcel(Evaluation $evaluation)
    {
        $user = auth()->user();
        abort_unless($this->canPrintEvaluation($user, $evaluation), 403, 'غير مصرح لك بتصدير هذه الاستمارة.');

        [$scores, $evidenceByItem] = $this->prepareEvaluationReportData($evaluation);

        $safeName = preg_replace('/[^\p{Arabic}\p{L}\p{N}\-_]+/u', '_', $evaluation->teachingStaff->full_name ?? 'form39');
        $fileName = 'form39_' . $evaluation->id . '_' . $safeName . '.xls';

        return response()
            ->view('reports.form39_excel', [
                'evaluation' => $evaluation,
                'scores' => $scores,
                'form39Axes' => Form39DefinitionService::axes(),
                'evidenceByItem' => $evidenceByItem,
            ])
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    private function prepareEvaluationReportData(Evaluation $evaluation): array
    {
        $evaluation->load([
            'teachingStaff.department',
            'teachingStaff.college',
            'academicYear',
            'semester',
            'evidenceRecords',
            'administrativePenalties',
            'workflowHistories.user',
        ]);

        return [
            $this->scoringService->calculateEvaluationScores($evaluation),
            $evaluation->evidenceRecords->whereNotNull('item_key')->groupBy('item_key'),
        ];
    }

    private function canPrintEvaluation($user, Evaluation $evaluation): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isUniversityQADirector()) {
            return true;
        }

        if ($user->isCollegeQA()) {
            return (int)$evaluation->college_id === (int)$user->college_id;
        }

        if ($user->isDepartmentHead()) {
            return (int)$evaluation->department_id === (int)$user->department_id;
        }

        return false;
    }
}
