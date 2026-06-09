<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', 'string', Rule::in(['porteur', 'institution'])],
            'telephone' => 'nullable|string|max:30|unique:users,telephone',

            'institution_name' => 'required_if:role,institution|string|max:255',
            'institution_email' => 'nullable|email',
            'institution_phone' => 'nullable|string|max:30',
            'institution_address' => 'required_if:role,institution|string|max:255',
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'telephone' => $data['telephone'] ?? null,
                'statut' => 'actif',
            ]);

            if ($data['role'] === 'institution') {
                Institution::create([
                    'user_id' => $user->id,
                    'nom' => $data['institution_name'],
                    'email' => $data['institution_email'] ?? $data['email'],
                    'telephone' => $data['institution_phone'] ?? $data['telephone'] ?? null,
                    'adresse' => $data['institution_address'],
                    'statut' => 'actif',
                ]);
            }

            return $user;
        });

        // La creation du projet se fera apres la confirmation de compte.

        event(new Registered($user));

        return response()->json([
            'message' => 'Inscription reussie. Un email de confirmation a ete envoye.',
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $identifier = trim($data['identifier']);
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            $user = User::where('email', $identifier)->first();
        } else {
            $normalizedPhone = preg_replace('/\D+/', '', $identifier);
            if (str_starts_with($identifier, '+')) {
                $normalizedPhone = '+'.$normalizedPhone;
            }
            $user = User::where('telephone', $normalizedPhone)->first();
        }

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => ['Identifiants invalides.'],
            ]);
        }

        if (! $user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Veuillez confirmer votre adresse email avant de vous connecter.',
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function resendVerification(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            return response()->json([
                'message' => 'Aucun compte ne correspond a cet email.',
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Cet email est deja confirme.',
            ], 200);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Email de confirmation renvoye. Verifiez votre boite de reception.',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Deconnecte']);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => $user,
        ]);
    }
}
