@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500">استمارة رقم (39) الأساسية</p>
                <h1 class="text-2xl font-bold text-anbar-900">{{ $evaluation->teachingStaff->full_name }}</h1>
                <p class="text-sm text-gray-600">{{ $evaluation->academicYear->name }} - {{ $evaluation->semester->name_ar }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('evaluations.print', $evaluation) }}" target="_blank" class="px-4 py-2 rounded bg-gray-800 text-white text-sm font-bold">طباعة الاستمارة</a>
                <a href="{{ route('evaluations.excel', $evaluation) }}" class="px-4 py-2 rounded bg-green-700 text-white text-sm font-bold">تصدير Excel</a>
                <a href="{{ route('dept.dashboard') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-800 text-sm font-bold">العودة</a>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @foreach(['axis_1' => 'المحور الأول', 'axis_2' => 'المحور الثاني', 'axis_3' => 'المحور الثالث', 'axis_4' => 'المحور الرابع'] as $key => $label)
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <p class="text-sm text-gray-500">{{ $label }}</p>
                <p class="text-2xl font-extrabold text-anbar-900">{{ number_format($scores[$key]['score'] ?? $scores[$key]['total'] ?? 0, 2) }}</p>
                <p class="text-xs text-gray-500">من {{ $scores[$key]['max'] ?? 0 }}</p>
            </div>
        @endforeach
        <div class="bg-anbar-900 text-white rounded-lg p-4">
            <p class="text-sm text-white/80">المجموع</p>
            <p class="text-2xl font-extrabold">{{ number_format($scores['total_score'], 2) }}</p>
            <p class="text-xs text-white/80">من 100</p>
        </div>
    </section>

    <section class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <div>
                <h2 class="font-bold text-lg text-gray-900">اكتمال الاستمارة قبل الرفع</h2>
                <p class="text-sm text-gray-500 mt-1">يقيس عدد الفقرات التي تم إدخال درجة وتوثيق لها ضمن المحاور القابلة للتحرير.</p>
            </div>
            <div class="text-3xl font-extrabold text-anbar-900">{{ $completionSummary['percent'] }}%</div>
        </div>
        <div class="h-3 bg-gray-100 rounded overflow-hidden">
            <div class="h-full bg-gold-600" style="width: {{ $completionSummary['percent'] }}%"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4 text-sm">
            @foreach([2 => 'المحور الثاني', 3 => 'المحور الثالث', 4 => 'المحور الرابع'] as $axisNo => $axisLabel)
                @php
                    $axisCompletion = $completionSummary['axis_progress'][$axisNo];
                @endphp
                <div class="border border-gray-200 rounded p-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-bold text-gray-800">{{ $axisLabel }}</span>
                        <span class="text-anbar-900 font-bold">{{ $axisCompletion['percent'] }}%</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $axisCompletion['completed'] }} من {{ $axisCompletion['total'] }} فقرات موثقة</p>
                </div>
            @endforeach
        </div>
        @if(!empty($completionSummary['blocking_messages']))
            <div class="mt-4 bg-amber-50 border border-amber-200 rounded p-3 text-sm text-amber-900 space-y-1">
                @foreach($completionSummary['blocking_messages'] as $message)
                    <div>{{ $message }}</div>
                @endforeach
            </div>
        @endif
    </section>

    <section x-data="{ axis: 'all', completion: 'all' }" class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
            <div>
                <h2 class="font-bold text-lg text-gray-900">فقرات الاستمارة والدرجات والتوثيق</h2>
                <p class="text-sm text-gray-500 mt-1">كل فقرة تحفظ درجتها ورابط توثيقها بشكل مستقل ضمن الاستمارة الأساسية.</p>
            </div>
            <span class="text-sm text-gray-500">الحالة: {{ $evaluation->status_label }}</span>
        </div>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 mb-5">
            <div class="flex flex-wrap gap-2 text-xs font-bold">
                <button type="button" @click="axis = 'all'" :class="axis === 'all' ? 'bg-anbar-900 text-white' : 'bg-gray-100 text-gray-700'" class="px-3 py-2 rounded">كل المحاور</button>
                @foreach($form39Axes as $axisFilter)
                    <button type="button" @click="axis = '{{ $axisFilter['key'] }}'" :class="axis === '{{ $axisFilter['key'] }}' ? 'bg-anbar-900 text-white' : 'bg-gray-100 text-gray-700'" class="px-3 py-2 rounded">{{ $axisFilter['title'] }}</button>
                @endforeach
            </div>
            <div class="flex flex-wrap gap-2 text-xs font-bold">
                <button type="button" @click="completion = 'all'" :class="completion === 'all' ? 'bg-gold-600 text-white' : 'bg-gray-100 text-gray-700'" class="px-3 py-2 rounded">الكل</button>
                <button type="button" @click="completion = 'completed'" :class="completion === 'completed' ? 'bg-gold-600 text-white' : 'bg-gray-100 text-gray-700'" class="px-3 py-2 rounded">المكتمل</button>
                <button type="button" @click="completion = 'missing'" :class="completion === 'missing' ? 'bg-gold-600 text-white' : 'bg-gray-100 text-gray-700'" class="px-3 py-2 rounded">الناقص</button>
            </div>
        </div>

        <div class="space-y-6">
            @foreach($form39Axes as $axis)
                <div x-show="axis === 'all' || axis === '{{ $axis['key'] }}'" class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                        <h3 class="font-bold text-anbar-900">{{ $axis['title'] }}</h3>
                        <span class="text-sm text-gray-600">الدرجة القصوى: {{ $axis['max'] }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-right">
                            <thead class="bg-white text-gray-600 border-b">
                                <tr>
                                    <th class="p-3 w-12">ت</th>
                                    <th class="p-3 min-w-72">الفقرة</th>
                                    <th class="p-3 w-24">الدرجة</th>
                                    <th class="p-3 min-w-64">التوثيق</th>
                                    <th class="p-3 min-w-72">ملاحظات التوثيق</th>
                                    <th class="p-3 w-28">إجراء</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($axis['items'] as $item)
                                    @php
                                        $records = $evidenceByItem[$item['key']] ?? collect();
                                        $record = $records->last();
                                        $isReadonly = !empty($item['readonly']) || $evaluation->isLocked();
                                        $currentScore = $records->sum('score_awarded');
                                        $formId = 'evidence-form-' . $item['key'];
                                        if (!empty($item['readonly']) && $item['key'] === 'axis1_student_feedback') {
                                            $currentScore = $scores['score_axis_1'] ?? 0;
                                        }
                                        $isCompleted = !empty($item['readonly']) || $records->isNotEmpty();
                                    @endphp
                                    <tr x-show="completion === 'all' || (completion === 'completed' && {{ $isCompleted ? 'true' : 'false' }}) || (completion === 'missing' && {{ $isCompleted ? 'false' : 'true' }})" class="align-top">
                                        <td class="p-3 text-gray-500">{{ $loop->iteration }}</td>
                                        <td class="p-3">
                                            <div class="font-bold text-gray-900">{{ $item['title'] }}</div>
                                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                                <span class="text-xs text-gray-500">الحد الأعلى: {{ $item['max'] }}</span>
                                                @if($isCompleted)
                                                    <span class="text-[11px] bg-green-100 text-green-800 rounded px-2 py-0.5">مكتمل</span>
                                                @else
                                                    <span class="text-[11px] bg-amber-100 text-amber-800 rounded px-2 py-0.5">ناقص</span>
                                                @endif
                                            </div>
                                            @if($records->isNotEmpty())
                                                <div class="mt-3 space-y-2">
                                                    @foreach($records as $savedRecord)
                                                        <div class="rounded border border-gray-200 bg-gray-50 p-2 text-xs">
                                                            <div class="flex justify-between gap-2">
                                                                <span class="font-bold text-gray-700">{{ $savedRecord->metadata_json['option_label'] ?? 'درجة مدخلة' }}</span>
                                                                <span class="font-bold text-anbar-900">{{ number_format($savedRecord->score_awarded, 2) }}</span>
                                                            </div>
                                                            @if(!empty($savedRecord->metadata_json['is_blacklisted_journal']) || (array_key_exists('has_first_university_affiliation', $savedRecord->metadata_json ?? []) && empty($savedRecord->metadata_json['has_first_university_affiliation']) && empty($savedRecord->metadata_json['has_phd_student_exception'])))
                                                                <div class="text-red-700 font-bold mt-1">هذا النشاط البحثي مستبعد من الاحتساب حسب الضوابط.</div>
                                                            @endif
                                                            <div class="text-gray-600 mt-1">{{ $savedRecord->description }}</div>
                                                            <div class="flex items-center justify-between gap-2 mt-1">
                                                                <a class="text-anbar-700 underline break-all" href="{{ $savedRecord->evidence_url }}" target="_blank">{{ $savedRecord->evidence_url }}</a>
                                                                @unless($evaluation->isLocked())
                                                                    <form method="POST" action="{{ route('dept.evaluations.evidence.destroy', [$evaluation, $savedRecord]) }}">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="text-red-700 font-bold whitespace-nowrap">حذف</button>
                                                                    </form>
                                                                @endunless
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if(!empty($item['official_notes']))
                                                <div class="text-xs text-amber-700 mt-2 leading-5">{{ $item['official_notes'] }}</div>
                                            @endif
                                        </td>
                                        @if($isReadonly)
                                            <td class="p-3 font-bold">{{ number_format((float)($currentScore ?? 0), 2) }}</td>
                                            <td class="p-3">
                                                @if($record?->evidence_url)
                                                    <a class="text-anbar-700 underline" href="{{ $record->evidence_url }}" target="_blank">فتح التوثيق</a>
                                                @else
                                                    <span class="text-gray-400">محسوبة آلياً أو غير موثقة</span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-gray-600">{{ $record?->description ?? 'تحسب من تقييمات الطلبة المكتملة.' }}</td>
                                            <td class="p-3 text-gray-400">-</td>
                                        @else
                                            <td class="p-3">
                                                <form id="{{ $formId }}" method="POST" action="{{ route('dept.evaluations.evidence.store', $evaluation) }}" class="hidden">
                                                    @csrf
                                                    <input type="hidden" name="item_key" value="{{ $item['key'] }}">
                                                </form>
                                                @if(!empty($item['options']))
                                                    <div class="font-bold">{{ number_format((float)$currentScore, 2) }}</div>
                                                    <div class="text-xs text-gray-500 mt-1">مجموع النشاطات المسجلة</div>
                                                @else
                                                    <input form="{{ $formId }}" name="score_awarded" type="number" min="0" max="{{ $item['max'] }}" step="0.25" value="{{ old('score_awarded', ((int)$item['axis_number'] === 3 ? $record?->score_awarded : null)) }}" class="w-24 border rounded p-2" required>
                                                @endif
                                            </td>
                                            <td class="p-3">
                                                @if(!empty($item['options']))
                                                    <select form="{{ $formId }}" name="option_code" class="w-full border rounded p-2 mb-2" required>
                                                        <option value="">اختر النشاط الرسمي</option>
                                                        @foreach($item['options'] as $option)
                                                            <option value="{{ $option['code'] }}">
                                                                {{ $option['label'] }} - {{ number_format((float)$option['score'], 2) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                                @if(!empty($item['research_metadata']))
                                                    <textarea form="{{ $formId }}" name="evidence_urls" rows="3" class="w-full border rounded p-2" placeholder="روابط البحوث، كل رابط في سطر مستقل">{{ old('evidence_urls') }}</textarea>
                                                    <input form="{{ $formId }}" name="evidence_url" type="url" value="{{ old('evidence_url') }}" class="w-full border rounded p-2 mt-2" placeholder="أو رابط بحث واحد فقط">
                                                    <p class="text-[11px] text-gray-500 mt-1">كل رابط بحث يُحفظ كسجل مستقل بنفس نوع النشاط المحدد.</p>
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2 text-xs">
                                                        <label class="flex items-center gap-1"><input form="{{ $formId }}" type="checkbox" name="is_first_author" value="1"> مؤلف أول</label>
                                                        <label class="flex items-center gap-1"><input form="{{ $formId }}" type="checkbox" name="is_corresponding_author" value="1"> باحث مراسل</label>
                                                        <label class="flex items-center gap-1"><input form="{{ $formId }}" type="checkbox" name="has_first_university_affiliation" value="1" checked> انتساب الجامعة أولاً</label>
                                                        <label class="flex items-center gap-1"><input form="{{ $formId }}" type="checkbox" name="has_phd_student_exception" value="1"> استثناء طالب دكتوراه</label>
                                                        <label class="flex items-center gap-1 text-red-700"><input form="{{ $formId }}" type="checkbox" name="is_blacklisted_journal" value="1"> مجلة ضمن القائمة السوداء</label>
                                                        <label class="block">تسلسل الباحث
                                                            <input form="{{ $formId }}" name="author_position" type="number" min="1" value="1" class="mt-1 w-full border rounded p-1" title="ترتيب المؤلف">
                                                        </label>
                                                    </div>
                                                @else
                                                    <input form="{{ $formId }}" name="evidence_url" type="url" value="{{ old('evidence_url') }}" class="w-full border rounded p-2" placeholder="رابط التوثيق" required>
                                                @endif
                                            </td>
                                            <td class="p-3">
                                                <textarea form="{{ $formId }}" name="description" rows="2" class="w-full border rounded p-2" required>{{ old('description') }}</textarea>
                                            </td>
                                            <td class="p-3">
                                                <button form="{{ $formId }}" class="px-3 py-2 rounded bg-anbar-900 text-white text-sm font-bold">حفظ</button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    @unless($evaluation->isLocked())
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
                <h2 class="font-bold text-lg">العقوبات الإدارية</h2>
                @if($evaluation->administrativePenalties->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($evaluation->administrativePenalties as $penalty)
                            <div class="border border-red-100 bg-red-50 rounded p-3 text-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="font-bold text-red-900">{{ \App\Models\AdministrativePenalty::getPenaltyTypeLabel($penalty->penalty_type) }}</div>
                                        <div class="text-xs text-red-800 mt-1">
                                            {{ $penalty->penalty_date?->format('Y-m-d') }}
                                            @if($penalty->order_number)
                                                - أمر {{ $penalty->order_number }}
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-700 mt-1">{{ $penalty->description }}</div>
                                    </div>
                                    <form method="POST" action="{{ route('dept.evaluations.penalty.destroy', [$evaluation, $penalty]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-700 font-bold text-xs whitespace-nowrap">حذف</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">لا توجد عقوبات إدارية مسجلة على هذه الاستمارة.</p>
                @endif
                <form method="POST" action="{{ route('dept.evaluations.penalty.store', $evaluation) }}" class="space-y-4 border-t border-gray-100 pt-4">
                    @csrf
                    <label class="block text-sm">نوع العقوبة
                        <select name="penalty_type" class="mt-1 w-full border rounded p-2">
                            <option value="notice_of_attention">لفت نظر</option>
                            <option value="warning">إنذار</option>
                            <option value="salary_suspension">قطع راتب</option>
                            <option value="reprimand">توبيخ</option>
                            <option value="salary_reduction">إنقاص راتب</option>
                            <option value="rank_reduction">تنزيل درجة</option>
                        </select>
                    </label>
                    <label class="block text-sm">تاريخ العقوبة <input name="penalty_date" type="date" class="mt-1 w-full border rounded p-2" required></label>
                    <label class="block text-sm">رقم الأمر <input name="order_number" class="mt-1 w-full border rounded p-2"></label>
                    <label class="block text-sm">الوصف <textarea name="description" class="mt-1 w-full border rounded p-2" required></textarea></label>
                    <button class="px-4 py-2 rounded bg-red-700 text-white font-bold">حفظ العقوبة</button>
                </form>
            </div>

            <form method="POST" action="{{ route('dept.evaluations.submit', $evaluation) }}" class="bg-white border border-gray-200 rounded-lg p-6">
                @csrf
                <h2 class="font-bold text-lg mb-2">رفع إلى ضمان الجودة في الكلية</h2>
                <p class="text-sm text-gray-600 mb-4">بعد الرفع تصبح الاستمارة مقفلة للتعديل الاعتيادي لحين إرجاعها أو قبولها.</p>
                <button class="px-4 py-2 rounded bg-gold-600 text-white font-bold">رفع الاستمارة</button>
            </form>
        </section>
    @else
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-amber-900">هذه الاستمارة مقفلة حالياً حسب مرحلة سير العمل.</div>
    @endunless
</div>
@endsection
