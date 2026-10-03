@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-anbar-900">توزيع التكليفات التدريسية وإدارة الطلبة المقيّمين</h1>
            <p class="text-xs text-gray-500 mt-1">تحديد المادة لكل تدريسي وتعيين عدد الطلبة المقيّمين (الحد الأدنى 10 طلبة إجباري).</p>
        </div>
        <button @click="showModal = true" class="bg-anbar-900 hover:bg-anbar-800 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition shadow flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>إضافة تكليف تدريسي جديد</span>
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="bg-gray-50 text-gray-700 text-xs font-bold uppercase border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">أستاذ المادة</th>
                        <th class="py-3 px-4">المادة المكلف بها</th>
                        <th class="py-3 px-4">المرحلة والشعبة</th>
                        <th class="py-3 px-4 text-center">الطلبة المعينين للتقييم</th>
                        <th class="py-3 px-4 text-center">حالة شرط الـ 10 طلبة</th>
                        <th class="py-3 px-4 text-center">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($assignments as $index => $asgn)
                        @php
                            $evalCount = $asgn->evaluatorAssignments->count();
                            $validMin = $evalCount >= $asgn->min_evaluators;
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-4 font-bold text-gray-400">{{ $index + 1 }}</td>
                            <td class="py-4 px-4 font-bold text-anbar-900">
                                {{ $asgn->teachingStaff->full_name }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="font-bold text-gray-800">{{ $asgn->course->name_ar }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ $asgn->course->code }}</div>
                            </td>
                            <td class="py-4 px-4 text-xs text-gray-600">
                                المرحلة {{ $asgn->stage }} - شعبة {{ $asgn->class_group ?? 'A' }}
                            </td>
                            <td class="py-4 px-4 text-center font-extrabold text-base text-anbar-900">
                                {{ $evalCount }} / {{ $asgn->min_evaluators }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                @if($validMin)
                                    <span class="bg-green-100 text-green-800 border border-green-300 px-2.5 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        مكتمل الشرط (مفعل)
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-800 border border-red-300 px-2.5 py-1 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                        أقل من 10 (محظور التفعيل)
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                <a href="{{ route('dept.assignments.evaluators', $asgn->id) }}" class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition shadow">
                                    <span>تعيين الطلبة المقيّمين</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500 text-xs">لا توجد تكليفات تدريسية مضافة بالقسم حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Adding Assignment -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-bold text-lg text-anbar-900">إضافة تكليف تدريسي لمادة جديدة</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('dept.assignments.store') }}" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-gray-700 mb-1">أستاذ المادة *</label>
                    <select name="teaching_staff_id" required class="w-full px-3 py-2 border rounded-xl bg-white">
                        @foreach($staffMembers as $s)
                            <option value="{{ $s->id }}">{{ $s->full_name }} ({{ $s->rank_label }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">المادة الدراسية *</label>
                    <select name="course_id" required class="w-full px-3 py-2 border rounded-xl bg-white">
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->name_ar }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">السنة الدراسية</label>
                        <select name="academic_year_id" required class="w-full px-3 py-2 border rounded-xl bg-white">
                            @foreach($academicYears as $y)
                                <option value="{{ $y->id }}">{{ $y->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">الفصل الدراسي</label>
                        <select name="semester_id" required class="w-full px-3 py-2 border rounded-xl bg-white">
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}">{{ $sem->name_ar }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">المرحلة</label>
                        <input type="number" name="stage" value="2" min="1" max="6" required class="w-full px-3 py-2 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">الشعبة / القاعة</label>
                        <input type="text" name="class_group" value="A" class="w-full px-3 py-2 border rounded-xl">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">الحد الأدنى المطلوب للطلبة المقيّمين (إجباري لا يقل عن 10)</label>
                    <input type="number" name="min_evaluators" value="10" min="10" required class="w-full px-3 py-2 border rounded-xl font-bold text-anbar-900">
                </div>

                <div class="flex justify-end gap-2 border-t pt-4">
                    <button type="button" @click="showModal = false" class="px-4 py-2 border rounded-xl text-xs font-bold text-gray-600">إلغاء</button>
                    <button type="submit" class="px-6 py-2 bg-anbar-900 text-white rounded-xl text-xs font-bold">حفظ التكليف</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
