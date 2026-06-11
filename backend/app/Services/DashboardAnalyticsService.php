<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Models\Dispute;
use App\Models\Funding;
use App\Models\Institution;
use App\Models\InstitutionAnalysis;
use App\Models\Project;
use App\Models\Repayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAnalyticsService
{
    /**
     * Get global KPI statistics for Admin
     */
    public function getAdminKpis()
    {
        return [
            'projects' => [
                'total' => Project::count(),
                'active' => Project::whereIn('statut', ['active', 'funded'])->count(),
                'validated' => Project::where('statut', 'admin_validated')->count(),
                'suspended' => Project::where('statut', 'suspended')->count(),
            ],
            'finances' => [
                'total_funded' => (float) Funding::sum('montant'),
                'total_repaid' => (float) Repayment::where('statut', 'paye')->sum('montant_rembourse'),
                'over_funded' => $this->getOverFundedAmount(),
                'late_repayments' => (float) Repayment::where('statut', 'en_retard')->sum('montant_total'),
            ],
            'users' => [
                'total' => User::count(),
                'institutions' => Institution::count(),
                'porteurs' => User::where('role', 'porteur')->count(),
                'suspended' => User::where('statut', 'suspendu')->count(),
            ],
            'risks' => [
                'active_disputes' => Dispute::whereIn('statut', ['ouvert', 'en_mediation'])->count(),
                'critical_projects' => Project::whereHas('analyses', function ($q) {
                    $q->where('risk_score', '<', 40);
                })->count(),
                'repayment_defaults' => Repayment::where('statut', 'en_retard')->count(),
                'suspicious_activities' => 0, // Placeholder for future logic
            ],
        ];
    }

    /**
     * Get summary metrics for an institution dashboard
     */
    public function getSummaryMetrics(Institution $institution): array
    {
        $fundings = Funding::where('institution_id', $institution->id);
        $repayments = Repayment::where('institution_id', $institution->id);

        $totalFunded = (float) (clone $fundings)->sum('montant_decaisse');
        $fundedProjectIds = (clone $fundings)->pluck('project_id');

        $activeCount = Project::whereIn('statut', ProjectStatus::institutionPortfolioValues())
            ->whereIn('id', $fundedProjectIds)
            ->count();

        $totalRepaid = (float) (clone $repayments)->where('statut', 'paye')->sum('montant_rembourse');
        $totalDue = (float) (clone $repayments)->sum('montant_total');
        $lateCount = (int) (clone $repayments)->where('statut', 'en_retard')->count();

        $repaymentRate = $totalDue > 0 ? round(($totalRepaid / $totalDue) * 100) : 0;

        $atRiskCount = InstitutionAnalysis::where('institution_id', $institution->id)
            ->where('risk_score', '<', 60)
            ->count();

        $avgRoi = $totalFunded > 0 ? round((($totalRepaid - $totalFunded) / $totalFunded) * 100, 1) : 0;

        $analyzedCount = InstitutionAnalysis::where('institution_id', $institution->id)->count();

        return [
            'portfolio' => [
                'total_funded' => $totalFunded,
                'active_count' => $activeCount,
                'avg_roi' => $avgRoi,
            ],
            'repayments' => [
                'rate' => $repaymentRate,
                'total_repaid' => $totalRepaid,
                'late_count' => $lateCount,
            ],
            'risks' => [
                'at_risk_count' => $atRiskCount,
            ],
            'pipeline' => [
                'analyzed_count' => $analyzedCount,
            ],
        ];
    }

    /**
     * Get recent activity timeline for an institution
     */
    public function getRecentActivity(Institution $institution): array
    {
        $activities = [];

        $recentFundings = Funding::where('institution_id', $institution->id)
            ->with('project')
            ->latest()
            ->take(5)
            ->get();

        foreach ($recentFundings as $funding) {
            $activities[] = [
                'title' => 'Financement accordé',
                'description' => $funding->project?->titre ?? 'Projet #'.$funding->project_id,
                'date' => $funding->created_at?->diffForHumans() ?? 'Récent',
                'icon' => 'fe fe-briefcase',
                'color' => 'primary',
                'created_at' => $funding->created_at?->timestamp ?? 0,
            ];
        }

        $recentAnalyses = InstitutionAnalysis::where('institution_id', $institution->id)
            ->with('project')
            ->latest()
            ->take(5)
            ->get();

        foreach ($recentAnalyses as $analysis) {
            $activities[] = [
                'title' => 'Analyse effectuée',
                'description' => $analysis->project?->titre ?? 'Projet #'.$analysis->project_id,
                'date' => $analysis->created_at?->diffForHumans() ?? 'Récent',
                'icon' => 'fe fe-search',
                'color' => 'info',
                'created_at' => $analysis->created_at?->timestamp ?? 0,
            ];
        }

        $recentRepayments = Repayment::where('institution_id', $institution->id)
            ->with('project')
            ->latest()
            ->take(5)
            ->get();

        foreach ($recentRepayments as $repayment) {
            $activities[] = [
                'title' => 'Remboursement '.($repayment->statut === 'paye' ? 'reçu' : 'en attente'),
                'description' => $repayment->project?->titre ?? 'Projet #'.$repayment->project_id,
                'date' => $repayment->date_paiement?->diffForHumans()
                    ?? $repayment->created_at?->diffForHumans()
                    ?? 'Récent',
                'icon' => 'fe fe-check-circle',
                'color' => $repayment->statut === 'paye' ? 'success' : 'warning',
                'created_at' => ($repayment->date_paiement ?? $repayment->created_at)?->timestamp ?? 0,
            ];
        }

        usort($activities, fn ($a, $b) => $b['created_at'] <=> $a['created_at']);

        return array_slice($activities, 0, 10);
    }

    /**
     * Get alerts for an institution
     */
    public function getAlerts(Institution $institution): array
    {
        $lateRepayments = Repayment::where('institution_id', $institution->id)
            ->where('statut', 'en_retard')
            ->with('project')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'project_id' => $r->project_id,
                'project_name' => $r->project?->titre ?? '',
                'amount' => (float) $r->montant_restant,
                'due_date' => $r->date_echeance?->format('d/m/Y') ?? '',
            ]);

        $highRiskProjects = InstitutionAnalysis::where('institution_id', $institution->id)
            ->where('risk_score', '<', 60)
            ->whereHas('project', fn ($q) => $q->whereIn('statut', ['active', 'funded']))
            ->with('project')
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'project_id' => $a->project_id,
                'project_name' => $a->project?->titre ?? '',
                'risk_score' => $a->risk_score,
            ]);

        return [
            'late_repayments' => $lateRepayments,
            'high_risk_projects' => $highRiskProjects,
        ];
    }

    /**
     * Get data for charts
     */
    public function getChartData(?Institution $institution = null)
    {
        if ($institution) {
            return $this->getInstitutionChartData($institution);
        }

        return [
            'evolution' => $this->getPlatformEvolution(),
            'sectors' => $this->getSectorsDistribution(),
            'status' => $this->getProjectStatusDistribution(),
            'risks' => $this->getPortfolioRiskDistribution(),
        ];
    }

    /**
     * Get institution-scoped chart data
     */
    private function getInstitutionChartData(Institution $institution): array
    {
        $fundings = Funding::where('institution_id', $institution->id);

        $fundingEvolution = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $fundingEvolution[] = [
                'month' => $date->month,
                'total' => (float) (clone $fundings)
                    ->whereMonth('date_decaissement', $date->month)
                    ->whereYear('date_decaissement', $date->year)
                    ->sum('montant_decaisse'),
            ];
        }

        $sectorDistribution = Project::whereIn('id', (clone $fundings)->pluck('project_id'))
            ->select('secteur', DB::raw('count(*) as count'))
            ->groupBy('secteur')
            ->get()
            ->map(fn ($item) => ['secteur' => $item->secteur ?? 'Autre', 'count' => $item->count]);

        $repaymentStatus = Repayment::where('institution_id', $institution->id)
            ->select('statut', DB::raw('count(*) as count'))
            ->groupBy('statut')
            ->get()
            ->map(fn ($item) => ['statut' => $item->statut, 'count' => $item->count]);

        $riskPortion = InstitutionAnalysis::where('institution_id', $institution->id)
            ->whereNotNull('risk_score')
            ->select('risk_score')
            ->get()
            ->groupBy(fn ($i) => match (true) {
                $i->risk_score >= 80 => 'faible',
                $i->risk_score >= 60 => 'moyen',
                $i->risk_score >= 40 => 'eleve',
                default => 'critique',
            })
            ->map(fn ($group, $level) => ['niveau_risque' => $level, 'count' => $group->count()])
            ->values();

        $riskDefaults = collect([
            ['niveau_risque' => 'critique', 'count' => 0],
            ['niveau_risque' => 'eleve', 'count' => 0],
            ['niveau_risque' => 'moyen', 'count' => 0],
            ['niveau_risque' => 'faible', 'count' => 0],
        ]);

        $riskPortion = $riskDefaults->keyBy('niveau_risque')
            ->merge($riskPortion->keyBy('niveau_risque'))
            ->values();

        return [
            'funding_evolution' => $fundingEvolution,
            'sector_distribution' => $sectorDistribution,
            'repayment_status' => $repaymentStatus,
            'risk_portion' => $riskPortion,
        ];
    }

    private function getOverFundedAmount()
    {
        return DB::table('projects')
            ->join('financements', 'projects.id', '=', 'financements.project_id')
            ->select(DB::raw('SUM(financements.montant) - SUM(projects.montant_demande) as over_amount'))
            ->having('over_amount', '>', 0)
            ->value('over_amount') ?? 0;
    }

    private function getPlatformEvolution()
    {
        $months = [];
        $projectsCreated = [];
        $projectsValidated = [];
        $funding = [];
        $repayments = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M Y');
            $months[] = $month;

            $projectsCreated[] = Project::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)->count();

            $projectsValidated[] = Project::where('statut', 'admin_validated')
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)->count();

            $funding[] = Funding::whereMonth('date_financement', $date->month)
                ->whereYear('date_financement', $date->year)->sum('montant');

            $repayments[] = Repayment::where('statut', 'paye')
                ->whereMonth('date_paiement', $date->month)
                ->whereYear('date_paiement', $date->year)->sum('montant_rembourse');
        }

        return [
            'labels' => $months,
            'datasets' => [
                ['name' => 'Projets Créés', 'data' => $projectsCreated],
                ['name' => 'Projets Validés', 'data' => $projectsValidated],
                ['name' => 'Financements', 'data' => $funding],
                ['name' => 'Remboursements', 'data' => $repayments],
            ],
        ];
    }

    private function getSectorsDistribution()
    {
        return Project::select('secteur', DB::raw('count(*) as count'))
            ->groupBy('secteur')
            ->get()
            ->map(fn ($item) => ['label' => $item->secteur ?? 'Inconnu', 'value' => $item->count]);
    }

    private function getProjectStatusDistribution()
    {
        return Project::select('statut', DB::raw('count(*) as count'))
            ->groupBy('statut')
            ->get()
            ->map(fn ($item) => [
                'label' => ProjectStatus::tryFrom($item->statut)?->label() ?? $item->statut,
                'value' => $item->count,
            ]);
    }

    private function getPortfolioRiskDistribution()
    {
        $total = Project::count();
        if ($total === 0) {
            return [
                ['label' => 'Faible', 'value' => 0],
                ['label' => 'Moyen', 'value' => 0],
                ['label' => 'Élevé', 'value' => 0],
                ['label' => 'Critique', 'value' => 0],
            ];
        }

        $riskScores = InstitutionAnalysis::select('risk_score')
            ->whereNotNull('risk_score')
            ->pluck('risk_score');

        $levels = ['faible' => 0, 'moyen' => 0, 'eleve' => 0, 'critique' => 0];
        foreach ($riskScores as $score) {
            if ($score >= 80) {
                $levels['faible']++;
            } elseif ($score >= 60) {
                $levels['moyen']++;
            } elseif ($score >= 40) {
                $levels['eleve']++;
            } else {
                $levels['critique']++;
            }
        }

        $unanalyzed = Project::whereDoesntHave('analyses')->count();

        return [
            ['label' => 'Faible', 'value' => $levels['faible']],
            ['label' => 'Moyen', 'value' => $levels['moyen']],
            ['label' => 'Élevé', 'value' => $levels['eleve']],
            ['label' => 'Critique', 'value' => $levels['critique'] + $unanalyzed],
        ];
    }
}
