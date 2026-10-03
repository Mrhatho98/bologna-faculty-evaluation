<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display the main landing page with 4 role access cards.
     */
    public function index()
    {
        return view('landing');
    }

    /**
     * Display login page for specific role.
     */
    public function showLoginForm(string $portal)
    {
        $portals = [
            'student' => [
                'title_ar' => 'بوابة الطالب التقويمية',
                'title_en' => 'Student Evaluation Portal',
                'role' => 'student',
                'icon' => 'user-academic',
                'badge' => 'الطلبة المكلفين بالتقييم',
            ],
            'department_head' => [
                'title_ar' => 'بوابة رئيس القسم العلمي',
                'title_en' => 'Head of Scientific Department Portal',
                'role' => 'department_head',
                'icon' => 'building-library',
                'badge' => 'إدارة القسم والحقائب والتقييم',
            ],
            'college_qa' => [
                'title_ar' => 'بوابة مسؤول وحدة ضمان الجودة بالكلية',
                'title_en' => 'College QA Officer Portal',
                'role' => 'college_qa',
                'icon' => 'shield-check',
                'badge' => 'مراجعة وتدقيق التقييمات',
            ],
            'university_qa_director' => [
                'title_ar' => 'بوابة مدير قسم ضمان الجودة والأداء الجامعي',
                'title_en' => 'Director of QA and University Performance Portal',
                'role' => 'university_qa_director',
                'icon' => 'academic-cap',
                'badge' => 'الإشراف الشامل والاعتماد النهائي',
            ],
        ];

        if (!array_key_exists($portal, $portals)) {
            return redirect()->route('landing');
        }

        return view('auth.login', [
            'portal' => $portal,
            'portalInfo' => $portals[$portal],
        ]);
    }

    /**
     * Process login authentication.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'portal' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        // Attempt authentication using username or email
        $loginField = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$loginField => $credentials['username'], 'password' => $credentials['password'], 'is_active' => true], $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            AuditLog::logAction('user_login', 'Authentication', $user->id, null, [
                'username' => $user->username,
                'role' => $user->role,
            ]);

            return $this->redirectUserByRole($user);
        }

        return back()->withErrors([
            'username' => __('اسم المستخدم أو كلمة المرور غير صحيحة، أو الحساب غير مفعل.'),
        ])->onlyInput('username');
    }

    /**
     * Redirect authenticated user to their role dashboard.
     */
    protected function redirectUserByRole($user)
    {
        return match ($user->role) {
            'student' => redirect()->route('student.dashboard'),
            'department_head' => redirect()->route('dept.dashboard'),
            'college_qa' => redirect()->route('college.dashboard'),
            'university_qa_director' => redirect()->route('university.dashboard'),
            default => redirect()->route('landing'),
        };
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::logAction('user_logout', 'Authentication', Auth::id());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }

    /**
     * Switch language between Arabic (RTL) and English (LTR).
     */
    public function switchLanguage(string $locale)
    {
        if (in_array($locale, ['ar', 'en'])) {
            session(['locale' => $locale]);
            app()->setLocale($locale);
        }

        return back();
    }
}
