@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header banner -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-100 text-sky-800 mb-2">
                <span>بوابة الطالب التقييمية</span>
                <span>•</span>
                <span>مسار بولونيا</span>
            </div>
            <h1 class="text-2xl font-bold text-anbar-900">مرحباً بك، {{ $student->user->name }}</h1>
            <p class="text-xs text-gray-500 mt-1">
                القسم: <strong class="text-gray-800">{{ $student->department->name_ar }}</strong> |
                المرحلة: <strong class="text-gray-800">المرحلة {{ $student->stage }}</strong> |
                الرقم الجامعي: <strong class="text-gray-800 font-mono">{{ $student->student_id_number }}</strong>
            </p>
        </div>

        @if($activePeriod)
            <div class="bg-sky-50 border border-sky-200 rounded-xl p-3 text-xs text-sky-900 flex items-center gap-3">
                <div class="w-3 h-3 bg-green-500 rounded-full animate-ping"></div>
                <div>
                    <p class="font-bold">فترة التقييم الحالية نشطة</p>
                    <p class="text-[11px] text-sky-700">تنتهي في: {{ $activePeriod->end_date->format('Y-m-d') }}</p>
                </div>
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-900">
                <p class="font-bold">فترة التقييم غير نشطة حالياً</p>
            </div>
        @endif
    </div>

    <!-- Assigned Course Evaluations Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">المواد والمدرسين المكلف بتقييمهم (المحور الأول)</h2>
            <span class="text-xs text-gray-500">إجمالي التكليفات: {{ $assignments->count() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="bg-gray-50 text-gray-700 text-xs font-bold uppercase border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">المادة الدراسية</th>
                        <th class="py-3 px-4">أستاذ المادة</th>
                        <th class="py-3 px-4">المرحلة والشعبة</th>
                        <th class="py-3 px-4">حالة التقييم</th>
                        <th class="py-3 px-4 text-center">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($assignments as $index => $item)
                        @php
                            $assignment = $item->teachingAssignment;
                            $staff = $assignment->teachingStaff;
                            $course = $assignment->course;
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-4 font-bold text-gray-400">{{ $index + 1 }}</td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-anbar-900">{{ $course->name_ar }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $course->code }}</div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-semibold text-gray-800">{{ $staff->full_name }}</div>
                                <div class="text-xs text-gold-600 font-bold">{{ $staff->rank_label }}</div>
                            </td>
                            <td class="py-4 px-4 text-xs text-gray-600">
                                المرحلة {{ $assignment->stage }} - شعبة {{ $assignment->class_group ?? 'A' }}
                            </td>
                            <td class="py-4 px-4">
                                @if($item->is_completed)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                        <svg class="w-3.5 h-3.5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        مكتمل ومقفل
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                        بانتظار التقييم
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if(!$item->is_completed && $activePeriod && $activePeriod->isOpen())
                                    <a href="{{ route('student.evaluate', $assignment->id) }}" class="inline-flex items-center gap-1 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold py-2 px-4 rounded-xl transition shadow">
                                        <span>بدء التقييم الآن</span>
                                        <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                @elseif($item->is_completed)
                                    <span class="text-xs text-gray-400 font-medium">تم التقديم قفل التعديل</span>
                                @else
                                    <span class="text-xs text-red-500 font-medium">فترة التقييم مغلقة</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 text-xs">
                                لا توجد مواد دراسية مكلف بتقييمها حالياً من قبل رئيس القسم.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
