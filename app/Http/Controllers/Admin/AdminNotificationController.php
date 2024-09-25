<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Notifications\DatabaseNotification;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->user_type == 1) {
            // For admin users, fetch all notifications
            $notifications = DatabaseNotification::orderBy('created_at', 'desc')->paginate(10);
            $unreadCount = DatabaseNotification::whereNull('read_at')->count();
        } else {
            // For non-admin users, fetch only their notifications
            $notifications = $user->notifications()->orderBy('created_at', 'desc')->paginate(10);
            $unreadCount = $user->unreadNotifications()->count();
            // dd($unreadCount);

        }

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead($id)
    {
        $notification = DatabaseNotification::findOrFail($id);
        $notification->markAsRead();
        return redirect()->back()->with('success', 'Notification marked as read');
    }

    public function markAllAsRead()
    {
        $user = auth()->user();
        if ($user->user_type === 1) {
            DatabaseNotification::whereNull('read_at')->update(['read_at' => now()]);
        } else {
            $user->unreadNotifications->markAsRead();
        }
        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    public function destroy($id)
    {
        $notification = DatabaseNotification::findOrFail($id);
        $notification->delete();
        return redirect()->back()->with('success', 'Notification deleted');
    }

    public function getLatestNotifications()
    {
        $user = auth()->user();
        if ($user->user_type == 1) {
            $latestNotifications = DatabaseNotification::latest()->take(5)->get();
            $unreadCount = DatabaseNotification::whereNull('read_at')->count();
        } else {
            $latestNotifications = $user->notifications()->latest()->take(5)->get();
            $unreadCount = $user->unreadNotifications()->count();
        }

        return response()->json([
            'notifications' => $latestNotifications,
            'unreadCount' => $unreadCount
        ]);
    }

    public function viewNotification($id){
        $notification = DatabaseNotification::findOrFail($id);
        $notification->markAsRead();
        return view('admin.notifications.show', compact('notification'));
    }
}
