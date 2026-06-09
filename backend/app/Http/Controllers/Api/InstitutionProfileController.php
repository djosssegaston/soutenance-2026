<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Institution;
use App\Models\Repayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InstitutionProfileController extends Controller
{
    /**
     * Get institution profile
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $institution = $user->institution;

        if (! $institution) {
            return response()->json([
                'user' => $user,
                'institution' => null,
                'stats' => ['projets_finances' => 0, 'portefeuille_actif' => 0, 'roi_moyen' => 0, 'taux_remboursement' => 0],
            ]);
        }

        $funderIds = $institution->financements()->pluck('id');
        $totalDue = Repayment::whereIn('funding_id', $funderIds)->sum('montant_rembourse') + Repayment::whereIn('funding_id', $funderIds)->sum('montant_restant');
        $totalPaid = Repayment::whereIn('funding_id', $funderIds)->sum('montant_rembourse');
        $tauxRemboursement = $totalDue > 0 ? round(($totalPaid / $totalDue) * 100, 1) : 0;

        $stats = [
            'projets_finances' => $institution->financements()->where('statut', 'decaisse')->count(),
            'portefeuille_actif' => $institution->financements()->where('statut', 'decaisse')->sum('montant_valide'),
            'roi_moyen' => $institution->financements()->avg('taux_interet') ?? 0,
            'taux_remboursement' => $tauxRemboursement,
        ];

        return response()->json([
            'user' => $user,
            'institution' => $institution,
            'stats' => $stats,
        ]);
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $institution = $user->institution;

        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'nom_institution' => 'required|string',
            'adresse' => 'nullable|string',
            'telephone' => 'nullable|string',
            'site_web' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:5000',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $institution->update([
            'nom' => $request->nom_institution,
            'adresse' => $request->adresse,
            'telephone' => $request->telephone,
            'description' => $request->description,
            'site_web' => $request->site_web,
        ]);

        AuditLog::log($user->id, 'Mise à jour du profil', 'bi-pencil', 'primary', 'info');

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'user' => $user,
            'institution' => $institution,
        ]);
    }

    /**
     * Upload logo
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = $request->user();
        $institution = $user->institution;

        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('institutions/logos', 'public');
            $institution->update(['logo' => $path]);

            AuditLog::log($user->id, 'Changement du logo', 'bi-camera', 'info', 'success');

            return response()->json([
                'message' => 'Logo mis à jour.',
                'logo_url' => Storage::url($path),
            ]);
        }

        return response()->json(['message' => 'Fichier manquant.'], 400);
    }

    /**
     * Get profile history (audit logs)
     */
    public function history(Request $request)
    {
        $logs = AuditLog::where('user_id', $request->user()->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($log) {
                return [
                    'action' => $log->action,
                    'icon' => $log->icon,
                    'color' => $log->color,
                    'ip' => $log->ip,
                    'date' => $log->date->format('d M Y H:i'),
                ];
            });

        return response()->json($logs);
    }
}
