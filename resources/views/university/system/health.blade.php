@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="bg-white border border-gray-200 rounded-lg p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">فحص قبل التشغيل</p>
            <h1 class="text-2xl font-bold text-anbar-900">جاهزية النظام</h1>
            <p class="text-sm text-gray-600 mt-1">مؤشرات سريعة للتأكد من اكتمال البنية الأساسية قبل التشغيل الرسمي.</p>
        </div>
        <a href="{{ route('university.dashboard') }}" class="px-4 py-2 rounded bg-gray-100 text-gray-800 text-sm font-bold">العودة للوحة الجامعة</a>
    </section>

    <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="p-3">الفحص</th>
                    <th class="p-3">الحالة</th>
                    <th class="p-3">التفاصيل</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($checks as $check)
                    <tr>
                        <td class="p-3 font-bold">{{ $check['label'] }}</td>
                        <td class="p-3">
                            @if($check['status'])
                                <span class="inline-flex px-3 py-1 rounded bg-green-100 text-green-800 font-bold">جاهز</span>
                            @else
                                <span class="inline-flex px-3 py-1 rounded bg-red-100 text-red-800 font-bold">يحتاج متابعة</span>
                            @endif
                        </td>
                        <td class="p-3 text-gray-700">{{ $check['details'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</div>
@endsection
