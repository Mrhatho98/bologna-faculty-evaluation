@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <section class="bg-white border border-gray-200 rounded-lg p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500">مركز الإشعارات</p>
            <h1 class="text-2xl font-bold text-anbar-900">الإشعارات</h1>
            <p class="text-sm text-gray-600 mt-1">متابعة إجراءات الاستمارات، الإرجاع، الاعتماد، وتقييمات الطلبة.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('notifications.index') }}" class="px-4 py-2 rounded text-sm font-bold {{ $status === 'all' ? 'bg-anbar-900 text-white' : 'bg-gray-100 text-gray-800' }}">الكل</a>
            <a href="{{ route('notifications.index', ['status' => 'unread']) }}" class="px-4 py-2 rounded text-sm font-bold {{ $status === 'unread' ? 'bg-anbar-900 text-white' : 'bg-gray-100 text-gray-800' }}">غير المقروء</a>
            <a href="{{ route('notifications.index', ['status' => 'read']) }}" class="px-4 py-2 rounded text-sm font-bold {{ $status === 'read' ? 'bg-anbar-900 text-white' : 'bg-gray-100 text-gray-800' }}">المقروء</a>
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button class="px-4 py-2 rounded bg-gold-600 text-white text-sm font-bold">تعليم الكل كمقروء</button>
            </form>
        </div>
    </section>

    <section class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="divide-y">
            @forelse($notifications as $notification)
                @php($data = $notification->data)
                <div class="p-4 {{ $notification->read_at ? 'bg-white' : 'bg-amber-50' }}">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="font-bold text-gray-900">{{ $data['title'] ?? 'إشعار' }}</h2>
                                @unless($notification->read_at)
                                    <span class="text-[11px] bg-gold-500 text-white rounded px-2 py-0.5">جديد</span>
                                @endunless
                            </div>
                            <p class="text-sm text-gray-700 mt-1">{{ $data['body'] ?? '' }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $notification->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if(!empty($data['url']))
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    <button class="px-3 py-2 rounded bg-anbar-900 text-white text-xs font-bold">فتح</button>
                                </form>
                            @elseif(!$notification->read_at)
                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                    @csrf
                                    <button class="px-3 py-2 rounded bg-gray-100 text-gray-800 text-xs font-bold">تعليم كمقروء</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">لا توجد إشعارات حالياً.</div>
            @endforelse
        </div>
        <div class="p-4">{{ $notifications->links() }}</div>
    </section>
</div>
@endsection
