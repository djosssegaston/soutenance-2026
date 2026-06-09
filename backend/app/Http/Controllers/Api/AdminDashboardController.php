<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Funding;
use App\Models\Project;
use App\Models\Repayment;
use App\Services\DashboardAnalyticsService;
use App\Services\MonitoringService;

class AdminDashboardController extends Controller
{
    protected $analytics;

    protected $monitoring;

    public function __construct(DashboardAnalyticsService $analytics, MonitoringService $monitoring)
    {
        $this->analytics = $analytics;
        $this->monitoring = $monitoring;
    }

    /**
     * Main dashboard data (KPIs)
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'kpis' => $this->analytics->getAdminKpis(),
        ]);
    }

    /**
     * Chart data
     */
    public function charts()
    {
        return response()->json([
            'success' => true,
            'charts' => $this->analytics->getChartData(),
        ]);
    }

    /**
     * Critical alerts
     */
    public function alerts()
    {
        $alerts = [];

        // Check for over-funding
        $overFunded = Project::whereRaw('montant_finance > montant_demande')->get();
        foreach ($overFunded as $p) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Surfinancement détecté',
                'message' => "Le projet {$p->titre} dépasse son objectif de financement.",
                'project_id' => $p->id,
            ];
        }

        // Check for late repayments
        $late = Repayment::where('statut', 'en_retard')->with('project')->take(5)->get();
        foreach ($late as $r) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Défaut de remboursement',
                'message' => 'Retard constaté sur le projet '.optional($r->project)->titre." (Echéance: {$r->date_echeance}).",
                'project_id' => $r->project_id,
            ];
        }

        return response()->json([
            'success' => true,
            'alerts' => $alerts,
        ]);
    }

    /**
     * Real-time activity timeline
     */
    public function activity()
    {
        // Get various activities and merge them
        $projects = Project::latest()->take(5)->get()->map(fn ($p) => [
            'type' => 'project',
            'title' => 'Nouveau projet',
            'message' => "Le projet \"{$p->titre}\" a été créé.",
            'time' => $p->created_at->diffForHumans(),
            'timestamp' => $p->created_at,
            'icon' => 'fe fe-plus-circle',
            'variant' => 'primary',
        ]);

        $validations = Project::where('statut', 'admin_validated')->latest('updated_at')->take(5)->get()->map(fn ($p) => [
            'type' => 'validation',
            'title' => 'Validation Admin',
            'message' => "Le projet \"{$p->titre}\" a été validé par l'administration.",
            'time' => $p->updated_at->diffForHumans(),
            'timestamp' => $p->updated_at,
            'icon' => 'fe fe-check-circle',
            'variant' => 'success',
        ]);

        $fundings = Funding::latest()->take(5)->get()->map(fn ($f) => [
            'type' => 'funding',
            'title' => 'Financement',
            'message' => 'Un versement de '.number_format($f->montant).' FCFA a été effectué.',
            'time' => $f->created_at->diffForHumans(),
            'timestamp' => $f->created_at,
            'icon' => 'fe fe-dollar-sign',
            'variant' => 'info',
        ]);

        $activities = $projects->concat($validations)->concat($fundings)
            ->sortByDesc('timestamp')
            ->take(10)
            ->values();

        return response()->json([
            'success' => true,
            'activities' => $activities,
        ]);
    }

    /**
     * System status monitoring
     */
    public function system()
    {
        return response()->json([
            'success' => true,
            'system' => $this->monitoring->getSystemStatus(),
        ]);
    }

    /**
     * Get status label in French
     */
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

    /**
     * Get status badge color
     */
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
     * Recent projects for the table
     */
    public function recentProjects()
    {
        $projects = Project::with(['owner', 'financements'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($p) {
                $isFinanced = ($p->montant_finance > 0) || $p->financements->isNotEmpty();
                $statutLabel = $isFinanced
                    ? 'Projet déjà financé'
                    : $this->getStatusLabel($p->statut ?? 'draft');
                $statutColor = $isFinanced
                    ? 'success'
                    : $this->getStatusColor($p->statut ?? 'draft');

                return [
                    'id' => $p->id,
                    'titre' => $p->titre,
                    'owner' => optional($p->owner)->name ?? 'N/A',
                    'montant' => (float) $p->montant_demande,
                    'funded' => (float) $p->montant_finance,
                    'statut' => $p->statut,
                    'statut_label' => $statutLabel,
                    'statut_color' => $statutColor,
                    'is_financed' => $isFinanced,
                    'risk' => 'moyen', // Default for now
                    'date' => $p->created_at->format('d/m/Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'projects' => $projects,
        ]);
    }
}
