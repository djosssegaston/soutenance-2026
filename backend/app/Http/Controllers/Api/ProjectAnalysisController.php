<?php

namespace App\Http\Controllers\Api;

use App\Enums\FundingStatus;
use App\Http\Controllers\Controller;
use App\Models\AnalysisHistory;
use App\Models\InstitutionAnalysis;
use App\Services\ProjectWorkflowService;
use App\Services\RiskAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectAnalysisController extends Controller
{
    protected RiskAnalysisService $riskService;

    protected ProjectWorkflowService $workflow;

    public function __construct(RiskAnalysisService $riskService, ProjectWorkflowService $workflow)
    {
        $this->riskService = $riskService;
        $this->workflow = $workflow;
    }

    /**
     * Liste des analyses de l'institution
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user || $user->role !== 'institution') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $institution = $user->institution;
        if (! $institution) {
            return response()->json(['data' => [], 'links' => [], 'meta' => ['current_page' => 1, 'last_page' => 1, 'total' => 0]]);
        }

        $query = InstitutionAnalysis::where('institution_id', $institution->id)
            ->with(['project.owner', 'analyste', 'histories']);

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtrer uniquement les projets sans financement actif (pour proposition)
        if ($request->boolean('financeable')) {
            $query->whereDoesntHave('project.financements', function ($q) {
                $q->whereIn('statut', [
                    FundingStatus::PROPOSED->value,
                    FundingStatus::AWAITING_BORROWER_PLAN->value,
                    FundingStatus::AWAITING_IMF_VALIDATION->value,
                    FundingStatus::APPROVED->value,
                    FundingStatus::DISBURSED->value,
                    FundingStatus::ACTIVE->value,
                ]);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('project', function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%");
            });
        }

        $analyses = $query->latest()->paginate(10);

        return response()->json($analyses);
    }

    /**
     * Statistiques des analyses
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        $institution = $user->institution;
        if (! $institution) {
            return response()->json([
                'en_cours' => 0,
                'approuves' => 0,
                'rejetes' => 0,
                'risque_eleve' => 0,
                'montant_potentiel' => 0,
            ]);
        }

        $institutionId = $institution->id;

        $stats = [
            'en_cours' => InstitutionAnalysis::where('institution_id', $institutionId)->where('statut', 'en_analyse')->count(),
            'approuves' => InstitutionAnalysis::where('institution_id', $institutionId)->where('statut', 'approuve')->count(),
            'rejetes' => InstitutionAnalysis::where('institution_id', $institutionId)->where('statut', 'rejete')->count(),
            'risque_eleve' => InstitutionAnalysis::where('institution_id', $institutionId)->where('risk_score', '>', 70)->count(),
            'montant_potentiel' => DB::table('institution_analyses')
                ->join('projects', 'institution_analyses.project_id', '=', 'projects.id')
                ->where('institution_analyses.institution_id', $institutionId)
                ->where('institution_analyses.statut', 'approuve')
                ->sum('projects.montant_demande'),
        ];

        return response()->json($stats);
    }

    /**
     * Détails d'une analyse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $institution = $user->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        $analysis = InstitutionAnalysis::with(['project.owner', 'project.documents', 'project.financements', 'analyste', 'histories'])
            ->where('institution_id', $institution->id)
            ->findOrFail($id);

        $data = $analysis->toArray();
        $data['peut_etre_finance'] = $analysis->project->peutEtreFinance();

        return response()->json($data);
    }

    /**
     * Approuver un projet
     */
    public function approve(Request $request, $id)
    {
        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        $analysis = InstitutionAnalysis::where('institution_id', $institution->id)->findOrFail($id);
        $project = $analysis->project;

        $this->updateStatus($request, $id, 'approuve', 'Projet approuvé pour financement.');

        try {
            $this->workflow->institutionAccept($project, $request->user()->id);
        } catch (\RuntimeException $e) {
            \Illuminate\Support\Facades\Log::warning('Project status transition skipped: '.$e->getMessage());
        }

        return response()->json(['message' => 'Projet approuvé pour financement.', 'analysis' => $analysis->fresh()]);
    }

    /**
     * Rejeter un projet
     */
    public function reject(Request $request, $id)
    {
        return $this->updateStatus($request, $id, 'rejete', 'Projet rejeté.');
    }

    /**
     * Demander des informations
     */
    public function requestInfo(Request $request, $id)
    {
        return $this->updateStatus($request, $id, 'info_demandee', 'Informations complémentaires demandées au porteur.');
    }

    /**
     * Programmer un RDV
     */
    public function scheduleInterview(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date|after:now',
        ]);

        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        $analysis = InstitutionAnalysis::where('institution_id', $institution->id)->findOrFail($id);

        $analysis->update([
            'statut' => 'entretien_planifie',
            'entretien_date' => $request->date,
        ]);

        AnalysisHistory::create([
            'analysis_id' => $analysis->id,
            'action' => 'entretien_planifie',
            'auteur' => $request->user()->name,
            'details' => 'Entretien programmé pour le '.$request->date,
        ]);

        return response()->json(['message' => 'Entretien programmé', 'analysis' => $analysis]);
    }

    private function updateStatus(Request $request, $id, $statut, $defaultMessage)
    {
        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        $analysis = InstitutionAnalysis::where('institution_id', $institution->id)->findOrFail($id);

        $oldStatut = $analysis->statut;
        $analysis->update([
            'statut' => $statut,
            'commentaire' => $request->commentaire ?? $analysis->commentaire,
        ]);

        AnalysisHistory::create([
            'analysis_id' => $analysis->id,
            'action' => 'status_updated',
            'auteur' => $request->user()->name,
            'details' => "Statut changé de {$oldStatut} à {$statut}. ".($request->commentaire ?? ''),
        ]);

        return response()->json(['message' => $defaultMessage, 'analysis' => $analysis]);
    }
}
