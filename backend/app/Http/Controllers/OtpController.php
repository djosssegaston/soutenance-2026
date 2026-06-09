<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller
{
    private const OTP_CACHE_PREFIX = 'otp_';

    private const OTP_EXPIRY_MINUTES = 5;

    private const CODE_LENGTH = 4;

    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string'],
        ]);

        $phone = $this->normalizePhone($request->input('phone'));

        $user = User::where('telephone', $phone)->first();
        if (! $user) {
            return response()->json([
                'message' => 'Aucun compte trouvé avec ce numéro de téléphone.',
            ], 422);
        }

        $code = (string) random_int(10 ** (self::CODE_LENGTH - 1), (10 ** self::CODE_LENGTH) - 1);

        Cache::put(self::OTP_CACHE_PREFIX.$phone, [
            'code' => $code,
            'user_id' => $user->id,
        ], now()->addMinutes(self::OTP_EXPIRY_MINUTES));

        Log::info("OTP pour {$phone}: {$code}");

        return response()->json([
            'message' => 'Code de vérification envoyé par SMS.',
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'code' => ['required', 'string', 'size:'.self::CODE_LENGTH],
        ]);

        $phone = $this->normalizePhone($request->input('phone'));
        $code = $request->input('code');

        $cached = Cache::get(self::OTP_CACHE_PREFIX.$phone);

        if (! $cached || $cached['code'] !== $code) {
            return response()->json([
                'message' => 'Code invalide ou expiré. Veuillez demander un nouveau code.',
            ], 422);
        }

        Cache::forget(self::OTP_CACHE_PREFIX.$phone);

        $user = User::find($cached['user_id']);
        if (! $user) {
            return response()->json([
                'message' => 'Utilisateur introuvable.',
            ], 422);
        }

        if (! $user->telephone_verified_at) {
            $user->telephone_verified_at = now();
            $user->save();
        }

        Auth::login($user, true);

        $request->session()->regenerate();

        $redirect = match ($user->role) {
            'admin' => route('dashboard02.file', ['role' => 'admin', 'path' => 'index.php']),
            'institution' => route('dashboard02.file', ['role' => 'institution', 'path' => 'index.php']),
            default => route('dashboard02.file', ['role' => 'porteur', 'path' => 'index.php']),
        };

        return response()->json([
            'message' => 'Connexion réussie.',
            'redirect' => $redirect,
        ]);
    }

    private function normalizePhone(string $phone): string
    {
        $phone = trim($phone);
        if (str_starts_with($phone, '+')) {
            return '+'.preg_replace('/\D+/', '', substr($phone, 1));
        }

        return preg_replace('/\D+/', '', $phone);
    }
}
