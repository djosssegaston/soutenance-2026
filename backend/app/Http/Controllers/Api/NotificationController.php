<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'all');

        return response()->json(
            UserNotification::where('user_id', $request->user()->id)
                ->when($type === 'unread', fn ($q) => $q->unread())
                ->when($type === 'read', fn ($q) => $q->read())
                ->when($type === 'archived', fn ($q) => $q->archived())
                ->when($type === 'all', fn ($q) => $q->notArchived())
                ->latest()
                ->get()
        );
    }

    public function markRead(Request $request, UserNotification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $notification->markAsRead();

        return response()->json($notification);
    }

    public function markAllRead(Request $request)
    {
        $userId = $request->user()->id;

        $updated = UserNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'message' => 'Toutes les notifications marquées comme lues',
            'updated_count' => $updated,
        ]);
    }

    public function unreadCount(Request $request)
    {
        $count = UserNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    public function archive(Request $request, UserNotification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $notification->archive();

        return response()->json(['message' => 'Notification archivée']);
    }

    public function restore(Request $request, UserNotification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $notification->restore();

        return response()->json(['message' => 'Notification restaurée']);
    }

    public function destroy(Request $request, UserNotification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification supprimée']);
    }

    public function adminIndex(Request $request)
    {
        $query = UserNotification::where('user_id', $request->user()->id);

        $type = $request->query('type');
        $read = $request->query('read');

        if ($type) {
            $query->where('type', $type);
        }

        if ($read === 'unread') {
            $query->where('is_read', false);
        } elseif ($read === 'read') {
            $query->where('is_read', true);
        }

        $notifications = $query->latest()->paginate(20);

        $today = now()->startOfDay();
        $stats = [
            'unread' => UserNotification::where('user_id', $request->user()->id)->where('is_read', false)->count(),
            'today' => UserNotification::where('user_id', $request->user()->id)->where('created_at', '>=', $today)->count(),
            'critical' => UserNotification::where('user_id', $request->user()->id)->where('type', 'critical')->count(),
            'month' => UserNotification::where('user_id', $request->user()->id)->whereMonth('created_at', now()->month)->count(),
        ];

        $data = $notifications->map(fn ($n) => [
            'id' => $n->id,
            'title' => $n->type,
            'subject' => $n->title,
            'message' => $n->content,
            'type' => $n->type,
            'read' => (bool) $n->is_read,
            'created_at' => $n->created_at,
        ]);

        return response()->json([
            'success' => true,
            'total' => $notifications->total(),
            'stats' => $stats,
            'data' => $data,
        ]);
    }

    public function adminMarkAllRead(Request $request)
    {
        UserNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications marquées comme lues.',
        ]);
    }

    public function adminMarkRead(Request $request, UserNotification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue.',
        ]);
    }

    public function adminArchive(Request $request, UserNotification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $notification->archive();

        return response()->json([
            'success' => true,
            'message' => 'Notification archivée.',
        ]);
    }

    public function adminDestroy(Request $request, UserNotification $notification)
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification supprimée.',
        ]);
    }
}
