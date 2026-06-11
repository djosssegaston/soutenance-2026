<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\UserNotification;
use App\Services\ProjectMonitoringService;
use App\Services\ProjectWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminProjectController extends Controller
{
    public function __construct(
        protected ProjectMonitoringService $monitoringService,
        protected ProjectWorkflowService $workflowService
    ) {}

    /**
     * Liste tous les projets avec filtres
     */
    public function index(Request $request)
    {
        $query = Project::with(['owner', 'documents', 'financements', 'remboursements']);

        // Filtres
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('secteur')) {
            $query->where('secteur', $request->secteur);
        }
        if ($request->filled('search')) {
            $query->where('titre', 'like', '%'.$request->search.'%');
        }

        $projects = $query->latest()->paginate(10);

        // Ajouter le score de risque à chaque projet
        foreach ($projects as $project) {
            $calculated = $this->monitoringService->calculateRiskScore($project);
            try {
                $risk = DB::table('project_risk_scores')->where('project_id', $project->id)->first();
                if (! $risk) {
                    DB::table('project_risk_scores')->insert([
                        'project_id' => $project->id,
                        'risk_level' => $calculated['level'],
                        'score' => $calculated['score'],
                        'analysis' => $calculated['analysis'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                // Table project_risk_scores peut ne pas exister (migration pas encore jouée)
            }
            $project->risk = $calculated;
        }

        return response()->json([
            'success' => true,
            'data' => $projects->items(),
            'total' => $projects->total(),
            'last_page' => $projects->lastPage(),
            'links' => $projects->linkCollection(),
        ]);
    }

    /**
     * Détails d'un projet
     */
    public function show($id)
    {
        $project = Project::with(['owner', 'documents', 'financements', 'remboursements'])->find($id);

        if (! $project) {
            return response()->json(['success' => false, 'message' => 'Projet introuvable.'], 404);
        }

        $calculated = $this->monitoringService->calculateRiskScore($project);

        try {
            $risk = DB::table('project_risk_scores')->where('project_id', $project->id)->first();
            $project->risk = $risk ? (array) $risk : $calculated;
        } catch (\Exception $e) {
            $project->risk = $calculated;
        }

        try {
            $audits = DB::table('project_audits')
                ->join('users', 'project_audits.performed_by', '=', 'users.id')
                ->where('project_id', $project->id)
                ->select('project_audits.*', 'users.name as admin_name')
                ->latest()
                ->get();
        } catch (\Exception $e) {
            $audits = collect();
        }
        $project->audits = $audits;

        $aFundingDecaisse = $project->financements->contains(fn ($f) => in_array($f->statut, ['disbursed', 'active']));
        $aFundingPropose = $project->financements->contains(fn ($f) => in_array($f->statut, ['proposed', 'awaiting_borrower_plan', 'awaiting_imf_validation']));
        $isFinanced = ($project->montant_finance > 0) || $aFundingDecaisse;
        $data = $project->toArray();
        $data['statut_label'] = $isFinanced ? 'Projet déjà financé' : ($aFundingPropose ? 'Financement proposé' : $this->getStatusLabel($project->statut ?? 'draft'));
        $data['statut_color'] = $isFinanced ? 'success' : ($aFundingPropose ? 'info' : $this->getStatusColor($project->statut ?? 'draft'));
        $data['is_financed'] = $isFinanced;

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    private function getStatusLabel(string $status): string
    {
        $labels = [
            'draft' => 'Brouillon',
            'submitted' => 'Soumis',
            'under_admin_review' => 'En révision admin',
            'admin_rejected' => 'Rejeté par admin',
            'admin_validated' => 'Validé par admin',
            'available_for_imf' => 'Disponible pour IMF',
            'under_institution_review' => 'En analyse institution',
            'interview_scheduled' => 'Entretien planifié',
            'interview_confirmed' => 'Entretien confirmé',
            'documents_requested' => 'Documents demandés',
            'institution_rejected' => 'Rejeté par institution',
            'institution_accepted' => 'Accepté par institution',
            'funded' => 'Financé',
            'active' => 'En cours',
            'suspended' => 'Suspendu',
            'completed' => 'Terminé',
            'cancelled' => 'Annulé',
        ];

        return $labels[$status] ?? $status;
    }

    private function getStatusColor(string $status): string
    {
        $map = [
            'draft' => 'secondary',
            'submitted' => 'info',
            'under_admin_review' => 'warning',
            'admin_rejected' => 'danger',
            'admin_validated' => 'success',
            'available_for_imf' => 'info',
            'under_institution_review' => 'warning',
            'interview_scheduled' => 'info',
            'interview_confirmed' => 'success',
            'documents_requested' => 'warning',
            'institution_rejected' => 'danger',
            'institution_accepted' => 'success',
            'funded' => 'success',
            'active' => 'primary',
            'suspended' => 'danger',
            'completed' => 'dark',
            'cancelled' => 'dark',
        ];

        return $map[$status] ?? 'secondary';
    }

    /**
     * Validation d'un projet
     */
    public function validateProject(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        try {
            DB::transaction(function () use ($project, $request) {
                $this->workflowService->adminValidate($project, auth()->id());

                DB::table('project_audits')->insert([
                    'project_id' => $project->id,
                    'action' => 'validation',
                    'performed_by' => auth()->id(),
                    'details' => $request->commentaire ?? 'Projet validé par l\'administrateur.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            UserNotification::create([
                'user_id' => $project->user_id,
                'type' => 'validation_projet',
                'title' => 'Projet validé',
                'content' => 'Votre projet "'.$project->titre.'" a été validé par l\'administration.',
                'is_read' => false,
            ]);

            UserNotification::create([
                'user_id' => auth()->id(),
                'type' => 'validation_projet_admin',
                'title' => 'Projet validé',
                'content' => 'Vous avez validé le projet "'.$project->titre.'".',
                'is_read' => false,
            ]);

            return response()->json(['success' => true, 'message' => 'Projet validé avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Rejet d'un projet
     */
    public function rejectProject(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        try {
            DB::transaction(function () use ($project, $request) {
                $this->workflowService->adminReject($project, auth()->id(), $request->commentaire);

                DB::table('project_audits')->insert([
                    'project_id' => $project->id,
                    'action' => 'rejection',
                    'performed_by' => auth()->id(),
                    'details' => $request->commentaire ?? 'Projet rejeté par l\'administrateur.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            $reason = $request->commentaire ?? 'Motif non spécifié';

            UserNotification::create([
                'user_id' => $project->user_id,
                'type' => 'refus_projet',
                'title' => 'Projet rejeté',
                'content' => 'Votre projet "'.$project->titre.'" a été rejeté. Motif: '.$reason,
                'is_read' => false,
            ]);

            UserNotification::create([
                'user_id' => auth()->id(),
                'type' => 'refus_projet_admin',
                'title' => 'Projet rejeté',
                'content' => 'Vous avez rejeté le projet "'.$project->titre.'". Motif: '.$reason,
                'is_read' => false,
            ]);

            return response()->json(['success' => true, 'message' => 'Projet rejeté.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Suspension d'un projet
     */
    public function suspendProject(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        try {
            DB::transaction(function () use ($project, $request) {
                $this->workflowService->suspend(
                    $project,
                    auth()->id(),
                    $request->commentaire ?? 'Projet suspendu pour investigation.'
                );

                DB::table('project_audits')->insert([
                    'project_id' => $project->id,
                    'action' => 'suspension',
                    'performed_by' => auth()->id(),
                    'details' => $request->commentaire ?? 'Projet suspendu pour investigation.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

            $reason = $request->commentaire ?? 'Investigation en cours';

            UserNotification::create([
                'user_id' => $project->user_id,
                'type' => 'suspension_projet',
                'title' => 'Projet suspendu',
                'content' => 'Votre projet "'.$project->titre.'" a été suspendu. Raison: '.$reason,
                'is_read' => false,
            ]);

            UserNotification::create([
                'user_id' => auth()->id(),
                'type' => 'suspension_projet_admin',
                'title' => 'Projet suspendu',
                'content' => 'Vous avez suspendu le projet "'.$project->titre.'". Raison: '.$reason,
                'is_read' => false,
            ]);

            return response()->json(['success' => true, 'message' => 'Projet suspendu.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Statistiques globales
     */
    public function statistics()
    {
        $stats = $this->monitoringService->getGlobalStats();

        // Données pour les graphiques (exemple : évolution mensuelle)
        $evolution = Project::select(DB::raw('COUNT(*) as count'), DB::raw('MONTH(created_at) as month'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->get();

        $stats['charts'] = [
            'evolution' => $evolution,
            'secteurs' => Project::select('secteur', DB::raw('COUNT(*) as count'))->groupBy('secteur')->get(),
            'statuts' => Project::select('statut', DB::raw('COUNT(*) as count'))->groupBy('statut')->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Alertes critiques
     */
    public function alerts()
    {
        return response()->json([
            'success' => true,
            'alerts' => $this->monitoringService->getCriticalAlerts(),
        ]);
    }
}
