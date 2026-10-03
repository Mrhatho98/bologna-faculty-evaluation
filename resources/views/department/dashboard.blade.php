@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 mb-2">
                <span>بوابة رئيس القسم العلمي</span>
                <span>•</span>
                <span>{{ $college->name_ar }}</span>
            </div>
            <h1 class="text-2xl font-bold text-anbar-900">{{ $department->name_ar }}</h1>
            <p class="text-xs text-gray-500 mt-1">إدارة الهيئة التدريسية، الأنصبة، توليد حسابات الطلبة، الحقيبة التدريسية، ورفع التقييمات لجودة الكلية.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('dept.staff.index') }}" class="bg-anbar-900 hover:bg-anbar-800 text-white text-xs font-bold px-3.5 py-2.5 rounded-xl transition shadow flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>إضافة تدريسي</span>
            </a>
            <a href="{{ route('dept.students.generate') }}" class="bg-gold-500 hover:bg-gold-600 text-white text-xs font-bold px-3.5 py-2.5 rounded-xl transition shadow flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"/></svg>
                <span>توليد حسابات الطلبة</span>
            </a>
        </div>
    </div>

    <!-- Quick Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-bold mb-1">أعضاء الهيئة التدريسية</p>
                <p class="text-2xl font-extrabold text-anbar-900">{{ $staffCount }}</p>
            </div>
            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-bold">
                👨‍🏫
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-bold mb-1">المواد الدراسية</p>
                <p class="text-2xl font-extrabold text-anbar-900">{{ $courseCount }}</p>
            </div>
            <div class="w-10 h-10 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center font-bold">
                📚
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-bold mb-1">حسابات الطلبة المقيّمين</p>
                <p class="text-2xl font-extrabold text-anbar-900">{{ $studentCount }}</p>
            </div>
            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-bold">
                🎓
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-bold mb-1">استمارات التقييم بالقسم</p>
                <p class="text-2xl font-extrabold text-anbar-900">{{ $evaluations->count() }}</p>
            </div>
            <div class="w-10 h-10 bg-amber-50 text-gold-600 rounded-xl flex items-center justify-center font-bold">
                📋
            </div>
        </div>
    </div>

    <!-- Actions Quick Links Bar -->
    <div class="bg-anbar-900 text-white rounded-2xl p-4 shadow flex flex-wrap items-center justify-around gap-3 text-xs font-bold">
        <a href="{{ route('dept.staff.index') }}" class="hover:text-gold-500 transition flex items-center gap-1.5">
            <span>إدارة التدريسيين</span>
        </a>
        <span>•</span>
        <a href="{{ route('dept.courses.index') }}" class="hover:text-gold-500 transition flex items-center gap-1.5">
            <span>إدارة المواد الدراسية</span>
        </a>
        <span>•</span>
        <a href="{{ route('dept.assignments.index') }}" class="hover:text-gold-500 transition flex items-center gap-1.5">
            <span>توزيع الأنصبة والطلبة المقيّمين (شرط 10)</span>
        </a>
        <span>•</span>
        <a href="{{ route('dept.students.generate') }}" class="hover:text-gold-500 transition flex items-center gap-1.5">
            <span>إنشاء حسابات الطلبة الدفعية</span>
        </a>
    </div>

    <!-- Staff Evaluations Overview Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">سجل استمارات تقويم أداء التدريسيين (استمارة 39)</h2>
            <span class="text-xs text-gray-500">إجمالي الاستمارات: {{ $evaluations->count() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="bg-gray-50 text-gray-700 text-xs font-bold uppercase border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">أستاذ المادة</th>
                        <th class="py-3 px-4">الدرجة العلمية</th>
                        <th class="py-3 px-4 text-center">المحاور (1-4)</th>
                        <th class="py-3 px-4 text-center">المجموع النهائي</th>
                        <th class="py-3 px-4 text-center">التقدير النهائي</th>
                        <th class="py-3 px-4">حالة الاستمارة</th>
                        <th class="py-3 px-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($evaluations as $index => $eval)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-4 font-bold text-gray-400">{{ $index + 1 }}</td>
                            <td class="py-4 px-4 font-bold text-anbar-900">
                                {{ $eval->teachingStaff->full_name }}
                            </td>
                            <td class="py-4 px-4 text-xs font-bold text-gold-600">
                                {{ $eval->teachingStaff->rank_label }}
                            </td>
                            <td class="py-4 px-4 text-center text-xs dir-ltr font-mono">
                                {{ number_format($eval->score_axis_1, 1) }} | {{ number_format($eval->score_axis_2, 1) }} | {{ number_format($eval->score_axis_3, 1) }} | {{ number_format($eval->score_axis_4, 1) }}
                            </td>
                            <td class="py-4 px-4 text-center font-extrabold text-base text-anbar-900">
                                {{ number_format($eval->total_score, 2) }} / 100
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                    @if($eval->final_classification == 'Excellent') bg-green-100 text-green-800
                                    @elseif($eval->final_classification == 'Very Good') bg-blue-100 text-blue-800
                                    @elseif($eval->final_classification == 'Good') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $eval->classification_label }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                    @if($eval->status == 'ACCEPTED') bg-green-100 text-green-800
                                    @elseif($eval->status == 'REJECTED') bg-red-100 text-red-800
                                    @elseif($eval->status == 'DRAFT') bg-gray-100 text-gray-800
                                    @else bg-purple-100 text-purple-800 @endif">
                                    {{ $eval->status_label }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center space-x-2 space-x-reverse">
                                <a href="{{ route('dept.evaluations.edit', $eval->id) }}" class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition">
                                    <span>إدخال الحقيبة والأدلة</span>
                                </a>
                                <a href="{{ route('evaluations.print', $eval->id) }}" target="_blank" class="inline-flex items-center gap-1 bg-gray-200 hover:bg-gray-300 text-gray-800 text-xs font-bold py-1.5 px-3 rounded-lg transition">
                                    <span>عرض الاستمارة (39)</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-gray-500 text-xs">
                                لا توجد استمارات تقييم بالقسم حالياً. قم بإضافة التدريسيين وتكليفهم بالمواد للبدء.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
