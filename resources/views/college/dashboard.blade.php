@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="bg-white border border-gray-200 rounded-lg p-6">
        <h1 class="text-2xl font-bold text-anbar-900">لوحة وحدة ضمان الجودة في الكلية</h1>
        <p class="text-sm text-gray-600 mt-1">مراجعة الاستمارات الواردة من الأقسام، فتح الأدلة، ثم تحويلها إلى ضمان الجودة الجامعي.</p>
    </section>

    <form method="GET" class="bg-white border border-gray-200 rounded-lg p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <select name="status" class="border rounded p-2">
            <option value="">كل الحالات</option>
            @foreach(['SUBMITTED_TO_COLLEGE_QA' => 'وارد للكلية', 'UNDER_COLLEGE_QA_REVIEW' => 'قيد مراجعة الكلية', 'SUBMITTED_TO_UNIVERSITY_QA' => 'محول للجامعة', 'REJECTED' => 'معاد للتصحيح', 'ACCEPTED' => 'مقبول'] as $value => $label)
                <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="department_id" class="border rounded p-2">
            <option value="">كل الأقسام</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}" @selected((string)$selectedDept === (string)$department->id)>{{ $department->name_ar }}</option>
            @endforeach
        </select>
        <button class="bg-anbar-900 text-white rounded p-2 font-bold">تصفية</button>
    </form>

    <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="p-3">التدريسي</th>
                    <th class="p-3">القسم</th>
                    <th class="p-3">السنة/الفصل</th>
                    <th class="p-3">النتيجة</th>
                    <th class="p-3">الحالة</th>
                    <th class="p-3">الإجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($evaluations as $evaluation)
                    <tr>
                        <td class="p-3 font-bold">{{ $evaluation->teachingStaff->full_name }}</td>
                        <td class="p-3">{{ $evaluation->teachingStaff->department->name_ar }}</td>
                        <td class="p-3">{{ $evaluation->academicYear->name }} / {{ $evaluation->semester->name_ar }}</td>
                        <td class="p-3">{{ number_format($evaluation->total_score, 2) }}</td>
                        <td class="p-3">{{ $evaluation->status_label }}</td>
                        <td class="p-3"><a class="text-anbar-700 underline font-bold" href="{{ route('college.evaluations.review', $evaluation) }}">مراجعة</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-gray-500">لا توجد استمارات مطابقة.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</div>
@endsection
