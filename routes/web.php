<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CollegeQADashboardController;
use App\Http\Controllers\DepartmentHeadDashboardController;
use App\Http\Controllers\EvaluationReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\UniversityQADashboardController;
use Illuminate\Support\Facades\Route;

// Public Landing Page & Auth Portals
Route::get('/', [AuthController::class, 'index'])->name('landing');
Route::get('/login/{portal}', [AuthController::class, 'showLoginForm'])->name('login.portal');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/lang/{locale}', [AuthController::class, 'switchLanguage'])->name('lang.switch');

Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
});

// Student Portal Routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/evaluate/{assignment}', [StudentDashboardController::class, 'showEvaluationForm'])->name('evaluate');
    Route::post('/evaluate/{assignment}', [StudentDashboardController::class, 'submitEvaluation'])->name('evaluate.submit');
});

// Head of Scientific Department Portal Routes
Route::middleware(['auth', 'role:department_head'])->prefix('department')->name('dept.')->group(function () {
    Route::get('/dashboard', [DepartmentHeadDashboardController::class, 'index'])->name('dashboard');

    // Teaching Staff Management
    Route::get('/staff', [DepartmentHeadDashboardController::class, 'teachingStaffIndex'])->name('staff.index');
    Route::post('/staff', [DepartmentHeadDashboardController::class, 'storeTeachingStaff'])->name('staff.store');

    // Courses Management
    Route::get('/courses', [DepartmentHeadDashboardController::class, 'coursesIndex'])->name('courses.index');
    Route::post('/courses', [DepartmentHeadDashboardController::class, 'storeCourse'])->name('courses.store');

    // Teaching Assignments
    Route::get('/assignments', [DepartmentHeadDashboardController::class, 'assignmentsIndex'])->name('assignments.index');
    Route::post('/assignments', [DepartmentHeadDashboardController::class, 'storeAssignment'])->name('assignments.store');

    // Evaluator Management (Minimum 10 Rule)
    Route::get('/assignments/{assignment}/evaluators', [DepartmentHeadDashboardController::class, 'manageEvaluators'])->name('assignments.evaluators');
    Route::post('/assignments/{assignment}/evaluators', [DepartmentHeadDashboardController::class, 'updateEvaluators'])->name('assignments.evaluators.update');

    // Student Credentials Generation
    Route::get('/students/generate', [DepartmentHeadDashboardController::class, 'generateStudentCredentialsForm'])->name('students.generate');
    Route::post('/students/generate', [DepartmentHeadDashboardController::class, 'processGenerateStudentCredentials'])->name('students.generate.process');

    // Portfolio & Form 39 Entry
    Route::get('/evaluations/{evaluation}/edit', [DepartmentHeadDashboardController::class, 'editEvaluation'])->name('evaluations.edit');
    Route::post('/evaluations/{evaluation}/evidence', [DepartmentHeadDashboardController::class, 'storeEvidence'])->name('evaluations.evidence.store');
    Route::delete('/evaluations/{evaluation}/evidence/{evidenceRecord}', [DepartmentHeadDashboardController::class, 'destroyEvidence'])->name('evaluations.evidence.destroy');
    Route::post('/evaluations/{evaluation}/penalty', [DepartmentHeadDashboardController::class, 'storePenalty'])->name('evaluations.penalty.store');
    Route::delete('/evaluations/{evaluation}/penalty/{penalty}', [DepartmentHeadDashboardController::class, 'destroyPenalty'])->name('evaluations.penalty.destroy');
    Route::post('/evaluations/{evaluation}/submit', [DepartmentHeadDashboardController::class, 'submitToCollegeQA'])->name('evaluations.submit');
});

// College QA Officer Portal Routes
Route::middleware(['auth', 'role:college_qa'])->prefix('college')->name('college.')->group(function () {
    Route::get('/dashboard', [CollegeQADashboardController::class, 'index'])->name('dashboard');
    Route::get('/evaluations/{evaluation}/review', [CollegeQADashboardController::class, 'review'])->name('evaluations.review');
    Route::post('/evaluations/{evaluation}/forward', [CollegeQADashboardController::class, 'forwardToUniversityQA'])->name('evaluations.forward');
    Route::post('/evaluations/{evaluation}/return', [CollegeQADashboardController::class, 'returnToDepartment'])->name('evaluations.return');
});

// Director of Quality Assurance and University Performance Portal Routes
Route::middleware(['auth', 'role:university_qa_director'])->prefix('university')->name('university.')->group(function () {
    Route::get('/dashboard', [UniversityQADashboardController::class, 'index'])->name('dashboard');
    Route::get('/reports', [UniversityQADashboardController::class, 'reportsIndex'])->name('reports.index');
    Route::get('/reports/excel', [UniversityQADashboardController::class, 'reportsExcel'])->name('reports.excel');
    Route::get('/system-health', [UniversityQADashboardController::class, 'systemHealth'])->name('system.health');

    Route::get('/evaluations', [UniversityQADashboardController::class, 'evaluationsList'])->name('evaluations.index');
    Route::get('/evaluations/{evaluation}/review', [UniversityQADashboardController::class, 'reviewEvaluation'])->name('evaluations.review');
    Route::post('/evaluations/{evaluation}/accept', [UniversityQADashboardController::class, 'acceptEvaluation'])->name('evaluations.accept');
    Route::post('/evaluations/{evaluation}/reject', [UniversityQADashboardController::class, 'rejectEvaluation'])->name('evaluations.reject');

    // Colleges & Departments
    Route::get('/colleges', [UniversityQADashboardController::class, 'collegesIndex'])->name('colleges.index');
    Route::post('/colleges', [UniversityQADashboardController::class, 'storeCollege'])->name('colleges.store');

    Route::get('/departments', [UniversityQADashboardController::class, 'departmentsIndex'])->name('departments.index');
    Route::post('/departments', [UniversityQADashboardController::class, 'storeDepartment'])->name('departments.store');

    // Academic Years, Semesters & Evaluation Periods
    Route::get('/academic-structure', [UniversityQADashboardController::class, 'academicStructureIndex'])->name('academic.index');
    Route::post('/academic-years', [UniversityQADashboardController::class, 'storeAcademicYear'])->name('academic-years.store');
    Route::post('/semesters', [UniversityQADashboardController::class, 'storeSemester'])->name('semesters.store');
    Route::post('/evaluation-periods', [UniversityQADashboardController::class, 'storeEvaluationPeriod'])->name('evaluation-periods.store');

    // Administrative Users
    Route::get('/users', [UniversityQADashboardController::class, 'usersIndex'])->name('users.index');
    Route::post('/users', [UniversityQADashboardController::class, 'storeUser'])->name('users.store');

    // Audit Trail
    Route::get('/audit-logs', [UniversityQADashboardController::class, 'auditLogsIndex'])->name('audit.index');

    // Settings
    Route::get('/settings', [UniversityQADashboardController::class, 'settingsIndex'])->name('settings.index');
    Route::get('/settings/scoring-rules', [UniversityQADashboardController::class, 'scoringRulesIndex'])->name('settings.rules');
    Route::post('/settings', [UniversityQADashboardController::class, 'updateSettings'])->name('settings.update');
});

// Official Printable Form No. (39) Route (Shared authenticated view)
Route::middleware('auth')->get('/evaluations/{evaluation}/print', [EvaluationReportController::class, 'printForm39'])->name('evaluations.print');
Route::middleware('auth')->get('/evaluations/{evaluation}/excel', [EvaluationReportController::class, 'exportExcel'])->name('evaluations.excel');
