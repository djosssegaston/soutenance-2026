<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AdminAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('action', 'like', "%{$s}%")
                ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$s}%"));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('level')) {
            $query->where('type', $request->level);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $today = now()->startOfDay();
        $stats = [
            'today' => (clone $query)->where('created_at', '>=', $today)->count(),
            'validations' => (clone $query)->where('action', 'like', '%valid%')->count(),
            'rejections' => (clone $query)->where('action', 'like', '%rejet%')->count(),
            'security' => (clone $query)->where('type', 'critical')->count(),
        ];

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        $data = $logs->map(fn ($l) => [
            'id' => $l->id,
            'created_at' => $l->created_at,
            'user_name' => $l->user?->name ?? 'Système',
            'user_role' => $l->user?->role ?? '',
            'action' => $l->action,
            'target' => $l->action ?? '',
            'level' => $l->type ?? 'info',
            'ip_address' => $l->ip,
        ]);

        return response()->json([
            'success' => true,
            'total' => $logs->total(),
            'stats' => $stats,
            'data' => $data,
        ]);
    }
}
