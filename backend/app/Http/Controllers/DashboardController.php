<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Models\Funding;
use App\Models\Institution;
use App\Models\InstitutionAnalysis;
use App\Models\Project;
use App\Models\Repayment;
use App\Models\User;
use App\Models\UserNotification;
use App\Support\DashboardDataHelpers;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    use DashboardDataHelpers;

    public function admin()
    {
        $admin = auth()->user();

        $projects = Project::with('owner')->latest()->take(5)->get();
        $totalProjects = Project::count();
        $totalFunding = Funding::sum('montant');
        $totalRepayments = Repayment::sum('montant_total');
        $paidRepayments = Repayment::where('statut', 'paye')->sum('montant_rembourse');
        $repaymentRate = $totalRepayments > 0 ? round(($paidRepayments / $totalRepayments) * 100, 1) : 0;
        $defaultProjects = Project::whereHas('remboursements', function ($query) {
            $query->where('statut', 'en_retard');
        })->distinct()->count();
        $defaultRate = $totalProjects > 0 ? round(($defaultProjects / $totalProjects) * 100, 1) : 0;

        $projectTrend = $this->buildTrend(
            Project::whereBetween('created_at', [$this->monthStart(0), $this->monthEnd(0)])->count(),
            Project::whereBetween('created_at', [$this->monthStart(1), $this->monthEnd(1)])->count(),
            ''
        );

        $fundingTrend = $this->buildTrend(
            Funding::whereBetween('date_financement', [$this->monthStart(0), $this->monthEnd(0)])->sum('montant'),
            Funding::whereBetween('date_financement', [$this->monthStart(1), $this->monthEnd(1)])->sum('montant'),
            '%'
        );

        $repaymentTrend = $this->buildTrend(
            $paidRepayments,
            Repayment::where('statut', 'paye')->whereBetween('date_paiement', [$this->monthStart(1), $this->monthEnd(1)])->sum('montant_rembourse'),
            '%'
        );

        $defaultTrend = $this->buildTrend(
            $defaultProjects,
            Project::whereHas('remboursements', function ($query) {
                $query->where('statut', 'en_retard');
            })->whereBetween('created_at', [$this->monthStart(1), $this->monthEnd(1)])->distinct()->count(),
            '%',
            invert: true
        );

        $admin_kpis = [
            [
                'icon' => 'bi-folder2',
                'title' => 'Projets Totaux',
                'value' => (string) $totalProjects,
                'trend' => $projectTrend['label'],
                'trend_class' => $projectTrend['class'],
                'trend_icon' => $projectTrend['icon'],
                'icon_bg' => 'rgba(0, 196, 134, 0.15)',
                'icon_color' => 'var(--primary-color-1)',
            ],
            [
                'icon' => 'bi-cash-coin',
                'title' => 'Volume Finance',
                'value' => $this->formatMoneyCompact($totalFunding),
                'trend' => $fundingTrend['label'],
                'trend_class' => $fundingTrend['class'],
                'trend_icon' => $fundingTrend['icon'],
                'icon_bg' => 'rgba(252, 160, 40, 0.18)',
                'icon_color' => 'var(--primary-color-3)',
            ],
            [
                'icon' => 'bi-graph-up-arrow',
                'title' => 'Taux Remboursement',
                'value' => $repaymentRate.'%',
                'trend' => $repaymentTrend['label'],
                'trend_class' => $repaymentTrend['class'],
                'trend_icon' => $repaymentTrend['icon'],
                'icon_bg' => 'rgba(0, 72, 220, 0.12)',
                'icon_color' => 'var(--primary-color-2)',
            ],
            [
                'icon' => 'bi-exclamation-triangle',
                'title' => 'Taux Defaut',
                'value' => $defaultRate.'%',
                'trend' => $defaultTrend['label'],
                'trend_class' => $defaultTrend['class'],
                'trend_icon' => $defaultTrend['icon'],
                'icon_bg' => 'rgba(243, 81, 32, 0.12)',
                'icon_color' => 'var(--primary-color-4)',
            ],
        ];

        $recent_projects = $projects->map(function (Project $project) {
            $statusMeta = $this->mapProjectStatus($project->statut);

            return [
                'code' => $this->projectCode($project->id),
                'title' => (string) $project->titre,
                'owner' => optional($project->owner)->name ?? 'N/A',
                'amount' => $this->formatMoneyCompact($project->montant_demande),
                'status_label' => $statusMeta['label'],
                'status_class' => $statusMeta['class'],
            ];
        })->all();

        $sector_breakdown = $this->buildSectorBreakdown();

        $top_institutions = Institution::withSum('financements as total_financed', 'montant')
            ->withCount('financements')
            ->orderByDesc('total_financed')
            ->take(3)
            ->get()
            ->map(function (Institution $institution) {
                $score = $this->institutionPerformanceScore($institution);

                return [
                    'name' => $institution->nom,
                    'amount' => $this->formatMoneyCompact((float) $institution->total_financed),
                    'projects' => $institution->financements_count.' projets',
                    'score' => (int) $score,
                    'bar_class' => $this->scoreClass($score),
                ];
            })
            ->all();

        $header_notifications = $this->formatNotifications(UserNotification::where('user_id', $admin->id)->latest()->take(3)->get());

        $admin_funding_chart = $this->buildFundingChart();
        $admin_sector_chart = [
            'labels' => Arr::pluck($sector_breakdown, 'label'),
            'values' => Arr::pluck($sector_breakdown, 'percent'),
        ];

        return view('dashboard.dashboard_admin', [
            'page_title' => 'Tableau de bord',
            'page_subtitle' => 'Administration generale',
            'mobile_nav_messages_badge' => $this->countUnreadMessages($admin),
            'mobile_nav_notifications_badge' => UserNotification::where('user_id', $admin->id)->where('is_read', false)->count(),
            'dashboard_user_name' => $admin->name,
            'dashboard_user_id' => 'ADM-'.str_pad((string) $admin->id, 4, '0', STR_PAD_LEFT),
            'dashboard_user_role' => 'Administrateur plateforme',
            'header_user_name' => $admin->name,
            'header_user_initials' => $this->initials($admin->name),
            'header_notifications' => $header_notifications,
            'admin_kpis' => $admin_kpis,
            'recent_projects' => $recent_projects,
            'sector_breakdown' => $sector_breakdown,
            'top_institutions' => $top_institutions,
            'admin_funding_chart' => $admin_funding_chart,
            'admin_sector_chart' => $admin_sector_chart,
        ]);
    }

    public function porteur()
    {
        $porteur = auth()->user();
        $projectIds = $porteur->projects()->pluck('id');

        $projects = Project::whereIn('id', $projectIds)->get();
        $fundedTotal = Funding::whereIn('project_id', $projectIds)->sum('montant');
        $repaymentTotal = Repayment::whereIn('project_id', $projectIds)->sum('montant_total');
        $repaymentPaid = Repayment::whereIn('project_id', $projectIds)->where('statut', 'paye')->sum('montant_rembourse');
        $repaymentRate = $repaymentTotal > 0 ? round(($repaymentPaid / $repaymentTotal) * 100, 1) : 0;

        $projectsThisMonth = Project::whereIn('id', $projectIds)->whereBetween('created_at', [$this->monthStart(0), $this->monthEnd(0)])->count();
        $projectsLastMonth = Project::whereIn('id', $projectIds)->whereBetween('created_at', [$this->monthStart(1), $this->monthEnd(1)])->count();
        $projectsTrend = $this->buildTrend($projectsThisMonth, $projectsLastMonth, '');

        $fundingThisMonth = Funding::whereIn('project_id', $projectIds)->whereBetween('date_financement', [$this->monthStart(0), $this->monthEnd(0)])->sum('montant');
        $fundingLastMonth = Funding::whereIn('project_id', $projectIds)->whereBetween('date_financement', [$this->monthStart(1), $this->monthEnd(1)])->sum('montant');
        $fundingTrend = $this->buildTrend($fundingThisMonth, $fundingLastMonth, '%');

        $repaymentThisMonth = Repayment::whereIn('project_id', $projectIds)->where('statut', 'paye')->whereBetween('date_paiement', [$this->monthStart(0), $this->monthEnd(0)])->sum('montant_rembourse');
        $repaymentLastMonth = Repayment::whereIn('project_id', $projectIds)->where('statut', 'paye')->whereBetween('date_paiement', [$this->monthStart(1), $this->monthEnd(1)])->sum('montant_rembourse');
        $repaymentTrend = $this->buildTrend($repaymentThisMonth, $repaymentLastMonth, '%');

        $porteur_kpis = [
            [
                'icon' => 'bi-folder2',
                'title' => 'Mes Projets',
                'value' => (string) $projects->count(),
                'trend' => $projectsTrend['label'],
                'trend_class' => $projectsTrend['class'],
                'trend_icon' => $projectsTrend['icon'],
                'icon_bg' => 'rgba(0, 196, 134, 0.15)',
                'icon_color' => 'var(--primary-color-1)',
            ],
            [
                'icon' => 'bi-cash-coin',
                'title' => 'Finance total recu',
                'value' => $this->formatMoneyCompact($fundedTotal),
                'trend' => $fundingTrend['label'],
                'trend_class' => $fundingTrend['class'],
                'trend_icon' => $fundingTrend['icon'],
                'icon_bg' => 'rgba(252, 160, 40, 0.18)',
                'icon_color' => 'var(--primary-color-3)',
            ],
            [
                'icon' => 'bi-graph-up-arrow',
                'title' => 'Pourcentage total de remboursement',
                'value' => $repaymentRate.'%',
                'trend' => $repaymentTrend['label'],
                'trend_class' => $repaymentTrend['class'],
                'trend_icon' => $repaymentTrend['icon'],
                'icon_bg' => 'rgba(0, 72, 220, 0.12)',
                'icon_color' => 'var(--primary-color-2)',
            ],
            [
                'icon' => 'bi-credit-card-2-front',
                'title' => 'Remboursement total effectue',
                'value' => $this->formatMoneyCompact($repaymentPaid),
                'trend' => 'Mensuel',
                'trend_class' => 'is-neutral',
                'trend_icon' => 'bi-calendar-check',
                'icon_bg' => 'rgba(243, 81, 32, 0.12)',
                'icon_color' => 'var(--primary-color-4)',
            ],
        ];

        $porteur_projects = $projects->map(function (Project $project) {
            // Utiliser la state machine au lieu des anciennes colonnes
            $currentStatus = ProjectStatus::tryFrom($project->statut) ?? ProjectStatus::DRAFT;

            return [
                'code' => $this->projectCode($project->id),
                'title' => (string) $project->titre,
                'sector' => $project->secteur ?? 'N/A',
                'amount' => $this->formatMoneyCompact($project->montant_demande),
                'status_label' => $currentStatus->label(),
                'status_class' => 'status-'.$currentStatus->color().' '.$currentStatus->color(),
                'validation_label' => $currentStatus->label(),
                'validation_class' => 'status-'.$currentStatus->color().' '.$currentStatus->color(),
                'funding_label' => $currentStatus->isActive() ? 'Actif' : 'En attente',
                'funding_class' => $currentStatus->isActive() ? 'status-approved approved' : 'status-submitted submitted',
            ];
        })->all();

        $nextRepayment = Repayment::whereIn('project_id', $projectIds)
            ->whereIn('statut', ['en_attente', 'en_retard'])
            ->orderBy('date_echeance')
            ->first();

        $next_payment = [
            'project' => $nextRepayment ? optional($nextRepayment->project)->titre : 'Aucun remboursement',
            'date' => $nextRepayment && $nextRepayment->date_echeance ? Carbon::parse($nextRepayment->date_echeance)->locale('fr')->isoFormat('D MMMM YYYY') : 'A definir',
            'amount' => $nextRepayment ? $this->formatMoney($nextRepayment->montant_total) : '0 FCFA',
        ];

        $activity_timeline = $this->formatTimeline(UserNotification::where('user_id', $porteur->id)->latest()->take(4)->get());

        $porteur_repayment_chart = $this->buildRepaymentChart($projectIds);

        return view('dashboard.dashboard_porteur', [
            'page_title' => 'Tableau de bord',
            'page_subtitle' => $porteur->name.' - Porteur de projet',
            'mobile_nav_messages_badge' => $this->countUnreadMessages($porteur),
            'mobile_nav_notifications_badge' => UserNotification::where('user_id', $porteur->id)->where('is_read', false)->count(),
            'dashboard_user_name' => $porteur->name,
            'dashboard_user_id' => 'PRT-'.str_pad((string) $porteur->id, 4, '0', STR_PAD_LEFT),
            'dashboard_user_role' => 'Porteur de projet',
            'header_user_name' => $porteur->name,
            'header_user_initials' => $this->initials($porteur->name),
            'header_notifications' => $this->formatNotifications(UserNotification::where('user_id', $porteur->id)->latest()->take(3)->get()),
            'porteur_kpis' => $porteur_kpis,
            'porteur_projects' => $porteur_projects,
            'next_payment' => $next_payment,
            'activity_timeline' => $activity_timeline,
            'porteur_repayment_chart' => $porteur_repayment_chart,
        ]);
    }

    public function institution()
    {
        $institutionUser = auth()->user();
        $institution = $institutionUser->institution;

        $projectIds = Funding::where('institution_id', $institution->id)->pluck('project_id')->unique();
        $fundedTotal = Funding::where('institution_id', $institution->id)->sum('montant');
        $paidRepayments = Repayment::whereIn('project_id', $projectIds)->where('statut', 'paye')->sum('montant_rembourse');

        $roi = $fundedTotal > 0 ? round((($paidRepayments / $fundedTotal) - 1) * 100, 1) : 0;
        $performanceScore = $this->institutionPerformanceScore($institution);

        $portfolioTrend = $this->buildTrend(
            Funding::where('institution_id', $institution->id)->whereBetween('created_at', [$this->monthStart(0), $this->monthEnd(0)])->count(),
            Funding::where('institution_id', $institution->id)->whereBetween('created_at', [$this->monthStart(1), $this->monthEnd(1)])->count(),
            ''
        );

        $volumeTrend = $this->buildTrend(
            Funding::where('institution_id', $institution->id)->whereBetween('date_financement', [$this->monthStart(0), $this->monthEnd(0)])->sum('montant'),
            Funding::where('institution_id', $institution->id)->whereBetween('date_financement', [$this->monthStart(1), $this->monthEnd(1)])->sum('montant'),
            '%'
        );

        $roiTrend = $this->buildTrend($roi, $roi - 1.2, '%');
        $performanceTrend = $this->buildTrend($performanceScore, $performanceScore - 2, '');

        $institution_kpis = [
            'portfolio_active' => $projectIds->count().' projets',
            'portfolio_trend' => $portfolioTrend['label'],
            'volume_invested' => $this->formatMoneyCompact($fundedTotal),
            'volume_trend' => $volumeTrend['label'],
            'roi_avg' => $roi.'%',
            'roi_trend' => $roiTrend['label'],
            'performance_score' => (int) $performanceScore.'/100',
            'performance_trend' => $performanceTrend['label'],
        ];

        $analysisRows = InstitutionAnalysis::with('project')
            ->where('institution_id', $institution->id)
            ->whereHas('project', function ($query) {
                $query->whereIn('statut', ProjectStatus::institutionAvailableValues());
            })
            ->latest()
            ->take(6)
            ->get();

        $available_projects = $analysisRows->map(function (InstitutionAnalysis $analysis) {
            $project = $analysis->project;
            $riskMeta = $this->mapRisk($analysis->risk_score);
            $statusMeta = $this->mapProjectStatus($project->statut);

            return [
                'code' => $this->projectCode($project->id),
                'title' => (string) $project->titre,
                'sector' => $project->secteur ?? 'N/A',
                'amount' => $this->formatMoneyCompact($project->montant_demande),
                'risk_label' => $riskMeta['label'],
                'risk_class' => $riskMeta['class'],
                'score' => (int) $analysis->risk_score,
                'score_class' => $riskMeta['score_class'],
                'status_label' => $statusMeta['label'],
                'status_class' => $statusMeta['class'],
            ];
        })->all();

        $repayments = Repayment::whereIn('project_id', $projectIds)->get();
        $totalRepaymentsCount = $repayments->count();
        $onTimeCount = $repayments->filter(function (Repayment $repayment) {
            return $repayment->statut === 'paye' && $repayment->date_paiement && $repayment->date_echeance && $repayment->date_paiement->lessThanOrEqualTo($repayment->date_echeance);
        })->count();
        $lateCount = $repayments->where('statut', 'en_retard')->count();
        $defaultCount = Project::whereIn('id', $projectIds)
            ->whereHas('remboursements', function ($query) {
                $query->where('statut', 'en_retard');
            })
            ->count();

        $institution_portfolio = [
            'total' => max($totalRepaymentsCount, 1),
            'on_time_count' => $onTimeCount,
            'late_count' => $lateCount,
            'default_count' => $defaultCount,
            'on_time_percent' => $this->percent($onTimeCount, $totalRepaymentsCount),
            'late_percent' => $this->percent($lateCount, $totalRepaymentsCount),
            'default_percent' => $this->percent($defaultCount, $totalRepaymentsCount),
        ];

        $institution_performance_chart = $this->buildInstitutionPerformanceChart($institution);

        return view('dashboard.dashboard_institution', [
            'page_title' => 'Tableau de bord',
            'page_subtitle' => $institution->nom.' - Vue d ensemble',
            'mobile_nav_messages_badge' => $this->countUnreadMessages($institutionUser),
            'mobile_nav_notifications_badge' => UserNotification::where('user_id', $institutionUser->id)->where('is_read', false)->count(),
            'dashboard_user_name' => $institution->nom,
            'dashboard_user_id' => $institutionUser->email,
            'dashboard_user_role' => 'Institution financiere',
            'header_user_name' => $institution->nom,
            'header_user_initials' => $this->initials($institution->nom),
            'header_notifications' => $this->formatNotifications(UserNotification::where('user_id', $institutionUser->id)->latest()->take(3)->get()),
            'institution_kpis' => $institution_kpis,
            'available_projects' => $available_projects,
            'institution_portfolio' => $institution_portfolio,
            'institution_performance_chart' => $institution_performance_chart,
        ]);
    }

    private function monthStart(int $offset): Carbon
    {
        return Carbon::now()->subMonths($offset)->startOfMonth();
    }

    private function monthEnd(int $offset): Carbon
    {
        return Carbon::now()->subMonths($offset)->endOfMonth();
    }

    private function buildTrend(float $current, float $previous, string $suffix): array
    {
        $delta = $current - $previous;
        $label = $suffix === '' ? (string) ($delta >= 0 ? '+' : '').round($delta, 1) : sprintf('%+0.1f%s', $previous > 0 ? ($delta / $previous) * 100 : 0, $suffix);

        return [
            'label' => $label,
            'class' => $delta >= 0 ? 'is-up' : 'is-down',
            'icon' => $delta >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right',
        ];
    }

    private function formatMoney(int|float|string $amount): string
    {
        return number_format((float) $amount, 0, ',', ' ').' FCFA';
    }

    private function formatMoneyCompact(int|float|string $amount): string
    {
        $amount = (float) $amount;

        if ($amount >= 1000000000) {
            return round($amount / 1000000000, 1).' Mrd FCFA';
        }
        if ($amount >= 1000000) {
            return round($amount / 1000000, 1).'M FCFA';
        }
        if ($amount >= 1000) {
            return round($amount / 1000, 1).'K FCFA';
        }

        return $amount.' FCFA';
    }

    private function projectCode(int $id): string
    {
        return 'PRJ-'.str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }

        return $initials ?: '--';
    }

    private function mapProjectStatus(ProjectStatus|string|null $status): array
    {
        $enum = ProjectStatus::fromStorageOr($status);

        return [
            'label' => $enum->label(),
            'class' => $enum->badgeClass(),
        ];
    }

    private function mapValidationStatus(string $status): array
    {
        $map = [
            'en_attente' => ['label' => 'en attente', 'class' => 'status-submitted submitted'],
            'en_analyse_admin' => ['label' => 'non analyse', 'class' => 'status-submitted submitted'],
            'valide' => ['label' => 'valide', 'class' => 'status-approved approved'],
            'refuse' => ['label' => 'refuse', 'class' => 'status-rejected rejected'],
        ];

        return $map[$status] ?? ['label' => $status, 'class' => 'status-submitted submitted'];
    }

    private function mapFundingStatus(string $status): array
    {
        $map = [
            'en_cours_de_traitement' => ['label' => 'en cours de traitement', 'class' => 'status-submitted submitted'],
            'en_analyse' => ['label' => 'en analyse', 'class' => 'status-submitted submitted'],
            'entretien_planifie' => ['label' => 'entretien planifie', 'class' => 'status-approved approved'],
            'accepte' => ['label' => 'accepte', 'class' => 'status-approved approved'],
            'finance' => ['label' => 'deja finance', 'class' => 'status-funded funded'],
            'termine' => ['label' => 'termine', 'class' => 'status-approved approved'],
        ];

        return $map[$status] ?? ['label' => $status, 'class' => 'status-submitted submitted'];
    }

    private function mapRisk(int $score): array
    {
        if ($score >= 80) {
            return ['label' => 'Faible', 'class' => 'risk-low', 'score_class' => 'mini-progress__bar--success'];
        }
        if ($score >= 60) {
            return ['label' => 'Moyen', 'class' => 'risk-medium', 'score_class' => ''];
        }

        return ['label' => 'Eleve', 'class' => 'risk-high', 'score_class' => 'mini-progress__bar--danger'];
    }

    private function scoreClass(float $score): string
    {
        if ($score >= 85) {
            return 'bg-success-token';
        }
        if ($score >= 70) {
            return 'bg-warning-token';
        }

        return 'bg-danger-token';
    }

    private function formatNotifications(Collection $notifications): array
    {
        return $notifications->map(function (UserNotification $notification) {
            return [
                'title' => $this->notificationTitle($notification->type),
                'message' => $notification->content,
                'time' => $notification->created_at ? $notification->created_at->diffForHumans() : 'Maintenant',
                'read' => (bool) $notification->is_read,
            ];
        })->all();
    }

    private function formatTimeline(Collection $notifications): array
    {
        return $notifications->map(function (UserNotification $notification) {
            $variant = 'info';
            if (str_contains($notification->type, 'remboursement')) {
                $variant = 'success';
            } elseif (str_contains($notification->type, 'litige')) {
                $variant = 'warning';
            }

            return [
                'title' => $this->notificationTitle($notification->type),
                'message' => $notification->content,
                'time' => $notification->created_at ? $notification->created_at->diffForHumans() : 'A l instant',
                'variant' => $variant,
            ];
        })->all();
    }

    private function notificationTitle(string $type): string
    {
        $map = [
            'validation_projet' => 'Projet valide',
            'refus_projet' => 'Projet refuse',
            'financement_recu' => 'Financement recu',
            'entretien_planifie' => 'Entretien planifie',
            'remboursement_confirme' => 'Paiement confirme',
            'litige_cree' => 'Litige ouvert',
            'message' => 'Nouveau message',
        ];

        return $map[$type] ?? 'Notification';
    }

    private function buildSectorBreakdown(): array
    {
        $totals = Project::selectRaw('secteur, COUNT(*) as total')
            ->whereNotNull('secteur')
            ->groupBy('secteur')
            ->orderByDesc('total')
            ->get();

        $totalProjects = $totals->sum('total');
        $colors = ['#00C486', '#0048DC', '#FCA028', '#F35120', '#6A726F'];

        $breakdown = [];
        $top = $totals->take(4);
        $used = 0;
        foreach ($top as $index => $row) {
            $percent = $totalProjects > 0 ? round(($row->total / $totalProjects) * 100) : 0;
            $used += $percent;
            $breakdown[] = [
                'label' => $row->secteur,
                'percent' => $percent,
                'color' => $colors[$index] ?? '#6A726F',
            ];
        }
        $remaining = max(0, 100 - $used);
        $breakdown[] = [
            'label' => 'Autre',
            'percent' => $remaining,
            'color' => $colors[4],
        ];

        return $breakdown;
    }

    private function buildFundingChart(): array
    {
        $months = $this->lastMonths(6);
        $labels = $months->map(fn (Carbon $date) => $this->monthShort($date))->all();
        $fundingValues = [];
        $repaymentValues = [];

        foreach ($months as $date) {
            $fundingValues[] = round(Funding::whereBetween('date_financement', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])->sum('montant') / 1000000, 1);
            $repaymentValues[] = round(Repayment::whereBetween('date_paiement', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])->sum('montant_rembourse') / 1000000, 1);
        }

        return [
            'labels' => $labels,
            'financements' => $fundingValues,
            'remboursements' => $repaymentValues,
        ];
    }

    private function buildRepaymentChart(Collection $projectIds): array
    {
        $months = $this->lastMonths(6);
        $labels = $months->map(fn (Carbon $date) => $this->monthShort($date))->all();
        $values = [];

        foreach ($months as $date) {
            $values[] = round(Repayment::whereIn('project_id', $projectIds)
                ->where('statut', 'paye')
                ->whereBetween('date_paiement', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
                ->sum('montant_rembourse') / 1000, 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function buildInstitutionPerformanceChart(Institution $institution): array
    {
        $months = $this->lastMonths(6);
        $labels = $months->map(fn (Carbon $date) => $this->monthShort($date))->all();
        $roiValues = [];
        $riskValues = [];

        $projectIds = Funding::where('institution_id', $institution->id)->pluck('project_id');

        foreach ($months as $date) {
            $funded = Funding::where('institution_id', $institution->id)
                ->whereBetween('date_financement', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
                ->sum('montant');
            $paid = Repayment::whereIn('project_id', $projectIds)
                ->where('statut', 'paye')
                ->whereBetween('date_paiement', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
                ->sum('montant_rembourse');
            $roiValues[] = $funded > 0 ? round((($paid / $funded) - 1) * 100, 1) : 0;

            $lateCount = Repayment::whereIn('project_id', $projectIds)
                ->where('statut', 'en_retard')
                ->whereBetween('date_echeance', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
                ->count();
            $dueCount = Repayment::whereIn('project_id', $projectIds)
                ->whereBetween('date_echeance', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
                ->count();
            $riskValues[] = $dueCount > 0 ? round(($lateCount / $dueCount) * 100, 1) : 0;
        }

        return [
            'labels' => $labels,
            'roi' => $roiValues,
            'risk' => $riskValues,
        ];
    }

    private function institutionPerformanceScore(Institution $institution): float
    {
        $projectIds = Funding::where('institution_id', $institution->id)->pluck('project_id');
        $total = Repayment::whereIn('project_id', $projectIds)->count();
        if ($total === 0) {
            return 0;
        }
        $onTime = Repayment::whereIn('project_id', $projectIds)
            ->where('statut', 'paye')
            ->whereColumn('date_paiement', '<=', 'date_echeance')
            ->count();

        return round(($onTime / $total) * 100, 1);
    }

    private function percent(int $value, int $total): int
    {
        return $total > 0 ? (int) round(($value / $total) * 100) : 0;
    }

    private function lastMonths(int $count): Collection
    {
        return collect(range($count - 1, 0))->map(fn ($i) => Carbon::now()->subMonths($i));
    }

    private function monthShort(Carbon $date): string
    {
        $map = [
            1 => 'Jan',
            2 => 'Fev',
            3 => 'Mar',
            4 => 'Avr',
            5 => 'Mai',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aou',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];

        return $map[$date->month] ?? $date->format('M');
    }

    private function countUnreadMessages(User $user): int
    {
        return $user->receivedMessages()
            ->where('is_read', false)
            ->count();
    }
}
