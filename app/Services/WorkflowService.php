<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Evaluation;
use App\Models\User;
use App\Models\WorkflowHistory;
use App\Notifications\SystemNotification;
use Exception;

class WorkflowService
{
    protected EvaluationScoringService $scoringService;

    public function __construct(EvaluationScoringService $scoringService)
    {
        $this->scoringService = $scoringService;
    }

    /**
     * Transition an evaluation to a new status according to strict workflow state machine rules.
     */
    public function transition(Evaluation $evaluation, string $toStatus, ?string $comments = null): Evaluation
    {
        $fromStatus = $evaluation->status;

        $this->validateTransition($fromStatus, $toStatus, $comments);

        // Recalculate scores prior to status transition
        $scores = $this->scoringService->calculateEvaluationScores($evaluation);
        $evaluation->fill($scores);

        $evaluation->status = $toStatus;

        if ($toStatus === 'SUBMITTED_TO_COLLEGE_QA') {
            $evaluation->submitted_at = now();
        } elseif ($toStatus === 'ACCEPTED') {
            $evaluation->accepted_at = now();
        } elseif ($toStatus === 'REJECTED') {
            $evaluation->rejected_at = now();
        }

        $evaluation->current_reviewer_id = auth()->id();
        $evaluation->save();

        // Record workflow history log
        WorkflowHistory::create([
            'evaluation_id' => $evaluation->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'user_id' => auth()->id() ?? 1,
            'comments' => $comments,
        ]);

        AuditLog::logAction('workflow_transition', 'Evaluations', $evaluation->id, [
            'status' => $fromStatus,
        ], [
            'status' => $toStatus,
            'comments' => $comments,
            'total_score' => $evaluation->total_score,
        ]);

        $this->notifyTransition($evaluation->fresh(['teachingStaff']), $fromStatus, $toStatus, $comments);

        return $evaluation;
    }

    /**
     * Validate transition rules.
     */
    protected function validateTransition(string $from, string $to, ?string $comments): void
    {
        $allowedTransitions = [
            'DRAFT' => ['SUBMITTED_TO_COLLEGE_QA'],
            'SUBMITTED_TO_COLLEGE_QA' => ['UNDER_COLLEGE_QA_REVIEW', 'SUBMITTED_TO_UNIVERSITY_QA', 'REJECTED'],
            'UNDER_COLLEGE_QA_REVIEW' => ['SUBMITTED_TO_UNIVERSITY_QA', 'REJECTED'],
            'SUBMITTED_TO_UNIVERSITY_QA' => ['UNDER_UNIVERSITY_QA_REVIEW', 'ACCEPTED', 'REJECTED'],
            'UNDER_UNIVERSITY_QA_REVIEW' => ['ACCEPTED', 'REJECTED'],
            'REJECTED' => ['DRAFT', 'SUBMITTED_TO_COLLEGE_QA'], // Resubmission workflow
        ];

        if (!isset($allowedTransitions[$from]) || !in_array($to, $allowedTransitions[$from])) {
            throw new Exception("الانتقال من حالة [{$from}] إلى حالة [{$to}] غير مسموح به.");
        }

        // Rejection MUST include mandatory comments/reason
        if ($to === 'REJECTED' && (empty($comments) || trim($comments) === '')) {
            throw new Exception("يجب تقديم سبب الإرجاع/الرفض بالتفصيل عند الإرجاع.");
        }
    }

    private function notifyTransition(Evaluation $evaluation, string $fromStatus, string $toStatus, ?string $comments): void
    {
        $staffName = $evaluation->teachingStaff->full_name ?? 'أحد التدريسيين';
        $departmentUsers = User::where('role', 'department_head')
            ->where('department_id', $evaluation->department_id)
            ->where('is_active', true)
            ->get();
        $collegeUsers = User::where('role', 'college_qa')
            ->where('college_id', $evaluation->college_id)
            ->where('is_active', true)
            ->get();
        $universityUsers = User::where('role', 'university_qa_director')
            ->where('is_active', true)
            ->get();

        if ($toStatus === 'SUBMITTED_TO_COLLEGE_QA') {
            $url = route('college.evaluations.review', $evaluation);
            foreach ($collegeUsers as $user) {
                $user->notify(new SystemNotification(
                    'استمارة جديدة بانتظار تدقيق الكلية',
                    "تم رفع استمارة {$staffName} إلى وحدة ضمان الجودة في الكلية.",
                    $url,
                    'info',
                    ['evaluation_id' => $evaluation->id, 'status' => $toStatus]
                ));
            }
        }

        if ($toStatus === 'SUBMITTED_TO_UNIVERSITY_QA') {
            $url = route('university.evaluations.review', $evaluation);
            foreach ($universityUsers as $user) {
                $user->notify(new SystemNotification(
                    'استمارة محولة إلى ضمان الجودة الجامعي',
                    "تم تحويل استمارة {$staffName} من الكلية إلى الجامعة للتدقيق النهائي.",
                    $url,
                    'info',
                    ['evaluation_id' => $evaluation->id, 'status' => $toStatus]
                ));
            }
        }

        if ($toStatus === 'REJECTED') {
            $url = route('dept.evaluations.edit', $evaluation);
            foreach ($departmentUsers as $user) {
                $user->notify(new SystemNotification(
                    'استمارة معادة للتصحيح',
                    "تم إرجاع استمارة {$staffName} للتصحيح. الملاحظة: " . trim((string)$comments),
                    $url,
                    'warning',
                    ['evaluation_id' => $evaluation->id, 'from_status' => $fromStatus, 'status' => $toStatus]
                ));
            }

            if (str_contains($fromStatus, 'UNIVERSITY')) {
                $collegeUrl = route('college.evaluations.review', $evaluation);
                foreach ($collegeUsers as $user) {
                    $user->notify(new SystemNotification(
                        'استمارة أعادتها الجامعة للتصحيح',
                        "أعاد ضمان الجودة الجامعي استمارة {$staffName}. الملاحظة: " . trim((string)$comments),
                        $collegeUrl,
                        'warning',
                        ['evaluation_id' => $evaluation->id, 'from_status' => $fromStatus, 'status' => $toStatus]
                    ));
                }
            }
        }

        if ($toStatus === 'ACCEPTED') {
            $url = route('evaluations.print', $evaluation);
            foreach ($departmentUsers->merge($collegeUsers) as $user) {
                $user->notify(new SystemNotification(
                    'تم اعتماد الاستمارة نهائياً',
                    "تم اعتماد استمارة {$staffName} نهائياً بدرجة {$evaluation->total_score}.",
                    $url,
                    'success',
                    ['evaluation_id' => $evaluation->id, 'status' => $toStatus]
                ));
            }
        }
    }

}
