<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSecurityController extends Controller
{
    public function index()
    {
        $sessions = DB::table('sessions')
            ->where('last_activity', '>=', now()->subHours(2)->timestamp)
            ->select('user_id')->distinct()
            ->count('user_id');
        $failedLogins = AuditLog::where('action', 'like', '%échec%')
            ->where('created_at', '>=', now()->subDay())
            ->count();
        $blocked = User::where('statut', 'suspendu')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'sessions' => $sessions,
                'failed_logins' => $failedLogins,
                'blocked' => $blocked,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'password_min_length' => 'nullable|integer|min:6|max:64',
            'max_login_attempts' => 'nullable|integer|min:1|max:20',
            'session_lifetime' => 'nullable|integer|min:15|max:1440',
            'require_2fa' => 'nullable|boolean',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => 'security_'.$key],
                ['value' => $value]
            );
        }

        AuditLog::log($request->user()->id, 'Politique de sécurité mise à jour', 'bi-shield', 'success', 'warning');

        return response()->json([
            'success' => true,
            'message' => 'Politique de sécurité mise à jour.',
        ]);
    }
}
