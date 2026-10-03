<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\EvaluationPeriod;
use App\Models\StudentEvaluation;
use App\Models\StudentEvaluationAssignment;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentDashboardController extends Controller
{
    /**
     * Student main dashboard displaying assigned teaching assignments & evaluation status.
     */
    public function index()
    {
        $user = auth()->user();
        $student = $user->studentProfile;
        if (!$student) {
            abort(403, 'الملف الشخصي للطالب غير موجود.');
        }

        $assignments = StudentEvaluationAssignment::with([
            'teachingAssignment.teachingStaff',
            'teachingAssignment.course',
            'teachingAssignment.academicYear',
            'teachingAssignment.semester',
        ])->where('student_id', $student->id)->get();

        $activePeriod = EvaluationPeriod::open()->latest('start_date')->first();

        return view('student.dashboard', [
            'student' => $student,
            'assignments' => $assignments,
            'activePeriod' => $activePeriod,
        ]);
    }

    /**
     * Form to evaluate a specific course teaching assignment (Axis 1).
     */
    public function showEvaluationForm(TeachingAssignment $assignment)
    {
        $user = auth()->user();
        $student = $user->studentProfile;

        $evalAssignment = StudentEvaluationAssignment::where('student_id', $student->id)
            ->where('teaching_assignment_id', $assignment->id)
            ->firstOrFail();

        $activePeriod = EvaluationPeriod::open()->latest('start_date')->first();
        if (!$activePeriod || !$activePeriod->isOpen()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'فترة تقييم الطلبة مغلقة حالياً ولا يمكن تقديم تقييمات جديدة.');
        }

        // Check if student already submitted this evaluation
        $existingEval = StudentEvaluation::where('student_id', $student->id)
            ->where('teaching_assignment_id', $assignment->id)
            ->where('evaluation_period_id', $activePeriod->id)
            ->first();

        if ($existingEval || $evalAssignment->is_completed) {
            return redirect()->route('student.dashboard')
                ->with('error', 'لقد قمت بإكمال تقييم هذه المادة الدراسية سابقاً ولا يمكن تعديله.');
        }

        $questionsSectionA = [
            'q1' => 'يقوم المحاضر بتقديم موجز عن عملية التدريس والتقييم وفق مسار بولونيا في أول محاضرة.',
            'q2' => 'يقوم المحاضر بتقديم وشرح دليل وصف المادة الدراسية في الأسبوع الأول للفصل الدراسي.',
            'q3' => 'يلتزم المحاضر بطريقة التقييم التكويني من حيث عدد الامتحانات القصيرة والواجبات المنزلية والفعاليات العلمية ومواعيدها المذكورة في دليل وصف المادة الدراسية.',
            'q4' => 'يقوم المحاضر بتوضيح علاقة مفردات المنهاج الأسبوعي بمخرجات التعلم.',
        ];

        $questionsSectionB = [
            'q1' => 'طريقة التدريس والمحاضرة مثيرة للاهتمام وتحفز الطالب ليفهم المادة العلمية.',
            'q2' => 'يتم تزويد الطالب بقائمة من المراجع المختلفة بالإضافة للمراجع الرئيسية.',
            'q3' => 'يعطي المحاضر الوقت الكافي للأسئلة والأجوبة خلال المحاضرة.',
            'q4' => 'يستخدم المحاضر التقنيات اللازمة وأدوات الصوت والفيديو لشرح محاضراته.',
            'q5' => 'خلال المحاضرة يعامل المحاضر الطلاب باحترام.',
            'q6' => 'تعكس أسئلة الامتحان محتويات المادة العلمية المرتبطة بمخرجات التعلم.',
        ];

        return view('student.evaluate', [
            'assignment' => $assignment,
            'evalAssignment' => $evalAssignment,
            'activePeriod' => $activePeriod,
            'questionsSectionA' => $questionsSectionA,
            'questionsSectionB' => $questionsSectionB,
        ]);
    }

    /**
     * Store student evaluation submission (Axis 1).
     */
    public function submitEvaluation(Request $request, TeachingAssignment $assignment)
    {
        $user = auth()->user();
        $student = $user->studentProfile;
        abort_unless($student, 403);

        $evalAssignment = StudentEvaluationAssignment::where('student_id', $student->id)
            ->where('teaching_assignment_id', $assignment->id)
            ->firstOrFail();

        if ($evalAssignment->is_completed) {
            return redirect()->route('student.dashboard')
                ->with('error', 'تم تقديم هذا التقييم سابقاً ولا يمكن تعديله.');
        }

        $activePeriod = EvaluationPeriod::open()->latest('start_date')->firstOrFail();
        if (!$activePeriod->isOpen()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'فترة التقييم مغلقة حالياً.');
        }

        if ((int) $activePeriod->academic_year_id !== (int) $assignment->academic_year_id ||
            (int) $activePeriod->semester_id !== (int) $assignment->semester_id) {
            return redirect()->route('student.dashboard')
                ->with('error', 'فترة التقييم الفعالة لا تطابق السنة والفصل لهذا التكليف.');
        }

        $validated = $request->validate([
            'sec_a_q1' => ['required', 'integer', 'in:0,1'],
            'sec_a_q2' => ['required', 'integer', 'in:0,1'],
            'sec_a_q3' => ['required', 'integer', 'in:0,1'],
            'sec_a_q4' => ['required', 'integer', 'in:0,1'],
            'sec_b_q1' => ['required', 'integer', 'in:0,1'],
            'sec_b_q2' => ['required', 'integer', 'in:0,1'],
            'sec_b_q3' => ['required', 'integer', 'in:0,1'],
            'sec_b_q4' => ['required', 'integer', 'in:0,1'],
            'sec_b_q5' => ['required', 'integer', 'in:0,1'],
            'sec_b_q6' => ['required', 'integer', 'in:0,1'],
        ]);

        $scoreSectionA = array_sum([
            $validated['sec_a_q1'],
            $validated['sec_a_q2'],
            $validated['sec_a_q3'],
            $validated['sec_a_q4'],
        ]); // max 4

        $scoreSectionB = array_sum([
            $validated['sec_b_q1'],
            $validated['sec_b_q2'],
            $validated['sec_b_q3'],
            $validated['sec_b_q4'],
            $validated['sec_b_q5'],
            $validated['sec_b_q6'],
        ]); // max 6

        $totalScore = $scoreSectionA + $scoreSectionB; // max 10

        DB::transaction(function () use ($student, $assignment, $activePeriod, $validated, $scoreSectionA, $scoreSectionB, $totalScore) {
            // Prevent duplicate submission
            $existing = StudentEvaluation::where('student_id', $student->id)
                ->where('teaching_assignment_id', $assignment->id)
                ->where('evaluation_period_id', $activePeriod->id)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                throw new \Exception('لقد قمت بتقديم هذا التقييم سابقاً.');
            }

            StudentEvaluation::create([
                'student_id' => $student->id,
                'teaching_assignment_id' => $assignment->id,
                'evaluation_period_id' => $activePeriod->id,
                'answers_json' => $validated,
                'score_section_a' => $scoreSectionA,
                'score_section_b' => $scoreSectionB,
                'total_score' => $totalScore,
                'submitted_at' => now(),
            ]);

            // Mark student assignment completed
            StudentEvaluationAssignment::where('student_id', $student->id)
                ->where('teaching_assignment_id', $assignment->id)
                ->update(['is_completed' => true]);

            AuditLog::logAction('submit_student_evaluation', 'StudentEvaluations', $assignment->id, null, [
                'total_score' => $totalScore,
            ]);
        });

        return redirect()->route('student.dashboard')
            ->with('success', 'تم تقديم تقييمك للمادة بنجاح وقفل الاستمارة.');
    }
}
