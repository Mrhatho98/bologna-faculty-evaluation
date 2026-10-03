<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\College;
use App\Models\Course;
use App\Models\Department;
use App\Models\Evaluation;
use App\Models\EvaluationPeriod;
use App\Models\EvaluationScoringRule;
use App\Models\Semester;
use App\Models\Student;
use App\Models\SystemSetting;
use App\Models\TeachingStaff;
use App\Models\User;
use App\Services\Form39DefinitionService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UniversityQADashboardController extends Controller
{
    protected WorkflowService $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * Director of QA & University Performance Executive Dashboard.
     */
    public function index()
    {
        $collegeCount = College::count();
        $departmentCount = Department::count();
        $staffCount = TeachingStaff::count();
        $studentCount = Student::count();

        $stats = [
            'total' => Evaluation::count(),
            'draft' => Evaluation::where('status', 'DRAFT')->count(),
            'college_qa_pending' => Evaluation::whereIn('status', ['SUBMITTED_TO_COLLEGE_QA', 'UNDER_COLLEGE_QA_REVIEW'])->count(),
            'university_qa_pending' => Evaluation::whereIn('status', ['SUBMITTED_TO_UNIVERSITY_QA', 'UNDER_UNIVERSITY_QA_REVIEW'])->count(),
            'accepted' => Evaluation::where('status', 'ACCEPTED')->count(),
            'rejected' => Evaluation::where('status', 'REJECTED')->count(),
        ];

        $recentEvaluations = Evaluation::with(['teachingStaff.department', 'college', 'academicYear'])
            ->orderBy('updated_at', 'desc')
            ->take(15)
            ->get();

        return view('university.dashboard', [
            'collegeCount' => $collegeCount,
            'departmentCount' => $departmentCount,
            'staffCount' => $staffCount,
            'studentCount' => $studentCount,
            'stats' => $stats,
            'recentEvaluations' => $recentEvaluations,
        ]);
    }

    /**
     * University-wide executive reports for Form 39 progress and scores.
     */
    public function reportsIndex(Request $request)
    {
        $statusLabels = [
            'DRAFT' => 'مسودة',
            'SUBMITTED_TO_COLLEGE_QA' => 'مقدمة لجودة الكلية',
            'UNDER_COLLEGE_QA_REVIEW' => 'قيد مراجعة الكلية',
            'SUBMITTED_TO_UNIVERSITY_QA' => 'مقدمة لجودة الجامعة',
            'UNDER_UNIVERSITY_QA_REVIEW' => 'قيد مراجعة الجامعة',
            'ACCEPTED' => 'معتمدة',
            'REJECTED' => 'معادة للتصحيح',
        ];

        $baseQuery = Evaluation::query();
        if ($request->filled('academic_year_id')) {
            $baseQuery->where('academic_year_id', $request->academic_year_id);
        }
        if ($request->filled('semester_id')) {
            $baseQuery->where('semester_id', $request->semester_id);
        }
        if ($request->filled('college_id')) {
            $baseQuery->where('college_id', $request->college_id);
        }
        if ($request->filled('department_id')) {
            $baseQuery->where('department_id', $request->department_id);
        }

        $statusCounts = (clone $baseQuery)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get()
            ->map(fn ($row) => [
                'status' => $row->status,
                'label' => $statusLabels[$row->status] ?? $row->status,
                'total' => $row->total,
            ]);

        $scoreAverages = (clone $baseQuery)
            ->selectRaw('
                AVG(score_axis_1) as axis_1,
                AVG(score_axis_2) as axis_2,
                AVG(score_axis_3) as axis_3,
                AVG(score_axis_4) as axis_4,
                AVG(total_score) as total_score
            ')
            ->first();

        $collegePerformance = (clone $baseQuery)
            ->join('colleges', 'evaluations.college_id', '=', 'colleges.id')
            ->select(
                'colleges.name_ar',
                DB::raw('COUNT(evaluations.id) as evaluations_count'),
                DB::raw('AVG(evaluations.total_score) as average_score'),
                DB::raw("SUM(CASE WHEN evaluations.status = 'ACCEPTED' THEN 1 ELSE 0 END) as accepted_count")
            )
            ->groupBy('colleges.id', 'colleges.name_ar')
            ->orderByDesc('evaluations_count')
            ->get();

        $departmentPerformance = (clone $baseQuery)
            ->join('departments', 'evaluations.department_id', '=', 'departments.id')
            ->join('colleges', 'evaluations.college_id', '=', 'colleges.id')
            ->select(
                'departments.name_ar as department_name',
                'colleges.name_ar as college_name',
                DB::raw('COUNT(evaluations.id) as evaluations_count'),
                DB::raw('AVG(evaluations.total_score) as average_score'),
                DB::raw("SUM(CASE WHEN evaluations.status = 'ACCEPTED' THEN 1 ELSE 0 END) as accepted_count")
            )
            ->groupBy('departments.id', 'departments.name_ar', 'colleges.name_ar')
            ->orderByDesc('evaluations_count')
            ->get();

        $classificationCounts = (clone $baseQuery)
            ->select('final_classification', DB::raw('COUNT(*) as total'))
            ->whereNotNull('final_classification')
            ->groupBy('final_classification')
            ->get();

        $studentParticipationQuery = DB::table('student_evaluation_assignments')
            ->join('teaching_assignments', 'student_evaluation_assignments.teaching_assignment_id', '=', 'teaching_assignments.id')
            ->leftJoin('courses', 'teaching_assignments.course_id', '=', 'courses.id');
        if ($request->filled('academic_year_id')) {
            $studentParticipationQuery->where('teaching_assignments.academic_year_id', $request->academic_year_id);
        }
        if ($request->filled('semester_id')) {
            $studentParticipationQuery->where('teaching_assignments.semester_id', $request->semester_id);
        }
        if ($request->filled('college_id')) {
            $studentParticipationQuery->where('courses.college_id', $request->college_id);
        }
        if ($request->filled('department_id')) {
            $studentParticipationQuery->where('teaching_assignments.department_id', $request->department_id);
        }
        $studentParticipation = $studentParticipationQuery
            ->selectRaw('COUNT(student_evaluation_assignments.id) as assigned_count')
            ->selectRaw('SUM(CASE WHEN student_evaluation_assignments.is_completed = 1 THEN 1 ELSE 0 END) as completed_count')
            ->first();
        $studentParticipation->completion_percent = ($studentParticipation->assigned_count ?? 0) > 0
            ? round(((int)$studentParticipation->completed_count / (int)$studentParticipation->assigned_count) * 100, 2)
            : 0;

        $recentAccepted = (clone $baseQuery)
            ->with(['teachingStaff.department', 'college'])
            ->where('status', 'ACCEPTED')
            ->orderByDesc('accepted_at')
            ->orderByDesc('updated_at')
            ->take(10)
            ->get();

        return view('university.reports.index', [
            'statusCounts' => $statusCounts,
            'scoreAverages' => $scoreAverages,
            'collegePerformance' => $collegePerformance,
            'departmentPerformance' => $departmentPerformance,
            'classificationCounts' => $classificationCounts,
            'studentParticipation' => $studentParticipation,
            'recentAccepted' => $recentAccepted,
            'academicYears' => AcademicYear::orderByDesc('name')->get(),
            'semesters' => Semester::orderBy('id')->get(),
            'colleges' => College::with('departments')->get(),
            'filters' => $request->only(['academic_year_id', 'semester_id', 'college_id', 'department_id']),
        ]);
    }

    public function reportsExcel(Request $request)
    {
        $query = Evaluation::with(['teachingStaff.department', 'college', 'academicYear', 'semester']);

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }
        if ($request->filled('college_id')) {
            $query->where('college_id', $request->college_id);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $evaluations = $query->orderByDesc('updated_at')->get();
        $fileName = 'university_form39_report_' . now()->format('Ymd_His') . '.xls';

        return response()
            ->view('university.reports.excel', [
                'evaluations' => $evaluations,
                'filters' => $request->only(['academic_year_id', 'semester_id', 'college_id', 'department_id']),
            ])
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function systemHealth()
    {
        $checks = [
            [
                'label' => 'جدول الإشعارات',
                'status' => Schema::hasTable('notifications'),
                'details' => Schema::hasTable('notifications') ? 'موجود ومفعل' : 'غير موجود',
            ],
            [
                'label' => 'بنية استمارة 39',
                'status' => Schema::hasTable('evaluation_axes') && DB::table('evaluation_items')->count() > 0,
                'details' => 'المحاور: ' . (Schema::hasTable('evaluation_axes') ? DB::table('evaluation_axes')->count() : 0)
                    . '، الفقرات: ' . (Schema::hasTable('evaluation_items') ? DB::table('evaluation_items')->count() : 0),
            ],
            [
                'label' => 'قواعد الاحتساب',
                'status' => Schema::hasTable('evaluation_scoring_rules') && DB::table('evaluation_scoring_rules')->count() > 0,
                'details' => 'عدد القواعد: ' . (Schema::hasTable('evaluation_scoring_rules') ? DB::table('evaluation_scoring_rules')->count() : 0),
            ],
            [
                'label' => 'فترة تقييم فعالة',
                'status' => EvaluationPeriod::open()->exists(),
                'details' => EvaluationPeriod::open()->latest('start_date')->first()?->name_ar ?? 'لا توجد فترة تقييم مفتوحة بتاريخ اليوم',
            ],
            [
                'label' => 'حسابات الأدوار الأساسية',
                'status' => User::where('role', 'department_head')->exists()
                    && User::where('role', 'college_qa')->exists()
                    && User::where('role', 'university_qa_director')->exists(),
                'details' => 'رؤساء الأقسام: ' . User::where('role', 'department_head')->count()
                    . '، جودة الكلية: ' . User::where('role', 'college_qa')->count()
                    . '، جودة الجامعة: ' . User::where('role', 'university_qa_director')->count(),
            ],
            [
                'label' => 'أدوات الاختبار PHPUnit',
                'status' => file_exists(base_path('vendor/bin/phpunit')) || file_exists(base_path('vendor/bin/phpunit.bat')),
                'details' => file_exists(base_path('vendor/bin/phpunit')) || file_exists(base_path('vendor/bin/phpunit.bat'))
                    ? 'متوفرة'
                    : 'غير مثبتة حالياً، يلزم تثبيت dev dependencies لتشغيل الاختبارات الآلية',
            ],
        ];

        return view('university.system.health', [
            'checks' => $checks,
        ]);
    }

    /**
     * University-wide list of evaluations with filters.
     */
    public function evaluationsList(Request $request)
    {
        $query = Evaluation::with(['teachingStaff.department', 'college', 'academicYear', 'semester']);

        if ($request->filled('college_id')) {
            $query->where('college_id', $request->college_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $evaluations = $query->orderBy('updated_at', 'desc')->paginate(20);

        $colleges = College::with('departments')->get();

        return view('university.evaluations.index', [
            'evaluations' => $evaluations,
            'colleges' => $colleges,
            'filters' => $request->only(['college_id', 'department_id', 'status']),
        ]);
    }

    /**
     * Review evaluation details.
     */
    public function reviewEvaluation(Evaluation $evaluation)
    {
        $evaluation->load([
            'teachingStaff.department',
            'teachingStaff.college',
            'academicYear',
            'semester',
            'evidenceRecords.createdBy',
            'administrativePenalties',
            'workflowHistories.user',
        ]);

        return view('university.evaluations.review', [
            'evaluation' => $evaluation,
            'form39Axes' => Form39DefinitionService::axes(),
            'evidenceByItem' => $evaluation->evidenceRecords->whereNotNull('item_key')->groupBy('item_key'),
        ]);
    }

    /**
     * Accept evaluation -> Marks status ACCEPTED and locks permanently.
     */
    public function acceptEvaluation(Request $request, Evaluation $evaluation)
    {
        $notes = $request->input('notes', 'تمت المصادقة النهائية والاعتماد من قبل مدير قسم ضمان الجودة والأداء الجامعي.');

        try {
            $this->workflowService->transition($evaluation, 'ACCEPTED', $notes);

            AuditLog::logAction('university_qa_accept', 'Evaluations', $evaluation->id, null, [
                'accepted_by' => auth()->user()->name,
                'total_score' => $evaluation->total_score,
                'final_classification' => $evaluation->final_classification,
            ]);

            return redirect()->route('university.evaluations.index')
                ->with('success', 'تم اعتماد تقييم التدريسي رسمياً وقفله بصفة نهائية.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject evaluation -> Requires MANDATORY comments/reason and returns to College QA correction workflow.
     */
    public function rejectEvaluation(Request $request, Evaluation $evaluation)
    {
        $validated = $request->validate([
            'rejection_comments' => ['required', 'string', 'min:5'],
        ], [
            'rejection_comments.required' => 'يجب كتابة أسباب الرفض والملاحظات المطلوبة للتصحيح قبل الإرجاع.',
        ]);

        try {
            $this->workflowService->transition($evaluation, 'REJECTED', $validated['rejection_comments']);

            AuditLog::logAction('university_qa_reject', 'Evaluations', $evaluation->id, null, [
                'rejected_by' => auth()->user()->name,
                'comments' => $validated['rejection_comments'],
            ]);

            return redirect()->route('university.evaluations.index')
                ->with('success', 'تم إرجاع التقييم إلى الكلية والقسم المعني مع تسجيل أسباب الرفض بالتفصيل.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Manage Colleges.
     */
    public function collegesIndex()
    {
        $colleges = College::withCount('departments')->get();
        return view('university.colleges.index', ['colleges' => $colleges]);
    }

    public function storeCollege(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:colleges,code'],
        ]);

        $validated['is_active'] = true;
        $college = College::create($validated);

        AuditLog::logAction('create_college', 'Colleges', $college->id, null, $validated);

        return redirect()->route('university.colleges.index')
            ->with('success', 'تم إضافـة الكلية بنجاح.');
    }

    /**
     * Manage Departments.
     */
    public function departmentsIndex()
    {
        $departments = Department::with('college')->get();
        $colleges = College::all();

        return view('university.departments.index', [
            'departments' => $departments,
            'colleges' => $colleges,
        ]);
    }

    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'college_id' => ['required', 'exists:colleges,id'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50'],
        ]);

        $validated['is_active'] = true;
        $department = Department::create($validated);

        AuditLog::logAction('create_department', 'Departments', $department->id, null, $validated);

        return redirect()->route('university.departments.index')
            ->with('success', 'تم إضافـة القسم العلمي بنجاح.');
    }

    /**
     * Manage Administrative Users.
     */
    public function usersIndex()
    {
        $users = User::with(['college', 'department'])->where('role', '!=', 'student')->get();
        $colleges = College::all();
        $departments = Department::all();

        return view('university.users.index', [
            'users' => $users,
            'colleges' => $colleges,
            'departments' => $departments,
        ]);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:department_head,college_qa,university_qa_director'],
            'college_id' => ['nullable', 'exists:colleges,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        if ($validated['role'] === 'department_head' && empty($validated['department_id'])) {
            return back()->withInput()->withErrors([
                'department_id' => 'يجب تحديد القسم لرئيس القسم العلمي.',
            ]);
        }

        if (in_array($validated['role'], ['department_head', 'college_qa']) && empty($validated['college_id'])) {
            return back()->withInput()->withErrors([
                'college_id' => 'يجب تحديد الكلية لهذا الدور.',
            ]);
        }

        if (!empty($validated['department_id']) && !empty($validated['college_id'])) {
            $departmentMatchesCollege = Department::where('id', $validated['department_id'])
                ->where('college_id', $validated['college_id'])
                ->exists();

            if (!$departmentMatchesCollege) {
                return back()->withInput()->withErrors([
                    'department_id' => 'القسم المختار لا يتبع الكلية المحددة.',
                ]);
            }
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true;

        $user = User::create($validated);

        AuditLog::logAction('create_admin_user', 'Users', $user->id, null, [
            'username' => $user->username,
            'role' => $user->role,
        ]);

        return redirect()->route('university.users.index')
            ->with('success', 'تم إنشاء حساب المستخدم الإداري بنجاح.');
    }

    /**
     * Manage academic years, semesters and student evaluation periods.
     */
    public function academicStructureIndex()
    {
        $academicYears = AcademicYear::with(['semesters', 'evaluationPeriods.semester'])
            ->orderByDesc('name')
            ->get();

        $semesters = Semester::with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderBy('id')
            ->get();

        $periods = EvaluationPeriod::with(['academicYear', 'semester'])
            ->orderByDesc('start_date')
            ->get();

        return view('university.academic.index', [
            'academicYears' => $academicYears,
            'semesters' => $semesters,
            'periods' => $periods,
        ]);
    }

    public function storeAcademicYear(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20', 'unique:academic_years,name'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($validated) {
            if (!empty($validated['is_current'])) {
                AcademicYear::query()->update(['is_current' => false]);
            }

            $year = AcademicYear::create([
                'name' => $validated['name'],
                'is_current' => !empty($validated['is_current']),
            ]);

            AuditLog::logAction('create_academic_year', 'AcademicYears', $year->id, null, $year->toArray());
        });

        return redirect()->route('university.academic.index')
            ->with('success', 'تمت إضافة السنة الدراسية بنجاح.');
    }

    public function storeSemester(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $semester = Semester::create([
            'academic_year_id' => $validated['academic_year_id'],
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
            'is_active' => !empty($validated['is_active']),
        ]);

        AuditLog::logAction('create_semester', 'Semesters', $semester->id, null, $semester->toArray());

        return redirect()->route('university.academic.index')
            ->with('success', 'تمت إضافة الفصل الدراسي بنجاح.');
    }

    public function storeEvaluationPeriod(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester_id' => [
                'required',
                Rule::exists('semesters', 'id')->where('academic_year_id', $request->input('academic_year_id')),
            ],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $academicYear = AcademicYear::findOrFail($validated['academic_year_id']);
        $periodDates = EvaluationPeriod::annualDatesForAcademicYearName($academicYear->name);

        if (!$periodDates) {
            throw ValidationException::withMessages([
                'academic_year_id' => 'اسم السنة الدراسية يجب أن يحتوي سنة بداية واضحة مثل 2026-2027 حتى يتم تحديد الفترة من 1/9 إلى 31/8.',
            ]);
        }

        DB::transaction(function () use ($validated, $periodDates) {
            if (!empty($validated['is_active'])) {
                EvaluationPeriod::where('academic_year_id', $validated['academic_year_id'])
                    ->where('semester_id', $validated['semester_id'])
                    ->update(['is_active' => false]);
            }

            $period = EvaluationPeriod::create([
                'academic_year_id' => $validated['academic_year_id'],
                'semester_id' => $validated['semester_id'],
                'name_ar' => $validated['name_ar'],
                'name_en' => $validated['name_en'],
                'start_date' => $periodDates['start_date'],
                'end_date' => $periodDates['end_date'],
                'is_active' => !empty($validated['is_active']),
            ]);

            AuditLog::logAction('create_evaluation_period', 'EvaluationPeriods', $period->id, null, $period->toArray());
        });

        return redirect()->route('university.academic.index')
            ->with('success', 'تمت إضافة فترة التقييم بنجاح.');
    }

    /**
     * System-wide Audit Trail viewer.
     */
    public function auditLogsIndex()
    {
        $logs = AuditLog::orderBy('created_at', 'desc')->paginate(30);

        return view('university.audit.index', [
            'logs' => $logs,
        ]);
    }

    /**
     * Manage Configurable System Settings.
     */
    public function settingsIndex()
    {
        $rankMultipliers = SystemSetting::get('academic_rank_multipliers', [
            'professor' => 1.0,
            'assistant_professor' => 1.4,
            'lecturer' => 1.6,
            'assistant_lecturer' => 1.8,
        ]);

        $minEvaluators = SystemSetting::get('min_student_evaluators', 10);
        $formula = SystemSetting::get('student_evaluation_aggregation_formula', 'mean');

        return view('university.settings.index', [
            'rankMultipliers' => $rankMultipliers,
            'minEvaluators' => $minEvaluators,
            'formula' => $formula,
        ]);
    }

    public function scoringRulesIndex()
    {
        $rules = EvaluationScoringRule::orderBy('scope_type')
            ->orderBy('scope_code')
            ->orderBy('rule_key')
            ->get()
            ->groupBy('scope_type');

        return view('university.settings.rules', [
            'rules' => $rules,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'prof_multiplier' => ['required', 'numeric', 'min:0'],
            'assoc_prof_multiplier' => ['required', 'numeric', 'min:0'],
            'lecturer_multiplier' => ['required', 'numeric', 'min:0'],
            'asst_lecturer_multiplier' => ['required', 'numeric', 'min:0'],
            'min_evaluators' => ['required', 'integer', 'min:10'],
            'formula' => ['required', 'string', 'in:mean,weighted_mean'],
        ]);

        SystemSetting::set('academic_rank_multipliers', [
            'professor' => (float)$validated['prof_multiplier'],
            'assistant_professor' => (float)$validated['assoc_prof_multiplier'],
            'lecturer' => (float)$validated['lecturer_multiplier'],
            'assistant_lecturer' => (float)$validated['asst_lecturer_multiplier'],
        ], 'scoring');

        SystemSetting::set('min_student_evaluators', (int)$validated['min_evaluators'], 'workflow');
        SystemSetting::set('student_evaluation_aggregation_formula', $validated['formula'], 'scoring');

        AuditLog::logAction('update_system_settings', 'SystemSettings', null, null, $validated);

        return redirect()->route('university.settings.index')
            ->with('success', 'تم إعداد القواعد التشغيلية وشروط الحسابات بنجاح.');
    }
}
