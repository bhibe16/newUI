<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    /**
     * Display all notifications.
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications; // Fetch Laravel notifications
        $employee = Employee::where('user_id', $user->id)->first(); // Get employee details

        // Fetch API notifications (termination notifications)
        $response = Http::get('https://hr1.gwamerchandise.com/api/resigned');

        // Check if the request was successful
        $terminationNotifications = $response->successful() ? $response->json() : [];

        return view('admin.notifications', compact('notifications', 'employee', 'terminationNotifications'));
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete selected notifications.
     */
    public function deleteSelected(Request $request)
    {
        $notificationIds = explode(',', $request->selected_notifications);
        Auth::user()->notifications()->whereIn('id', $notificationIds)->delete();
        return back()->with('success', 'Selected notifications deleted.');
    }
}
