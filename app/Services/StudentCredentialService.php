<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Str;

class StudentCredentialService
{
    /**
     * Generate secure student user credentials in bulk for a department.
     */
    public function generateStudentCredentials(
        int $departmentId,
        int $academicYearId,
        int $semesterId,
        array $studentsData
    ): array {
        $generatedCredentials = [];

        foreach ($studentsData as $studentInfo) {
            $studentIdNo = trim($studentInfo['student_id_number']);
            $name = trim($studentInfo['name']);
            $stage = (int)($studentInfo['stage'] ?? 1);
            $classGroup = trim($studentInfo['class_group'] ?? 'A');

            // Unique username generation: e.g. stu_cs_2025_001
            $prefix = 'stu_' . strtolower(Str::slug($studentIdNo, '_'));
            $username = $prefix;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $prefix . '_' . $counter;
                $counter++;
            }

            // Secure random 8-character password (letters + numbers)
            $plainPassword = Str::random(8);

            // Create User record
            $user = User::create([
                'name' => $name,
                'username' => $username,
                'email' => $username . '@student.uoanbar.edu.iq',
                'password' => $plainPassword, // Hashed via User model cast
                'role' => 'student',
                'department_id' => $departmentId,
                'is_active' => true,
            ]);

            // Create Student profile record
            $student = Student::create([
                'user_id' => $user->id,
                'department_id' => $departmentId,
                'student_id_number' => $studentIdNo,
                'stage' => $stage,
                'class_group' => $classGroup,
                'academic_year_id' => $academicYearId,
                'semester_id' => $semesterId,
            ]);

            $generatedCredentials[] = [
                'student_id' => $student->id,
                'name' => $name,
                'student_id_number' => $studentIdNo,
                'username' => $username,
                'plain_password' => $plainPassword,
            ];
        }

        AuditLog::logAction('generate_student_credentials', 'Students', null, null, [
            'count' => count($generatedCredentials),
            'department_id' => $departmentId,
        ]);

        return $generatedCredentials;
    }
}
