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
    </style>
</head>
<body>
<table>
    <tr><td colspan="13" class="title">جامعة الأنبار - تقرير استمارات رقم (39) لمسار بولونيا</td></tr>
    <tr><td colspan="13">تاريخ التصدير: {{ now()->format('Y-m-d H:i') }}</td></tr>
</table>

<br>

<table>
    <thead>
        <tr>
            <th>ت</th>
            <th>التدريسي</th>
            <th>الكلية</th>
            <th>القسم</th>
            <th>السنة</th>
            <th>الفصل</th>
            <th>الحالة</th>
            <th>المحور 1</th>
            <th>المحور 2</th>
            <th>المحور 3</th>
            <th>المحور 4</th>
            <th>المجموع</th>
            <th>التصنيف</th>
        </tr>
    </thead>
    <tbody>
        @forelse($evaluations as $evaluation)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $evaluation->teachingStaff->full_name ?? '-' }}</td>
                <td>{{ $evaluation->college->name_ar ?? '-' }}</td>
                <td>{{ $evaluation->teachingStaff->department->name_ar ?? '-' }}</td>
                <td>{{ $evaluation->academicYear->name ?? '-' }}</td>
                <td>{{ $evaluation->semester->name_ar ?? '-' }}</td>
                <td>{{ $evaluation->status_label }}</td>
                <td>{{ number_format($evaluation->score_axis_1, 2) }}</td>
                <td>{{ number_format($evaluation->score_axis_2, 2) }}</td>
                <td>{{ number_format($evaluation->score_axis_3, 2) }}</td>
                <td>{{ number_format($evaluation->score_axis_4, 2) }}</td>
                <td>{{ number_format($evaluation->total_score, 2) }}</td>
                <td>{{ $evaluation->classification_label }}</td>
            </tr>
        @empty
            <tr><td colspan="13">لا توجد استمارات ضمن الفلاتر المحددة.</td></tr>
        @endforelse
    </tbody>
</table>
</body>
</html>
