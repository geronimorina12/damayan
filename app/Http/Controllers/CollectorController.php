<?php

namespace App\Http\Controllers;

use App\Models\DeathReportModel;
use App\Models\NotificationModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CollectorController extends Controller
{
    public function index()
    {
        return Inertia::render('collector/dashboard/Home');
    }
    public function hasNotification()
    {
          $userId = Auth::id();

        // Fetch all notifications for this user (you can modify orderBy or limit if needed)
        $notifications = NotificationModel::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Return response with the notification data
        return response()->json([
            'status' => $notifications->isNotEmpty(),
            'notifications' => $notifications
        ]);
    }
    public function markAsRead($id)
    {
        $notification = NotificationModel::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        $notification->read_status = true;
        $notification->save();

        return response()->json(['message' => 'Notification marked as read successfully']);
    }
}
