@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="bg-white border border-gray-200 rounded-lg p-6">
        <h1 class="text-2xl font-bold text-anbar-900">إدارة الأقسام العلمية</h1>
    </section>

    <form method="POST" action="{{ route('university.departments.store') }}" class="bg-white border border-gray-200 rounded-lg p-6 grid grid-cols-1 md:grid-cols-5 gap-4">
        @csrf
        <select name="college_id" class="border rounded p-2" required>
            <option value="">اختر الكلية</option>
            @foreach($colleges as $college)
                <option value="{{ $college->id }}">{{ $college->name_ar }}</option>
            @endforeach
        </select>
        <input name="name_ar" class="border rounded p-2" placeholder="اسم القسم بالعربية" required>
        <input name="name_en" class="border rounded p-2" placeholder="Department name in English" required>
        <input name="code" class="border rounded p-2" placeholder="الرمز">
        <button class="bg-anbar-900 text-white rounded p-2 font-bold">إضافة</button>
    </form>

    <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600"><tr><th class="p-3">الكلية</th><th class="p-3">القسم</th><th class="p-3">English</th><th class="p-3">الرمز</th><th class="p-3">الحالة</th></tr></thead>
            <tbody class="divide-y">
                @foreach($departments as $department)
                    <tr>
                        <td class="p-3">{{ $department->college->name_ar }}</td>
                        <td class="p-3 font-bold">{{ $department->name_ar }}</td>
                        <td class="p-3">{{ $department->name_en }}</td>
                        <td class="p-3">{{ $department->code }}</td>
                        <td class="p-3">{{ $department->is_active ? 'فعال' : 'متوقف' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</div>
@endsection
