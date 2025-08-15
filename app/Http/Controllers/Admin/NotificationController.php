<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(10);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->notifiable_id == auth()->id()) {
            $notification->markAsRead();
        }
        return back();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'تم وضع علامة على جميع الإشعارات كمقروءة');
    }

    public function destroy(Notification $notification)
    {
        if ($notification->notifiable_id == auth()->id()) {
            $notification->delete();
        }
        return back()->with('success', 'تم حذف الإشعار بنجاح');
    }
}