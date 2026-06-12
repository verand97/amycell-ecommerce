<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get recent notifications and unread count for the admin dropdown.
     */
    public function index(Request $request)
    {
        $unreadCount = AdminNotification::unread()->count();
        
        $notifications = AdminNotification::orderByRaw('read_at IS NOT NULL ASC')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'type' => $notif->type,
                    'link' => route('admin.notifications.read', $notif->id),
                    'read_at' => $notif->read_at,
                    'created_at_human' => $notif->created_at->locale('id')->diffForHumans(),
                ];
            });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a notification as read and redirect to its admin action link.
     */
    public function markAsRead(AdminNotification $notification)
    {
        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        return redirect($notification->link);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllRead()
    {
        AdminNotification::unread()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi telah ditandai sebagai dibaca.',
        ]);
    }
}
