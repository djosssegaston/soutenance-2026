<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Funding;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\Repayment;
use App\Models\UserNotification;
use App\Support\DashboardDataHelpers;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardPagesController extends Controller
{
    use DashboardDataHelpers;

    public function adminPage(Request $request, ?string $page = null)
    {
        $page = $page ?? (string) $request->route('page');

        return $this->redirectToDashboard02($request, 'admin', $page);
    }

    public function porteurPage(Request $request, ?string $page = null)
    {
        $page = $page ?? (string) $request->route('page');

        return $this->redirectToDashboard02($request, 'porteur', $page);
    }

    public function institutionPage(Request $request, ?string $page = null)
    {
        $page = $page ?? (string) $request->route('page');

        return $this->redirectToDashboard02($request, 'institution', $page);
    }

    public function projectDetails(Request $request, \App\Models\Project $project)
    {
        $this->ensureDashboardRoleAccess($request, 'porteur');

        $project->load(['financements.institution', 'documents', 'analyses.institution']);

        return $this->renderFrontendFile(
            base_path('../frontend/dashboard02/porteur'),
            'details_projet.php',
            '/frontend/dashboard02/porteur/details_projet.php',
            [
                'csrf_token' => csrf_token(),
                'user' => $request->user(),
                'logout_url' => route('logout'),
                'project' => $project,
            ]
        );
    }

    public function serveDashboard02(Request $request, string $role, string $path = '')
    {
        $this->ensureDashboardRoleAccess($request, $role);

        $relativePath = trim($path, '/');
        if ($relativePath === '') {
            $relativePath = $this->dashboard02HomeFile($role);
        }

        $data = [
            'csrf_token' => csrf_token(),
            'user' => $request->user(),
            'logout_url' => route('logout'),
        ];

        // Données du tableau de bord pour la page d'accueil index.php
        if ($relativePath === $this->dashboard02HomeFile($role) && $role === 'porteur') {
            $user = $request->user();
            if ($user) {
                $projectIds = $user->projects()->pluck('id');

                $totalProjects = Project::whereIn('id', $projectIds)->count();
                $montantDemande = Project::whereIn('id', $projectIds)->sum('montant_demande');
                $totalFinance = Funding::whereIn('project_id', $projectIds)->sum('montant');

                $totalRepayment = Repayment::whereIn('project_id', $projectIds)->sum('montant_total');
                $paidRepayment = Repayment::whereIn('project_id', $projectIds)->where('statut', 'paye')->sum('montant_rembourse');
                $tauxRemb = $totalRepayment > 0 ? round(($paidRepayment / $totalRepayment) * 100, 1) : 0;

                $recentProjects = Project::with('financements.institution')
                    ->whereIn('id', $projectIds)
                    ->latest()
                    ->take(10)
                    ->get();

                // Graphique mensuel (12 mois) : montants demandés vs financés
                $months = collect(range(11, 0))->map(fn ($i) => Carbon::now()->subMonths($i));
                $chartLabels = $months->map(fn (Carbon $d) => $d->locale('fr')->isoFormat('MMM'))->all();
                $chartDemandes = [];
                $chartFinancements = [];
                foreach ($months as $date) {
                    $start = $date->copy()->startOfMonth();
                    $end = $date->copy()->endOfMonth();
                    $chartDemandes[] = round(
                        Project::whereIn('id', $projectIds)
                            ->whereBetween('created_at', [$start, $end])
                            ->sum('montant_demande') / 1000000, 1
                    );
                    $chartFinancements[] = round(
                        Funding::whereIn('project_id', $projectIds)
                            ->whereBetween('date_financement', [$start, $end])
                            ->sum('montant') / 1000000, 1
                    );
                }

                $data['dashboard'] = [
                    'total_projects' => $totalProjects,
                    'funding_stats' => ['montant_demande' => $montantDemande, 'montant_finance' => $totalFinance, 'progression' => $montantDemande > 0 ? (int) round(($totalFinance / $montantDemande) * 100) : 0],
                    'repayment_stats' => ['total_rembourse' => $paidRepayment, 'taux_remboursement' => $tauxRemb, 'total_echeances' => Repayment::whereIn('project_id', $projectIds)->count()],
                    'recent_projects' => $recentProjects,
                    'all_projects' => collect(),
                    'chart_labels' => $chartLabels,
                    'chart_demandes' => $chartDemandes,
                    'chart_financements' => $chartFinancements,
                ];
            } else {
                $data['dashboard'] = [
                    'total_projects' => 0,
                    'funding_stats' => ['montant_demande' => 0, 'montant_finance' => 0, 'progression' => 0],
                    'repayment_stats' => ['total_rembourse' => 0, 'taux_remboursement' => 0, 'total_echeances' => 0],
                    'recent_projects' => collect(),
                    'all_projects' => collect(),
                    'chart_labels' => [],
                    'chart_demandes' => [],
                    'chart_financements' => [],
                ];
            }
        }

        // Toujours passer les documents pour la page document.php
        if ($relativePath === 'document.php' && $role === 'porteur') {
            $user = $request->user();
            if ($user) {
                $projectIds = $user->projects()->pluck('id');
                $documents = ProjectDocument::with('project')
                    ->whereIn('project_id', $projectIds)
                    ->latest()
                    ->get();
            } else {
                $documents = collect();
                $projectIds = collect();
            }

            $data['documents'] = $documents;
            $data['projectIds'] = $projectIds;
        }

        // Passer les projets pour la page mes_projets.php
        if ($relativePath === 'mes_projets.php' && $role === 'porteur') {
            $user = $request->user();
            if ($user) {
                $data['projects'] = $user->projects()->with(['financements.institution'])->latest()->get();
            } else {
                $data['projects'] = collect();
            }
        }

        // Passer les données profil pour la page profils.php
        if ($relativePath === 'profils.php' && $role === 'porteur') {
            $user = $request->user();
            if ($user) {
                $profileHistory = \App\Models\AuditLog::where('user_id', $user->id)
                    ->whereIn('action', ['profile_update', 'email_change', 'photo_change', 'telephone_verified'])
                    ->latest()
                    ->limit(10)
                    ->get()
                    ->map(function ($log) {
                        $labels = [
                            'profile_update' => ['label' => 'Modification profil', 'icon' => 'bi-person', 'color' => 'primary'],
                            'email_change' => ['label' => 'Changement email', 'icon' => 'bi-envelope', 'color' => 'warning'],
                            'photo_change' => ['label' => 'Changement photo', 'icon' => 'bi-image', 'color' => 'info'],
                            'telephone_verified' => ['label' => 'Téléphone vérifié', 'icon' => 'bi-phone', 'color' => 'success'],
                        ];
                        $action = $labels[$log->action] ?? ['label' => $log->action, 'icon' => 'bi-clock', 'color' => 'secondary'];

                        return [
                            'action' => $action['label'],
                            'icon' => $action['icon'],
                            'color' => $action['color'],
                            'ip' => $log->ip ?? 'N/A',
                            'date' => $log->date ? $log->date->format('d M Y H:i') : ($log->created_at ? $log->created_at->format('d M Y H:i') : 'N/A'),
                        ];
                    });

                $data['profileHistory'] = $profileHistory;
            } else {
                $data['profileHistory'] = collect();
            }
        }

        // Passer les notifications pour la page notifications.php
        if ($relativePath === 'notifications.php') {
            $user = $request->user();
            if ($user) {
                $notifications = UserNotification::where('user_id', $user->id)
                    ->latest()
                    ->get();

                $data['notifications'] = $notifications;
                $data['notificationStats'] = [
                    'unread' => UserNotification::where('user_id', $user->id)->where('is_read', false)->whereNull('archived_at')->count(),
                    'read' => UserNotification::where('user_id', $user->id)->where('is_read', true)->whereNull('archived_at')->count(),
                    'archived' => UserNotification::where('user_id', $user->id)->whereNotNull('archived_at')->count(),
                    'total' => UserNotification::where('user_id', $user->id)->whereNull('archived_at')->count(),
                ];
            } else {
                $data['notifications'] = collect();
                $data['notificationStats'] = ['unread' => 0, 'read' => 0, 'archived' => 0, 'total' => 0];
            }
        }

        // Passer les données profil pour la page profils.php
        if ($relativePath === 'profils.php') {
            $user = $request->user();
            if ($user) {
                $profileHistory = \App\Models\AuditLog::where('user_id', $user->id)
                    ->whereIn('action', ['profile_update', 'email_change', 'photo_change', 'logo_change'])
                    ->latest()
                    ->limit(10)
                    ->get()
                    ->map(function ($log) {
                        $labels = [
                            'profile_update' => ['label' => 'Modification profil', 'icon' => 'bi-person', 'color' => 'primary'],
                            'email_change' => ['label' => 'Changement email', 'icon' => 'bi-envelope', 'color' => 'warning'],
                            'photo_change' => ['label' => 'Changement photo', 'icon' => 'bi-image', 'color' => 'info'],
                            'logo_change' => ['label' => 'Changement logo', 'icon' => 'bi-image', 'color' => 'info'],
                        ];
                        $action = $labels[$log->action] ?? ['label' => $log->action, 'icon' => 'bi-clock', 'color' => 'secondary'];

                        return [
                            'action' => $action['label'],
                            'icon' => $action['icon'],
                            'color' => $action['color'],
                            'ip' => $log->ip ?? 'N/A',
                            'date' => $log->date ? $log->date->format('d M Y H:i') : ($log->created_at ? $log->created_at->format('d M Y H:i') : 'N/A'),
                        ];
                    });

                $data['profileHistory'] = $profileHistory;
            } else {
                $data['profileHistory'] = collect();
            }
        }

        // Passer les données sécurité pour la page securite.php
        if ($relativePath === 'securite.php') {
            $user = $request->user();
            if ($user) {
                $securityController = new \App\Http\Controllers\SecurityController;
                $sessionsReq = $request->duplicate();
                $sessionsRes = $securityController->sessions($sessionsReq);
                $data['userSessions'] = $sessionsRes->getData(true);

                $logsReq = $request->duplicate();
                $logsRes = $securityController->auditLogs($logsReq);
                $data['auditLogs'] = $logsRes->getData(true);

                $data['securityStats'] = [
                    'active_sessions' => count(array_filter($data['userSessions'] ?? [], fn ($s) => ! ($s['is_current'] ?? false))) + 1,
                    'password_updated' => AuditLog::where('user_id', $user->id)->where('action', 'password_change')->latest()->first()?->date?->format('d M Y') ?? 'Jamais',
                    'failed_attempts' => AuditLog::where('user_id', $user->id)->where('action', 'failed_login')->count(),
                    'total_logs' => count($data['auditLogs'] ?? []),
                ];
            } else {
                $data['userSessions'] = [];
                $data['auditLogs'] = [];
                $data['securityStats'] = ['active_sessions' => 0, 'password_updated' => 'Jamais', 'failed_attempts' => 0, 'total_logs' => 0];
            }
        }

        // Passer les remboursements pour la page remboursement.php
        if ($relativePath === 'remboursement.php' && $role === 'porteur') {
            $user = $request->user();
            if ($user) {
                $repaymentService = new \App\Services\RepaymentService;
                $result = $repaymentService->getPorteurRepayments($user);
                $data['repayments'] = $result['repayments'];
                $data['stats'] = $result['stats'];
            } else {
                $data['repayments'] = collect();
                $data['stats'] = [
                    'total_rembourse' => 0,
                    'total_restant' => 0,
                    'taux_remboursement' => 0,
                    'en_retard' => 0,
                    'total_echeances' => 0,
                ];
            }
        }

        // Passer les données financement pour la page financement.php
        if ($relativePath === 'financement.php' && $role === 'porteur') {
            $user = $request->user();
            if ($user) {
                $fundingService = new \App\Services\FundingService;
                $data['fundingStats'] = $fundingService->getPorteurFundingStats($user);
            } else {
                $data['fundingStats'] = [];
            }
        }

        // Passer les données pour la page echances.php (échéancier) via le nouveau système Echeance
        if ($relativePath === 'echances.php' && $role === 'porteur') {
            $user = $request->user();
            if ($user) {
                $projectIds = $user->projects()->pluck('id');
                $echeances = \App\Models\Echeance::with(['institution', 'project'])
                    ->whereIn('project_id', $projectIds)
                    ->orderBy('date_echeance')
                    ->get();

                $statusMap = [
                    'pending' => 'en_attente',
                    'upcoming' => 'en_attente',
                    'paid' => 'paye',
                    'overdue' => 'en_retard',
                    'partial' => 'en_attente',
                ];

                $repayments = $echeances->map(function ($e) use ($statusMap) {
                    $e->statut = $statusMap[$e->statut] ?? $e->statut;

                    return $e;
                });

                // Si aucune échéance réelle, projeter depuis les financements en attente
                if ($repayments->isEmpty()) {
                    $fundings = \App\Models\Funding::with(['institution', 'project'])
                        ->whereIn('project_id', $projectIds)
                        ->whereNotNull('montant_mensuel')
                        ->whereIn('statut', ['awaiting_imf_validation', 'approved', 'disbursed', 'active'])
                        ->get();

                    $projected = collect();
                    foreach ($fundings as $funding) {
                        $nbEcheances = (int) $funding->duree ?: 12;
                        $montantMensuel = (float) $funding->montant_mensuel;
                        $dateBase = $funding->date_validation ?: $funding->created_at ?: now();

                        for ($i = 1; $i <= $nbEcheances; $i++) {
                            $dateEcheance = (clone $dateBase)->addMonths($i);
                            $proj = new \stdClass;
                            $proj->id = -$funding->id * 1000 - $i;
                            $proj->statut = $dateEcheance->isPast() ? 'en_retard' : 'en_attente';
                            $proj->montant_total = $montantMensuel;
                            $proj->montant_restant = $montantMensuel;
                            $proj->montant_paye = 0;
                            $proj->date_echeance = $dateEcheance;
                            $proj->project = $funding->project;
                            $proj->institution = $funding->institution;
                            $proj->is_projected = true;
                            $projected->push($proj);
                        }
                    }
                    $repayments = $projected->sortBy('date_echeance')->values();
                }

                $payees = $repayments->where('statut', 'paye')->count();
                $totalEcheances = $repayments->count();
                $totalRembourse = (float) $repayments->where('statut', 'paye')->sum(function ($r) {
                    return $r->montant_paye ?? 0;
                });
                $totalRestant = (float) $repayments->sum('montant_restant');
                $enRetard = $repayments->where('statut', 'en_retard')->count();

                $data['repayments'] = $repayments;
                $data['stats'] = [
                    'total_rembourse' => $totalRembourse,
                    'total_restant' => $totalRestant,
                    'taux_remboursement' => $totalEcheances > 0 ? round(($payees / $totalEcheances) * 100, 2) : 0,
                    'en_retard' => $enRetard,
                    'total_echeances' => $totalEcheances,
                ];
            } else {
                $data['repayments'] = collect();
                $data['stats'] = [
                    'total_rembourse' => 0,
                    'total_restant' => 0,
                    'taux_remboursement' => 0,
                    'en_retard' => 0,
                    'total_echeances' => 0,
                ];
            }
        }

        // Passer les données pour la page d'accueil index.php (vue globale du porteur)
        if ($relativePath === 'index.php' && $role === 'porteur') {
            $user = $request->user();
            if ($user) {
                $projects = $user->projects()->with(['financements.institution', 'remboursements'])->latest()->get();
                $projectIds = $projects->pluck('id');

                $fundingService = new \App\Services\FundingService;
                $fundingStats = $fundingService->getPorteurFundingStats($user);

                $repaymentService = new \App\Services\RepaymentService;
                $repaymentResult = $repaymentService->getPorteurRepayments($user);

                $recentProjects = $projects->take(5);

                // Graphique mensuel (12 mois) : montants demandés vs financés
                $months = collect(range(11, 0))->map(fn ($i) => Carbon::now()->subMonths($i));
                $chartLabels = $months->map(fn (Carbon $d) => $d->locale('fr')->isoFormat('MMM'))->all();
                $chartDemandes = [];
                $chartFinancements = [];
                foreach ($months as $date) {
                    $start = $date->copy()->startOfMonth();
                    $end = $date->copy()->endOfMonth();
                    $chartDemandes[] = round(
                        Project::whereIn('id', $projectIds)
                            ->whereBetween('created_at', [$start, $end])
                            ->sum('montant_demande') / 1000000, 1
                    );
                    $chartFinancements[] = round(
                        Funding::whereIn('project_id', $projectIds)
                            ->whereBetween('date_financement', [$start, $end])
                            ->sum('montant') / 1000000, 1
                    );
                }

                // Répartition par secteur
                $sectorCounts = $projects->groupBy('secteur')->map(fn ($group) => $group->count());
                $chartSectors = $sectorCounts->keys()->map(fn ($s) => $s ?: 'Non spécifié')->values()->all();
                $chartSecteurData = $sectorCounts->values()->all();
                $topSector = $sectorCounts->isNotEmpty()
                    ? ($sectorCounts->keys()->first() ?: 'N/A')
                    : 'Aucun';
                $totalActivityPct = $sectorCounts->isNotEmpty() && $projects->count() > 0
                    ? round(($sectorCounts->max() / $projects->count()) * 100)
                    : 0;

                // Flux de trésorerie : 12 semaines / 12 mois
                $weeklyLabels = [];
                $weeklyData = [];
                for ($i = 11; $i >= 0; $i--) {
                    $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
                    $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();
                    $weeklyLabels[] = 'S'.$weekStart->isoWeek();
                    $in = Funding::whereIn('project_id', $projectIds)
                        ->whereBetween('date_financement', [$weekStart, $weekEnd])
                        ->sum('montant');
                    $out = Repayment::whereIn('project_id', $projectIds)
                        ->whereBetween('date_paiement', [$weekStart, $weekEnd])
                        ->sum('montant_rembourse');
                    $weeklyData[] = round(($in - $out) / 1000000, 1);
                }
                $monthlyLabels = collect(range(11, 0))->map(fn ($i) => Carbon::now()->subMonths($i)->locale('fr')->isoFormat('MMM'))->all();
                $monthlyData = [];
                foreach (collect(range(11, 0)) as $i) {
                    $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
                    $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
                    $in = Funding::whereIn('project_id', $projectIds)
                        ->whereBetween('date_financement', [$monthStart, $monthEnd])
                        ->sum('montant');
                    $out = Repayment::whereIn('project_id', $projectIds)
                        ->whereBetween('date_paiement', [$monthStart, $monthEnd])
                        ->sum('montant_rembourse');
                    $monthlyData[] = round(($in - $out) / 1000000, 1);
                }

                $data['dashboard'] = [
                    'total_projects' => $projects->count(),
                    'funding_stats' => $fundingStats,
                    'repayment_stats' => $repaymentResult['stats'],
                    'recent_projects' => $recentProjects,
                    'all_projects' => $projects,
                    'project_ids' => $projectIds,
                    'chart_labels' => $chartLabels,
                    'chart_demandes' => $chartDemandes,
                    'chart_financements' => $chartFinancements,
                    'chart_sectors' => $chartSectors,
                    'chart_secteur_data' => $chartSecteurData,
                    'top_sector' => $topSector,
                    'total_activity_pct' => $totalActivityPct,
                    'cashflow_weekly' => $weeklyData,
                    'cashflow_weekly_labels' => $weeklyLabels,
                    'cashflow_monthly' => $monthlyData,
                    'cashflow_monthly_labels' => $monthlyLabels,
                ];
            } else {
                $data['dashboard'] = [
                    'total_projects' => 0,
                    'funding_stats' => [
                        'montant_demande' => 0,
                        'montant_finance' => 0,
                        'progression' => 0,
                    ],
                    'repayment_stats' => [
                        'total_rembourse' => 0,
                        'taux_remboursement' => 0,
                        'en_retard' => 0,
                        'total_echeances' => 0,
                    ],
                    'recent_projects' => collect(),
                    'all_projects' => collect(),
                    'project_ids' => collect(),
                    'chart_labels' => ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                    'chart_demandes' => [],
                    'chart_financements' => [],
                    'chart_sectors' => [],
                    'chart_secteur_data' => [],
                    'top_sector' => '—',
                    'total_activity_pct' => 0,
                    'cashflow_weekly' => [],
                    'cashflow_weekly_labels' => [],
                    'cashflow_monthly' => [],
                    'cashflow_monthly_labels' => [],
                ];
            }
        }

        $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));

        if ($extension !== 'php' && ! $this->frontendFileExists(base_path('../frontend/dashboard02/'.$role), $relativePath)) {
            $assetPath = base_path('../frontend/asset/css');
            $relativePath = ltrim($relativePath, '/');
            if ($this->frontendFileExists($assetPath, $relativePath)) {
                return response()->file(
                    $this->resolveFrontendFile($assetPath, $relativePath),
                    $this->frontendFileHeaders($extension)
                );
            }
        }

        return $this->renderFrontendFile(
            base_path('../frontend/dashboard02/'.$role),
            $relativePath,
            '/frontend/dashboard02/'.$role.'/'.$relativePath,
            $data
        );
    }

    private function frontendFileExists(string $basePath, string $relativePath): bool
    {
        $base = realpath($basePath);
        if ($base === false) {
            return false;
        }

        $fullPath = realpath($base.DIRECTORY_SEPARATOR.ltrim($relativePath, '/'));
        if ($fullPath === false) {
            return false;
        }

        if (! str_starts_with($fullPath, $base.DIRECTORY_SEPARATOR) && $fullPath !== $base) {
            return false;
        }

        return file_exists($fullPath) && ! is_dir($fullPath);
    }

    public function serveFrontendAsset(string $path)
    {
        return $this->serveStaticFrontendFile(base_path('../frontend/asset'), $path);
    }

    public function serveSharedDashboard02(string $path)
    {
        return $this->serveStaticFrontendFile(base_path('../frontend/dashboard02/shared'), $path);
    }

    public function legacyRedirect(Request $request, string $path)
    {
        $normalizedPath = trim($path, '/');
        if ($normalizedPath === '') {
            return redirect()->route('dashboard.redirect');
        }

        if (str_starts_with($normalizedPath, 'assets/')) {
            return $this->serveStaticFrontendFile(
                base_path('../frontend/dashboard/assets'),
                substr($normalizedPath, strlen('assets/'))
            );
        }

        $routeName = $this->legacyDashboardRoute(basename($normalizedPath));
        if ($routeName !== null) {
            return $this->redirectToRoutePreservingQuery($request, $routeName);
        }

        return redirect()->route('dashboard.redirect');
    }

    private function redirectToDashboard02(Request $request, string $role, string $page)
    {
        return $this->redirectToRoutePreservingQuery($request, 'dashboard02.file', [
            'role' => $role,
            'path' => $this->dashboard02FileFor($role, $page),
        ]);
    }

    private function redirectToRoutePreservingQuery(Request $request, string $routeName, array $parameters = [])
    {
        return $this->redirectToUrlPreservingQuery($request, route($routeName, $parameters));
    }

    private function redirectToUrlPreservingQuery(Request $request, string $url)
    {
        $queryString = $request->getQueryString();
        if ($queryString !== null && $queryString !== '') {
            $url .= (str_contains($url, '?') ? '&' : '?').$queryString;
        }

        return redirect()->to($url);
    }

    private function ensureDashboardRoleAccess(Request $request, string $role): void
    {
        $allowedRoles = ['admin', 'porteur', 'institution'];
        if (! in_array($role, $allowedRoles, true)) {
            abort(404);
        }

        $user = $request->user();
        if (! $user) {
            abort(401);
        }

        if ($user->role !== $role) {
            abort(403);
        }
    }

    private function dashboard02HomeFile(string $role): string
    {
        return match ($role) {
            'admin' => 'index.php',
            'institution' => 'index.php',
            'porteur' => 'index.php',
            default => throw new \RuntimeException('Dashboard role unsupported.'),
        };
    }

    private function dashboard02FileFor(string $role, string $page): string
    {
        $map = [
            'admin' => [
                'projets_admin' => 'projets.php',
                'utilisateurs' => 'utilisateurs.php',
                'finance' => 'financementadmin.php',
                'litiges' => 'index.php',
                'audit_logs' => 'audit_logsadmin.php',
                'messages_admin' => 'messagesadmin.php',
                'notifications_admin' => 'notificationsadmin.php',
                'profil_admin' => 'profilsadmin.php',
                'securite_admin' => 'securiteadmin.php',
                'parametres' => 'parametresadmin.php',
            ],
            'institution' => [
                'parcourir_projets' => 'projets_disponibles.php',
                'remboursements_institution' => 'remboursement.php',
                'notifications_institution' => 'notifications.php',
                'messages_institution' => 'messages.php',
                'profil_institution' => 'profils.php',
                'securite_institution' => 'securite.php',
                'portfolio' => 'financement.php',
                'analyse_risque' => 'projets_analyses.php',
                'entretiens_planifies' => 'entretiens_planifies.php',
                'investissements' => 'financement.php',
                'validation' => 'projets_analyses.php',
            ],
            'porteur' => [
                'mes_projets' => 'mes_projets.php',
                'remboursements' => 'remboursement.php',
                'notifications' => 'notifications.php',
                'messages' => 'messages.php',
                'profil' => 'profils.php',
                'securite' => 'securite.php',
                'documents' => 'document.php',
                'financement' => 'financement.php',
                'echeancier_remboursement' => 'echances.php',
                'creer_projet' => 'creer_projet.php',
                'details_projet' => 'details_projet.php',
            ],
        ];

        // Route: /dashboard/porteur/projets/{project}/details is handled by projectDetails()
        return $map[$role][$page] ?? $this->dashboard02HomeFile($role);
    }

    private function legacyDashboardRoute(string $file): ?string
    {
        $map = [
            'dashboard_admin.php' => 'dashboard.admin',
            'dashboard_porteur.php' => 'dashboard.porteur',
            'dashboard_institution.php' => 'dashboard.institution',
            'projets_admin.php' => 'admin.projects',
            'utilisateurs.php' => 'admin.users',
            'finance.php' => 'admin.finance',
            'litiges.php' => 'admin.disputes',
            'audit_logs.php' => 'admin.audit',
            'messages_admin.php' => 'admin.messages',
            'notifications_admin.php' => 'admin.notifications',
            'profil_admin.php' => 'admin.profile',
            'securite_admin.php' => 'admin.security',
            'parametres.php' => 'admin.settings',
            'mes_projets.php' => 'porteur.projects',
            'remboursements.php' => 'porteur.repayments',
            'notifications.php' => 'porteur.notifications',
            'messages.php' => 'porteur.messages',
            'profil.php' => 'porteur.profile',
            'securite.php' => 'porteur.security',
            'documents.php' => 'porteur.documents',
            'financement.php' => 'porteur.funding',
            'echeancier_remboursement.php' => 'porteur.schedule',
            'creer_projet.php' => 'porteur.projects.create',
            'parcourir_projets.php' => 'institution.projects',
            'remboursements_institution.php' => 'institution.repayments',
            'notifications_institution.php' => 'institution.notifications',
            'messages_institution.php' => 'institution.messages',
            'profil_institution.php' => 'institution.profile',
            'securite_institution.php' => 'institution.security',
            'portfolio.php' => 'institution.portfolio',
            'analyse_risque.php' => 'institution.risk',
            'entretiens_planifies.php' => 'institution.interviews',
            'investissements.php' => 'institution.investments',
            'validation.php' => 'institution.validation',
        ];

        return $map[$file] ?? null;
    }

    private function renderFrontendFile(string $basePath, string $relativePath, string $scriptName, array $data = [])
    {
        $fullPath = $this->resolveFrontendFile($basePath, $relativePath);
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        if ($extension === 'php') {
            return view('frontend.raw', [
                'template' => $fullPath,
                'data' => array_merge($data, ['script_name' => $scriptName]),
            ]);
        }

        return response()->file($fullPath, $this->frontendFileHeaders($extension));
    }

    private function serveStaticFrontendFile(string $basePath, string $relativePath)
    {
        $fullPath = $this->resolveFrontendFile($basePath, $relativePath);
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        return response()->file($fullPath, $this->frontendFileHeaders($extension));
    }

    private function resolveFrontendFile(string $basePath, string $relativePath): string
    {
        $base = realpath($basePath);
        if ($base === false) {
            abort(404);
        }

        $fullPath = realpath($base.DIRECTORY_SEPARATOR.ltrim($relativePath, '/'));
        if ($fullPath === false || ! str_starts_with($fullPath, $base.DIRECTORY_SEPARATOR) && $fullPath !== $base) {
            abort(404);
        }

        if (! file_exists($fullPath) || is_dir($fullPath)) {
            abort(404);
        }

        return $fullPath;
    }

    private function frontendFileHeaders(string $extension): array
    {
        $mimeMap = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'mjs' => 'application/javascript',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
            'map' => 'application/json',
            'html' => 'text/html; charset=UTF-8',
        ];

        return isset($mimeMap[$extension])
            ? ['Content-Type' => $mimeMap[$extension]]
            : [];
    }
}
