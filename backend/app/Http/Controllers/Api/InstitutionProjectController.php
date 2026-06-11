<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\InstitutionAnalysis;
use App\Models\Project;
use App\Services\ProjectWorkflowService;
use App\Services\RiskAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstitutionProjectController extends Controller
{
    public function __construct(
        protected RiskAnalysisService $riskService,
        protected ProjectWorkflowService $workflowService
    ) {}

    /**
     * Liste des projets disponibles pour les institutions
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user || $user->role !== 'institution') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Project::query()
            ->whereIn('statut', ProjectStatus::institutionAvailableValues())
            ->with(['owner', 'documents', 'validations']);

        // Filtre Recherche
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('titre', 'like', '%'.$request->search.'%')
                    ->orWhere('secteur', 'like', '%'.$request->search.'%')
                    ->orWhereHas('owner', function ($sq) use ($request) {
                        $sq->where('name', 'like', '%'.$request->search.'%');
                    });
            });
        }

        // Filtre Secteur
        if ($request->secteur) {
            $query->where('secteur', $request->secteur);
        }

        // Filtre Montant
        if ($request->montant_min) {
            $query->where('montant_demande', '>=', $request->montant_min);
        }
        if ($request->montant_max) {
            $query->where('montant_demande', '<=', $request->montant_max);
        }

        // Filtre Localisation/Ville
        if ($request->ville) {
            $query->where('localisation', 'like', '%'.$request->ville.'%');
        }

        $projects = $query->latest()->paginate(12);

        $projects->getCollection()->transform(function ($project) {
            $riskScore = $this->riskService->calculateRiskScore($project);

            return [
                'id' => $project->id,
                'titre' => $project->titre,
                'description' => $project->description,
                'secteur' => $project->secteur,
                'ville' => $project->localisation,
                'porteur' => $project->owner?->name ?? '',
                'budget_total' => (float) $project->montant_demande,
                'montant_finance' => (float) $project->montant_finance,
                'progression' => $project->montant_demande > 0 ? round(($project->montant_finance / $project->montant_demande) * 100) : 0,
                'risk_score' => $riskScore,
                'credibility_score' => $this->riskService->calculateCredibilityScore($project),
                'viability' => $this->riskService->getViabilityLabel($riskScore),
                'date_publication' => $project->created_at->format('d/m/Y'),
                'statut' => $project->statut,
                'statut_label' => $project->statusEnum()->label(),
                'statut_color' => $project->statusEnum()->color(),
                'documents_count' => $project->documents->count(),
            ];
        });

        return response()->json($projects);
    }

    /**
     * Statistiques pour l'onglet Projets Disponibles
     */
    public function stats(Request $request)
    {
        $availableStatus = ProjectStatus::institutionAvailableValues();

        return response()->json([
            'total_disponibles' => Project::whereIn('statut', $availableStatus)->count(),
            'total_recherche' => (float) Project::whereIn('statut', $availableStatus)->sum('montant_demande'),
            'projets_urgents' => Project::whereIn('statut', $availableStatus)->where('created_at', '>=', now()->subDays(7))->count(),
            'secteurs_actifs' => Project::whereIn('statut', $availableStatus)
                ->select('secteur', DB::raw('count(*) as total'))
                ->groupBy('secteur')
                ->orderByDesc('total')
                ->take(5)
                ->get(),
            'faible_risque' => \App\Models\InstitutionAnalysis::where('risk_score', '<=', 30)->whereHas('project', function ($q) use ($availableStatus) {
                $q->whereIn('statut', $availableStatus);
            })->count(),
        ]);
    }

    /**
     * Détails d'un projet spécifique
     */
    public function show(Request $request, $id)
    {
        $project = Project::with(['owner', 'documents', 'validations', 'analyses.institution'])->findOrFail($id);

        if (! in_array($project->statut, ProjectStatus::institutionAvailableValues(), true)) {
            return response()->json(['message' => 'Ce projet n\'est pas encore disponible pour les institutions.'], 403);
        }

        $riskScore = $this->riskService->calculateRiskScore($project);

        return response()->json([
            'id' => $project->id,
            'titre' => $project->titre,
            'description' => $project->description,
            'secteur' => $project->secteur,
            'localisation' => $project->localisation,
            'duree' => $project->duree,
            'porteur' => [
                'nom' => $project->owner->name,
                'email' => $project->owner->email,
                'projets_precedents' => Project::where('user_id', $project->user_id)->where('id', '!=', $project->id)->count(),
            ],
            'finances' => [
                'montant_demande' => (float) $project->montant_demande,
                'montant_finance' => (float) $project->montant_finance,
                'progression' => $project->montant_demande > 0 ? round(($project->montant_finance / $project->montant_demande) * 100) : 0,
            ],
            'analyse' => [
                'risk_score' => $riskScore,
                'credibility_score' => $this->riskService->calculateCredibilityScore($project),
                'viability' => $this->riskService->getViabilityLabel($riskScore),
                'anomalies' => $this->riskService->detectAnomalies($project),
            ],
            'documents' => $project->documents->map(fn ($doc) => [
                'id' => $doc->id,
                'nom' => $doc->nom,
                'type' => $doc->type,
                'url' => $doc->url,
            ]),
            'statut' => $project->statut,
            'statut_label' => $project->statusEnum()->label(),
        ]);
    }

    /**
     * Lancer une analyse sur un projet
     */
    public function analyze(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        if (! in_array($project->statut, ProjectStatus::institutionAvailableValues(), true)) {
            return response()->json(['message' => 'Ce projet n\'est pas disponible pour analyse.'], 403);
        }

        $institution = $request->user()->institution;

        if (! $institution) {
            return response()->json(['message' => 'Institution non trouvée'], 404);
        }

        // Calculs de risque complets
        $risk = $this->riskService->calculateRiskScore($project);
        $credibility = $this->riskService->calculateCredibilityScore($project);
        $solvability = $this->riskService->calculateSolvabilityScore($project);
        $noteGlobale = $this->riskService->calculateGlobalNote($risk, $credibility, $solvability);

        // Créer ou mettre à jour l'analyse
        $analysis = InstitutionAnalysis::updateOrCreate(
            ['project_id' => $project->id, 'institution_id' => $institution->id],
            [
                'statut' => 'en_analyse',
                'analyste_id' => $request->user()->id,
                'risk_score' => $risk,
                'score_credibilite' => $credibility,
                'score_solvabilite' => $solvability,
                'note_globale' => $noteGlobale,
                'recommandation' => $this->riskService->getRecommendation($noteGlobale),
                'commentaire' => 'Analyse initiée par l\'institution.',
            ]
        );

        // Enregistrer dans l'historique
        \App\Models\AnalysisHistory::create([
            'analysis_id' => $analysis->id,
            'action' => 'creation',
            'auteur' => $request->user()->name,
            'details' => 'Analyse du projet initiée.',
        ]);

        // Changer le statut du projet via le workflow
        if ($project->statut === ProjectStatus::ADMIN_VALIDATED->value) {
            $this->workflowService->startInstitutionReview($project, $request->user()->id);
        }

        return response()->json([
            'message' => 'Analyse lancée avec succès',
            'analysis' => $analysis,
        ]);
    }

    /**
     * Récupérer les filtres disponibles
     */
    public function filters()
    {
        $availableStatus = ProjectStatus::institutionAvailableValues();

        $secteurs = Project::select('secteur')
            ->whereNotNull('secteur')
            ->whereIn('statut', $availableStatus)
            ->distinct()
            ->pluck('secteur');

        $villes = Project::select('localisation')
            ->whereNotNull('localisation')
            ->whereIn('statut', $availableStatus)
            ->distinct()
            ->pluck('localisation');

        return response()->json([
            'secteurs' => $secteurs,
            'villes' => $villes,
        ]);
    }
}
