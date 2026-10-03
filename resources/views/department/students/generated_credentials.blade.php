@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between border-b pb-4 mb-4">
            <div>
                <span class="inline-block bg-green-100 text-green-800 text-xs px-2.5 py-0.5 rounded-full font-bold mb-1">تم إنشاء الحسابات بنجاح</span>
                <h1 class="text-xl font-bold text-anbar-900">سجل بيانات الدخول المنشأة للطلبة المقيّمين</h1>
                <p class="text-xs text-gray-500 mt-1">يمكنك طباعة هذه القائمة أو نسخها لتسليم البيانات للطلبة. لن يتم إظهار كلمات المرور مرة أخرى لدواعي الأمان.</p>
            </div>
            <button onclick="window.print()" class="bg-anbar-900 text-white font-bold text-xs px-4 py-2 rounded-xl no-print">طباعة القائمة</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-sm">
                <thead class="bg-gray-50 text-gray-700 text-xs font-bold border-b">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">اسم الطالب</th>
                        <th class="py-3 px-4">الرقم الجامعي</th>
                        <th class="py-3 px-4">اسم المستخدم (Username)</th>
                        <th class="py-3 px-4">كلمة المرور الأولية (Password)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($generated as $index => $row)
                        <tr>
                            <td class="py-3 px-4 font-bold text-gray-400">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-gray-900">{{ $row['name'] }}</td>
                            <td class="py-3 px-4 text-xs font-mono text-gray-600">{{ $row['student_id_number'] }}</td>
                            <td class="py-3 px-4 text-xs font-mono font-bold text-anbar-900 bg-gray-50">{{ $row['username'] }}</td>
                            <td class="py-3 px-4 text-xs font-mono font-bold text-red-600 bg-red-50/50">{{ $row['plain_password'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 border-t pt-4 flex justify-between items-center no-print">
            <a href="{{ route('dept.dashboard') }}" class="text-xs font-bold text-anbar-900">العودة للوحة تحكم القسم</a>
        </div>
    </div>
</div>
@endsection
