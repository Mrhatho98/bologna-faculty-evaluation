@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-anbar-900">إدارة المواد الدراسية بالقسم</h1>
            <p class="text-xs text-gray-500 mt-1">إضافة وتعديل المواد الدراسية لمسار بولونيا وتحديد المراحل والفصول.</p>
        </div>
        <button @click="showModal = true" class="bg-anbar-900 hover:bg-anbar-800 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition shadow flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>إضافة مادة دراسية جديدة</span>
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="bg-gray-50 text-gray-700 text-xs font-bold uppercase border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">رمز المادة</th>
                        <th class="py-3 px-4">اسم المادة بالعربية</th>
                        <th class="py-3 px-4">اسم المادة بالإنكليزية</th>
                        <th class="py-3 px-4">المرحلة</th>
                        <th class="py-3 px-4">الفصل الدراسي</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($courses as $index => $course)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-4 font-bold text-gray-400">{{ $index + 1 }}</td>
                            <td class="py-4 px-4 font-mono font-bold text-anbar-900">{{ $course->code }}</td>
                            <td class="py-4 px-4 font-bold text-gray-800">{{ $course->name_ar }}</td>
                            <td class="py-4 px-4 text-xs font-mono text-gray-600 dir-ltr text-right">{{ $course->name_en }}</td>
                            <td class="py-4 px-4 text-xs font-bold text-indigo-700">المرحلة {{ $course->stage }}</td>
                            <td class="py-4 px-4 text-xs text-gray-600">{{ $course->semester->name_ar ?? 'الفصل الأول' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 text-xs">لا توجد مواد دراسية مضافة حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Adding Course -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-bold text-lg text-anbar-900">إضافة مادة دراسية جديدة لمسار بولونيا</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('dept.courses.store') }}" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">رمز المادة (Code) *</label>
                    <input type="text" name="code" required placeholder="مثال: CS101" class="w-full px-3 py-2 border rounded-xl font-mono">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">اسم المادة بالعربية *</label>
                    <input type="text" name="name_ar" required placeholder="مثال: أنظمة قواعد البيانات" class="w-full px-3 py-2 border rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">اسم المادة بالإنكليزية *</label>
                    <input type="text" name="name_en" required placeholder="Database Systems" class="w-full px-3 py-2 border rounded-xl dir-ltr">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">المرحلة الدراسية *</label>
                    <select name="stage" required class="w-full px-3 py-2 border rounded-xl bg-white">
                        <option value="1">المرحلة الأولى</option>
                        <option value="2" selected>المرحلة الثانية</option>
                        <option value="3">المرحلة الثالثة</option>
                        <option value="4">المرحلة الرابعة</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">الفصل الدراسي</label>
                    <select name="semester_id" class="w-full px-3 py-2 border rounded-xl bg-white">
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}">{{ $sem->name_ar }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 border-t pt-4">
                    <button type="button" @click="showModal = false" class="px-4 py-2 border rounded-xl text-xs font-bold text-gray-600">إلغاء</button>
                    <button type="submit" class="px-6 py-2 bg-anbar-900 text-white rounded-xl text-xs font-bold">حفظ المادة</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
