<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Evaluation;

use App\Services\Form39DefinitionService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class CollegeQADashboardController extends Controller
{
    protected WorkflowService $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * College QA Dashboard listing submitted evaluations from departments in college.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $collegeId = $user->college_id;

        $query = Evaluation::with(['teachingStaff.department', 'academicYear', 'semester'])
            ->where('college_id', $collegeId);

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('department_id') && !empty($request->department_id)) {
            $query->where('department_id', $request->department_id);
        }

        $evaluations = $query->orderBy('updated_at', 'desc')->get();
        $departments = $user->college?->departments ?? [];

        return view('college.dashboard', [
            'evaluations' => $evaluations,
            'departments' => $departments,
            'selectedStatus' => $request->status,
            'selectedDept' => $request->department_id,
        ]);
    }

    /**
     * Review evaluation, evidence URLs, descriptions, and verification checklist.
     */
    public function review(Evaluation $evaluation)
    {
        $user = auth()->user();
        if ($evaluation->college_id !== $user->college_id && !$user->isUniversityQADirector()) {
            abort(403, 'غير مصرح لك بمراجعة تقييم خارج كليتك.');
        }

        $evaluation->load([
            'teachingStaff.department',
            'teachingStaff.college',
            'academicYear',
            'semester',
            'evidenceRecords.createdBy',
            'administrativePenalties',
            'workflowHistories.user',
        ]);

        return view('college.review', [
            'evaluation' => $evaluation,
            'form39Axes' => Form39DefinitionService::axes(),
            'evidenceByItem' => $evaluation->evidenceRecords->whereNotNull('item_key')->groupBy('item_key'),
        ]);
    }

    /**
     * Forward verified evaluation to University QA Department.
     * College QA Officer responsibility: Review -> Verify -> Forward.
     */
    public function forwardToUniversityQA(Request $request, Evaluation $evaluation)
    {
        $user = auth()->user();
        if ($evaluation->college_id !== $user->college_id) {
            abort(403, 'غير مصرح لك باتخاذ إجراء على تقييم هذه الكلية.');
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $notes = 'تم تدقيق كافة الأدلة والروابط وتأكيد مطابقتها بالكامل. ' . ($validated['notes'] ?? '');
            $this->workflowService->transition($evaluation, 'SUBMITTED_TO_UNIVERSITY_QA', $notes);

            AuditLog::logAction('college_qa_forward', 'Evaluations', $evaluation->id, null, [
                'forwarded_by' => $user->name,
            ]);

            return redirect()->route('college.dashboard')
                ->with('success', 'تم تدقيق الاستمارة بنجاح وتحويلها إلى قسم ضمان الجودة والأداء الجامعي.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Return a submitted evaluation to the department for correction before university review.
     */
    public function returnToDepartment(Request $request, Evaluation $evaluation)
    {
        $user = auth()->user();
        if ($evaluation->college_id !== $user->college_id) {
            abort(403, 'غير مصرح لك باتخاذ إجراء على تقييم هذه الكلية.');
        }

        $validated = $request->validate([
            'return_comments' => ['required', 'string', 'min:10'],
        ]);

        try {
            $this->workflowService->transition($evaluation, 'REJECTED', $validated['return_comments']);

            AuditLog::logAction('college_qa_return_to_department', 'Evaluations', $evaluation->id, null, [
                'returned_by' => $user->name,
                'comments' => $validated['return_comments'],
            ]);

            return redirect()->route('college.dashboard')
                ->with('success', 'تم إرجاع الاستمارة إلى القسم للتصحيح مع تثبيت الملاحظات.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
