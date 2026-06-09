<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthWebController extends Controller
{
    public function showLogin(Request $request)
    {
        return view('auth.login', [
            'prefill_email' => $request->get('email'),
            'prefill_role' => $request->get('role'),
        ]);
    }

    public function showInstitutionLogin(Request $request)
    {
        if (Auth::check()) {
            return redirect($this->redirectFor(Auth::user()->role));
        }

        return view('auth.institution-login', [
            'prefill_email' => $request->get('email'),
            'prefill_role' => 'institution',
        ]);
    }

    public function showPorteurLogin(Request $request)
    {
        if (Auth::check()) {
            return redirect($this->redirectFor(Auth::user()->role));
        }

        return view('auth.porteur-login', [
            'prefill_email' => $request->get('email'),
            'prefill_role' => 'porteur',
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = $credentials['identifier'];
        $password = $credentials['password'];
        $remember = (bool) $request->boolean('remember_me');

        $attempts = [];
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $attempts[] = ['email' => $identifier];
        }

        $normalizedPhone = preg_replace('/\D+/', '', $identifier);
        if ($normalizedPhone !== '') {
            $attempts[] = ['telephone' => $normalizedPhone];
        }
        if ($identifier !== $normalizedPhone) {
            $attempts[] = ['telephone' => $identifier];
        }

        $authenticated = false;
        foreach ($attempts as $attempt) {
            $attempt['password'] = $password;
            if (Auth::attempt($attempt, $remember)) {
                $authenticated = true;
                break;
            }
        }

        if (! $authenticated) {
            $message = 'Connexion impossible. Verifiez vos identifiants.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->withErrors(['identifier' => $message])->withInput();
        }

        // Bloquer les utilisateurs dont l'email n'est pas vérifié
        $user = Auth::user();
        if (! $user->hasVerifiedEmail()) {
            Auth::logout();

            $message = 'Veuillez confirmer votre adresse email avant de vous connecter.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }

            return redirect()->route('verification.notice')
                ->with('error', $message);
        }

        // Détruire les autres sessions de cet utilisateur (sécurité)
        $currentSessionId = $request->session()->getId();

        \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        $request->session()->regenerate();
        $redirect = $this->redirectFor($user->role);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Connexion reussie.', 'redirect' => $redirect]);
        }

        session()->flash('welcome', true);

        return redirect()->intended($redirect);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Deconnexion reussie.']);
        }

        return redirect()->route('login');
    }

    private function redirectFor(string $role): string
    {
        return match ($role) {
            'admin' => route('dashboard02.file', ['role' => 'admin', 'path' => 'index.php']),
            'institution' => route('dashboard02.file', ['role' => 'institution', 'path' => 'index.php']),
            default => route('dashboard02.file', ['role' => 'porteur', 'path' => 'index.php']),
        };
    }
}
