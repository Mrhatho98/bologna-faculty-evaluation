<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Colleges
        Schema::create('colleges', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Scientific Departments
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('college_id')->constrained('colleges')->onDelete('cascade');
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Academic Years
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. "2025-2026"
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        // 4. Semesters
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->string('name_ar'); // e.g. "الفصل الأول"
            $table->string('name_en'); // e.g. "First Semester"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Evaluation Periods
        Schema::create('evaluation_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->string('name_ar');
            $table->string('name_en');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Teaching Staff (Full Form 39 baseline fields)
        Schema::create('teaching_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('college_id')->constrained('colleges')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            
            // Full 4-generation Arabic name & surname
            $table->string('first_name');
            $table->string('father_name');
            $table->string('grandfather_name');
            $table->string('great_grandfather_name')->nullable();
            $table->string('surname')->nullable();

            // Maternal lineage
            $table->string('mother_name')->nullable();
            $table->string('mother_father_name')->nullable();
            $table->string('mother_grandfather_name')->nullable();

            // Unified Card / National ID details
            $table->string('national_id')->nullable();
            $table->string('record_number')->nullable();
            $table->string('page_number')->nullable();
            $table->date('issue_date')->nullable();

            // Degree details
            $table->string('degree')->nullable(); // Doctorate, Master, etc.
            $table->string('ministerial_order_no')->nullable();
            $table->date('degree_date')->nullable();
            $table->string('degree_country')->nullable();
            $table->string('degree_university')->nullable();
            $table->string('degree_college')->nullable();
            $table->string('degree_department')->nullable();

            // Specialization
            $table->string('general_specialization')->nullable();
            $table->string('specific_specialization')->nullable();

            // Academic Rank
            $table->enum('academic_rank', [
                'professor',
                'assistant_professor',
                'lecturer',
                'assistant_lecturer'
            ])->default('lecturer');
            $table->string('rank_awarding_body')->nullable();
            $table->date('rank_date')->nullable();

            // Contact
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });

        // 7. Courses
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('code');
            $table->string('name_ar');
            $table->string('name_en');
            $table->integer('stage')->default(1); // 1, 2, 3, 4, etc.
            $table->foreignId('semester_id')->nullable()->constrained('semesters')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 8. Teaching Assignments (Teaching Staff + Course + Period)
        Schema::create('teaching_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_staff_id')->constrained('teaching_staff')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->integer('stage')->default(1);
            $table->string('class_group')->nullable();
            $table->integer('min_evaluators')->default(10); // Minimum 10 enforcement
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 9. Students
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('student_id_number')->unique();
            $table->integer('stage')->default(1);
            $table->string('class_group')->nullable();
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // 10. Student Evaluation Assignments
        Schema::create('student_evaluation_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('teaching_assignment_id')->constrained('teaching_assignments')->onDelete('cascade');
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->unique(['student_id', 'teaching_assignment_id'], 'stu_eval_assign_unique');
        });

        // 11. Student Evaluations (Axis 1 Submission)
        Schema::create('student_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('teaching_assignment_id')->constrained('teaching_assignments')->onDelete('cascade');
            $table->foreignId('evaluation_period_id')->constrained('evaluation_periods')->onDelete('cascade');
            $table->json('answers_json'); // Detailed per-item responses
            $table->decimal('score_section_a', 5, 2)->default(0); // Max 4
            $table->decimal('score_section_b', 5, 2)->default(0); // Max 6
            $table->decimal('total_score', 5, 2)->default(0);     // Max 10
            $table->timestamp('submitted_at');
            $table->timestamps();

            // ONE evaluation per student per course assignment per period
            $table->unique(
                ['student_id', 'teaching_assignment_id', 'evaluation_period_id'],
                'idx_stu_eval_unique'
            );
        });

        // 12. Main Evaluation Form 39 Master Record
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_staff_id')->constrained('teaching_staff')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('college_id')->constrained('colleges')->onDelete('cascade');
            
            $table->enum('status', [
                'DRAFT',
                'SUBMITTED_TO_COLLEGE_QA',
                'UNDER_COLLEGE_QA_REVIEW',
                'SUBMITTED_TO_UNIVERSITY_QA',
                'UNDER_UNIVERSITY_QA_REVIEW',
                'ACCEPTED',
                'REJECTED'
            ])->default('DRAFT');

            // Four Axes Scores
            $table->decimal('score_axis_1', 5, 2)->default(0); // Student feedback (max 10)
            $table->decimal('score_axis_2', 5, 2)->default(0); // Portfolio (max 60)
            $table->decimal('score_axis_3', 5, 2)->default(0); // Department Head (max 15)
            $table->decimal('score_axis_4', 5, 2)->default(0); // Educational & Guidance (max 15)
            $table->decimal('total_score', 5, 2)->default(0);  // Total (max 100)

            $table->enum('final_classification', [
                'Excellent',  // 90+
                'Very Good',  // 80-89
                'Good',       // 70-79
                'Weak'        // <70
            ])->nullable();

            $table->json('axes_details_json')->nullable(); // Detailed sub-category breakdowns

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('current_reviewer_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });

        // 13. Evidence Records (URLs + Descriptions, NO file uploads)
        Schema::create('evidence_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->integer('axis_number')->default(2);
            $table->string('section_key');
            $table->string('item_key')->nullable()->index();
            $table->string('title');
            $table->text('description');
            $table->string('evidence_url');
            $table->decimal('score_awarded', 5, 2)->default(0);
            $table->json('metadata_json')->nullable();
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 14. Administrative Penalties (Axis 3 Deductions)
        Schema::create('administrative_penalties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->enum('penalty_type', [
                'notice_of_attention', // -3
                'warning',             // -4
                'salary_suspension',   // -5
                'reprimand',           // -6
                'salary_reduction',    // -7
                'rank_reduction'       // -8
            ]);
            $table->date('penalty_date');
            $table->string('order_number')->nullable();
            $table->text('description');
            $table->string('evidence_url')->nullable();
            $table->text('evidence_description')->nullable();
            $table->decimal('deduction_points', 5, 2)->default(0);
            $table->timestamps();
        });

        // 15. Workflow History (Rejection comments, status audit trail)
        Schema::create('workflow_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->string('from_status');
            $table->string('to_status');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('comments')->nullable();
            $table->timestamps();
        });

        // 16. Audit Log (System-wide security audit trail)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name')->nullable();
            $table->string('role')->nullable();
            $table->string('action');
            $table->string('module');
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('old_values_json')->nullable();
            $table->json('new_values_json')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        // 17. System Settings (Configurable business rules)
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('group')->default('general');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('workflow_histories');
        Schema::dropIfExists('administrative_penalties');
        Schema::dropIfExists('evidence_records');
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('student_evaluations');
        Schema::dropIfExists('student_evaluation_assignments');
        Schema::dropIfExists('students');
        Schema::dropIfExists('teaching_assignments');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('teaching_staff');
        Schema::dropIfExists('evaluation_periods');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('colleges');
    }
};
