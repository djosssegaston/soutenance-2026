<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;

class InstitutionSecurityController extends Controller
{
    /**
     * Get security stats and sessions
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($session) {
                try {
                    $agent = new Agent;
                    $agent->setUserAgent($session->user_agent);
                    $device = $agent->isMobile() ? 'Mobile' : ($agent->isTablet() ? 'Tablette' : 'Ordinateur');
                    $browser = $agent->browser();
                    $platform = $agent->platform();
                } catch (\Throwable) {
                    $device = 'Inconnu';
                    $browser = 'Inconnu';
                    $platform = 'Inconnu';
                }

                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'is_current' => $session->id === session()->getId(),
                    'last_activity' => date('d M Y H:i', $session->last_activity),
                    'last_activity_ago' => \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    'device' => $device,
                    'browser' => $browser,
                    'platform' => $platform,
                ];
            });

        $stats = [
            'active_sessions' => $sessions->count(),
            'password_updated' => $user->updated_at->format('d M Y'),
            'failed_attempts' => AuditLog::where('user_id', $user->id)->where('action', 'LIKE', '%connect%')->where('action', 'LIKE', '%échou%')->count(),
            'total_logs' => AuditLog::where('user_id', $user->id)->count(),
        ];

        return response()->json([
            'sessions' => $sessions,
            'stats' => $stats,
            'audit_logs' => AuditLog::where('user_id', $user->id)->latest()->take(10)->get(),
        ]);
    }

    /**
     * Change password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Le mot de passe actuel est incorrect.'], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        AuditLog::log($user->id, 'Changement de mot de passe', 'bi-key', 'success', 'warning');

        return response()->json(['message' => 'Mot de passe mis à jour avec succès.']);
    }

    /**
     * Kill a specific session
     */
    public function killSession(Request $request, $id)
    {
        DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->delete();

        AuditLog::log($request->user()->id, 'Session révoquée', 'bi-x-circle', 'danger', 'info');

        return response()->json(['message' => 'Session terminée.']);
    }

    /**
     * Get password history
     */
    public function passwordHistory(Request $request)
    {
        $logs = AuditLog::where('user_id', $request->user()->id)
            ->where('action', 'LIKE', '%mot de passe%')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($log) {
                return [
                    'date' => $log->date->format('d M Y H:i'),
                    'ip' => $log->ip,
                ];
            });

        return response()->json($logs);
    }
}
