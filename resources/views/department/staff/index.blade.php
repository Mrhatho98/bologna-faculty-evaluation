@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ showModal: false }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-anbar-900">إدارة أعضاء الهيئة التدريسية بالقسم</h1>
            <p class="text-xs text-gray-500 mt-1">إضافة وتحديث بيانات التدريسيين والدرجات العلمية وفق النماذج الرسمية.</p>
        </div>
        <button @click="showModal = true" class="bg-anbar-900 hover:bg-anbar-800 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition shadow flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>إضافة أستاذ/تدريسي جديد</span>
        </button>
    </div>

    <!-- Staff Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="bg-gray-50 text-gray-700 text-xs font-bold uppercase border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">الاسم الكامل (رباعي واللقب)</th>
                        <th class="py-3 px-4">اللقب العلمي (المعامل)</th>
                        <th class="py-3 px-4">الشهادة والتخصص</th>
                        <th class="py-3 px-4">الهاتف / البريد الإلكتروني</th>
                        <th class="py-3 px-4 text-center">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($staffMembers as $index => $staff)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-4 font-bold text-gray-400">{{ $index + 1 }}</td>
                            <td class="py-4 px-4 font-bold text-anbar-900">
                                {{ $staff->full_name }}
                            </td>
                            <td class="py-4 px-4">
                                <span class="bg-gold-50 text-gold-600 border border-gold-200 px-2.5 py-1 rounded-full text-xs font-bold">
                                    {{ $staff->rank_label }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-xs text-gray-600">
                                <div>{{ $staff->degree ?? 'دكتوراه' }}</div>
                                <div class="text-[11px] text-gray-400">{{ $staff->specific_specialization ?? $staff->general_specialization }}</div>
                            </td>
                            <td class="py-4 px-4 text-xs font-mono text-gray-700">
                                <div>{{ $staff->mobile ?? '—' }}</div>
                                <div class="text-[11px] text-gray-400">{{ $staff->email }}</div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-green-100 text-green-800">مفعل</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500 text-xs">لا يوجد تدريسيين مضافين بالقسم حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Adding Staff -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="font-bold text-lg text-anbar-900">إضافة عضو هيئة تدريسية جديد بالقسم</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 font-bold">&times;</button>
            </div>

            <form method="POST" action="{{ route('dept.staff.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">الاسم الأول *</label>
                        <input type="text" name="first_name" required class="w-full px-3 py-2 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">اسم الأب *</label>
                        <input type="text" name="father_name" required class="w-full px-3 py-2 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">اسم الجد *</label>
                        <input type="text" name="grandfather_name" required class="w-full px-3 py-2 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">اسم جد الأب واللقب</label>
                        <input type="text" name="surname" class="w-full px-3 py-2 border rounded-xl" placeholder="مثال: الدليمي">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">اللقب العلمي *</label>
                        <select name="academic_rank" required class="w-full px-3 py-2 border rounded-xl bg-white">
                            <option value="lecturer">مدرس (160%)</option>
                            <option value="assistant_lecturer">مدرس مساعد (180%)</option>
                            <option value="assistant_professor">أستاذ مساعد (140%)</option>
                            <option value="professor">أستاذ (100%)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">الشهادة</label>
                        <input type="text" name="degree" placeholder="دكتوراه / ماجستير" class="w-full px-3 py-2 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">التخصص العام</label>
                        <input type="text" name="general_specialization" class="w-full px-3 py-2 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">التخصص الدقيق</label>
                        <input type="text" name="specific_specialization" class="w-full px-3 py-2 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">رقم الهاتف</label>
                        <input type="text" name="mobile" class="w-full px-3 py-2 border rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">البريد الإلكتروني الرسمي</label>
                        <input type="email" name="email" class="w-full px-3 py-2 border rounded-xl">
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t pt-4">
                    <button type="button" @click="showModal = false" class="px-4 py-2 border rounded-xl text-xs font-bold text-gray-600">إلغاء</button>
                    <button type="submit" class="px-6 py-2 bg-anbar-900 text-white rounded-xl text-xs font-bold">حفظ التدريسي</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
