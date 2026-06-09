<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    private const ALLOWED_PROVIDERS = ['google', 'facebook', 'twitter-oauth-2'];

    public function redirect(string $provider, Request $request)
    {
        if (! in_array($provider, self::ALLOWED_PROVIDERS, true)) {
            abort(404);
        }

        $role = $request->query('role', 'porteur');

        session(['social_login_role' => $role]);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider, Request $request)
    {
        if (! in_array($provider, self::ALLOWED_PROVIDERS, true)) {
            abort(404);
        }

        if ($request->missing('code')) {
            return redirect()->route('login')->withErrors(['social' => 'Connexion sociale annulée.']);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            Log::error('Social auth error: '.$e->getMessage());

            return redirect()->route('login')->withErrors(['social' => 'Erreur lors de la connexion sociale.']);
        }

        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if (! $user) {
            $user = User::where('email', $socialUser->getEmail())->first();
        }

        $role = session()->pull('social_login_role', 'porteur');

        if (! $user) {
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Utilisateur',
                'email' => $socialUser->getEmail(),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'password' => bcrypt(str()->random(32)),
                'role' => $role,
                'statut' => 'actif',
            ]);

            if ($socialUser->getEmail()) {
                $user->markEmailAsVerified();
            }
        } else {
            $user->update([
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        $redirect = match ($user->role) {
            'admin' => route('dashboard02.file', ['role' => 'admin', 'path' => 'index.php']),
            'institution' => route('dashboard02.file', ['role' => 'institution', 'path' => 'index.php']),
            default => route('dashboard02.file', ['role' => 'porteur', 'path' => 'index.php']),
        };

        return redirect()->intended($redirect);
    }
}
