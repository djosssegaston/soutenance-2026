<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Get current user profile
     */
    public function show(Request $request)
    {
        $user = $request->user();

        // Charger les données supplémentaires selon le rôle
        $profileData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->telephone ?? '',
            'role' => $user->role,
            'created_at' => $user->created_at->format('d/m/Y'),
        ];

        // Données spécifiques selon le rôle
        if ($user->role === 'institution') {
            $institution = $user->institution;
            if ($institution) {
                $profileData['institution_name'] = $institution->nom;
                $profileData['institution_address'] = $institution->adresse;
                $profileData['institution_phone'] = $institution->telephone;
            }
        }

        return response()->json($profileData);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'telephone' => [
                'sometimes', 'string', 'max:20',
                Rule::unique('users', 'telephone')->ignore($user->id),
            ],
            'phone' => [
                'sometimes', 'string', 'max:20',
                Rule::unique('users', 'telephone')->ignore($user->id),
            ],
        ]);

        $updates = [];
        if (array_key_exists('name', $validated)) {
            $updates['name'] = $validated['name'];
        }

        if (array_key_exists('telephone', $validated) || array_key_exists('phone', $validated)) {
            $updates['telephone'] = $validated['telephone'] ?? $validated['phone'];
        }

        if ($updates !== []) {
            try {
                $user->update($updates);
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() === '23000') {
                    return response()->json([
                        'message' => 'Ce numéro de téléphone est déjà utilisé par un autre compte.',
                    ], 422);
                }

                return response()->json([
                    'message' => 'Erreur lors de la mise à jour du profil.',
                ], 500);
            }
        }

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->telephone,
            ],
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Vérifier le mot de passe actuel
        if (! Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Mot de passe actuel incorrect',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => 'Mot de passe modifié avec succès',
        ]);
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if (! Schema::hasColumn('users', 'avatar_url')) {
            return response()->json([
                'message' => 'Le stockage d’avatar n’est pas encore configuré dans le schéma utilisateur.',
            ], 422);
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar_url' => Storage::url($path)]);
        }

        return response()->json([
            'message' => 'Avatar téléchargé avec succès',
            'avatar_url' => $user->avatar_url,
        ]);
    }
}
