<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\College;
use App\Models\Course;
use App\Models\Department;
use App\Models\Evaluation;
use App\Models\EvaluationPeriod;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentEvaluationAssignment;
use App\Models\SystemSetting;
use App\Models\TeachingAssignment;
use App\Models\TeachingStaff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BolognaSystemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Base System Settings
        SystemSetting::set('min_student_evaluators', 10, 'workflow', 'Minimum required student evaluators per course');
        SystemSetting::set('academic_rank_multipliers', [
            'professor' => 1.0,
            'assistant_professor' => 1.4,
            'lecturer' => 1.6,
            'assistant_lecturer' => 1.8,
        ], 'scoring', 'Official academic rank multipliers for research category');
        SystemSetting::set('student_evaluation_aggregation_formula', 'mean', 'scoring', 'Aggregation method for student evaluations');

        // 2. Colleges & Departments
        $csCollege = College::create([
            'name_ar' => 'كلية علوم الحاسوب وتكنولوجيا المعلومات',
            'name_en' => 'College of Computer Science & IT',
            'code' => 'CSIT',
            'is_active' => true,
        ]);

        $deptCS = Department::create([
            'college_id' => $csCollege->id,
            'name_ar' => 'قسم علوم الحاسوب',
            'name_en' => 'Computer Science',
            'code' => 'CS',
            'is_active' => true,
        ]);

        $deptIT = Department::create([
            'college_id' => $csCollege->id,
            'name_ar' => 'قسم تكنولوجيا المعلومات',
            'name_en' => 'Information Technology',
            'code' => 'IT',
            'is_active' => true,
        ]);

        Department::create([
            'college_id' => $csCollege->id,
            'name_ar' => 'قسم شبكات الحاسوب',
            'name_en' => 'Computer Networks',
            'code' => 'CN',
            'is_active' => true,
        ]);

        Department::create([
            'college_id' => $csCollege->id,
            'name_ar' => 'قسم الذكاء الاصطناعي',
            'name_en' => 'Artificial Intelligence',
            'code' => 'AI',
            'is_active' => true,
        ]);

        // College of Science
        $sciCollege = College::create([
            'name_ar' => 'كلية العلوم',
            'name_en' => 'College of Science',
            'code' => 'SCI',
            'is_active' => true,
        ]);
        Department::create(['college_id' => $sciCollege->id, 'name_ar' => 'قسم الفيزياء', 'name_en' => 'Physics', 'code' => 'PHYS']);
        Department::create(['college_id' => $sciCollege->id, 'name_ar' => 'قسم الكيمياء', 'name_en' => 'Chemistry', 'code' => 'CHEM']);

        // College of Admin & Economics
        $econCollege = College::create([
            'name_ar' => 'كلية الإدارة والاقتصاد',
            'name_en' => 'College of Administration and Economics',
            'code' => 'ECO',
            'is_active' => true,
        ]);
        Department::create(['college_id' => $econCollege->id, 'name_ar' => 'قسم إدارة الأعمال', 'name_en' => 'Business Admin', 'code' => 'BA']);

        // College of Engineering
        $engCollege = College::create([
            'name_ar' => 'كلية الهندسة',
            'name_en' => 'College of Engineering',
            'code' => 'ENG',
            'is_active' => true,
        ]);
        Department::create(['college_id' => $engCollege->id, 'name_ar' => 'قسم الهندسة المدنية', 'name_en' => 'Civil Engineering', 'code' => 'CE']);

        // College of Excellence for Business
        $busCollege = College::create([
            'name_ar' => 'كلية التميز للأعمال',
            'name_en' => 'College of Excellence for Business',
            'code' => 'CEB',
            'is_active' => true,
        ]);
        Department::create(['college_id' => $busCollege->id, 'name_ar' => 'قسم التسويق الرقمي', 'name_en' => 'Digital Marketing', 'code' => 'DM']);
        Department::create(['college_id' => $busCollege->id, 'name_ar' => 'قسم المالية والاستثمار', 'name_en' => 'Finance & Investment', 'code' => 'FI']);

        // College of Applied Sciences — Heet
        $heetCollege = College::create([
            'name_ar' => 'كلية العلوم التطبيقية - هيت',
            'name_en' => 'College of Applied Sciences - Heet',
            'code' => 'CASH',
            'is_active' => true,
        ]);
        Department::create(['college_id' => $heetCollege->id, 'name_ar' => 'قسم الكيمياء التطبيقية', 'name_en' => 'Applied Chemistry', 'code' => 'AC']);

        // 3. Academic Year & Semester
        $year = AcademicYear::create([
            'name' => '2025-2026',
            'is_current' => true,
        ]);

        $semester = Semester::create([
            'academic_year_id' => $year->id,
            'name_ar' => 'الفصل الدراسي الأول',
            'name_en' => 'First Semester',
            'is_active' => true,
        ]);

        $period = EvaluationPeriod::create([
            'academic_year_id' => $year->id,
            'semester_id' => $semester->id,
            'name_ar' => 'فترة تقييم الأداء لمسار بولونيا - الفصل الأول 2025-2026',
            'name_en' => 'Bologna Performance Evaluation Period - First Semester 2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-08-31',
            'is_active' => true,
        ]);

        // 4. Create Administrative Users
        // Director of Quality Assurance and University Performance
        User::create([
            'name' => 'مدير قسم ضمان الجودة والأداء الجامعي',
            'username' => 'qa.director',
            'email' => 'qa.director@uoanbar.edu.iq',
            'password' => Hash::make('Password123!'),
            'role' => 'university_qa_director',
            'is_active' => true,
        ]);

        // College QA Officer
        User::create([
            'name' => 'مسؤول وحدة ضمان الجودة - كلية علوم الحاسوب',
            'username' => 'qa.cs',
            'email' => 'qa.cs@uoanbar.edu.iq',
            'password' => Hash::make('Password123!'),
            'role' => 'college_qa',
            'college_id' => $csCollege->id,
            'is_active' => true,
        ]);

        // Head of Scientific Department (CS)
        $headUser = User::create([
            'name' => 'رئيس قسم علوم الحاسوب',
            'username' => 'head.cs',
            'email' => 'head.cs@uoanbar.edu.iq',
            'password' => Hash::make('Password123!'),
            'role' => 'department_head',
            'college_id' => $csCollege->id,
            'department_id' => $deptCS->id,
            'is_active' => true,
        ]);

        // Head of Scientific Department (IT)
        User::create([
            'name' => 'رئيس قسم تكنولوجيا المعلومات',
            'username' => 'head.it',
            'email' => 'head.it@uoanbar.edu.iq',
            'password' => Hash::make('Password123!'),
            'role' => 'department_head',
            'college_id' => $csCollege->id,
            'department_id' => $deptIT->id,
            'is_active' => true,
        ]);

        // 5. Teaching Staff
        $staffUser = User::create([
            'name' => 'د. أحمد محمد علي',
            'username' => 'ahmed.m',
            'email' => 'ahmed.m@uoanbar.edu.iq',
            'password' => Hash::make('Password123!'),
            'role' => 'department_head', // Can log in or be viewed
            'college_id' => $csCollege->id,
            'department_id' => $deptCS->id,
            'is_active' => true,
        ]);

        $staff = TeachingStaff::create([
            'user_id' => $staffUser->id,
            'college_id' => $csCollege->id,
            'department_id' => $deptCS->id,
            'first_name' => 'أحمد',
            'father_name' => 'محمد',
            'grandfather_name' => 'علي',
            'great_grandfather_name' => 'حسن',
            'surname' => 'الدليمي',
            'mother_name' => 'فاطمة',
            'mother_father_name' => 'عبدالله',
            'mother_grandfather_name' => 'محمود',
            'national_id' => '198512345678',
            'record_number' => '102',
            'page_number' => '45',
            'issue_date' => '2018-05-12',
            'degree' => 'دكتوراه',
            'ministerial_order_no' => 'وزاري/1425',
            'degree_date' => '2015-06-20',
            'degree_country' => 'العراق',
            'degree_university' => 'جامعة الانبار',
            'degree_college' => 'كلية العلوم',
            'degree_department' => 'علوم الحاسوب',
            'general_specialization' => 'علوم الحاسوب',
            'specific_specialization' => 'أنظمة قواعد البيانات والذكاء الاصطناعي',
            'academic_rank' => 'lecturer', // مدرس
            'rank_awarding_body' => 'جامعة الأنبار',
            'rank_date' => '2019-09-15',
            'mobile' => '07811234567',
            'email' => 'ahmed.m@uoanbar.edu.iq',
            'is_active' => true,
        ]);

        // 6. Courses & Teaching Assignments
        $course1 = Course::create([
            'department_id' => $deptCS->id,
            'code' => 'CS101',
            'name_ar' => 'أنظمة قواعد البيانات',
            'name_en' => 'Database Systems',
            'stage' => 2,
            'semester_id' => $semester->id,
            'is_active' => true,
        ]);

        $assignment = TeachingAssignment::create([
            'teaching_staff_id' => $staff->id,
            'course_id' => $course1->id,
            'department_id' => $deptCS->id,
            'academic_year_id' => $year->id,
            'semester_id' => $semester->id,
            'stage' => 2,
            'class_group' => 'A',
            'min_evaluators' => 10,
            'is_active' => true,
        ]);

        // 7. Seed 12 Student Accounts & Assignments (>=10 minimum enforcement)
        for ($i = 1; $i <= 12; $i++) {
            $username = 'stu_cs_' . sprintf('%02d', $i);
            $stuUser = User::create([
                'name' => "الطالب/ة طالب {$i}",
                'username' => $username,
                'email' => "{$username}@student.uoanbar.edu.iq",
                'password' => Hash::make('Password123!'),
                'role' => 'student',
                'college_id' => $csCollege->id,
                'department_id' => $deptCS->id,
                'is_active' => true,
            ]);

            $student = Student::create([
                'user_id' => $stuUser->id,
                'department_id' => $deptCS->id,
                'student_id_number' => "2025-CS-" . sprintf('%03d', $i),
                'stage' => 2,
                'class_group' => 'A',
                'academic_year_id' => $year->id,
                'semester_id' => $semester->id,
            ]);

            StudentEvaluationAssignment::create([
                'student_id' => $student->id,
                'teaching_assignment_id' => $assignment->id,
                'is_completed' => false,
            ]);
        }

        // 8. Create baseline Evaluation Record for Dr. Ahmed
        Evaluation::create([
            'teaching_staff_id' => $staff->id,
            'academic_year_id' => $year->id,
            'semester_id' => $semester->id,
            'department_id' => $deptCS->id,
            'college_id' => $csCollege->id,
            'status' => 'DRAFT',
            'score_axis_1' => 0.0,
            'score_axis_2' => 0.0,
            'score_axis_3' => 15.0,
            'score_axis_4' => 0.0,
            'total_score' => 15.0,
            'final_classification' => 'Weak',
        ]);
    }
}
