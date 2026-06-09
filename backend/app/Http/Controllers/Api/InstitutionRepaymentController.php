<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Echeance;
use App\Models\Project;
use App\Models\Repayment;
use App\Models\RepaymentDispute;
use App\Services\RepaymentMonitoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstitutionRepaymentController extends Controller
{
    protected RepaymentMonitoringService $monitoringService;

    public function __construct(RepaymentMonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    /**
     * Liste des remboursements du portefeuille
     */
    public function index(Request $request)
    {
        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['data' => [], 'links' => [], 'meta' => ['current_page' => 1, 'last_page' => 1, 'total' => 0]]);
        }

        $query = Repayment::where('institution_id', $institution->id)
            ->with(['project.owner']);

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('risque')) {
            $query->where('niveau_risque', $request->risque);
        }

        $repayments = $query->latest()->paginate(10);

        // Mise à jour dynamique du risque et pénalités lors de la consultation
        $repayments->getCollection()->transform(function ($r) {
            $r->niveau_risque = $this->monitoringService->calculateRiskLevel($r);
            $r->penalites = $this->monitoringService->calculatePenalties($r);

            return $r;
        });

        return response()->json($repayments);
    }

    /**
     * Statistiques globales du portefeuille
     */
    public function stats(Request $request)
    {
        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json([
                'total_finance' => 0,
                'total_rembourse' => 0,
                'total_restant' => 0,
                'taux_remboursement' => 0,
                'retards' => 0,
                'litiges' => 0,
            ]);
        }

        $institutionId = $institution->id;

        $stats = [
            'total_finance' => DB::table('remboursements')->where('institution_id', $institutionId)->sum('montant_total'),
            'total_rembourse' => DB::table('remboursements')->where('institution_id', $institutionId)->sum('montant_rembourse'),
            'total_restant' => DB::table('remboursements')->where('institution_id', $institutionId)->sum('montant_restant'),
            'taux_remboursement' => 0,
            'retards' => DB::table('remboursements')->where('institution_id', $institutionId)->where('date_echeance', '<', now())->where('statut', '!=', 'paye')->count(),
            'litiges' => DB::table('repayment_disputes')->where('institution_id', $institutionId)->where('statut', 'ouvert')->count(),
        ];

        if ($stats['total_finance'] > 0) {
            $stats['taux_remboursement'] = round(($stats['total_rembourse'] / $stats['total_finance']) * 100, 2);
        }

        return response()->json($stats);
    }

    /**
     * Détails d'un remboursement
     */
    public function show(Request $request, $id)
    {
        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        $repayment = Repayment::where('institution_id', $institution->id)
            ->with(['project', 'events'])
            ->findOrFail($id);

        return response()->json([
            'id' => $repayment->id,
            'project' => [
                'titre' => $repayment->project?->titre ?? 'N/A',
            ],
            'montant_total' => $repayment->montant_total,
            'montant_rembourse' => $repayment->montant_rembourse,
            'montant_restant' => $repayment->montant_restant,
            'date_echeance' => $repayment->date_echeance,
            'niveau_risque' => $repayment->niveau_risque,
            'penalites' => $repayment->penalites,
            'events' => $repayment->events->map(fn ($e) => [
                'type' => $e->type,
                'description' => $e->description,
                'created_at' => $e->created_at,
                'user_name' => $e->user_name,
            ]),
        ]);
    }

    /**
     * Valider un remboursement reçu
     */
    public function validateRepayment(Request $request, $id)
    {
        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        $repayment = Repayment::where('institution_id', $institution->id)->findOrFail($id);

        $repayment->update([
            'statut' => 'paye',
            'montant_rembourse' => $repayment->montant_total,
            'montant_restant' => 0,
            'date_paiement' => now(),
        ]);

        $this->monitoringService->logEvent($repayment->id, 'validation', 'Remboursement validé par l\'institution.', $request->user()->name);

        return response()->json(['message' => 'Remboursement validé avec succès.']);
    }

    /**
     * Ouvrir un litige
     */
    public function openDispute(Request $request, $id)
    {
        $request->validate(['motif' => 'required|string']);

        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        $repayment = Repayment::where('institution_id', $institution->id)->findOrFail($id);

        $dispute = RepaymentDispute::create([
            'repayment_id' => $repayment->id,
            'institution_id' => $institution->id,
            'porteur_id' => $repayment->project->user_id,
            'motif' => $request->motif,
            'statut' => 'ouvert',
        ]);

        $repayment->update(['statut' => 'litige', 'niveau_risque' => 'critique']);

        $this->monitoringService->logEvent($repayment->id, 'litige', "Litige ouvert: {$request->motif}", $request->user()->name);

        return response()->json(['message' => 'Litige ouvert et transmis à l\'administration.', 'dispute' => $dispute]);
    }

    public function projects(Request $request)
    {
        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['success' => true, 'stats' => ['total_projects' => 0, 'total_echeances' => 0, 'total_overdue' => 0, 'total_paid' => 0], 'data' => []]);
        }

        $institutionId = $institution->id;

        $query = Project::with('owner')
            ->whereHas('echeances', fn ($q) => $q->where('institution_id', $institutionId));

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('titre', 'like', "%{$s}%")
                    ->orWhereHas('owner', fn ($oq) => $oq->where('name', 'like', "%{$s}%"));
            });
        }

        $projects = $query->latest()->paginate(15);

        $data = $projects->map(function ($p) use ($institutionId) {
            $echeances = $p->echeances->where('institution_id', $institutionId);

            return [
                'id' => $p->id,
                'titre' => $p->titre,
                'porteur_name' => $p->owner?->name ?? 'N/A',
                'echeances_count' => $echeances->count(),
                'total_due' => $echeances->sum('montant_total'),
                'total_paid' => $echeances->sum('montant_paye'),
                'total_remaining' => $echeances->sum('montant_restant'),
                'overdue_count' => $echeances->where('statut', 'overdue')->count(),
            ];
        });

        $stats = [
            'total_projects' => $projects->total(),
            'total_echeances' => Echeance::where('institution_id', $institutionId)->count(),
            'total_overdue' => Echeance::where('institution_id', $institutionId)->where('statut', 'overdue')->count(),
            'total_paid' => Echeance::where('institution_id', $institutionId)->where('statut', 'paid')->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'data' => $data,
        ]);
    }

    public function projectEcheances(Request $request, $projectId)
    {
        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $institutionId = $institution->id;

        $project = Project::with('owner')->findOrFail($projectId);
        $echeances = $project->echeances()
            ->with('institution')
            ->where('institution_id', $institutionId)
            ->orderBy('date_echeance', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'project' => [
                'id' => $project->id,
                'titre' => $project->titre,
                'porteur_name' => $project->owner?->name ?? 'N/A',
            ],
            'data' => $echeances->map(fn ($e) => [
                'id' => $e->id,
                'montant_total' => $e->montant_total,
                'montant_paye' => $e->montant_paye,
                'montant_restant' => $e->montant_restant,
                'date_echeance' => $e->date_echeance,
                'statut' => $e->statut,
                'institution_nom' => $e->institution?->nom ?? 'N/A',
            ]),
        ]);
    }
}
