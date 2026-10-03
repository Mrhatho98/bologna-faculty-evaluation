@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
            <div>
                <span class="inline-block bg-sky-100 text-sky-800 text-xs px-2.5 py-0.5 rounded-full font-bold mb-1">
                    المحور الأول — فيدباك الطالب حول المادة وطريقة التدريس (الدرجة القصوى 10)
                </span>
                <h1 class="text-2xl font-bold text-anbar-900">تقييم المادة: {{ $assignment->course->name_ar }} ({{ $assignment->course->code }})</h1>
                <p class="text-sm text-gray-600 mt-1">أستاذ المادة: <strong class="text-gray-900">{{ $assignment->teachingStaff->full_name }}</strong> ({{ $assignment->teachingStaff->rank_label }})</p>
            </div>
            <a href="{{ route('student.dashboard') }}" class="text-xs text-gray-500 hover:text-gray-800 border border-gray-300 px-3 py-1.5 rounded-lg">إلغاء والعودة</a>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-900 flex items-start gap-2">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="font-bold mb-0.5">تنبيه هام للسرية ومسؤولية التقييم:</p>
                <p>تقييمك سري للغاية ولن يظهر اسمك للأستاذ أو القسم. يرجى التقييم بكل أمانة وموضوعية لدعم جودة التعليم في مسار بولونيا بجامعة الأنبار.</p>
            </div>
        </div>
    </div>

    <!-- Questionnaire Form -->
    <form method="POST" action="{{ route('student.evaluate.submit', $assignment->id) }}" x-data="{ confirmed: false }" class="space-y-6">
        @csrf

        <!-- Section A -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 space-y-4">
            <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                <h2 class="text-base font-bold text-anbar-900">القسم (أ): التدريس وفق مسار بولونيا (4 درجات)</h2>
                <span class="text-xs text-sky-700 bg-sky-50 px-2 py-1 rounded font-bold">درجة واحدة لكل فقرة تنطبق</span>
            </div>

            <div class="space-y-4 divide-y divide-gray-100">
                @foreach($questionsSectionA as $key => $qText)
                    <div class="pt-3 first:pt-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <span class="text-sm font-medium text-gray-800 leading-relaxed max-w-2xl">
                            <strong class="text-anbar-900 ml-1">{{ $loop->iteration }}.</strong> {{ $qText }}
                        </span>
                        <div class="flex items-center gap-4 shrink-0 bg-gray-50 p-2 rounded-xl border border-gray-200">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-green-700 cursor-pointer">
                                <input type="radio" name="sec_a_{{ $key }}" value="1" required class="text-green-600 focus:ring-green-500">
                                <span>ينطبق (1)</span>
                            </label>
                            <label class="flex items-center gap-1.5 text-xs font-bold text-red-700 cursor-pointer">
                                <input type="radio" name="sec_a_{{ $key }}" value="0" required class="text-red-600 focus:ring-red-500">
                                <span>لا ينطبق (0)</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Section B -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 space-y-4">
            <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
                <h2 class="text-base font-bold text-anbar-900">القسم (ب): كفاءة الأستاذ وطريقة التدريس (6 درجات)</h2>
                <span class="text-xs text-sky-700 bg-sky-50 px-2 py-1 rounded font-bold">درجة واحدة لكل فقرة تنطبق</span>
            </div>

            <div class="space-y-4 divide-y divide-gray-100">
                @foreach($questionsSectionB as $key => $qText)
                    <div class="pt-3 first:pt-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <span class="text-sm font-medium text-gray-800 leading-relaxed max-w-2xl">
                            <strong class="text-anbar-900 ml-1">{{ $loop->iteration }}.</strong> {{ $qText }}
                        </span>
                        <div class="flex items-center gap-4 shrink-0 bg-gray-50 p-2 rounded-xl border border-gray-200">
                            <label class="flex items-center gap-1.5 text-xs font-bold text-green-700 cursor-pointer">
                                <input type="radio" name="sec_b_{{ $key }}" value="1" required class="text-green-600 focus:ring-green-500">
                                <span>ينطبق (1)</span>
                            </label>
                            <label class="flex items-center gap-1.5 text-xs font-bold text-red-700 cursor-pointer">
                                <input type="radio" name="sec_b_{{ $key }}" value="0" required class="text-red-600 focus:ring-red-500">
                                <span>لا ينطبق (0)</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Submission Action Box -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer">
                <input type="checkbox" x-model="confirmed" class="rounded text-anbar-700 focus:ring-anbar-700 w-4 h-4">
                <span>أؤكد إجاباتي وأفهم أن التقييم سيتم قفله نهائياً فور الإرسال.</span>
            </label>

            <button type="submit" :disabled="!confirmed" :class="confirmed ? 'bg-anbar-900 hover:bg-anbar-800' : 'bg-gray-300 cursor-not-allowed'"
                class="text-white font-bold text-sm py-3 px-8 rounded-xl transition shadow flex items-center gap-2">
                <span>تسليم التقييم وقفل الاستمارة</span>
                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </div>
    </form>
</div>
@endsection
