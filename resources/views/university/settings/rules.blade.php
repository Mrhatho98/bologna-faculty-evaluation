@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="bg-white border border-gray-200 rounded-lg p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">استمارة رقم (39)</p>
            <h1 class="text-2xl font-bold text-anbar-900">قواعد احتساب الدرجات والسقوف</h1>
            <p class="text-sm text-gray-600 mt-1">عرض تدقيقي للقواعد الرسمية المخزنة في النظام.</p>
        </div>
        <a href="{{ route('university.settings.index') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-800 text-sm font-bold">العودة للإعدادات</a>
    </section>

    @forelse($rules as $scopeType => $scopeRules)
        <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="p-4 border-b font-bold text-anbar-900">النطاق: {{ $scopeType }}</div>
            <table class="w-full text-sm text-right">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="p-3">الكود</th>
                        <th class="p-3">المفتاح</th>
                        <th class="p-3">العنوان</th>
                        <th class="p-3">الوصف</th>
                        <th class="p-3">المعاملات</th>
                        <th class="p-3">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($scopeRules as $rule)
                        <tr>
                            <td class="p-3 font-mono text-xs">{{ $rule->scope_code }}</td>
                            <td class="p-3 font-mono text-xs">{{ $rule->rule_key }}</td>
                            <td class="p-3 font-bold">{{ $rule->title_ar }}</td>
                            <td class="p-3 text-gray-700">{{ $rule->description_ar }}</td>
                            <td class="p-3 text-xs font-mono">
                                @if(!empty($rule->parameters_json))
                                    {{ json_encode($rule->parameters_json, JSON_UNESCAPED_UNICODE) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-3">
                                @if($rule->is_active)
                                    <span class="text-green-700 font-bold">فعالة</span>
                                @else
                                    <span class="text-red-700 font-bold">معطلة</span>
                                @endif
                                @if($rule->is_configurable)
                                    <div class="text-xs text-gold-600 mt-1">قابلة للإعداد</div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @empty
        <section class="bg-white border border-gray-200 rounded-lg p-8 text-center text-gray-500">لا توجد قواعد احتساب مخزنة.</section>
    @endforelse
</div>
@endsection
