<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Tahoma, Arial, sans-serif; direction: rtl; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #777; padding: 6px; vertical-align: top; }
        th { background: #e5e7eb; font-weight: bold; }
        .title { font-size: 18px; font-weight: bold; text-align: center; }
        .section { background: #f3f4f6; font-weight: bold; }
    </style>
</head>
<body>
<table>
    <tr><td colspan="5" class="title">جامعة الأنبار - استمارة رقم (39) تقييم أداء أعضاء الهيئة التدريسية لمسار بولونيا</td></tr>
    <tr><td colspan="5">العام الدراسي: {{ $evaluation->academicYear->name }} - {{ $evaluation->semester->name_ar }}</td></tr>
    <tr><td>الاسم</td><td>{{ $evaluation->teachingStaff->full_name }}</td><td>اللقب العلمي</td><td colspan="2">{{ $evaluation->teachingStaff->rank_label }}</td></tr>
    <tr><td>الكلية</td><td>{{ $evaluation->college->name_ar }}</td><td>القسم</td><td colspan="2">{{ $evaluation->department->name_ar }}</td></tr>
    <tr><td>التخصص العام</td><td>{{ $evaluation->teachingStaff->general_specialization }}</td><td>التخصص الدقيق</td><td colspan="2">{{ $evaluation->teachingStaff->specific_specialization }}</td></tr>
</table>

<br>

<table>
    <tr><th colspan="3">خلاصة المحاور</th></tr>
    <tr><th>المحور</th><th>الدرجة</th><th>الحد الأعلى</th></tr>
    <tr><td>المحور الأول: تقييم الطلبة</td><td>{{ number_format($scores['score_axis_1'], 2) }}</td><td>10</td></tr>
    <tr><td>المحور الثاني: حقيبة التدريسي</td><td>{{ number_format($scores['score_axis_2'], 2) }}</td><td>60</td></tr>
    <tr><td>المحور الثالث: تقييم رئيس القسم</td><td>{{ number_format($scores['score_axis_3'], 2) }}</td><td>15</td></tr>
    <tr><td>المحور الرابع: الجانب التربوي والإرشادي</td><td>{{ number_format($scores['score_axis_4'], 2) }}</td><td>15</td></tr>
    <tr><td>المجموع النهائي</td><td>{{ number_format($scores['total_score'], 2) }}</td><td>100</td></tr>
    <tr><td>التقدير النهائي</td><td colspan="2">{{ $scores['final_classification'] }}</td></tr>
</table>

<br>

<table>
    <tr><th colspan="3">تفاصيل الاحتساب والسقوف</th></tr>
    <tr><th>البند</th><th>القيمة</th><th>ملاحظة</th></tr>
    <tr><td>واجبات ومسؤوليات التدريسي</td><td>{{ number_format($scores['axis_2']['duties_score'] ?? 0, 2) }}</td><td>السقف الرسمي 8 درجات</td></tr>
    <tr><td>مهارات ومعارف وقيم</td><td>{{ number_format($scores['axis_2']['skills_score'] ?? 0, 2) }}</td><td>السقف الرسمي 5 درجات</td></tr>
    <tr><td>التدريب والتعليم المستمر</td><td>{{ number_format($scores['axis_2']['training_score'] ?? 0, 2) }}</td><td>السقف الرسمي 20 درجة</td></tr>
    <tr><td>البحوث والكتب وبراءات الاختراع</td><td>{{ number_format($scores['axis_2']['research']['final_score'] ?? 0, 2) }}</td><td>المعامل: {{ $scores['axis_2']['research']['multiplier'] ?? 1 }}، المستبعد: {{ $scores['axis_2']['research']['excluded_count'] ?? 0 }}</td></tr>
    <tr><td>النشاط الدولي</td><td>{{ number_format($scores['axis_2']['international_score'] ?? 0, 2) }}</td><td>السقف الرسمي 5 درجات</td></tr>
    <tr><td>دعم إيرادات الجامعة</td><td>{{ number_format($scores['axis_2']['revenue_score'] ?? 0, 2) }}</td><td>السقف الرسمي درجتان</td></tr>
    <tr><td>خصومات العقوبات الإدارية</td><td>{{ number_format($scores['axis_3']['total_deduction'] ?? 0, 2) }}</td><td>تخصم من تقييم رئيس القسم</td></tr>
</table>

<br>

@foreach($form39Axes as $axis)
    <table>
        <tr><th colspan="6">{{ $axis['title'] }} - الدرجة القصوى {{ $axis['max'] }}</th></tr>
        <tr>
            <th>ت</th>
            <th>الفقرة</th>
            <th>الدرجة</th>
            <th>الأعلى</th>
            <th>التوثيق/الوصف</th>
            <th>رابط التوثيق</th>
        </tr>
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
            @if($records->isNotEmpty())
                @foreach($records as $record)
                    <tr>
                        <td>{{ $loop->parent->iteration }}</td>
                        <td>{{ $item['title'] }}</td>
                        <td>{{ number_format((float)$itemScore, 2) }}</td>
                        <td>{{ $item['max'] }}</td>
                        <td>
                            @if(!empty($record->metadata_json['option_label']))
                                نوع النشاط: {{ $record->metadata_json['option_label'] }} -
                            @endif
                            @if(!empty($record->metadata_json['is_blacklisted_journal']) || (array_key_exists('has_first_university_affiliation', $record->metadata_json ?? []) && empty($record->metadata_json['has_first_university_affiliation']) && empty($record->metadata_json['has_phd_student_exception'])))
                                مستبعد من الاحتساب حسب الضوابط -
                            @endif
                            {{ $record->description }}
                        </td>
                        <td>{{ $record->evidence_url }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item['title'] }}</td>
                    <td>{{ number_format((float)$itemScore, 2) }}</td>
                    <td>{{ $item['max'] }}</td>
                    <td>{{ !empty($item['readonly']) ? 'محسوبة آلياً' : 'لا يوجد توثيق مسجل' }}</td>
                    <td></td>
                </tr>
            @endif
        @endforeach
    </table>
    <br>
@endforeach

@if($evaluation->administrativePenalties->isNotEmpty())
    <table>
        <tr><th colspan="5">العقوبات الإدارية والخصومات</th></tr>
        <tr><th>نوع العقوبة</th><th>التاريخ</th><th>رقم الأمر</th><th>الخصم</th><th>الوصف</th></tr>
        @foreach($evaluation->administrativePenalties as $penalty)
            <tr>
                <td>{{ \App\Models\AdministrativePenalty::getPenaltyTypeLabel($penalty->penalty_type) }}</td>
                <td>{{ $penalty->penalty_date?->format('Y-m-d') }}</td>
                <td>{{ $penalty->order_number }}</td>
                <td>{{ number_format($penalty->deduction_points, 2) }}</td>
                <td>{{ $penalty->description }}</td>
            </tr>
        @endforeach
    </table>
@endif
</body>
</html>
