<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TwoFactorAuthController extends Controller
{
    public function status(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'enabled' => ! is_null($user->two_factor_secret),
                'confirmed_at' => $user->two_factor_confirmed_at?->format('d/m/Y H:i'),
                'methods' => $user->two_factor_secret ? ['app'] : [],
            ],
        ]);
    }

    public function enable(Request $request)
    {
        $user = $request->user();

        if ($user->two_factor_secret) {
            return response()->json(['success' => false, 'message' => '2FA déjà activé.'], 422);
        }

        $secret = $this->generateSecret();
        $backupCodes = $this->generateBackupCodes();

        $user->update([
            'two_factor_secret' => $secret,
            'two_factor_backup_codes' => json_encode($backupCodes),
            'two_factor_confirmed_at' => null,
        ]);

        AuditLog::log($user->id, '2FA activé', 'bi-shield-check', 'success', 'warning');

        return response()->json([
            'success' => true,
            'message' => '2FA activé. Veuillez vérifier votre application d\'authentification.',
            'data' => [
                'secret' => $secret,
                'qr_url' => $this->getQrUrl($user->email, $secret),
                'backup_codes' => $backupCodes,
            ],
        ]);
    }

    public function confirm(Request $request)
    {
        $user = $request->user();

        if (! $user->two_factor_secret) {
            return response()->json(['success' => false, 'message' => 'Veuillez d\'abord activer 2FA.'], 422);
        }

        if ($user->two_factor_confirmed_at) {
            return response()->json(['success' => false, 'message' => '2FA déjà confirmé.'], 422);
        }

        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        if (! $this->verifyCode($user->two_factor_secret, $request->code)) {
            AuditLog::log($user->id, '2FA: code de confirmation invalide', 'bi-shield-exclamation', 'danger', 'warning');

            return response()->json(['success' => false, 'message' => 'Code invalide.'], 422);
        }

        $user->update(['two_factor_confirmed_at' => now()]);

        AuditLog::log($user->id, '2FA confirmé', 'bi-shield-check', 'success', 'info');

        return response()->json([
            'success' => true,
            'message' => '2FA confirmé avec succès.',
        ]);
    }

    public function disable(Request $request)
    {
        $user = $request->user();

        if (! $user->two_factor_secret) {
            return response()->json(['success' => false, 'message' => '2FA n\'est pas activé.'], 422);
        }

        $request->validate([
            'password' => 'required|string',
        ]);

        if (! Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Mot de passe incorrect.'], 422);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_backup_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);

        AuditLog::log($user->id, '2FA désactivé', 'bi-shield', 'warning', 'warning');

        return response()->json([
            'success' => true,
            'message' => '2FA désactivé.',
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();
        $code = $request->code;

        if (! $user->two_factor_secret) {
            return response()->json(['success' => false, 'message' => '2FA n\'est pas activé.'], 422);
        }

        // Check TOTP code
        if ($this->verifyCode($user->two_factor_secret, $code)) {
            return response()->json(['success' => true, 'message' => 'Code valide.']);
        }

        // Check backup code
        $backupCodes = json_decode($user->two_factor_backup_codes ?? '[]', true);
        $codeIndex = array_search($code, $backupCodes);
        if ($codeIndex !== false) {
            unset($backupCodes[$codeIndex]);
            $user->update(['two_factor_backup_codes' => json_encode(array_values($backupCodes))]);

            return response()->json([
                'success' => true,
                'message' => 'Code de secours valide. Ce code a été utilisé et ne pourra plus être réutilisé.',
            ]);
        }

        AuditLog::log($user->id, '2FA: code invalide', 'bi-shield-exclamation', 'danger', 'warning');

        return response()->json(['success' => false, 'message' => 'Code invalide.'], 422);
    }

    public function recoveryCodes(Request $request)
    {
        $user = $request->user();

        if (! $user->two_factor_secret) {
            return response()->json(['success' => false, 'message' => '2FA n\'est pas activé.'], 422);
        }

        $request->validate([
            'password' => 'required|string',
        ]);

        if (! Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Mot de passe incorrect.'], 422);
        }

        $existingCodes = json_decode($user->two_factor_backup_codes ?? '[]', true);
        $remainingCount = count($existingCodes);

        if ($remainingCount === 0) {
            $newCodes = $this->generateBackupCodes();
            $user->update(['two_factor_backup_codes' => json_encode($newCodes)]);

            AuditLog::log($user->id, '2FA: nouveaux codes de secours générés', 'bi-key', 'info', 'info');

            return response()->json([
                'success' => true,
                'message' => 'Nouveaux codes de secours générés.',
                'data' => ['backup_codes' => $newCodes],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'backup_codes' => $existingCodes,
                'remaining' => $remainingCount,
            ],
        ]);
    }

    private function generateSecret(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';

        for ($i = 0; $i < 16; $i++) {
            $secret .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $secret;
    }

    private function generateBackupCodes(): array
    {
        $codes = [];

        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        }

        return $codes;
    }

    private function verifyCode(string $secret, string $code): bool
    {
        $code = trim($code);

        // Check against current 30-second window and adjacent windows
        $counter = floor(now()->timestamp / 30);

        for ($i = -1; $i <= 1; $i++) {
            $expected = $this->generateTotp($secret, $counter + $i);

            if (hash_equals($expected, $code)) {
                return true;
            }
        }

        return false;
    }

    private function generateTotp(string $secret, int $counter): string
    {
        $base32 = new \App\Support\Base32;
        $key = $base32->decode($secret);
        $counterBytes = pack('N*', 0).pack('N*', $counter);
        $hash = hash_hmac('sha1', $counterBytes, $key, true);
        $offset = ord($hash[19]) & 0xF;
        $code = (
            (ord($hash[$offset]) & 0x7F) << 24 |
            (ord($hash[$offset + 1]) & 0xFF) << 16 |
            (ord($hash[$offset + 2]) & 0xFF) << 8 |
            (ord($hash[$offset + 3]) & 0xFF)
        ) % 1000000;

        return str_pad((string) $code, 6, '0', STR_PAD_LEFT);
    }

    private function getQrUrl(string $email, string $secret): string
    {
        $issuer = urlencode(config('app.name', 'ALOGOTO'));
        $label = urlencode($email);

        return "otpauth://totp/{$issuer}:{$label}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";
    }
}
