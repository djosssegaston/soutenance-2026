<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SecurityController extends Controller
{
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Mot de passe actuel incorrect'], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'password_change',
            'ip' => $request->ip(),
            'date' => now(),
        ]);

        UserNotification::create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Mot de passe modifié',
            'content' => 'Votre mot de passe a été modifié avec succès.',
            'is_read' => false,
        ]);

        return response()->json(['message' => 'Mot de passe modifié avec succès']);
    }

    public function sessions(Request $request)
    {
        $sessions = DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                $agent = $this->parseUserAgent($session->user_agent ?? '');

                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address ?? 'N/A',
                    'device' => $agent['device'],
                    'browser' => $agent['browser'],
                    'platform' => $agent['platform'],
                    'last_activity' => $session->last_activity,
                    'last_activity_ago' => $this->timeAgo($session->last_activity),
                    'is_current' => $session->id === session()->getId(),
                ];
            });

        return response()->json($sessions);
    }

    public function killSession(Request $request, string $sessionId)
    {
        $session = DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $session) {
            return response()->json(['message' => 'Session introuvable'], 404);
        }

        if ($session->id === session()->getId()) {
            return response()->json(['message' => 'Impossible de déconnecter votre session actuelle'], 422);
        }

        DB::table('sessions')->where('id', $sessionId)->delete();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'session_killed',
            'ip' => $request->ip(),
            'date' => now(),
        ]);

        return response()->json(['message' => 'Session déconnectée']);
    }

    public function auditLogs(Request $request)
    {
        $logs = AuditLog::where('user_id', $request->user()->id)
            ->orWhereNull('user_id')
            ->latest()
            ->limit(50)
            ->get()
            ->map(function ($log) {
                $labels = [
                    'login' => ['label' => 'Connexion', 'icon' => 'bi-box-arrow-in-right', 'color' => 'success'],
                    'logout' => ['label' => 'Déconnexion', 'icon' => 'bi-box-arrow-right', 'color' => 'secondary'],
                    'password_change' => ['label' => 'Changement mot de passe', 'icon' => 'bi-key', 'color' => 'warning'],
                    'session_killed' => ['label' => 'Session terminée', 'icon' => 'bi-x-circle', 'color' => 'danger'],
                    'failed_login' => ['label' => 'Tentative échouée', 'icon' => 'bi-shield-exclamation', 'color' => 'danger'],
                    'document_view' => ['label' => 'Document consulté', 'icon' => 'bi-file-earmark', 'color' => 'info'],
                    'document_download' => ['label' => 'Document téléchargé', 'icon' => 'bi-download', 'color' => 'primary'],
                    '2fa_enabled' => ['label' => '2FA activé', 'icon' => 'bi-shield-check', 'color' => 'success'],
                    '2fa_disabled' => ['label' => '2FA désactivé', 'icon' => 'bi-shield', 'color' => 'warning'],
                ];
                $action = $labels[$log->action] ?? ['label' => $log->action, 'icon' => 'bi-clock', 'color' => 'secondary'];

                return [
                    'id' => $log->id,
                    'action' => $action['label'],
                    'icon' => $action['icon'],
                    'color' => $action['color'],
                    'ip' => $log->ip ?? 'N/A',
                    'date' => $log->date ? $log->date->format('d M Y H:i') : ($log->created_at ? $log->created_at->format('d M Y H:i') : 'N/A'),
                ];
            });

        return response()->json($logs);
    }

    public function passwordStrength(Request $request)
    {
        $request->validate(['password' => 'required|string']);
        $score = 0;
        $password = $request->password;
        $checks = [];

        if (strlen($password) >= 8) {
            $score += 20;
            $checks[] = ['pass' => true, 'text' => '8 caractères minimum'];
        } else {
            $checks[] = ['pass' => false, 'text' => '8 caractères minimum'];
        }

        if (preg_match('/[A-Z]/', $password)) {
            $score += 20;
            $checks[] = ['pass' => true, 'text' => 'Une majuscule'];
        } else {
            $checks[] = ['pass' => false, 'text' => 'Une majuscule'];
        }

        if (preg_match('/[a-z]/', $password)) {
            $score += 20;
            $checks[] = ['pass' => true, 'text' => 'Une minuscule'];
        } else {
            $checks[] = ['pass' => false, 'text' => 'Une minuscule'];
        }

        if (preg_match('/[0-9]/', $password)) {
            $score += 20;
            $checks[] = ['pass' => true, 'text' => 'Un chiffre'];
        } else {
            $checks[] = ['pass' => false, 'text' => 'Un chiffre'];
        }

        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $score += 20;
            $checks[] = ['pass' => true, 'text' => 'Un caractère spécial'];
        } else {
            $checks[] = ['pass' => false, 'text' => 'Un caractère spécial'];
        }

        $level = 'faible';
        $levelColor = 'danger';
        if ($score >= 80) {
            $level = 'très fort';
            $levelColor = 'success';
        } elseif ($score >= 60) {
            $level = 'fort';
            $levelColor = 'primary';
        } elseif ($score >= 40) {
            $level = 'moyen';
            $levelColor = 'warning';
        }

        return response()->json([
            'score' => $score,
            'level' => $level,
            'level_color' => $levelColor,
            'checks' => $checks,
        ]);
    }

    public function passwordHistory(Request $request)
    {
        return response()->json(
            AuditLog::where('user_id', $request->user()->id)
                ->where('action', 'password_change')
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn ($log) => [
                    'date' => $log->date ? $log->date->format('d M Y H:i') : ($log->created_at ? $log->created_at->format('d M Y H:i') : 'N/A'),
                    'ip' => $log->ip ?? 'N/A',
                ])
        );
    }

    private function parseUserAgent(?string $ua): array
    {
        $device = 'Inconnu';
        $browser = 'Inconnu';
        $platform = 'Inconnu';

        if (! $ua) {
            return compact('device', 'browser', 'platform');
        }

        if (stripos($ua, 'mobile') !== false || stripos($ua, 'android') !== false || stripos($ua, 'iphone') !== false) {
            $device = 'Mobile';
        } elseif (stripos($ua, 'tablet') !== false || stripos($ua, 'ipad') !== false) {
            $device = 'Tablette';
        } else {
            $device = 'Ordinateur';
        }

        if (stripos($ua, 'windows') !== false) {
            $platform = 'Windows';
        } elseif (stripos($ua, 'mac') !== false) {
            $platform = 'macOS';
        } elseif (stripos($ua, 'linux') !== false) {
            $platform = 'Linux';
        } elseif (stripos($ua, 'android') !== false) {
            $platform = 'Android';
        } elseif (stripos($ua, 'iphone') !== false || stripos($ua, 'ipad') !== false) {
            $platform = 'iOS';
        }

        if (stripos($ua, 'firefox') !== false) {
            $browser = 'Firefox';
        } elseif (stripos($ua, 'chrome') !== false) {
            $browser = 'Chrome';
        } elseif (stripos($ua, 'safari') !== false) {
            $browser = 'Safari';
        } elseif (stripos($ua, 'edge') !== false) {
            $browser = 'Edge';
        } elseif (stripos($ua, 'opera') !== false) {
            $browser = 'Opera';
        }

        return compact('device', 'browser', 'platform');
    }

    private function timeAgo(int $timestamp): string
    {
        $diff = time() - $timestamp;
        if ($diff < 60) {
            return 'À l\'instant';
        }
        if ($diff < 3600) {
            return floor($diff / 60).' min';
        }
        if ($diff < 86400) {
            return floor($diff / 3600).'h';
        }

        return floor($diff / 86400).'j';
    }
}
