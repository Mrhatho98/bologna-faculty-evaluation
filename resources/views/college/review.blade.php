@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="bg-white border border-gray-200 rounded-lg p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">مراجعة ضمان الجودة في الكلية</p>
            <h1 class="text-2xl font-bold text-anbar-900">{{ $evaluation->teachingStaff->full_name }}</h1>
            <p class="text-sm text-gray-600">{{ $evaluation->college->name_ar }} - {{ $evaluation->department->name_ar }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('evaluations.print', $evaluation) }}" target="_blank" class="px-4 py-2 rounded bg-gray-800 text-white text-sm font-bold">عرض استمارة 39</a>
            <a href="{{ route('evaluations.excel', $evaluation) }}" class="px-4 py-2 rounded bg-green-700 text-white text-sm font-bold">تصدير Excel</a>
        </div>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @foreach(['score_axis_1' => 'المحور 1', 'score_axis_2' => 'المحور 2', 'score_axis_3' => 'المحور 3', 'score_axis_4' => 'المحور 4', 'total_score' => 'المجموع'] as $field => $label)
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="text-2xl font-extrabold text-anbar-900">{{ number_format($evaluation->$field, 2) }}</p>
            </div>
        @endforeach
    </section>

    <section class="bg-white border border-gray-200 rounded-lg p-6">
        <h2 class="font-bold text-lg mb-4">الأدلة</h2>
        <div class="space-y-3">
            @forelse($evaluation->evidenceRecords as $record)
                <div class="border rounded p-4">
                    <div class="flex justify-between gap-4">
                        <div>
                            <p class="font-bold">{{ $record->title }}</p>
                            @if(!empty($record->metadata_json['option_label']))
                                <p class="text-xs text-anbar-700 font-bold mt-1">{{ $record->metadata_json['option_label'] }}</p>
                            @endif
                            @if(!empty($record->metadata_json['is_blacklisted_journal']) || (array_key_exists('has_first_university_affiliation', $record->metadata_json ?? []) && empty($record->metadata_json['has_first_university_affiliation']) && empty($record->metadata_json['has_phd_student_exception'])))
                                <p class="text-xs text-red-700 font-bold mt-1">مستبعد من الاحتساب حسب الضوابط.</p>
                            @endif
                            <p class="text-sm text-gray-600">{{ $record->description }}</p>
                        </div>
                        <span class="font-bold">{{ number_format($record->score_awarded, 2) }}</span>
                    </div>
                    <a class="text-anbar-700 underline text-sm" href="{{ $record->evidence_url }}" target="_blank">فتح رابط الدليل</a>
                </div>
            @empty
                <p class="text-gray-500">لا توجد أدلة.</p>
            @endforelse
        </div>
    </section>

    <section class="bg-white border border-gray-200 rounded-lg p-6">
        <h2 class="font-bold text-lg mb-4">سجل سير العمل</h2>
        <div class="space-y-3">
            @forelse($evaluation->workflowHistories as $event)
                <div class="border rounded p-3 text-sm">
                    <span class="font-bold">{{ $event->from_status }}</span>
                    <span>←</span>
                    <span class="font-bold">{{ $event->to_status }}</span>
                    <span class="text-gray-500">بواسطة {{ $event->user->name ?? 'النظام' }} - {{ $event->created_at->format('Y-m-d H:i') }}</span>
                    @if($event->comments)
                        <p class="text-gray-700 mt-1">{{ $event->comments }}</p>
                    @endif
                </div>
            @empty
                <p class="text-gray-500">لا يوجد سجل بعد.</p>
            @endforelse
        </div>
    </section>

    @if(in_array($evaluation->status, ['SUBMITTED_TO_COLLEGE_QA', 'UNDER_COLLEGE_QA_REVIEW']))
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <form method="POST" action="{{ route('college.evaluations.forward', $evaluation) }}" class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
                @csrf
                <h2 class="font-bold text-lg">تحويل إلى ضمان الجودة الجامعي</h2>
                <textarea name="notes" class="w-full border rounded p-3" rows="3" placeholder="ملاحظات داخلية اختيارية"></textarea>
                <button class="px-4 py-2 rounded bg-anbar-900 text-white font-bold">تدقيق وتحويل</button>
            </form>

            <form method="POST" action="{{ route('college.evaluations.return', $evaluation) }}" class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
                @csrf
                <h2 class="font-bold text-lg">إرجاع إلى القسم للتصحيح</h2>
                <textarea name="return_comments" class="w-full border rounded p-3" rows="3" required placeholder="اكتب سبب الإرجاع والملاحظات المطلوب تصحيحها"></textarea>
                <button class="px-4 py-2 rounded bg-red-700 text-white font-bold">إرجاع الاستمارة</button>
            </form>
        </section>
    @endif
</div>
@endsection
