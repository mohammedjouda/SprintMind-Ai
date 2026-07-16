<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $notifications = $user->notifications()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark the notification as read and redirect to its action URL.
     */
    public function readAndGo(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        $actionUrl = $notification->data['action_url'] ?? route('dashboard');

        if (str_starts_with($actionUrl, 'http://') || str_starts_with($actionUrl, 'https://')) {
            $parsed = parse_url($actionUrl);
            $path = $parsed['path'] ?? '';
            $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';
            $actionUrl = url($path . $query . $fragment);
        } else {
            $actionUrl = url($actionUrl);
        }

        return redirect($actionUrl);
    }

    /**
     * Mark the specified notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()
            ->unreadNotifications()
            ->findOrFail($id);

        $notification->markAsRead();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تعيين التنبيه كمقروء'
            ]);
        }

        return back()->with('success', 'تم قراءة التنبيه.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'تم تعيين جميع التنبيهات كمقروءة.');
    }

    /**
     * Remove the specified notification.
     */
    public function destroy(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        return back()->with('success', 'تم حذف التنبيه.');
    }
}
