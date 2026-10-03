@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="bg-white border border-gray-200 rounded-lg p-6">
        <h1 class="text-2xl font-bold text-anbar-900">لوحة مدير قسم ضمان الجودة والأداء الجامعي</h1>
        <p class="text-sm text-gray-600 mt-1">إدارة الهيكل الجامعي، المستخدمين، القواعد التشغيلية، ومتابعة سير استمارات التقييم.</p>
    </section>

    <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([['الكليات', $collegeCount], ['الأقسام', $departmentCount], ['التدريسيون', $staffCount], ['الطلبة', $studentCount]] as [$label, $value])
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="text-3xl font-extrabold text-anbar-900">{{ $value }}</p>
            </div>
        @endforeach
    </section>

    <section class="grid grid-cols-2 lg:grid-cols-6 gap-4">
        @foreach(['draft' => 'مسودات', 'college_qa_pending' => 'لدى الكلية', 'university_qa_pending' => 'لدى الجامعة', 'accepted' => 'مقبولة', 'rejected' => 'معادة', 'total' => 'الإجمالي'] as $key => $label)
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="text-2xl font-extrabold text-anbar-900">{{ $stats[$key] }}</p>
            </div>
        @endforeach
    </section>

    <nav class="bg-anbar-900 rounded-lg p-4 flex flex-wrap gap-3 text-sm font-bold">
        <a class="text-white hover:text-gold-500" href="{{ route('university.evaluations.index') }}">الاستمارات</a>
        <a class="text-white hover:text-gold-500" href="{{ route('university.reports.index') }}">التقارير</a>
        <a class="text-white hover:text-gold-500" href="{{ route('university.colleges.index') }}">الكليات</a>
        <a class="text-white hover:text-gold-500" href="{{ route('university.departments.index') }}">الأقسام</a>
        <a class="text-white hover:text-gold-500" href="{{ route('university.academic.index') }}">السنة والفترات</a>
        <a class="text-white hover:text-gold-500" href="{{ route('university.users.index') }}">المستخدمون</a>
        <a class="text-white hover:text-gold-500" href="{{ route('university.settings.index') }}">الإعدادات</a>
        <a class="text-white hover:text-gold-500" href="{{ route('university.settings.rules') }}">قواعد الاحتساب</a>
        <a class="text-white hover:text-gold-500" href="{{ route('university.system.health') }}">جاهزية النظام</a>
        <a class="text-white hover:text-gold-500" href="{{ route('university.audit.index') }}">سجل التدقيق</a>
    </nav>

    <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="p-4 border-b font-bold">آخر الاستمارات</div>
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600"><tr><th class="p-3">التدريسي</th><th class="p-3">الكلية</th><th class="p-3">الحالة</th><th class="p-3">المجموع</th><th class="p-3">الإجراء</th></tr></thead>
            <tbody class="divide-y">
                @forelse($recentEvaluations as $evaluation)
                    <tr>
                        <td class="p-3 font-bold">{{ $evaluation->teachingStaff->full_name }}</td>
                        <td class="p-3">{{ $evaluation->college->name_ar }}</td>
                        <td class="p-3">{{ $evaluation->status_label }}</td>
                        <td class="p-3">{{ number_format($evaluation->total_score, 2) }}</td>
                        <td class="p-3"><a class="text-anbar-700 underline" href="{{ route('university.evaluations.review', $evaluation) }}">مراجعة</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-gray-500">لا توجد استمارات.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>
@endsection
