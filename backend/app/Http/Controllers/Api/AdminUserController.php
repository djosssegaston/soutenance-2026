<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    private function mapStatutToStatus(?string $statut): string
    {
        return match ($statut) {
            'actif', 'verifie' => 'active',
            'suspendu' => 'suspended',
            'en_attente', 'pending' => 'pending',
            default => $statut ?? 'active',
        };
    }

    private function mapStatusToStatut(string $status): string
    {
        return match ($status) {
            'active' => 'actif',
            'suspended' => 'suspendu',
            'pending' => 'en_attente',
            default => $status,
        };
    }

    public function statistics()
    {
        $total = User::count();
        $porteurs = User::where('role', 'porteur')->count();
        $institutions = User::where('role', 'institution')->count();
        $suspended = User::where('statut', 'suspendu')->count();

        $monthly = User::selectRaw('MONTH(created_at) as m, COUNT(*) as c')
            ->whereYear('created_at', now()->year)
            ->groupBy('m')
            ->pluck('c', 'm');

        $registrations = array_map(fn ($m) => $monthly[$m] ?? 0, range(1, 12));

        $roles = [
            'porteurs' => $porteurs,
            'institutions' => $institutions,
            'admins' => User::where('role', 'admin')->count(),
        ];

        return response()->json([
            'success' => true,
            'total' => $total,
            'porteurs' => $porteurs,
            'institutions' => $institutions,
            'suspended' => $suspended,
            'registrations_monthly' => $registrations,
            'roles_distribution' => $roles,
        ]);
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('telephone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('statut', $this->mapStatusToStatut($request->status));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        $data = $users->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'phone' => $u->telephone,
            'role' => $u->role,
            'status' => $this->mapStatutToStatus($u->statut),
            'avatar' => $u->avatar_url ?? $u->avatar,
            'created_at' => $u->created_at,
            'last_login' => $u->last_login_at,
            'city' => $u->ville,
            'kyc_verified' => ($u->statut_kyc ?? 'non') === 'valide',
        ]);

        return response()->json([
            'success' => true,
            'total' => $users->total(),
            'data' => $data,
            'last_page' => $users->lastPage(),
            'links' => $users->linkCollection(),
        ]);
    }

    public function show($id)
    {
        $user = User::with(['projects' => fn ($q) => $q->select('id', 'user_id', 'titre', 'montant_demande', 'statut')])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->telephone,
            'role' => $user->role,
            'status' => $this->mapStatutToStatus($user->statut),
            'avatar' => $user->avatar_url ?? $user->avatar,
            'created_at' => $user->created_at,
            'last_login' => $user->last_login_at,
            'city' => $user->ville,
            'kyc_verified' => ($user->statut_kyc ?? 'non') === 'valide',
            'projects' => $user->projects->map(fn ($p) => [
                'id' => $p->id,
                'titre' => $p->titre,
                'montant_demande' => (float) $p->montant_demande,
                'statut' => $p->statut,
            ]),
        ]);
    }

    public function action(Request $request, $id, $action)
    {
        $user = User::findOrFail($id);
        $motif = $request->input('motif', 'Action admin');

        $allowed = ['activate', 'suspend', 'delete', 'reset-password'];
        if (! in_array($action, $allowed)) {
            return response()->json(['success' => false, 'message' => 'Action non autorisée.'], 400);
        }

        switch ($action) {
            case 'activate':
                $user->update(['statut' => 'actif']);
                AuditLog::log($request->user()->id, "Activation du compte {$user->name}", 'bi-person-check', 'success', 'info');
                break;

            case 'suspend':
                $user->update(['statut' => 'suspendu']);
                AuditLog::log($request->user()->id, "Suspension du compte {$user->name}: {$motif}", 'bi-person-x', 'warning', 'warning');
                break;

            case 'delete':
                DB::transaction(function () use ($user) {
                    $user->load('projects.financements.echeances', 'projects.financements.histories', 'projects.financements.documents', 'institution', 'kycDocument', 'sentMessages', 'receivedMessages', 'notifications');

                    // Cascade delete: projects → fundings → echeances → repayments
                    foreach ($user->projects as $project) {
                        foreach ($project->financements as $funding) {
                            $funding->echeances()->delete();
                            $funding->histories()->delete();
                            $funding->documents()->delete();
                        }
                        $project->financements()->delete();
                        $project->documents()->delete();
                        $project->validations()->delete();
                        $project->analyses()->delete();
                        $project->statusHistories()->delete();
                        $project->comments()->delete();
                        $project->echeances()->delete();
                        $project->remboursements()->each(fn ($r) => $r->events()->delete() + $r->disputes()->delete());
                        $project->remboursements()->delete();
                    }
                    $user->projects()->delete();

                    // Also handle institution-linked fundings (for institution role)
                    if ($user->institution) {
                        foreach ($user->institution->financements as $funding) {
                            $funding->echeances()->delete();
                            $funding->histories()->delete();
                            $funding->documents()->delete();
                        }
                        $user->institution->financements()->delete();
                        $user->institution()->delete();
                    }

                    $user->kycDocument()->delete();
                    $user->sentMessages()->delete();
                    $user->receivedMessages()->delete();
                    $user->notifications()->delete();

                    $user->delete();
                });

                AuditLog::log($request->user()->id, "Suppression du compte {$user->name}: {$motif}", 'bi-trash', 'danger', 'critical');

                return response()->json(['success' => true, 'message' => 'Compte supprimé avec toutes ses données liées.']);

            case 'reset-password':
                $newPassword = str()->random(16);
                $user->update(['password' => Hash::make($newPassword)]);
                AuditLog::log($request->user()->id, "Réinitialisation mot de passe {$user->name}", 'bi-key', 'info', 'warning');

                return response()->json(['success' => true, 'message' => 'Mot de passe réinitialisé.', 'new_password' => $newPassword]);
        }

        return response()->json(['success' => true, 'message' => 'Action effectuée avec succès.']);
    }
}
