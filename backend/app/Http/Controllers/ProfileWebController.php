<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileWebController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('institution');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'telephone' => $user->telephone,
            'adresse' => $user->adresse,
            'activite' => $user->activite,
            'avatar_url' => $user->avatar_url,
            'entreprise_nom' => $user->entreprise_nom,
            'entreprise_secteur' => $user->entreprise_secteur,
            'email_verified' => ! is_null($user->email_verified_at),
            'telephone_verified' => ! is_null($user->telephone_verified_at),
            'institution' => $user->institution,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'telephone' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'adresse' => 'nullable|string|max:500',
            'activite' => 'nullable|string|max:255',
            'entreprise_nom' => 'nullable|string|max:255',
            'entreprise_secteur' => 'nullable|string|max:255',
        ]);

        $changes = [];
        foreach ($validated as $field => $value) {
            $oldValue = $user->$field;
            if ($value !== $oldValue && (string) $value !== (string) $oldValue) {
                $changes[] = $field;
            }
        }

        $user->update($validated);

        if (! empty($changes)) {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'profile_update',
                'ip' => $request->ip(),
                'date' => now(),
            ]);

            UserNotification::create([
                'user_id' => $user->id,
                'type' => 'info',
                'title' => 'Profil mis à jour',
                'content' => 'Vos informations personnelles ont été modifiées : '.implode(', ', $changes).'.',
                'is_read' => false,
            ]);
        }

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'user' => $this->userData($user),
        ]);
    }

    public function updateEmail(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'required|string',
        ]);

        if (! Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Mot de passe incorrect'], 422);
        }

        $oldEmail = $user->email;
        $user->update([
            'email' => $validated['email'],
            'email_verified_at' => null,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'email_change',
            'ip' => $request->ip(),
            'date' => now(),
        ]);

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Email modifié. Un lien de vérification vous a été envoyé.',
            'user' => $this->userData($user),
        ]);
    }

    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email déjà vérifié'], 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Lien de vérification renvoyé']);
    }

    public function verifyTelephone(Request $request)
    {
        $user = $request->user();

        if (! is_null($user->telephone_verified_at)) {
            return response()->json(['message' => 'Téléphone déjà vérifié'], 422);
        }

        $validated = $request->validate([
            'code' => 'required|string|size:6',
        ]);

        // Temporaire: on simule la vérification (OTP SMS pas encore implémenté)
        // Dans une version future, vérifier le code OTP stocké en cache
        $user->update(['telephone_verified_at' => now()]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'telephone_verified',
            'ip' => $request->ip(),
            'date' => now(),
        ]);

        return response()->json([
            'message' => 'Téléphone vérifié avec succès',
            'user' => $this->userData($user),
        ]);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = $request->user();

        // Supprimer l'ancien avatar si présent
        if ($user->avatar_url && ! str_starts_with($user->avatar_url, 'http')) {
            Storage::disk('public')->delete($user->avatar_url);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar_url' => $path]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'photo_change',
            'ip' => $request->ip(),
            'date' => now(),
        ]);

        return response()->json([
            'message' => 'Photo de profil mise à jour',
            'avatar_url' => Storage::disk('public')->url($path),
            'user' => $this->userData($user),
        ]);
    }

    public function profileHistory(Request $request)
    {
        $logs = AuditLog::where('user_id', $request->user()->id)
            ->whereIn('action', ['profile_update', 'email_change', 'photo_change', 'telephone_verified'])
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($log) {
                $labels = [
                    'profile_update' => ['label' => 'Modification profil', 'icon' => 'bi-person', 'color' => 'primary'],
                    'email_change' => ['label' => 'Changement email', 'icon' => 'bi-envelope', 'color' => 'warning'],
                    'photo_change' => ['label' => 'Changement photo', 'icon' => 'bi-image', 'color' => 'info'],
                    'telephone_verified' => ['label' => 'Téléphone vérifié', 'icon' => 'bi-phone', 'color' => 'success'],
                ];
                $action = $labels[$log->action] ?? ['label' => $log->action, 'icon' => 'bi-clock', 'color' => 'secondary'];

                return [
                    'id' => $log->id,
                    'action' => $action['label'],
                    'icon' => $action['icon'],
                    'color' => $action['color'],
                    'ip' => $log->ip ?? '',
                    'date' => $log->date ? $log->date->format('d M Y H:i') : ($log->created_at ? $log->created_at->format('d M Y H:i') : ''),
                ];
            });

        return response()->json($logs);
    }

    private function userData($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'prenom' => $user->prenom,
            'email' => $user->email,
            'telephone' => $user->telephone,
            'adresse' => $user->adresse,
            'activite' => $user->activite,
            'avatar_url' => $user->avatar_url,
            'entreprise_nom' => $user->entreprise_nom,
            'entreprise_secteur' => $user->entreprise_secteur,
            'email_verified' => ! is_null($user->email_verified_at),
            'telephone_verified' => ! is_null($user->telephone_verified_at),
        ];
    }
}
