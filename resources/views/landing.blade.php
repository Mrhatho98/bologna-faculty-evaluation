@extends('layouts.app')

@section('content')
<div class="py-8">
    <!-- Hero Header -->
    <div class="text-center max-w-3xl mx-auto mb-12">
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-gold-500/10 text-gold-600 rounded-full text-xs font-bold mb-4 border border-gold-500/20">
            <span>استمارة رقم (39) الرسمية</span>
            <span>•</span>
            <span>العام الدراسي 2025–2026</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-anbar-900 tracking-tight mb-3">
            منظومة تقويم أداء أعضاء الهيئة التدريسية لمسار بولونيا
        </h1>
        <p class="text-gray-600 text-base leading-relaxed">
            منصة الكترونية مؤسسية متكاملة لجامعة الأنبار تضمن إدارة عمليات التقييم، فيدباك الطلبة، الحقيبة التدريسية، التحقق من الأدلة، والاعتماد النهائي وفق الضوابط والتعليمات الوزارية.
        </p>
    </div>

    <!-- 4 Main Access Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Card 1: Student -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group">
            <div class="p-6">
                <div class="w-14 h-14 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
                <div class="text-xs font-bold text-sky-600 uppercase tracking-wider mb-1">البوابة الأولى</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">الطالب</h3>
                <p class="text-gray-500 text-xs leading-relaxed mb-4">
                    تقديم الفيدباك والتقييم للمادة الدراسية وطريقة التدريس (المحور الأول) للمواد المكلف بها فقط وبسرية تامة.
                </p>
            </div>
            <div class="p-6 bg-gray-50 border-t border-gray-100">
                <a href="{{ route('login.portal', 'student') }}" class="w-full inline-flex justify-center items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm py-2.5 px-4 rounded-xl transition shadow">
                    <span>تسجيل دخول الطالب</span>
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        <!-- Card 2: Head of Scientific Department -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group">
            <div class="p-6">
                <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-1">البوابة الثانية</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">رئيس القسم العلمي</h3>
                <p class="text-gray-500 text-xs leading-relaxed mb-4">
                    إدارة التدريسيين والأنصبة، توليد حسابات الطلبة، استكمال حقيبة الأستاذ، إدخال روابط الأدلة والوصف، ورفع التقييم.
                </p>
            </div>
            <div class="p-6 bg-gray-50 border-t border-gray-100">
                <a href="{{ route('login.portal', 'department_head') }}" class="w-full inline-flex justify-center items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-2.5 px-4 rounded-xl transition shadow">
                    <span>دخول رئيس القسم</span>
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        <!-- Card 3: College QA Unit Officer -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group">
            <div class="p-6">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-1">البوابة الثالثة</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">مسؤول جودة الكلية</h3>
                <p class="text-gray-500 text-xs leading-relaxed mb-4">
                    مراجعة استمارات التقييم المرفوعة من الأقسام، فتح روابط الأدلة وقراءة الأوصاف، التحقق من المعلومات وتحويلها للجامعة.
                </p>
            </div>
            <div class="p-6 bg-gray-50 border-t border-gray-100">
                <a href="{{ route('login.portal', 'college_qa') }}" class="w-full inline-flex justify-center items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm py-2.5 px-4 rounded-xl transition shadow">
                    <span>دخول مسؤول الجودة بالكلية</span>
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

        <!-- Card 4: Director of QA and University Performance -->
        <div class="bg-white rounded-2xl shadow-sm border border-gold-500/30 hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group">
            <div class="p-6">
                <div class="w-14 h-14 bg-amber-50 text-gold-600 rounded-xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <div class="text-xs font-bold text-gold-600 uppercase tracking-wider mb-1">البوابة العليا</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">مدير قسم ضمان الجودة والأداء الجامعي</h3>
                <p class="text-gray-500 text-xs leading-relaxed mb-4">
                    إدارة النظام الشامل، الكليات، الأقسام، السنوات الدراسية، مصادقة التقييمات النهائية، الإرجاع للتصحيح، وسجل التدقيق.
                </p>
            </div>
            <div class="p-6 bg-gray-50 border-t border-gray-100">
                <a href="{{ route('login.portal', 'university_qa_director') }}" class="w-full inline-flex justify-center items-center gap-2 bg-anbar-900 hover:bg-anbar-800 text-white font-bold text-sm py-2.5 px-4 rounded-xl transition shadow">
                    <span>دخول المدير العام للجودة</span>
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>

    </div>

    <!-- Institutional Notice Banner -->
    <div class="mt-12 bg-anbar-900 text-white rounded-2xl p-6 sm:p-8 shadow-lg relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h2 class="text-xl font-bold text-gold-500 mb-2">تعليمات استمارة رقم (39) لمسار بولونيا — 2025-2026</h2>
                <p class="text-sm text-gray-200 leading-relaxed max-w-3xl">
                    تتوزع درجات التقييم البالغة (100) درجة على أربعة محاور: فيدباك الطالب (10%)، حقيبة الأستاذ (60%)، تقييم رئيس القسم والخصومات الإدارية (15%)، والجانب التربوي والإرشادي (15%). يتم تطبيق معامل اللقب العلمي على نتاجات البحث العلمي، ولا يقل عدد الطلبة المقيّمين عن 10 طلبة لكل مادة دراسية.
                </p>
            </div>
            <div class="shrink-0">
                <span class="inline-block bg-gold-500 text-anbar-900 font-extrabold px-5 py-3 rounded-xl text-sm shadow">
                    الاعتماد المؤسسي الرسمي
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
