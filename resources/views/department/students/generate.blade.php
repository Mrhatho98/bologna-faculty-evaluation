@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <div class="border-b border-gray-100 pb-4 mb-4">
            <h1 class="text-xl font-bold text-anbar-900">توليد حسابات الطلبة الدفعية (Bulk Student Generation)</h1>
            <p class="text-xs text-gray-500 mt-1">توليد أسماء المستخدمين وكلمات المرور العشوائية الآمنة لطلبة القسم دفعة واحدة.</p>
        </div>

        <form method="POST" action="{{ route('dept.students.generate.process') }}" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">العام الدراسي *</label>
                    <select name="academic_year_id" required class="w-full px-3 py-2 border rounded-xl bg-white">
                        @foreach($academicYears as $y)
                            <option value="{{ $y->id }}">{{ $y->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">الفصل الدراسي *</label>
                    <select name="semester_id" required class="w-full px-3 py-2 border rounded-xl bg-white">
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}">{{ $sem->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 mb-1">قائمة أسماء الطلبة والأرقام الامتحانية *</label>
                <p class="text-[11px] text-gray-500 mb-2">أدخل كل طالب في سطر منفصل بالصيغة: <code class="bg-gray-100 px-1 font-mono">الاسم الكامل, الرقم الجامعي, المرحلة, الشعبة</code></p>
                <textarea name="students_text" rows="8" required class="w-full p-3 border rounded-xl font-mono text-xs leading-relaxed"
                    placeholder="علي حسن أحمد, 2025-CS-001, 2, A
مريم خالد عمر, 2025-CS-002, 2, A
عمر فاروق محمود, 2025-CS-003, 2, B"></textarea>
            </div>

            <div class="flex justify-end gap-2 border-t pt-4">
                <a href="{{ route('dept.dashboard') }}" class="px-4 py-2 border rounded-xl font-bold text-gray-600">إلغاء</a>
                <button type="submit" class="px-6 py-2 bg-gold-500 hover:bg-gold-600 text-white font-bold rounded-xl shadow">توليد الحسابات وكلمات المرور</button>
            </div>
        </form>
    </div>
</div>
@endsection
