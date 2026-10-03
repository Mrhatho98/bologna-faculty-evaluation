<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'جامعة الأنبار - تقييم أداء الهيئة التدريسية لمسار بولونيا') }}</title>

    <!-- Google Fonts: Cairo & Tajawal -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN & Alpine.js CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        anbar: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        gold: {
                            500: '#d97706',
                            600: '#b45309',
                        }
                    },
                    fontFamily: {
                        sans: ['Cairo', 'Tajawal', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Cairo', 'Tajawal', sans-serif; }
        [x-cloak] { display: none !important; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col antialiased selection:bg-anbar-700 selection:text-white">

    <!-- Institutional Header Navigation -->
    <header class="bg-anbar-900 text-white border-b-4 border-gold-500 shadow-md no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Branding -->
                <div class="flex items-center space-x-3 space-x-reverse">
                    <a href="{{ route('landing') }}" class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-anbar-900 font-bold text-xl shadow-inner border-2 border-gold-500">
                            UoA
                        </div>
                        <div>
                            <h1 class="font-bold text-lg leading-tight">جامعة الأنبار — University of Anbar</h1>
                            <p class="text-xs text-gold-500 font-medium">نظام تقويم أداء أعضاء الهيئة التدريسية لمسار بولونيا</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links / User Controls -->
                <div class="flex items-center space-x-4 space-x-reverse">
                    <!-- Language Switcher -->
                    <div class="flex items-center bg-anbar-800 rounded-lg p-1 text-xs border border-anbar-700">
                        <a href="{{ route('lang.switch', 'ar') }}" class="px-2 py-1 rounded {{ app()->getLocale() == 'ar' ? 'bg-gold-500 text-white font-bold' : 'text-gray-300 hover:text-white' }}">عربي</a>
                        <a href="{{ route('lang.switch', 'en') }}" class="px-2 py-1 rounded {{ app()->getLocale() == 'en' ? 'bg-gold-500 text-white font-bold' : 'text-gray-300 hover:text-white' }}">EN</a>
                    </div>

                    @auth
                        @php
                            $unreadNotificationCount = auth()->user()->unreadNotifications()->count();
                            $recentNotifications = auth()->user()->notifications()->latest()->take(5)->get();
                        @endphp
                        <div x-data="{ open: false }" class="relative">
                            <button type="button" @click="open = !open" class="relative bg-anbar-800 hover:bg-anbar-700 text-white text-xs px-3 py-2 rounded-md font-semibold transition flex items-center gap-2 border border-anbar-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0m6 0H9"/></svg>
                                <span>الإشعارات</span>
                                @if($unreadNotificationCount > 0)
                                    <span class="absolute -top-2 -left-2 bg-red-600 text-white text-[10px] rounded-full min-w-5 h-5 flex items-center justify-center px-1">{{ $unreadNotificationCount }}</span>
                                @endif
                            </button>
                            <div x-show="open" @click.outside="open = false" x-cloak class="absolute left-0 mt-2 w-80 bg-white text-gray-900 rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden">
                                <div class="p-3 border-b flex items-center justify-between">
                                    <span class="font-bold text-sm">آخر الإشعارات</span>
                                    <a href="{{ route('notifications.index') }}" class="text-xs text-anbar-700 underline">عرض الكل</a>
                                </div>
                                <div class="max-h-80 overflow-y-auto divide-y">
                                    @forelse($recentNotifications as $notification)
                                        @php($notificationData = $notification->data)
                                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                            @csrf
                                            <button class="w-full text-right p-3 hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-amber-50' }}">
                                                <div class="font-bold text-xs text-gray-900">{{ $notificationData['title'] ?? 'إشعار' }}</div>
                                                <div class="text-xs text-gray-600 mt-1 line-clamp-2">{{ $notificationData['body'] ?? '' }}</div>
                                                <div class="text-[11px] text-gray-400 mt-1">{{ $notification->created_at->format('Y-m-d H:i') }}</div>
                                            </button>
                                        </form>
                                    @empty
                                        <div class="p-4 text-center text-sm text-gray-500">لا توجد إشعارات.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- User Identity Badge -->
                        <div class="hidden sm:flex flex-col text-left rtl:text-right px-3 py-1 bg-anbar-800 rounded-md text-xs border border-anbar-700">
                            <span class="font-semibold text-white">{{ auth()->user()->name }}</span>
                            <span class="text-gold-500 text-[10px]">
                                @if(auth()->user()->isStudent()) طالب مكيّف @endif
                                @if(auth()->user()->isDepartmentHead()) رئيس قسم علمي @endif
                                @if(auth()->user()->isCollegeQA()) مسؤول جودة الكلية @endif
                                @if(auth()->user()->isUniversityQADirector()) مدير قسم ضمان الجودة والأداء الجامعي @endif
                            </span>
                        </div>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-700 hover:bg-red-800 text-white text-xs px-3 py-2 rounded-md font-semibold transition flex items-center gap-1 shadow">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <span>خروج</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('landing') }}" class="text-xs bg-gold-500 hover:bg-gold-600 text-white px-3 py-2 rounded-md font-bold transition">دخول البوابات</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Global Toast Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 no-print">
        @if(session('success'))
            <div class="bg-green-100 border-r-4 border-green-600 text-green-800 p-4 rounded shadow-sm mb-4 flex items-center justify-between" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="font-medium text-sm">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-r-4 border-red-600 text-red-800 p-4 rounded shadow-sm mb-4 flex items-center justify-between" role="alert">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span class="font-medium text-sm">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="bg-red-50 border-r-4 border-red-500 text-red-700 p-4 rounded shadow-sm mb-4">
                <p class="font-bold text-sm mb-1">يرجى تصحيح الأخطاء التالية:</p>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Body Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Institutional Footer -->
    <footer class="bg-gray-900 text-gray-400 border-t border-gray-800 py-6 mt-12 no-print text-center text-xs">
        <div class="max-w-7xl mx-auto px-4">
            <p class="text-gray-300 font-semibold mb-1">جميع الحقوق محفوظة لجامعة الأنبار — جمهورية العراق © {{ date('Y') }}</p>
            <p class="text-gray-500">استمارة رقم (39) تقييم أداء أعضاء الهيئة التدريسية لمسار بولونيا للعام الدراسي 2025–2026</p>
            <p class="text-gold-500 mt-2 font-mono dir-ltr">Designed by Prog.Hudhaifa :)</p>
        </div>
    </footer>

</body>
</html>
