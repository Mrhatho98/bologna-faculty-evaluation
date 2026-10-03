<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->notifications()->latest();
        if ($request->input('status') === 'unread') {
            $query->whereNull('read_at');
        } elseif ($request->input('status') === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->paginate(20)->withQueryString();

        return view('notifications.index', [
            'notifications' => $notifications,
            'status' => $request->input('status', 'all'),
        ]);
    }

    public function markAsRead(string $notificationId)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $notificationId)
            ->firstOrFail();

        $notification->markAsRead();

        $url = $notification->data['url'] ?? null;
        if ($url) {
            return redirect($url);
        }

        return back()->with('success', 'تم تعليم الإشعار كمقروء.');
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'تم تعليم جميع الإشعارات كمقروءة.');
    }
}
