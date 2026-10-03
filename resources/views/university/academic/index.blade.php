@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="bg-white border border-gray-200 rounded-lg p-6">
        <h1 class="text-2xl font-bold text-anbar-900">إدارة السنة الدراسية والفصول وفترات التقييم</h1>
        <p class="text-sm text-gray-600 mt-1">هذه البيانات تتحكم بفتح وغلق تقييم الطلبة وربط الاستمارات بالسنة والفصل الصحيحين.</p>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <form method="POST" action="{{ route('university.academic-years.store') }}" class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
            @csrf
            <h2 class="font-bold text-lg">إضافة سنة دراسية</h2>
            <label class="block text-sm">
                اسم السنة
                <input name="name" class="mt-1 w-full border rounded p-2" placeholder="2026-2027" required>
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_current" value="1">
                تعيين كسنة حالية
            </label>
            <button class="w-full bg-anbar-900 text-white rounded p-2 font-bold">حفظ السنة</button>
        </form>

        <form method="POST" action="{{ route('university.semesters.store') }}" class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
            @csrf
            <h2 class="font-bold text-lg">إضافة فصل دراسي</h2>
            <label class="block text-sm">
                السنة الدراسية
                <select name="academic_year_id" class="mt-1 w-full border rounded p-2" required>
                    <option value="">اختر السنة</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm">الاسم بالعربية <input name="name_ar" class="mt-1 w-full border rounded p-2" required></label>
            <label class="block text-sm">Name in English <input name="name_en" class="mt-1 w-full border rounded p-2" required></label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" checked>
                فعال
            </label>
            <button class="w-full bg-anbar-900 text-white rounded p-2 font-bold">حفظ الفصل</button>
        </form>

        <form method="POST" action="{{ route('university.evaluation-periods.store') }}" class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
            @csrf
            <h2 class="font-bold text-lg">إضافة فترة تقييم</h2>
            <label class="block text-sm">
                السنة الدراسية
                <select name="academic_year_id" class="mt-1 w-full border rounded p-2" required>
                    <option value="">اختر السنة</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm">
                الفصل
                <select name="semester_id" class="mt-1 w-full border rounded p-2" required>
                    <option value="">اختر الفصل</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}">{{ $semester->academicYear->name }} - {{ $semester->name_ar }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm">الاسم بالعربية <input name="name_ar" class="mt-1 w-full border rounded p-2" required></label>
            <label class="block text-sm">Name in English <input name="name_en" class="mt-1 w-full border rounded p-2" required></label>
            <div class="rounded border border-sky-100 bg-sky-50 p-3 text-sm text-sky-900">
                يتم تحديد فترة التقييم تلقائياً من 1/9 للسنة المختارة إلى 31/8 من السنة التي بعدها.
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1">
                تفعيل الفترة
            </label>
            <button class="w-full bg-anbar-900 text-white rounded p-2 font-bold">حفظ الفترة</button>
        </form>
    </section>

    <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="p-4 border-b font-bold">السنوات والفصول</div>
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="p-3">السنة</th>
                    <th class="p-3">الحالية</th>
                    <th class="p-3">الفصول</th>
                    <th class="p-3">فترات التقييم</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($academicYears as $year)
                    <tr>
                        <td class="p-3 font-bold">{{ $year->name }}</td>
                        <td class="p-3">{{ $year->is_current ? 'نعم' : 'لا' }}</td>
                        <td class="p-3">
                            @forelse($year->semesters as $semester)
                                <span class="inline-flex px-2 py-1 bg-gray-100 rounded text-xs mb-1">{{ $semester->name_ar }}</span>
                            @empty
                                <span class="text-gray-500">لا توجد فصول</span>
                            @endforelse
                        </td>
                        <td class="p-3">
                            @forelse($year->evaluationPeriods as $period)
                                <div class="mb-1">
                                    <span class="font-bold">{{ $period->name_ar }}</span>
                                    <span class="text-xs text-gray-500">({{ $period->start_date->format('Y-m-d') }} إلى {{ $period->end_date->format('Y-m-d') }})</span>
                                    <span class="text-xs {{ $period->isOpen() ? 'text-green-700' : 'text-gray-500' }}">{{ $period->isOpen() ? 'مفتوحة' : 'مغلقة' }}</span>
                                </div>
                            @empty
                                <span class="text-gray-500">لا توجد فترات</span>
                            @endforelse
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-6 text-center text-gray-500">لا توجد سنوات دراسية.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="p-4 border-b font-bold">كل فترات التقييم</div>
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="p-3">الفترة</th>
                    <th class="p-3">السنة</th>
                    <th class="p-3">الفصل</th>
                    <th class="p-3">البداية</th>
                    <th class="p-3">النهاية</th>
                    <th class="p-3">الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($periods as $period)
                    <tr>
                        <td class="p-3 font-bold">{{ $period->name_ar }}</td>
                        <td class="p-3">{{ $period->academicYear->name }}</td>
                        <td class="p-3">{{ $period->semester->name_ar }}</td>
                        <td class="p-3">{{ $period->start_date->format('Y-m-d') }}</td>
                        <td class="p-3">{{ $period->end_date->format('Y-m-d') }}</td>
                        <td class="p-3">{{ $period->is_active ? ($period->isOpen() ? 'مفتوحة' : 'فعالة خارج التاريخ') : 'غير فعالة' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-gray-500">لا توجد فترات تقييم.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>
@endsection
