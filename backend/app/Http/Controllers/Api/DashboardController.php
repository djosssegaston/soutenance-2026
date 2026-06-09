<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Funding;
use App\Models\Institution;
use App\Models\InstitutionAnalysis;
use App\Models\Message;
use App\Models\Project;
use App\Models\Repayment;
use App\Models\User;
use App\Models\UserNotification;
use App\Support\DashboardDataHelpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use DashboardDataHelpers;

    public function getAdminStats(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $totalFunding = (float) Funding::sum('montant');
        $totalRepayments = (float) Repayment::sum('montant_total');
        $paidRepayments = (float) Repayment::where('statut', 'paye')->sum('montant_rembourse');

        $projectsThisMonth = Project::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $projectsLastMonth = Project::whereBetween('created_at', [
            now()->copy()->subMonth()->startOfMonth(),
            now()->copy()->subMonth()->endOfMonth(),
        ])->count();

        return response()->json([
            'total_users' => User::count(),
            'total_projects' => Project::count(),
            'total_funding' => $totalFunding,
            'total_repayments' => $paidRepayments,
            'repayment_rate' => $totalRepayments > 0 ? round(($paidRepayments / $totalRepayments) * 100, 1) : 0,
            'active_disputes' => Dispute::whereIn('statut', ['ouvert', 'en_mediation'])->count(),
            'projects_this_month' => $projectsThisMonth,
            'projects_last_month' => $projectsLastMonth,
            'project_trend' => $projectsLastMonth > 0
                ? round((($projectsThisMonth - $projectsLastMonth) / $projectsLastMonth) * 100, 1)
                : 0,
        ]);
    }

    public function getInstitutionStats(Request $request)
    {
        if ($request->user()->role !== 'institution') {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json([
                'projects_analyzed' => 0,
                'projects_financed' => 0,
                'total_invested' => 0,
                'roi_average' => 0,
                'performance_score' => 0,
            ]);
        }

        $institutionId = $institution->id;

        $fundings = Funding::where('institution_id', $institutionId);
        $projectIds = (clone $fundings)->pluck('project_id')->unique();

        $totalInvested = (float) (clone $fundings)->whereIn('statut', ['disbursed', 'active', 'completed'])->sum('montant_valide');

        $paidReturns = (float) \App\Models\Echeance::whereIn('project_id', $projectIds)
            ->where('statut', 'paid')
            ->sum('montant_paye');

        $totalEcheances = \App\Models\Echeance::whereIn('project_id', $projectIds)->count();
        $onTimeEcheances = \App\Models\Echeance::whereIn('project_id', $projectIds)
            ->where('statut', 'paid')
            ->whereColumn('date_paiement', '<=', 'date_echeance')
            ->count();

        return response()->json([
            'projects_analyzed' => InstitutionAnalysis::where('institution_id', $institutionId)->count(),
            'projects_financed' => $projectIds->count(),
            'total_invested' => $totalInvested,
            'roi_average' => $totalInvested > 0 ? round(($paidReturns / $totalInvested) * 100, 1) : 0,
            'performance_score' => $totalEcheances > 0 ? round(($onTimeEcheances / $totalEcheances) * 100, 1) : 0,
        ]);
    }

    public function getPorteurKpis(Request $request)
    {
        $user = $request->user();

        $projectIds = Project::where('user_id', $user->id)->pluck('id');
        $projectsCount = $projectIds->count();

        $totalFinance = (float) Funding::whereIn('project_id', $projectIds)
            ->whereIn('statut', ['disbursed', 'active', 'completed', 'approved'])
            ->sum('montant_valide');

        $totalRepaid = (float) \App\Models\Echeance::whereIn('project_id', $projectIds)
            ->where('statut', 'paid')
            ->sum('montant_paye');

        $totalEcheances = (float) \App\Models\Echeance::whereIn('project_id', $projectIds)
            ->sum('montant_total');

        $repaymentPercentage = $totalEcheances > 0
            ? round(($totalRepaid / $totalEcheances) * 100, 1)
            : 0;

        return response()->json([
            'projects_count' => $projectsCount,
            'total_finance_received' => $totalFinance,
            'total_finance' => $totalFinance,
            'repayment_percentage' => $repaymentPercentage,
            'total_repayment_done' => $totalRepaid,
            'total_funded' => $totalFinance,
            'repayment_rate' => $repaymentPercentage,
        ]);
    }

    public function getPorteurFinancingStats(Request $request)
    {
        $user = $request->user();
        $projectIds = Project::where('user_id', $user->id)->pluck('id');

        $fundings = Funding::with('institution')
            ->whereIn('project_id', $projectIds)
            ->latest()
            ->get();

        $enAttentePlan = $fundings->where('statut', 'awaiting_borrower_plan')->count();
        $enValidationImf = $fundings->where('statut', 'awaiting_imf_validation')->count();
        $actifs = $fundings->whereIn('statut', ['disbursed', 'active'])->count();
        $termines = $fundings->whereIn('statut', ['completed'])->count();

        $totalFinance = (float) $fundings->whereIn('statut', ['disbursed', 'active', 'completed'])->sum('montant_valide');

        $totalRembourse = (float) Echeance::whereIn('project_id', $projectIds)
            ->where('statut', 'paid')
            ->sum('montant_paye');

        $prochaineEcheance = Echeance::with('funding.institution')
            ->whereIn('project_id', $projectIds)
            ->whereIn('statut', ['pending', 'upcoming'])
            ->where('date_echeance', '>=', now())
            ->orderBy('date_echeance')
            ->first();

        return response()->json([
            'total_financements' => $fundings->count(),
            'en_attente_plan' => $enAttentePlan,
            'en_validation_imf' => $enValidationImf,
            'actifs' => $actifs,
            'termines' => $termines,
            'total_finance' => $totalFinance,
            'total_rembourse' => $totalRembourse,
            'prochaine_echeance' => $prochaineEcheance ? [
                'id' => $prochaineEcheance->id,
                'montant' => $prochaineEcheance->montant_total,
                'date' => $prochaineEcheance->date_echeance?->format('d M Y'),
                'projet' => $prochaineEcheance->funding?->project?->titre ?? 'N/A',
                'institution' => $prochaineEcheance->funding?->institution?->nom ?? 'N/A',
            ] : null,
        ]);
    }

    public function getPorteurProjects(Request $request)
    {
        $user = $request->user();

        $projects = Project::where('user_id', $user->id)
            ->with(['financements.institution', 'validations', 'analyses', 'repayments'])
            ->latest()
            ->get();

        $formattedProjects = $projects->map(function (Project $project) {
            $statusEnum = $project->statusEnum();
            $funded = (int) $project->montant_finance;
            $progress = $project->montant_demande > 0
                ? (int) round(($funded / $project->montant_demande) * 100)
                : 0;

            // Get latest funding for institution info
            $latestFunding = $project->financements->sortByDesc('date_financement')->first();

            // Check repayment status
            $repaymentStatus = 'en_attente';
            if ($project->repayments->where('statut', 'en_retard')->count() > 0) {
                $repaymentStatus = 'en_retard';
            } elseif ($project->repayments->where('statut', 'paye')->count() > 0) {
                $repaymentStatus = $project->repayments->where('statut', 'en_attente')->count() > 0 ? 'en_cours' : 'termine';
            }

            return [
                'id' => $project->id,
                'titre' => $project->titre,
                'secteur' => $project->secteur ?? 'N/A',
                'description' => $project->description ?? '',
                'montant_demande' => (float) $project->montant_demande,
                'montant_finance' => $funded,
                'progress' => min($progress, 100),
                'statut' => $project->statut,
                'statut_label' => $statusEnum->label(),
                'statut_color' => $statusEnum->color(),
                'statut_can_edit' => $statusEnum->canOwnerModify(),
                'statut_can_delete' => $statusEnum->canOwnerModify(),
                'statut_can_submit' => $statusEnum->canOwnerSubmit(),
                'date_creation' => $project->created_at ? $project->created_at->format('Y-m-d') : null,
                'date_creation_formatted' => $project->created_at ? $project->created_at->format('d M Y') : 'N/A',
                'timestamp' => $project->created_at ? $project->created_at->timestamp : 0,
                'institution' => $latestFunding && $latestFunding->institution
                    ? $latestFunding->institution->nom
                    : null,
                'remboursement' => $repaymentStatus,
                'duree' => $project->duree ?? 'N/A',
                'localisation' => $project->localisation ?? 'N/A',
            ];
        })->values();

        // Calculate statistics
        $stats = [
            'total' => $formattedProjects->count(),
            'submitted' => $formattedProjects->whereIn('statut', ['submitted', 'under_admin_review', 'admin_validated'])->count(),
            'funded' => $formattedProjects->whereIn('statut', ['funded', 'active', 'completed'])->count(),
            'rejected' => $formattedProjects->whereIn('statut', ['admin_rejected', 'institution_rejected'])->count(),
            'draft' => $formattedProjects->where('statut', 'draft')->count(),
        ];

        return response()->json([
            'success' => true,
            'projects' => $formattedProjects,
            'stats' => $stats,
        ]);
    }

    public function getNextRepayment(Request $request)
    {
        $projectIds = Project::where('user_id', $request->user()->id)->pluck('id');

        $nextRepayment = \App\Models\Echeance::with('project')
            ->whereIn('project_id', $projectIds)
            ->where('date_echeance', '>=', now())
            ->whereIn('statut', ['pending', 'upcoming', 'overdue'])
            ->orderBy('date_echeance')
            ->first();

        if (! $nextRepayment) {
            return response()->json(['next_payment' => null]);
        }

        return response()->json([
            'next_payment' => [
                'project' => optional($nextRepayment->project)->titre,
                'date' => Carbon::parse($nextRepayment->date_echeance)->format('d M Y'),
                'amount' => $this->formatMoney($nextRepayment->montant_total),
            ],
        ]);
    }

    public function getRepaymentHistory(Request $request)
    {
        $projectIds = Project::where('user_id', $request->user()->id)->pluck('id');

        $repayments = \App\Models\Echeance::whereIn('project_id', $projectIds)
            ->where('date_paiement', '>=', now()->subMonths(12))
            ->where('statut', 'paid')
            ->select(
                DB::raw('DATE_FORMAT(date_paiement, "%Y-%m") as month'),
                DB::raw('SUM(montant_paye) as total_amount')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $chartData = [];
        $cursor = now()->copy()->subMonths(11)->startOfMonth();

        for ($i = 0; $i < 12; $i++) {
            $monthKey = $cursor->format('Y-m');
            $entry = $repayments->firstWhere('month', $monthKey);

            $chartData[] = [
                'month' => $cursor->format('M Y'),
                'amount' => $entry ? (float) $entry->total_amount : 0,
            ];

            $cursor->addMonth();
        }

        return response()->json(['repayment_history' => $chartData]);
    }

    public function getRecentActivities(Request $request)
    {
        $user = $request->user();
        $projectIds = Project::where('user_id', $user->id)->pluck('id');

        $activities = collect();

        $activities = $activities->merge(
            UserNotification::where('user_id', $user->id)
                ->latest()
                ->take(6)
                ->get()
                ->map(fn (UserNotification $notification) => [
                    'title' => $this->notificationTitle($notification->type),
                    'message' => $notification->content,
                    'created_at' => $notification->created_at,
                    'variant' => $this->notificationVariant($notification->type),
                ])
        );

        $activities = $activities->merge(
            Funding::whereIn('project_id', $projectIds)
                ->latest()
                ->take(3)
                ->get()
                ->map(fn (Funding $funding) => [
                    'title' => 'Nouveau financement',
                    'message' => 'Un financement a été enregistré sur un de vos projets.',
                    'created_at' => $funding->created_at,
                    'variant' => 'warning',
                ])
        );

        $activities = $activities->merge(
            Repayment::whereIn('project_id', $projectIds)
                ->where('statut', 'paye')
                ->latest('date_paiement')
                ->take(3)
                ->get()
                ->map(fn (Repayment $repayment) => [
                    'title' => 'Paiement reçu',
                    'message' => 'Un remboursement a été confirmé.',
                    'created_at' => $repayment->updated_at,
                    'variant' => 'success',
                ])
        );

        $activities = $activities->merge(
            Project::whereIn('id', $projectIds)
                ->where('statut', '!=', ProjectStatus::DRAFT->value)
                ->latest()
                ->take(3)
                ->get()
                ->map(fn (Project $project) => [
                    'title' => 'Projet soumis',
                    'message' => $project->titre.' est en cours de traitement.',
                    'created_at' => $project->updated_at,
                    'variant' => 'info',
                ])
        );

        return response()->json([
            'activities' => $activities
                ->sortByDesc('created_at')
                ->take(10)
                ->values()
                ->map(fn (array $activity) => [
                    'title' => $activity['title'],
                    'message' => $activity['message'],
                    'time' => Carbon::parse($activity['created_at'])->diffForHumans(),
                    'variant' => $activity['variant'],
                ]),
        ]);
    }

    public function getCounts(Request $request)
    {
        return response()->json([
            'messages_count' => Message::where('receiver_id', $request->user()->id)
                ->where('is_read', false)
                ->count(),
            'notifications_count' => UserNotification::where('user_id', $request->user()->id)
                ->where('is_read', false)
                ->count(),
        ]);
    }

    public function getAdminRecentProjects(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $projects = Project::with('owner')
            ->latest()
            ->take(5)
            ->get()
            ->map(function (Project $project) {
                $status = $project->statusEnum();

                return [
                    'id' => $project->id,
                    'code' => $this->projectCode($project->id),
                    'titre' => $project->titre,
                    'owner_name' => optional($project->owner)->name ?? 'N/A',
                    'montant_demande' => (float) $project->montant_demande,
                    'statut' => $status->value,
                    'statut_label' => $status->label(),
                    'statut_class' => $status->badgeClass(),
                    'created_at' => $project->created_at?->format('d/m/Y'),
                ];
            });

        return response()->json(['projects' => $projects]);
    }

    public function getInstitutionProjects(Request $request)
    {
        if ($request->user()->role !== 'institution') {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json(['projects' => []]);
        }

        $projects = Project::whereIn('statut', ProjectStatus::institutionAvailableValues())
            ->whereDoesntHave('financements', function ($query) use ($institution) {
                $query->where('institution_id', $institution->id);
            })
            ->with('owner')
            ->latest()
            ->take(20)
            ->get()
            ->map(function (Project $project) use ($institution) {
                $analysis = InstitutionAnalysis::where('project_id', $project->id)
                    ->where('institution_id', $institution->id)
                    ->first();

                $riskScore = $analysis?->risk_score;

                $niveauRisque = match (true) {
                    $riskScore >= 80 => 'Faible',
                    $riskScore >= 60 => 'Moyen',
                    $riskScore >= 40 => 'Élevé',
                    $riskScore !== null => 'Critique',
                    default => 'Faible',
                };

                return [
                    'id' => $project->id,
                    'code' => $this->projectCode($project->id),
                    'titre' => $project->titre,
                    'secteur' => $project->secteur ?? 'N/A',
                    'montant_demande' => (float) $project->montant_demande,
                    'statut' => $project->statusEnum()->value,
                    'statut_label' => $project->statusEnum()->label(),
                    'statut_class' => $project->statusEnum()->badgeClass(),
                    'niveau_risque' => $niveauRisque,
                    'porteur' => $project->relationLoaded('owner') && $project->owner
                        ? ['name' => $project->owner->name]
                        : null,
                ];
            });

        return response()->json(['projects' => $projects]);
    }

    public function getInstitutionPortfolio(Request $request)
    {
        if ($request->user()->role !== 'institution') {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $institution = $request->user()->institution;
        if (! $institution) {
            return response()->json([
                'portfolio_active' => 0,
                'volume_investi' => 0,
                'roi_moyen' => 0,
                'performance_score' => 0,
                'labels' => [],
                'performance_data' => [],
            ]);
        }

        $projectIds = Funding::where('institution_id', $institution->id)->pluck('project_id')->unique();
        $volumeInvesti = (float) Funding::where('institution_id', $institution->id)
            ->whereIn('statut', array_merge(['disbursed', 'active', 'completed'], ['decaisse', 'confirme', 'finance']))
            ->sum(DB::raw('COALESCE(montant_valide, montant, 0)'));

        $paidReturns = (float) \App\Models\Echeance::whereIn('project_id', $projectIds)
            ->where('statut', 'paid')
            ->sum('montant_paye');

        $monthlyData = Funding::where('institution_id', $institution->id)
            ->where('date_financement', '>=', now()->subMonths(12))
            ->select(
                DB::raw('DATE_FORMAT(date_financement, "%Y-%m") as month'),
                DB::raw('SUM(COALESCE(montant_valide, montant, 0)) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $performanceData = [];
        $cursor = now()->copy()->subMonths(11)->startOfMonth();

        for ($i = 0; $i < 12; $i++) {
            $monthKey = $cursor->format('Y-m');
            $entry = $monthlyData->firstWhere('month', $monthKey);
            $labels[] = $cursor->format('M Y');
            $performanceData[] = $entry ? (float) $entry->total : 0;
            $cursor->addMonth();
        }

        return response()->json([
            'portfolio_active' => Project::whereIn('id', $projectIds)
                ->whereIn('statut', ProjectStatus::institutionPortfolioValues())
                ->count(),
            'volume_investi' => $volumeInvesti,
            'roi_moyen' => $volumeInvesti > 0 ? round(($paidReturns / $volumeInvesti) * 100, 1) : 0,
            'performance_score' => $this->institutionPerformanceScore($institution),
            'labels' => $labels,
            'performance_data' => $performanceData,
        ]);
    }
}
