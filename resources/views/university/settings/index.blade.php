@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="bg-white border border-gray-200 rounded-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-anbar-900">إعدادات قواعد التقييم</h1>
                <p class="text-sm text-gray-600 mt-1">هذه إعدادات تشغيلية قابلة للتغيير، ولا تعرض كقواعد رسمية إلا عند ورودها في تعليمات الاستمارة.</p>
            </div>
            <a href="{{ route('university.settings.rules') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-800 text-sm font-bold">عرض قواعد الاحتساب</a>
        </div>
    </section>

    <form method="POST" action="{{ route('university.settings.update') }}" class="bg-white border border-gray-200 rounded-lg p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        <label class="text-sm">معامل الأستاذ <input name="prof_multiplier" type="number" min="0" step="0.01" value="{{ $rankMultipliers['professor'] }}" class="mt-1 w-full border rounded p-2"></label>
        <label class="text-sm">معامل الأستاذ المساعد <input name="assoc_prof_multiplier" type="number" min="0" step="0.01" value="{{ $rankMultipliers['assistant_professor'] }}" class="mt-1 w-full border rounded p-2"></label>
        <label class="text-sm">معامل المدرس <input name="lecturer_multiplier" type="number" min="0" step="0.01" value="{{ $rankMultipliers['lecturer'] }}" class="mt-1 w-full border rounded p-2"></label>
        <label class="text-sm">معامل المدرس المساعد <input name="asst_lecturer_multiplier" type="number" min="0" step="0.01" value="{{ $rankMultipliers['assistant_lecturer'] }}" class="mt-1 w-full border rounded p-2"></label>
        <label class="text-sm">الحد الأدنى للطلبة المقيمين <input name="min_evaluators" type="number" min="10" value="{{ $minEvaluators }}" class="mt-1 w-full border rounded p-2"></label>
        <label class="text-sm">آلية تجميع تقييم الطلبة
            <select name="formula" class="mt-1 w-full border rounded p-2">
                <option value="mean" @selected($formula === 'mean')>متوسط حسابي تشغيلي</option>
                <option value="weighted_mean" @selected($formula === 'weighted_mean')>متوسط موزون</option>
            </select>
        </label>
        <button class="md:col-span-2 bg-anbar-900 text-white rounded p-2 font-bold">حفظ الإعدادات</button>
    </form>
</div>
@endsection
