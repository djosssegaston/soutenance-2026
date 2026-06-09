<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class InstitutionNotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated institution user
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $tab = $request->query('tab', 'all');

        $query = UserNotification::where('user_id', $user->id);

        if ($tab === 'unread') {
            $query->unread()->notArchived();
        } elseif ($tab === 'archived') {
            $query->archived();
        } else {
            $query->notArchived();
        }

        $notifications = $query->latest()->paginate(10);

        $stats = [
            'unread' => UserNotification::where('user_id', $user->id)->unread()->notArchived()->count(),
            'read' => UserNotification::where('user_id', $user->id)->read()->notArchived()->count(),
            'archived' => UserNotification::where('user_id', $user->id)->archived()->count(),
            'total' => UserNotification::where('user_id', $user->id)->count(),
        ];

        return response()->json([
            'notifications' => $notifications,
            'stats' => $stats,
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = UserNotification::where('user_id', $request->user()->id)->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marquée comme lue.']);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead(Request $request)
    {
        UserNotification::where('user_id', $request->user()->id)
            ->unread()
            ->update(['is_read' => true]);

        return response()->json(['message' => 'Toutes les notifications ont été marquées comme lues.']);
    }

    /**
     * Archive a notification
     */
    public function archive(Request $request, $id)
    {
        $notification = UserNotification::where('user_id', $request->user()->id)->findOrFail($id);
        $notification->archive();

        return response()->json(['message' => 'Notification archivée.']);
    }

    /**
     * Delete a notification
     */
    public function destroy(Request $request, $id)
    {
        $notification = UserNotification::where('user_id', $request->user()->id)->findOrFail($id);
        $notification->delete();

        return response()->json(['message' => 'Notification supprimée.']);
    }
}
