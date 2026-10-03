@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="bg-white border border-gray-200 rounded-lg p-6">
        <h1 class="text-2xl font-bold text-anbar-900">استمارات التقييم الجامعية</h1>
    </section>

    <form method="GET" class="bg-white border border-gray-200 rounded-lg p-4 grid grid-cols-1 md:grid-cols-4 gap-4">
        <select name="college_id" class="border rounded p-2">
            <option value="">كل الكليات</option>
            @foreach($colleges as $college)
                <option value="{{ $college->id }}" @selected(($filters['college_id'] ?? '') == $college->id)>{{ $college->name_ar }}</option>
            @endforeach
        </select>
        <select name="department_id" class="border rounded p-2">
            <option value="">كل الأقسام</option>
            @foreach($colleges as $college)
                @foreach($college->departments as $department)
                    <option value="{{ $department->id }}" @selected(($filters['department_id'] ?? '') == $department->id)>{{ $college->code }} - {{ $department->name_ar }}</option>
                @endforeach
            @endforeach
        </select>
        <select name="status" class="border rounded p-2">
            <option value="">كل الحالات</option>
            @foreach(['DRAFT', 'SUBMITTED_TO_COLLEGE_QA', 'SUBMITTED_TO_UNIVERSITY_QA', 'ACCEPTED', 'REJECTED'] as $status)
                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ $status }}</option>
            @endforeach
        </select>
        <button class="bg-anbar-900 text-white rounded p-2 font-bold">تصفية</button>
    </form>

    <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600"><tr><th class="p-3">التدريسي</th><th class="p-3">الكلية/القسم</th><th class="p-3">السنة</th><th class="p-3">المجموع</th><th class="p-3">الحالة</th><th class="p-3">الإجراء</th></tr></thead>
            <tbody class="divide-y">
                @forelse($evaluations as $evaluation)
                    <tr>
                        <td class="p-3 font-bold">{{ $evaluation->teachingStaff->full_name }}</td>
                        <td class="p-3">{{ $evaluation->college->name_ar }} / {{ $evaluation->teachingStaff->department->name_ar }}</td>
                        <td class="p-3">{{ $evaluation->academicYear->name }}</td>
                        <td class="p-3">{{ number_format($evaluation->total_score, 2) }}</td>
                        <td class="p-3">{{ $evaluation->status_label }}</td>
                        <td class="p-3"><a class="text-anbar-700 underline font-bold" href="{{ route('university.evaluations.review', $evaluation) }}">مراجعة</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-gray-500">لا توجد نتائج.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $evaluations->links() }}</div>
    </section>
</div>
@endsection
