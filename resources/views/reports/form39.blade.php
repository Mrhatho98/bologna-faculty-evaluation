@extends('layouts.app')

@section('content')
<div class="bg-white text-black border border-gray-300 rounded-lg p-8 print:border-0 print:p-0">
    <div class="no-print flex justify-end gap-2 mb-4">
        <a href="{{ route('evaluations.excel', $evaluation) }}" class="px-4 py-2 rounded bg-green-700 text-white text-sm font-bold">تصدير Excel</a>
        <button onclick="window.print()" class="px-4 py-2 rounded bg-gray-800 text-white text-sm font-bold">طباعة</button>
    </div>
    <div class="text-center border-b pb-4 mb-6">
        <h1 class="text-2xl font-bold">جامعة الأنبار</h1>
        <p class="font-bold">استمارة رقم (39) - تقييم أداء أعضاء الهيئة التدريسية لمسار بولونيا</p>
        <p class="text-sm">العام الدراسي {{ $evaluation->academicYear->name }} - {{ $evaluation->semester->name_ar }}</p>
    </div>

    <section class="mb-6">
        <h2 class="font-bold border-b pb-2 mb-3">أولاً: البيانات الأساسية</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <p><strong>الاسم:</strong> {{ $evaluation->teachingStaff->full_name }}</p>
            <p><strong>اللقب العلمي:</strong> {{ $evaluation->teachingStaff->rank_label }}</p>
            <p><strong>الكلية:</strong> {{ $evaluation->college->name_ar }}</p>
            <p><strong>القسم:</strong> {{ $evaluation->department->name_ar }}</p>
            <p><strong>التخصص العام:</strong> {{ $evaluation->teachingStaff->general_specialization }}</p>
            <p><strong>التخصص الدقيق:</strong> {{ $evaluation->teachingStaff->specific_specialization }}</p>
        </div>
    </section>

    <section class="mb-6">
        <h2 class="font-bold border-b pb-2 mb-3">ثانياً: خلاصة المحاور</h2>
        <table class="w-full text-sm border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">المحور</th>
                    <th class="border p-2">الدرجة</th>
                    <th class="border p-2">الحد الأعلى</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="border p-2">المحور الأول: تقييم الطلبة</td><td class="border p-2">{{ number_format($scores['score_axis_1'], 2) }}</td><td class="border p-2">10</td></tr>
                <tr><td class="border p-2">المحور الثاني: حقيبة التدريسي</td><td class="border p-2">{{ number_format($scores['score_axis_2'], 2) }}</td><td class="border p-2">60</td></tr>
                <tr><td class="border p-2">المحور الثالث: تقييم رئيس القسم</td><td class="border p-2">{{ number_format($scores['score_axis_3'], 2) }}</td><td class="border p-2">15</td></tr>
                <tr><td class="border p-2">المحور الرابع: الجانب التربوي والإرشادي</td><td class="border p-2">{{ number_format($scores['score_axis_4'], 2) }}</td><td class="border p-2">15</td></tr>
                <tr class="font-bold bg-gray-50"><td class="border p-2">المجموع النهائي</td><td class="border p-2">{{ number_format($scores['total_score'], 2) }}</td><td class="border p-2">100</td></tr>
            </tbody>
        </table>
        <p class="mt-3 font-bold">التقدير النهائي: {{ $scores['final_classification'] }}</p>
    </section>

    <section class="mb-6">
        <h2 class="font-bold border-b pb-2 mb-3">تفاصيل الاحتساب والسقوف</h2>
        <table class="w-full text-sm border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border p-2">البند</th>
                    <th class="border p-2">القيمة</th>
                    <th class="border p-2">ملاحظة</th>
                </tr>
            </thead>
            <tbody>
                <tr><td class="border p-2">واجبات ومسؤوليات التدريسي</td><td class="border p-2">{{ number_format($scores['axis_2']['duties_score'] ?? 0, 2) }}</td><td class="border p-2">السقف الرسمي 8 درجات</td></tr>
                <tr><td class="border p-2">مهارات ومعارف وقيم</td><td class="border p-2">{{ number_format($scores['axis_2']['skills_score'] ?? 0, 2) }}</td><td class="border p-2">السقف الرسمي 5 درجات</td></tr>
                <tr><td class="border p-2">التدريب والتعليم المستمر</td><td class="border p-2">{{ number_format($scores['axis_2']['training_score'] ?? 0, 2) }}</td><td class="border p-2">السقف الرسمي 20 درجة</td></tr>
                <tr><td class="border p-2">البحوث والكتب وبراءات الاختراع</td><td class="border p-2">{{ number_format($scores['axis_2']['research']['final_score'] ?? 0, 2) }}</td><td class="border p-2">المعامل: {{ $scores['axis_2']['research']['multiplier'] ?? 1 }}، المستبعد: {{ $scores['axis_2']['research']['excluded_count'] ?? 0 }}</td></tr>
                <tr><td class="border p-2">النشاط الدولي</td><td class="border p-2">{{ number_format($scores['axis_2']['international_score'] ?? 0, 2) }}</td><td class="border p-2">السقف الرسمي 5 درجات</td></tr>
                <tr><td class="border p-2">دعم إيرادات الجامعة</td><td class="border p-2">{{ number_format($scores['axis_2']['revenue_score'] ?? 0, 2) }}</td><td class="border p-2">السقف الرسمي درجتان</td></tr>
                <tr><td class="border p-2">خصومات العقوبات الإدارية</td><td class="border p-2">{{ number_format($scores['axis_3']['total_deduction'] ?? 0, 2) }}</td><td class="border p-2">تخصم من تقييم رئيس القسم</td></tr>
            </tbody>
        </table>
    </section>

    <section class="mb-6">
        <h2 class="font-bold border-b pb-2 mb-3">ثالثاً: الاستمارة الأساسية - الفقرات والدرجات والتوثيق</h2>
        <div class="space-y-5">
            @foreach($form39Axes as $axis)
                <div>
                    <div class="font-bold bg-gray-100 border border-gray-300 border-b-0 p-2">
                        {{ $axis['title'] }} - الدرجة القصوى {{ $axis['max'] }}
                    </div>
                    <table class="w-full text-sm border border-gray-300">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="border p-2 w-10">ت</th>
                                <th class="border p-2">الفقرة</th>
                                <th class="border p-2 w-20">الدرجة</th>
                                <th class="border p-2 w-20">الأعلى</th>
                                <th class="border p-2">التوثيق</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($axis['items'] as $item)
                                @php
                                    $records = $evidenceByItem[$item['key']] ?? collect();
                                    $itemScore = min((float)$item['max'], (float)$records->sum('score_awarded'));
                                    if (!empty($item['readonly']) && $item['key'] === 'axis1_student_feedback') {
                                        $itemScore = $scores['score_axis_1'] ?? 0;
                                    } elseif ($item['key'] === 'axis2_research_books_patents') {
                                        $itemScore = $scores['axis_2']['research']['final_score'] ?? $itemScore;
                                    }
                                @endphp
                                <tr>
                                    <td class="border p-2">{{ $loop->iteration }}</td>
                                    <td class="border p-2">{{ $item['title'] }}</td>
                                    <td class="border p-2">{{ number_format((float)$itemScore, 2) }}</td>
                                    <td class="border p-2">{{ $item['max'] }}</td>
                                    <td class="border p-2">
                                        @if($records->isNotEmpty())
                                            @foreach($records as $record)
                                                <div class="mb-2">
                                                    @if(!empty($record->metadata_json['option_label']))
                                                        <div><strong>نوع النشاط:</strong> {{ $record->metadata_json['option_label'] }}</div>
                                                    @endif
                                                    @if(!empty($record->metadata_json['is_blacklisted_journal']) || (array_key_exists('has_first_university_affiliation', $record->metadata_json ?? []) && empty($record->metadata_json['has_first_university_affiliation']) && empty($record->metadata_json['has_phd_student_exception'])))
                                                        <div class="font-bold text-red-700">مستبعد من الاحتساب حسب الضوابط.</div>
                                                    @endif
                                                    <div>{{ $record->description }}</div>
                                                    <div class="text-xs break-all">{{ $record->evidence_url }}</div>
                                                </div>
                                            @endforeach
                                        @elseif(!empty($item['readonly']))
                                            <span>محسوبة آلياً من تقييمات الطلبة.</span>
                                        @else
                                            <span>لا يوجد توثيق مسجل.</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </section>

    @if($evaluation->administrativePenalties->isNotEmpty())
        <section class="mb-6">
            <h2 class="font-bold border-b pb-2 mb-3">رابعاً: العقوبات الإدارية والخصومات</h2>
            <table class="w-full text-sm border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border p-2">نوع العقوبة</th>
                        <th class="border p-2">التاريخ</th>
                        <th class="border p-2">رقم الأمر</th>
                        <th class="border p-2">الخصم</th>
                        <th class="border p-2">الوصف</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evaluation->administrativePenalties as $penalty)
                        <tr>
                            <td class="border p-2">{{ \App\Models\AdministrativePenalty::getPenaltyTypeLabel($penalty->penalty_type) }}</td>
                            <td class="border p-2">{{ $penalty->penalty_date?->format('Y-m-d') }}</td>
                            <td class="border p-2">{{ $penalty->order_number }}</td>
                            <td class="border p-2">{{ number_format($penalty->deduction_points, 2) }}</td>
                            <td class="border p-2">{{ $penalty->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endif

    <section class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center mt-10">
        <div class="border-t pt-2">رئيس القسم العلمي</div>
        <div class="border-t pt-2">وحدة ضمان الجودة في الكلية</div>
        <div class="border-t pt-2">ضمان الجودة والأداء الجامعي</div>
    </section>
</div>
@endsection
