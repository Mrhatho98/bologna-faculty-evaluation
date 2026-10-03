<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('landing');
        }

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('landing')->with('error', 'الحساب غير مفعّل.');
        }

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        abort(403, 'غير مصرح لك بدخول هذه البوابة الإدارية.');
    }
}
