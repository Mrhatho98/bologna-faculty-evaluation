@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="bg-white border border-gray-200 rounded-lg p-6">
        <h1 class="text-2xl font-bold text-anbar-900">سجل التدقيق</h1>
    </section>

    <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600"><tr><th class="p-3">الوقت</th><th class="p-3">المستخدم</th><th class="p-3">الدور</th><th class="p-3">الإجراء</th><th class="p-3">الوحدة</th><th class="p-3">السجل</th><th class="p-3">مختصر التغيير</th></tr></thead>
            <tbody class="divide-y">
                @foreach($logs as $log)
                    <tr>
                        <td class="p-3">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        <td class="p-3">{{ $log->user_name }}</td>
                        <td class="p-3">{{ $log->role }}</td>
                        <td class="p-3 font-bold">{{ $log->action }}</td>
                        <td class="p-3">{{ $log->module }}</td>
                        <td class="p-3">{{ $log->record_id }}</td>
                        <td class="p-3 text-xs text-gray-600">
                            @php
                                $oldScores = $log->old_values_json['evaluation_scores'] ?? null;
                                $newScores = $log->new_values_json['evaluation_scores'] ?? null;
                            @endphp
                            @if($oldScores && $newScores)
                                <div>المجموع: {{ number_format((float)$oldScores['total_score'], 2) }} ← {{ number_format((float)$newScores['total_score'], 2) }}</div>
                                <div>المحاور: {{ number_format((float)$oldScores['score_axis_2'], 2) }}/{{ number_format((float)$oldScores['score_axis_3'], 2) }}/{{ number_format((float)$oldScores['score_axis_4'], 2) }} ← {{ number_format((float)$newScores['score_axis_2'], 2) }}/{{ number_format((float)$newScores['score_axis_3'], 2) }}/{{ number_format((float)$newScores['score_axis_4'], 2) }}</div>
                            @elseif(!empty($log->new_values_json['comments']))
                                {{ $log->new_values_json['comments'] }}
                            @elseif(!empty($log->new_values_json['total_score']))
                                المجموع: {{ number_format((float)$log->new_values_json['total_score'], 2) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $logs->links() }}</div>
    </section>
</div>
@endsection
