<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user(); // أو auth()->user()

        // استرجاع الإشعارات مع بيانات إضافية إذا لزم الأمر
        $notifications = $user->notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->data['message'] ?? 'لا توجد رسالة',
                'created_at' => $notification->created_at,
                'read_at' => $notification->read_at,
            ];
        });

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['message' => 'تم تعليم الإشعارات كمقروءة']);
    }
}
