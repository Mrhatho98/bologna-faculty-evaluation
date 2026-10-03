@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
            <div>
                <span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2.5 py-0.5 rounded-full font-bold mb-1">
                    تعيين الطلبة المقيّمين للمادة
                </span>
                <h1 class="text-xl font-bold text-anbar-900">المادة: {{ $assignment->course->name_ar }} ({{ $assignment->course->code }})</h1>
                <p class="text-xs text-gray-600 mt-1">الأستاذ: <strong>{{ $assignment->teachingStaff->full_name }}</strong> ({{ $assignment->teachingStaff->rank_label }})</p>
            </div>
            <a href="{{ route('dept.assignments.index') }}" class="text-xs border border-gray-300 px-3 py-1.5 rounded-lg text-gray-600 hover:bg-gray-50 font-bold">العودة للتكليفات</a>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-xs text-red-900 mb-6 flex items-start gap-3">
            <svg class="w-6 h-6 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="font-extrabold text-sm mb-1">القاعدة الحاسمة لمسار بولونيا:</p>
                <p>يجب ألا يقل عدد الطلبة المقيّمين للمادة الواحدة عن 10 طلبة إطلاقاً. النظام سيمنع التفعيل في حال تحديد أقل من 10 طلبة.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('dept.assignments.evaluators.update', $assignment->id) }}" class="space-y-6">
            @csrf

            <div class="flex items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-200">
                <span class="text-xs font-bold text-gray-700">قائمة طلاب القسم المتاحين بالتكليف:</span>
                <div class="text-xs font-bold text-anbar-900">
                    تم تحديد: <span id="selected-count" class="font-mono text-base text-gold-600 font-extrabold">{{ count($assignedStudentIds) }}</span> طلبة
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-96 overflow-y-auto p-2 border rounded-xl bg-gray-50/50">
                @foreach($students as $stu)
                    @php $checked = in_array($stu->id, $assignedStudentIds); @endphp
                    <label class="flex items-center gap-2.5 p-3 rounded-xl bg-white border border-gray-200 cursor-pointer hover:bg-indigo-50/50 transition">
                        <input type="checkbox" name="student_ids[]" value="{{ $stu->id }}" {{ $checked ? 'checked' : '' }}
                            onchange="updateCount()" class="evaluator-checkbox rounded text-anbar-700 focus:ring-anbar-700 w-4 h-4">
                        <div class="text-xs">
                            <div class="font-bold text-gray-900">{{ $stu->user->name }}</div>
                            <div class="text-[10px] text-gray-500 font-mono">{{ $stu->student_id_number }} (م{{ $stu->stage }})</div>
                        </div>
                    </label>
                @endforeach
            </div>

            <div class="flex justify-between items-center border-t pt-4">
                <a href="{{ route('dept.assignments.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-800">إلغاء</a>
                <button type="submit" class="bg-anbar-900 hover:bg-anbar-800 text-white font-bold text-sm py-2.5 px-6 rounded-xl transition shadow">
                    حفظ التكليف والاعتماد (شرط 10 طلبة)
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateCount() {
        const checkboxes = document.querySelectorAll('.evaluator-checkbox:checked');
        document.getElementById('selected-count').innerText = checkboxes.length;
    }
</script>
@endsection
