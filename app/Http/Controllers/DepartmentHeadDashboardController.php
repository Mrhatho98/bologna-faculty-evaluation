<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AdministrativePenalty;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Evaluation;
use App\Models\EvidenceRecord;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentEvaluationAssignment;
use App\Models\TeachingAssignment;
use App\Models\TeachingStaff;
use App\Models\User;
use App\Services\EvaluationScoringService;
use App\Services\Form39DefinitionService;
use App\Services\StudentCredentialService;
use App\Services\WorkflowService;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DepartmentHeadDashboardController extends Controller
{
    protected EvaluationScoringService $scoringService;
    protected WorkflowService $workflowService;
    protected StudentCredentialService $studentCredentialService;

    public function __construct(
        EvaluationScoringService $scoringService,
        WorkflowService $workflowService,
        StudentCredentialService $studentCredentialService
    ) {
        $this->scoringService = $scoringService;
        $this->workflowService = $workflowService;
        $this->studentCredentialService = $studentCredentialService;
    }

    /**
     * Department Head Main Dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        $departmentId = $user->department_id;
        $collegeId = $user->college_id;

        $department = $user->department;
        $college = $user->college;

        $staffCount = TeachingStaff::where('department_id', $departmentId)->count();
        $courseCount = Course::where('department_id', $departmentId)->count();
        $studentCount = Student::where('department_id', $departmentId)->count();

        $evaluations = Evaluation::with(['teachingStaff', 'academicYear', 'semester'])
            ->where('department_id', $departmentId)
            ->get();

        return view('department.dashboard', [
            'department' => $department,
            'college' => $college,
            'staffCount' => $staffCount,
            'courseCount' => $courseCount,
            'studentCount' => $studentCount,
            'evaluations' => $evaluations,
        ]);
    }

    /**
     * List teaching staff in department.
     */
    public function teachingStaffIndex()
    {
        $departmentId = auth()->user()->department_id;
        $staffMembers = TeachingStaff::where('department_id', $departmentId)->get();

        return view('department.staff.index', [
            'staffMembers' => $staffMembers,
        ]);
    }

    /**
     * Store new teaching staff member.
     */
    public function storeTeachingStaff(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'father_name' => ['required', 'string', 'max:255'],
            'grandfather_name' => ['required', 'string', 'max:255'],
            'great_grandfather_name' => ['nullable', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'academic_rank' => ['required', 'string', 'in:professor,assistant_professor,lecturer,assistant_lecturer'],
            'degree' => ['nullable', 'string'],
            'general_specialization' => ['nullable', 'string'],
            'specific_specialization' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'mobile' => ['nullable', 'string'],
        ]);

        $validated['college_id'] = $user->college_id;
        $validated['department_id'] = $user->department_id;
        $validated['is_active'] = true;

        $staff = TeachingStaff::create($validated);

        AuditLog::logAction('create_teaching_staff', 'TeachingStaff', $staff->id, null, $validated);

        return redirect()->route('dept.staff.index')
            ->with('success', 'تم إضافـة عضو الهيئة التدريسية بنجاح.');
    }

    /**
     * List courses in department.
     */
    public function coursesIndex()
    {
        $departmentId = auth()->user()->department_id;
        $courses = Course::with('semester')->where('department_id', $departmentId)->get();
        $semesters = Semester::where('is_active', true)->get();

        return view('department.courses.index', [
            'courses' => $courses,
            'semesters' => $semesters,
        ]);
    }

    /**
     * Store new course in department.
     */
    public function storeCourse(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'stage' => ['required', 'integer', 'min:1', 'max:6'],
            'semester_id' => ['nullable', 'exists:semesters,id'],
        ]);

        $validated['department_id'] = $user->department_id;
        $validated['is_active'] = true;

        $course = Course::create($validated);

        AuditLog::logAction('create_course', 'Courses', $course->id, null, $validated);

        return redirect()->route('dept.courses.index')
            ->with('success', 'تم إضافـة المادة الدراسية بنجاح.');
    }

    /**
     * Interface to assign teaching staff to course.
     */
    public function assignmentsIndex()
    {
        $departmentId = auth()->user()->department_id;
        $assignments = TeachingAssignment::with(['teachingStaff', 'course', 'academicYear', 'semester'])
            ->where('department_id', $departmentId)
            ->get();

        $staffMembers = TeachingStaff::where('department_id', $departmentId)->where('is_active', true)->get();
        $courses = Course::where('department_id', $departmentId)->where('is_active', true)->get();
        $academicYears = AcademicYear::all();
        $semesters = Semester::where('is_active', true)->get();

        return view('department.assignments.index', [
            'assignments' => $assignments,
            'staffMembers' => $staffMembers,
            'courses' => $courses,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
        ]);
    }

    /**
     * Store teaching assignment.
     */
    public function storeAssignment(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'teaching_staff_id' => [
                'required',
                Rule::exists('teaching_staff', 'id')->where('department_id', $user->department_id),
            ],
            'course_id' => [
                'required',
                Rule::exists('courses', 'id')->where('department_id', $user->department_id),
            ],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'stage' => ['required', 'integer', 'min:1'],
            'class_group' => ['nullable', 'string'],
            'min_evaluators' => ['required', 'integer', 'min:10'], // Enforce minimum 10
        ]);

        $validated['department_id'] = $user->department_id;
        $validated['is_active'] = true;

        $assignment = TeachingAssignment::create($validated);

        // Ensure baseline evaluation record exists for teaching staff
        Evaluation::firstOrCreate([
            'teaching_staff_id' => $assignment->teaching_staff_id,
            'academic_year_id' => $assignment->academic_year_id,
            'semester_id' => $assignment->semester_id,
            'department_id' => $user->department_id,
            'college_id' => $user->college_id,
        ], [
            'status' => 'DRAFT',
            'score_axis_1' => 0.0,
            'score_axis_2' => 0.0,
            'score_axis_3' => 15.0,
            'score_axis_4' => 0.0,
            'total_score' => 15.0,
            'final_classification' => 'Weak',
        ]);

        AuditLog::logAction('create_teaching_assignment', 'TeachingAssignments', $assignment->id, null, $validated);

        return redirect()->route('dept.assignments.index')
            ->with('success', 'تم تكليف التدريسي بالمادة الدراسية بنجاح.');
    }

    /**
     * Interface to manage student evaluators for a course assignment (Minimum 10 Rule Enforcement).
     */
    public function manageEvaluators(TeachingAssignment $assignment)
    {
        $departmentId = auth()->user()->department_id;
        abort_unless($assignment->department_id === $departmentId, 403);

        $students = Student::with('user')->where('department_id', $departmentId)->get();

        $assignedStudentIds = StudentEvaluationAssignment::where('teaching_assignment_id', $assignment->id)
            ->pluck('student_id')
            ->toArray();

        return view('department.assignments.evaluators', [
            'assignment' => $assignment,
            'students' => $students,
            'assignedStudentIds' => $assignedStudentIds,
        ]);
    }

    /**
     * Save student evaluators assignment for course (MUST NOT BE FEWER THAN 10 STUDENTS).
     */
    public function updateEvaluators(Request $request, TeachingAssignment $assignment)
    {
        $departmentId = auth()->user()->department_id;
        abort_unless($assignment->department_id === $departmentId, 403);

        $validated = $request->validate([
            'student_ids' => ['required', 'array'],
            'student_ids.*' => [
                Rule::exists('students', 'id')->where('department_id', $departmentId),
            ],
        ]);

        $selectedCount = count($validated['student_ids']);

        // Mandatory Business Rule: Minimum 10 student evaluators per course
        if ($selectedCount < 10) {
            return back()->withInput()->withErrors([
                'student_ids' => "شرط أساسي في مسار بولونيا: يجب ألا يقل عدد الطلبة المقيّمين للمادة الواحدة عن 10 طلاب (العدد المحدد حالياً: {$selectedCount}).",
            ]);
        }

        DB::transaction(function () use ($assignment, $validated) {
            // Clear previous uncompleted assignments for this course
            StudentEvaluationAssignment::where('teaching_assignment_id', $assignment->id)
                ->where('is_completed', false)
                ->delete();

            foreach ($validated['student_ids'] as $studentId) {
                StudentEvaluationAssignment::firstOrCreate([
                    'student_id' => $studentId,
                    'teaching_assignment_id' => $assignment->id,
                ]);
            }

            AuditLog::logAction('update_student_evaluators', 'TeachingAssignments', $assignment->id, null, [
                'count' => count($validated['student_ids']),
            ]);
        });

        $assignment->load(['course', 'teachingStaff']);
        $students = Student::with('user')
            ->whereIn('id', $validated['student_ids'])
            ->get();
        foreach ($students as $student) {
            if ($student->user) {
                $student->user->notify(new SystemNotification(
                    'تقييم مادة مطلوب',
                    'تم تكليفك بتقييم مادة ' . ($assignment->course->name_ar ?? 'دراسية') . ' للتدريسي ' . ($assignment->teachingStaff->full_name ?? ''),
                    route('student.evaluate', $assignment),
                    'info',
                    ['teaching_assignment_id' => $assignment->id]
                ));
            }
        }

        return redirect()->route('dept.assignments.index')
            ->with('success', 'تم تخصيص الطلبة المقيّمين بنجاح وحفظ قاعدة التقييم (الحد الأدنى 10 طلبة).');
    }

    /**
     * Generate student credentials in batch.
     */
    public function generateStudentCredentialsForm()
    {
        $academicYears = AcademicYear::all();
        $semesters = Semester::where('is_active', true)->get();

        return view('department.students.generate', [
            'academicYears' => $academicYears,
            'semesters' => $semesters,
        ]);
    }

    /**
     * Process student credential batch generation.
     */
    public function processGenerateStudentCredentials(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'students_text' => ['required', 'string'],
        ]);

        // Parse student names / IDs from text area lines
        $lines = explode("\n", $validated['students_text']);
        $studentsData = [];

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = array_map('trim', explode(',', $line));
            $name = $parts[0] ?? "طالب " . ($index + 1);
            $studentIdNo = $parts[1] ?? "2025-" . sprintf('%04d', rand(100, 9999));
            $stage = (int)($parts[2] ?? 1);
            $group = $parts[3] ?? 'A';

            $studentsData[] = [
                'name' => $name,
                'student_id_number' => $studentIdNo,
                'stage' => $stage,
                'class_group' => $group,
            ];
        }

        if (empty($studentsData)) {
            return back()->withErrors(['students_text' => 'لم يتم العثور على بيانات طلاب صالحة.']);
        }

        $generated = $this->studentCredentialService->generateStudentCredentials(
            $user->department_id,
            $validated['academic_year_id'],
            $validated['semester_id'],
            $studentsData
        );

        return view('department.students.generated_credentials', [
            'generated' => $generated,
        ]);
    }

    /**
     * View/Edit Teaching Staff Portfolio & Evaluation (Axes 2, 3, 4).
     */
    public function editEvaluation(Evaluation $evaluation)
    {
        $user = auth()->user();
        if ($evaluation->department_id !== $user->department_id) {
            abort(403, 'غير مصرح لك بالوصول لتقييم هذا القسم.');
        }

        $evaluation->load(['teachingStaff', 'evidenceRecords', 'administrativePenalties', 'workflowHistories.user']);
        $scores = $this->scoringService->calculateEvaluationScores($evaluation);

        return view('department.evaluations.edit', [
            'evaluation' => $evaluation,
            'scores' => $scores,
            'form39Axes' => Form39DefinitionService::axes(),
            'evidenceByItem' => $evaluation->evidenceRecords->whereNotNull('item_key')->groupBy('item_key'),
            'completionSummary' => $this->getForm39CompletionSummary($evaluation),
        ]);
    }

    /**
     * Store evidence record (URL + Description) for Axis 2, 3, or 4.
     */
    public function storeEvidence(Request $request, Evaluation $evaluation)
    {
        abort_unless($evaluation->department_id === auth()->user()->department_id, 403);

        if ($evaluation->isLocked()) {
            return back()->with('error', 'التقييم مقفل حالياً ولا يمكن تعديل الأدلة.');
        }

        $validated = $request->validate([
            'item_key' => ['required', 'string'],
            'option_code' => ['nullable', 'string'],
            'description' => ['required', 'string'],
            'evidence_url' => ['nullable', 'url'],
            'evidence_urls' => ['nullable', 'string'],
            'score_awarded' => ['nullable', 'numeric', 'min:0'],
            'is_first_author' => ['nullable', 'boolean'],
            'is_corresponding_author' => ['nullable', 'boolean'],
            'has_first_university_affiliation' => ['nullable', 'boolean'],
            'has_phd_student_exception' => ['nullable', 'boolean'],
            'is_blacklisted_journal' => ['nullable', 'boolean'],
            'author_position' => ['nullable', 'integer', 'min:1'],
        ]);

        $formItem = Form39DefinitionService::findItem($validated['item_key']);
        if (!$formItem || !empty($formItem['readonly'])) {
            return back()->withInput()->withErrors([
                'item_key' => 'الفقرة المحددة غير موجودة ضمن استمارة رقم (39).',
            ]);
        }

        $evidenceUrls = $this->validatedEvidenceUrls($request, !empty($formItem['research_metadata']));

        $selectedOption = null;
        $options = $formItem['options'] ?? [];
        if (!empty($options)) {
            $selectedOption = collect($options)->firstWhere('code', $validated['option_code'] ?? null);
            if (!$selectedOption) {
                return back()->withInput()->withErrors([
                    'option_code' => 'يجب اختيار نوع النشاط الرسمي لهذه الفقرة.',
                ]);
            }

            $scoreAwarded = (float)$selectedOption['score'];
        } else {
            if (!array_key_exists('score_awarded', $validated) || $validated['score_awarded'] === null) {
                return back()->withInput()->withErrors([
                    'score_awarded' => 'يجب إدخال الدرجة لهذه الفقرة.',
                ]);
            }

            $scoreAwarded = (float)$validated['score_awarded'];
        }

        if ($scoreAwarded > (float)$formItem['max']) {
            return back()->withInput()->withErrors([
                'score_awarded' => "درجة هذه الفقرة يجب ألا تتجاوز {$formItem['max']}.",
            ]);
        }

        $metadata = [
            'is_first_author' => $request->boolean('is_first_author'),
            'is_corresponding_author' => $request->boolean('is_corresponding_author'),
            'has_first_university_affiliation' => $request->boolean('has_first_university_affiliation'),
            'has_phd_student_exception' => $request->boolean('has_phd_student_exception'),
            'is_blacklisted_journal' => $request->boolean('is_blacklisted_journal'),
            'author_position' => (int)$request->input('author_position', 1),
            'option_code' => $selectedOption['code'] ?? null,
            'option_label' => $selectedOption['label'] ?? null,
            'official_base_score' => $selectedOption['score'] ?? $scoreAwarded,
        ];

        $oldAuditValues = [
            'evaluation_scores' => $this->evaluationScoreSnapshot($evaluation),
        ];

        $recordPayload = [
            'axis_number' => $formItem['axis_number'],
            'section_key' => $formItem['section_key'],
            'item_key' => $formItem['key'],
            'title' => $formItem['title'],
            'description' => $validated['description'],
            'score_awarded' => $scoreAwarded,
            'metadata_json' => $metadata,
            'created_by_id' => auth()->id(),
        ];

        $savedRecordsCount = 0;
        if ((int)$formItem['axis_number'] === 3) {
            $recordPayload['evidence_url'] = $evidenceUrls[0];
            EvidenceRecord::updateOrCreate([
                'evaluation_id' => $evaluation->id,
                'item_key' => $formItem['key'],
            ], $recordPayload);
            $savedRecordsCount = 1;
        } else {
            $recordPayload['evaluation_id'] = $evaluation->id;
            foreach ($evidenceUrls as $index => $evidenceUrl) {
                EvidenceRecord::create(array_merge($recordPayload, [
                    'evidence_url' => $evidenceUrl,
                    'metadata_json' => array_merge($metadata, [
                        'batch_index' => $index + 1,
                        'batch_count' => count($evidenceUrls),
                    ]),
                ]));
                $savedRecordsCount++;
            }
        }

        // Recalculate evaluation scores
        $scores = $this->scoringService->calculateEvaluationScores($evaluation);
        $evaluation->update($scores);

        AuditLog::logAction('add_evidence_record', 'EvidenceRecords', $evaluation->id, $oldAuditValues, [
            'item_key' => $formItem['key'],
            'title' => $formItem['title'],
            'score_awarded' => $scoreAwarded,
            'evidence_urls' => $evidenceUrls,
            'records_count' => $savedRecordsCount,
            'evaluation_scores' => $this->evaluationScoreSnapshot($evaluation->fresh()),
        ]);

        return back()->with('success', "تم حفظ {$savedRecordsCount} سجل توثيق ضمن استمارة رقم (39) وإعادة الحساب بنجاح.");
    }

    private function validatedEvidenceUrls(Request $request, bool $allowMultiple): array
    {
        $urls = [];
        $multipleUrlsText = trim((string)$request->input('evidence_urls', ''));

        if ($allowMultiple && $multipleUrlsText !== '') {
            $urls = preg_split('/\r\n|\r|\n/', $multipleUrlsText) ?: [];
        } elseif ($request->filled('evidence_url')) {
            $urls[] = (string)$request->input('evidence_url');
        }

        $urls = array_values(array_unique(array_filter(array_map('trim', $urls))));

        if (empty($urls)) {
            throw ValidationException::withMessages([
                'evidence_url' => 'يجب إدخال رابط توثيق واحد على الأقل.',
            ]);
        }

        $validator = Validator::make(['urls' => $urls], [
            'urls.*' => ['url'],
        ], [
            'urls.*.url' => 'كل روابط التوثيق يجب أن تكون روابط صحيحة.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $urls;
    }

    /**
     * Delete a draft evidence/activity record and recalculate the evaluation.
     */
    public function destroyEvidence(Evaluation $evaluation, EvidenceRecord $evidenceRecord)
    {
        abort_unless($evaluation->department_id === auth()->user()->department_id, 403);
        abort_unless($evidenceRecord->evaluation_id === $evaluation->id, 404);

        if ($evaluation->isLocked()) {
            return back()->with('error', 'التقييم مقفل حالياً ولا يمكن حذف الأدلة.');
        }

        $oldValues = [
            'evidence_record' => $evidenceRecord->toArray(),
            'evaluation_scores' => $this->evaluationScoreSnapshot($evaluation),
        ];
        $evidenceRecord->delete();

        $scores = $this->scoringService->calculateEvaluationScores($evaluation);
        $evaluation->update($scores);

        AuditLog::logAction('delete_evidence_record', 'EvidenceRecords', $evaluation->id, $oldValues, [
            'evaluation_scores' => $this->evaluationScoreSnapshot($evaluation->fresh()),
        ]);

        return back()->with('success', 'تم حذف النشاط/الدليل وإعادة احتساب الدرجة.');
    }

    /**
     * Store Administrative Penalty (Axis 3 deduction).
     */
    public function storePenalty(Request $request, Evaluation $evaluation)
    {
        abort_unless($evaluation->department_id === auth()->user()->department_id, 403);

        if ($evaluation->isLocked()) {
            return back()->with('error', 'التقييم مقفل حالياً.');
        }

        $validated = $request->validate([
            'penalty_type' => ['required', 'string', 'in:notice_of_attention,warning,salary_suspension,reprimand,salary_reduction,rank_reduction'],
            'penalty_date' => ['required', 'date'],
            'order_number' => ['nullable', 'string'],
            'description' => ['required', 'string'],
            'evidence_url' => ['nullable', 'url'],
            'evidence_description' => ['nullable', 'string'],
        ]);

        $deduction = AdministrativePenalty::getDeductionForType($validated['penalty_type']);
        $validated['deduction_points'] = $deduction;
        $validated['evaluation_id'] = $evaluation->id;

        $oldAuditValues = [
            'evaluation_scores' => $this->evaluationScoreSnapshot($evaluation),
        ];

        AdministrativePenalty::create($validated);

        // Recalculate evaluation scores
        $scores = $this->scoringService->calculateEvaluationScores($evaluation);
        $evaluation->update($scores);

        AuditLog::logAction('add_administrative_penalty', 'AdministrativePenalties', $evaluation->id, $oldAuditValues, [
            'penalty' => $validated,
            'evaluation_scores' => $this->evaluationScoreSnapshot($evaluation->fresh()),
        ]);

        return back()->with('success', 'تم إضافة العقوبة الإدارية وخصم النسبة المحددة أوتوماتيكياً.');
    }

    /**
     * Delete an administrative penalty from a draft evaluation.
     */
    public function destroyPenalty(Evaluation $evaluation, AdministrativePenalty $penalty)
    {
        abort_unless($evaluation->department_id === auth()->user()->department_id, 403);
        abort_unless($penalty->evaluation_id === $evaluation->id, 404);

        if ($evaluation->isLocked()) {
            return back()->with('error', 'التقييم مقفل حالياً ولا يمكن حذف العقوبات.');
        }

        $oldValues = [
            'penalty' => $penalty->toArray(),
            'evaluation_scores' => $this->evaluationScoreSnapshot($evaluation),
        ];
        $penalty->delete();

        $scores = $this->scoringService->calculateEvaluationScores($evaluation);
        $evaluation->update($scores);

        AuditLog::logAction('delete_administrative_penalty', 'AdministrativePenalties', $evaluation->id, $oldValues, [
            'evaluation_scores' => $this->evaluationScoreSnapshot($evaluation->fresh()),
        ]);

        return back()->with('success', 'تم حذف العقوبة الإدارية وإعادة احتساب الدرجة.');
    }

    /**
     * Submit evaluation to College QA Unit.
     */
    public function submitToCollegeQA(Evaluation $evaluation)
    {
        abort_unless($evaluation->department_id === auth()->user()->department_id, 403);

        try {
            $evaluation->loadMissing('evidenceRecords');
            $completionSummary = $this->getForm39CompletionSummary($evaluation);
            if (!empty($completionSummary['blocking_messages'])) {
                return back()->withErrors($completionSummary['blocking_messages']);
            }

            $this->workflowService->transition($evaluation, 'SUBMITTED_TO_COLLEGE_QA', 'تم رفع استمارة التقييم وحقيبة الأستاذ كاملة إلى وحدة ضمان الجودة بالكلية.');

            return redirect()->route('dept.dashboard')
                ->with('success', 'تم رفع استمارة التقييم بنجاح إلى وحدة ضمان الجودة بالكلية وقفل التعديل العادي.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Form 39 completion snapshot for the department head before submission.
     */
    private function getForm39CompletionSummary(Evaluation $evaluation): array
    {
        $evaluation->loadMissing('evidenceRecords');

        $axes = Form39DefinitionService::axes();
        $editableItems = collect($axes)
            ->flatMap(fn ($axis) => collect($axis['items'] ?? []))
            ->filter(fn ($item) => empty($item['readonly']));

        $completedKeys = $evaluation->evidenceRecords
            ->whereNotNull('item_key')
            ->pluck('item_key')
            ->unique()
            ->values();

        $totalRequired = $editableItems->count();
        $completedRequired = $editableItems
            ->whereIn('key', $completedKeys->all())
            ->count();

        $axisProgress = [];
        foreach ([2, 3, 4] as $axisNumber) {
            $axisItems = $editableItems->where('axis_number', $axisNumber);
            $axisCompleted = $axisItems->whereIn('key', $completedKeys->all())->count();
            $axisProgress[$axisNumber] = [
                'completed' => $axisCompleted,
                'total' => $axisItems->count(),
                'percent' => $axisItems->count() > 0 ? round(($axisCompleted / $axisItems->count()) * 100) : 100,
            ];
        }

        $blockingMessages = [];
        if (($axisProgress[2]['completed'] ?? 0) === 0) {
            $blockingMessages[] = 'لا يمكن رفع الاستمارة قبل إضافة توثيق واحد على الأقل في المحور الثاني.';
        }
        if (($axisProgress[3]['completed'] ?? 0) === 0) {
            $blockingMessages[] = 'لا يمكن رفع الاستمارة قبل إدخال درجة وتوثيق تقييم رئيس القسم في المحور الثالث.';
        }
        if (($axisProgress[4]['completed'] ?? 0) === 0) {
            $blockingMessages[] = 'لا يمكن رفع الاستمارة قبل إضافة توثيق واحد على الأقل في المحور الرابع.';
        }

        return [
            'completed' => $completedRequired,
            'total' => $totalRequired,
            'percent' => $totalRequired > 0 ? round(($completedRequired / $totalRequired) * 100) : 100,
            'axis_progress' => $axisProgress,
            'blocking_messages' => $blockingMessages,
        ];
    }

    private function evaluationScoreSnapshot(Evaluation $evaluation): array
    {
        return [
            'score_axis_1' => (float)$evaluation->score_axis_1,
            'score_axis_2' => (float)$evaluation->score_axis_2,
            'score_axis_3' => (float)$evaluation->score_axis_3,
            'score_axis_4' => (float)$evaluation->score_axis_4,
            'total_score' => (float)$evaluation->total_score,
            'final_classification' => $evaluation->final_classification,
        ];
    }
}
