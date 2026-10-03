@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="bg-white border border-gray-200 rounded-lg p-6">
        <h1 class="text-2xl font-bold text-anbar-900">إدارة الكليات</h1>
    </section>

    <form method="POST" action="{{ route('university.colleges.store') }}" class="bg-white border border-gray-200 rounded-lg p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        @csrf
        <input name="name_ar" class="border rounded p-2" placeholder="اسم الكلية بالعربية" required>
        <input name="name_en" class="border rounded p-2" placeholder="College name in English" required>
        <input name="code" class="border rounded p-2" placeholder="الرمز" required>
        <button class="bg-anbar-900 text-white rounded p-2 font-bold">إضافة</button>
    </form>

    <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600"><tr><th class="p-3">الرمز</th><th class="p-3">العربية</th><th class="p-3">English</th><th class="p-3">الأقسام</th><th class="p-3">الحالة</th></tr></thead>
            <tbody class="divide-y">
                @foreach($colleges as $college)
                    <tr>
                        <td class="p-3 font-bold">{{ $college->code }}</td>
                        <td class="p-3">{{ $college->name_ar }}</td>
                        <td class="p-3">{{ $college->name_en }}</td>
                        <td class="p-3">{{ $college->departments_count }}</td>
                        <td class="p-3">{{ $college->is_active ? 'فعال' : 'متوقف' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</div>
@endsection
