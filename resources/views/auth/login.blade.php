@extends('layouts.app')

@section('content')
<div class="py-12 flex justify-center items-center">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
        
        <!-- Header -->
        <div class="bg-anbar-900 text-white p-6 text-center relative border-b-4 border-gold-500">
            <span class="inline-block bg-gold-500/20 text-gold-400 text-xs px-3 py-1 rounded-full font-bold mb-2">
                {{ $portalInfo['badge'] }}
            </span>
            <h2 class="text-xl font-bold">{{ $portalInfo['title_ar'] }}</h2>
            <p class="text-xs text-gray-300 mt-1 dir-ltr">{{ $portalInfo['title_en'] }}</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="portal" value="{{ $portal }}">

            <div>
                <label for="username" class="block text-xs font-bold text-gray-700 mb-1">اسم المستخدم / البريد الإلكتروني</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-anbar-700 focus:border-anbar-700 text-sm transition"
                    placeholder="أدخل اسم المستخدم المعين...">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-gray-700 mb-1">كلمة المرور</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-anbar-700 focus:border-anbar-700 text-sm transition"
                    placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 text-gray-600">
                    <input type="checkbox" name="remember" class="rounded text-anbar-700 focus:ring-anbar-700">
                    <span>تذكر بيانات الدخول</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-anbar-900 hover:bg-anbar-800 text-white font-bold text-sm py-3 px-4 rounded-xl transition shadow-md flex justify-center items-center gap-2">
                <span>تسجيل الدخول إلى البوابة</span>
                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>

        </form>
    </div>
</div>
@endsection
