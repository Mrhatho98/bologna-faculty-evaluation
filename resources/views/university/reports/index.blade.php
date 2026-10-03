@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500">تقارير استمارة رقم (39)</p>
                <h1 class="text-2xl font-bold text-anbar-900">مؤشرات الأداء الجامعية</h1>
                <p class="text-sm text-gray-600 mt-1">ملخص مركزي لحالات الاستمارات، متوسطات الدرجات، وأداء الكليات.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('university.reports.excel', $filters) }}" class="px-4 py-2 rounded bg-green-700 text-white text-sm font-bold">تصدير Excel</a>
                <a href="{{ route('university.dashboard') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-800 text-sm font-bold">العودة للوحة الجامعة</a>
            </div>
        </div>
    </section>

    <section class="bg-white border border-gray-200 rounded-lg p-6">
        <form method="GET" action="{{ route('university.reports.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end text-sm">
            <label class="block">السنة الدراسية
                <select name="academic_year_id" class="mt-1 w-full border rounded p-2">
                    <option value="">كل السنوات</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" @selected(($filters['academic_year_id'] ?? '') == $year->id)>{{ $year->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">الفصل
                <select name="semester_id" class="mt-1 w-full border rounded p-2">
                    <option value="">كل الفصول</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" @selected(($filters['semester_id'] ?? '') == $semester->id)>{{ $semester->name_ar }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">الكلية
                <select name="college_id" class="mt-1 w-full border rounded p-2">
                    <option value="">كل الكليات</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}" @selected(($filters['college_id'] ?? '') == $college->id)>{{ $college->name_ar }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">القسم
                <select name="department_id" class="mt-1 w-full border rounded p-2">
                    <option value="">كل الأقسام</option>
                    @foreach($colleges as $college)
                        @foreach($college->departments as $department)
                            <option value="{{ $department->id }}" @selected(($filters['department_id'] ?? '') == $department->id)>{{ $department->name_ar }}</option>
                        @endforeach
                    @endforeach
                </select>
            </label>
            <div class="flex gap-2">
                <button class="px-4 py-2 rounded bg-anbar-900 text-white font-bold">تطبيق</button>
                <a href="{{ route('university.reports.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-800 font-bold">مسح</a>
            </div>
        </form>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @foreach([
            'axis_1' => 'تقييم الطلبة',
            'axis_2' => 'النشاطات العلمية',
            'axis_3' => 'رئيس القسم',
            'axis_4' => 'الجودة والخدمة',
            'total_score' => 'المعدل الكلي',
        ] as $key => $label)
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="text-2xl font-extrabold text-anbar-900">{{ number_format((float)($scoreAverages->{$key} ?? 0), 2) }}</p>
            </div>
        @endforeach
    </section>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <p class="text-sm text-gray-500">الطلبة المكلفون بالتقييم</p>
            <p class="text-2xl font-extrabold text-anbar-900">{{ (int)($studentParticipation->assigned_count ?? 0) }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <p class="text-sm text-gray-500">التقييمات المنجزة</p>
            <p class="text-2xl font-extrabold text-anbar-900">{{ (int)($studentParticipation->completed_count ?? 0) }}</p>
        </div>
        <div class="bg-anbar-900 text-white rounded-lg p-4">
            <p class="text-sm text-white/80">نسبة مشاركة الطلبة</p>
            <p class="text-2xl font-extrabold">{{ number_format((float)($studentParticipation->completion_percent ?? 0), 2) }}%</p>
        </div>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="p-4 border-b font-bold">توزيع الحالات</div>
            <table class="w-full text-sm text-right">
                <thead class="bg-gray-50 text-gray-600">
                    <tr><th class="p-3">الحالة</th><th class="p-3">العدد</th></tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($statusCounts as $row)
                        <tr>
                            <td class="p-3 font-bold">{{ $row['label'] }}</td>
                            <td class="p-3">{{ $row['total'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="p-6 text-center text-gray-500">لا توجد استمارات بعد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="p-4 border-b font-bold">تصنيف النتائج</div>
            <table class="w-full text-sm text-right">
                <thead class="bg-gray-50 text-gray-600">
                    <tr><th class="p-3">التصنيف</th><th class="p-3">العدد</th></tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($classificationCounts as $row)
                        <tr>
                            <td class="p-3 font-bold">{{ $row->final_classification ?: 'غير محدد' }}</td>
                            <td class="p-3">{{ $row->total }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="p-6 text-center text-gray-500">لا توجد نتائج مصنفة بعد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="p-4 border-b font-bold">أداء الكليات</div>
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="p-3">الكلية</th>
                    <th class="p-3">عدد الاستمارات</th>
                    <th class="p-3">المعتمدة</th>
                    <th class="p-3">متوسط الدرجة</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($collegePerformance as $college)
                    <tr>
                        <td class="p-3 font-bold">{{ $college->name_ar }}</td>
                        <td class="p-3">{{ $college->evaluations_count }}</td>
                        <td class="p-3">{{ $college->accepted_count }}</td>
                        <td class="p-3">{{ number_format((float)$college->average_score, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-6 text-center text-gray-500">لا توجد بيانات كليات للتقرير.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="p-4 border-b font-bold">أداء الأقسام</div>
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="p-3">القسم</th>
                    <th class="p-3">الكلية</th>
                    <th class="p-3">عدد الاستمارات</th>
                    <th class="p-3">المعتمدة</th>
                    <th class="p-3">متوسط الدرجة</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($departmentPerformance as $department)
                    <tr>
                        <td class="p-3 font-bold">{{ $department->department_name }}</td>
                        <td class="p-3">{{ $department->college_name }}</td>
                        <td class="p-3">{{ $department->evaluations_count }}</td>
                        <td class="p-3">{{ $department->accepted_count }}</td>
                        <td class="p-3">{{ number_format((float)$department->average_score, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-gray-500">لا توجد بيانات أقسام للتقرير.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="p-4 border-b font-bold">آخر الاستمارات المعتمدة</div>
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="p-3">التدريسي</th>
                    <th class="p-3">القسم</th>
                    <th class="p-3">الكلية</th>
                    <th class="p-3">المجموع</th>
                    <th class="p-3">الإجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($recentAccepted as $evaluation)
                    <tr>
                        <td class="p-3 font-bold">{{ $evaluation->teachingStaff->full_name }}</td>
                        <td class="p-3">{{ $evaluation->teachingStaff->department->name_ar ?? '-' }}</td>
                        <td class="p-3">{{ $evaluation->college->name_ar ?? '-' }}</td>
                        <td class="p-3">{{ number_format($evaluation->total_score, 2) }}</td>
                        <td class="p-3"><a class="text-anbar-700 underline" href="{{ route('university.evaluations.review', $evaluation) }}">فتح</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-gray-500">لا توجد استمارات معتمدة بعد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>
@endsection
