<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Funding;
use Illuminate\Http\Request;

class AdminFinancementController extends Controller
{
    public function index(Request $request)
    {
        $query = Funding::with(['project', 'institution']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('project', fn ($q) => $q->where('titre', 'like', "%{$s}%"));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        $financements = $query->orderBy('created_at', 'desc')->paginate(15);

        $data = $financements->map(fn ($f) => [
            'id' => $f->id,
            'project_title' => $f->project?->titre ?? 'N/A',
            'institution_name' => $f->institution?->nom ?? 'N/A',
            'montant' => $f->montant_valide ?? $f->montant_propose ?? $f->montant_demande,
            'type' => $f->type ?? 'loan',
            'statut' => $f->statut,
            'created_at' => $f->created_at,
        ]);

        return response()->json([
            'success' => true,
            'total' => $financements->total(),
            'data' => $data,
            'last_page' => $financements->lastPage(),
            'links' => $financements->linkCollection(),
        ]);
    }

    public function show(int $id)
    {
        $funding = Funding::with(['project.owner', 'institution', 'porteur', 'echeances'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $funding->id,
                'reference' => 'FIN-'.str_pad((string) $funding->id, 5, '0', STR_PAD_LEFT),
                'project_title' => $funding->project?->titre ?? 'N/A',
                'project_description' => $funding->project?->description ?? '',
                'project_secteur' => $funding->project?->secteur ?? 'N/A',
                'porteur_name' => $funding->porteur?->name ?? $funding->project?->owner?->name ?? 'N/A',
                'porteur_email' => $funding->porteur?->email ?? $funding->project?->owner?->email ?? '',
                'institution_name' => $funding->institution?->nom ?? 'N/A',
                'montant_demande' => (float) ($funding->montant_demande ?? 0),
                'montant_propose' => (float) ($funding->montant_propose ?? 0),
                'montant_valide' => (float) ($funding->montant_valide ?? 0),
                'montant_decaisse' => (float) ($funding->montant_decaisse ?? 0),
                'montant_mensuel' => (float) ($funding->montant_mensuel ?? 0),
                'taux_interet' => (float) ($funding->taux_interet ?? 0),
                'duree' => (int) ($funding->duree ?? 0),
                'statut' => $funding->statut,
                'statut_label' => $funding->statut_label,
                'type' => $funding->type ?? 'loan',
                'date_financement' => $funding->date_financement?->format('d/m/Y'),
                'date_validation' => $funding->date_validation?->format('d/m/Y'),
                'date_decaissement' => $funding->date_decaissement?->format('d/m/Y'),
                'date_approbation_imf' => $funding->date_approbation_imf?->format('d/m/Y'),
                'date_acceptation_porteur' => $funding->date_acceptation_porteur?->format('d/m/Y'),
                'date_cloture' => $funding->date_cloture?->format('d/m/Y'),
                'conditions' => $funding->conditions,
                'commentaires' => $funding->commentaires,
                'echeances_count' => $funding->echeances()->count(),
                'echeances_payees' => $funding->echeancesPayees()->count(),
                'progression' => $funding->progression_remboursement,
                'reste_du' => $funding->reste_du,
                'created_at' => $funding->created_at?->format('d/m/Y H:i'),
            ],
        ]);
    }

    public function statistics(Request $request)
    {
        $query = Funding::query();

        $monthly = Funding::selectRaw('MONTH(created_at) as m, SUM(montant_valide) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('m')
            ->pluck('total', 'm');

        $monthlyFlow = array_map(fn ($m) => (float) ($monthly[$m] ?? 0), range(1, 12));

        $statutCounts = (clone $query)
            ->selectRaw('statut as t, COUNT(*) as c')
            ->groupBy('t')
            ->pluck('c', 't');

        $incidents_count = \App\Models\Repayment::where('statut', 'litige')->orWhere('statut', 'retard')->count();

        return response()->json([
            'success' => true,
            'total_volume' => (float) (clone $query)->sum('montant_valide'),
            'active_count' => (clone $query)->whereIn('statut', ['finance', 'en_cours'])->count(),
            'pending_count' => (clone $query)->where('statut', 'pending')->count(),
            'incidents_count' => $incidents_count,
            'monthly_flow' => $monthlyFlow,
            'statut_distribution' => $statutCounts,
        ]);
    }
}
