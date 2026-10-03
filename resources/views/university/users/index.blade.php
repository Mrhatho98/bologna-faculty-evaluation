@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="bg-white border border-gray-200 rounded-lg p-6">
        <h1 class="text-2xl font-bold text-anbar-900">إدارة المستخدمين الإداريين</h1>
    </section>

    <form method="POST" action="{{ route('university.users.store') }}" class="bg-white border border-gray-200 rounded-lg p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        @csrf
        <input name="name" class="border rounded p-2" placeholder="الاسم" required>
        <input name="username" class="border rounded p-2" placeholder="اسم المستخدم" required>
        <input name="email" type="email" class="border rounded p-2" placeholder="البريد">
        <input name="password" type="password" class="border rounded p-2" placeholder="كلمة المرور" required>
        <select name="role" class="border rounded p-2" required>
            <option value="department_head">رئيس قسم علمي</option>
            <option value="college_qa">مسؤول ضمان جودة الكلية</option>
            <option value="university_qa_director">مدير ضمان الجودة والأداء الجامعي</option>
        </select>
        <select name="college_id" class="border rounded p-2">
            <option value="">الكلية</option>
            @foreach($colleges as $college)
                <option value="{{ $college->id }}">{{ $college->name_ar }}</option>
            @endforeach
        </select>
        <select name="department_id" class="border rounded p-2">
            <option value="">القسم</option>
            @foreach($departments as $department)
                <option value="{{ $department->id }}">{{ $department->college->code ?? '' }} - {{ $department->name_ar }}</option>
            @endforeach
        </select>
        <button class="bg-anbar-900 text-white rounded p-2 font-bold md:col-span-3">إنشاء المستخدم</button>
    </form>

    <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600"><tr><th class="p-3">الاسم</th><th class="p-3">المستخدم</th><th class="p-3">الدور</th><th class="p-3">الكلية</th><th class="p-3">القسم</th><th class="p-3">الحالة</th></tr></thead>
            <tbody class="divide-y">
                @foreach($users as $user)
                    <tr>
                        <td class="p-3 font-bold">{{ $user->name }}</td>
                        <td class="p-3">{{ $user->username }}</td>
                        <td class="p-3">{{ $user->role }}</td>
                        <td class="p-3">{{ $user->college->name_ar ?? '-' }}</td>
                        <td class="p-3">{{ $user->department->name_ar ?? '-' }}</td>
                        <td class="p-3">{{ $user->is_active ? 'فعال' : 'متوقف' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</div>
@endsection
