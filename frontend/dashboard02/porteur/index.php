<?php

require_once __DIR__ . '/header.php';

$dashboardData = $dashboard ?? [
    'total_projects' => 0,
    'funding_stats' => ['montant_demande' => 0, 'montant_finance' => 0, 'progression' => 0],
    'repayment_stats' => ['total_rembourse' => 0, 'taux_remboursement' => 0, 'total_echeances' => 0],
    'recent_projects' => collect(),
    'all_projects' => collect(),
    'chart_labels' => ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'],
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

function dashboard02_escape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function dashboard02_format_number(int|float $num): string
{
    return number_format((float) $num, 0, ',', ' ');
}

function dashboard02_format_fcfa(int|float $amount, bool $compact = true): string
{
    $amount = (float) $amount;

    if (!$compact) {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }

    if ($amount >= 1000000) {
        $formatted = number_format($amount / 1000000, 1, '.', '');
        return rtrim(rtrim($formatted, '0'), '.') . ' M FCFA';
    }

    return number_format($amount, 0, ',', ' ') . ' FCFA';
}

function dashboard02_project_status_badge(string $status): array
{
    $map = [
        'draft' => ['label' => 'Brouillon', 'color' => 'warning'],
        'submitted' => ['label' => 'Soumis', 'color' => 'info'],
        'under_admin_review' => ['label' => 'En revue', 'color' => 'warning'],
        'admin_rejected' => ['label' => 'Rejeté', 'color' => 'danger'],
        'admin_validated' => ['label' => 'Validé', 'color' => 'success'],
        'under_institution_review' => ['label' => 'En analyse', 'color' => 'warning'],
        'institution_accepted' => ['label' => 'Accepté', 'color' => 'info'],
        'funded' => ['label' => 'Financé', 'color' => 'success'],
        'active' => ['label' => 'Actif', 'color' => 'primary'],
        'completed' => ['label' => 'Terminé', 'color' => 'success'],
        'cancelled' => ['label' => 'Annulé', 'color' => 'dark'],
        'institution_rejected' => ['label' => 'Rejeté', 'color' => 'danger'],
        'interview_scheduled' => ['label' => 'Entretien', 'color' => 'info'],
        'interview_confirmed' => ['label' => 'Confirmé', 'color' => 'success'],
        'documents_requested' => ['label' => 'Documents requis', 'color' => 'warning'],
    ];

    $statusLower = strtolower($status);
    return $map[$statusLower] ?? ['label' => $status, 'color' => 'secondary'];
}

$totalProjects = $dashboardData['total_projects'] ?? 0;
$totalFinance = $dashboardData['funding_stats']['montant_finance'] ?? 0;
$tauxRemboursement = $dashboardData['repayment_stats']['taux_remboursement'] ?? 0;
$totalRembourse = $dashboardData['repayment_stats']['total_rembourse'] ?? 0;
$recentProjects = $dashboardData['recent_projects'] ?? collect();

$projectsJson = $recentProjects->map(function ($p) {
    $aFundingDecaisse = $p->financements->contains(fn($f) => in_array($f->statut, ['disbursed', 'active']));
    $aFundingPropose = $p->financements->contains(fn($f) => in_array($f->statut, ['proposed', 'awaiting_borrower_plan', 'awaiting_imf_validation']));
    $isFinanced = ($p->montant_finance > 0) || $aFundingDecaisse;
    $status = $isFinanced
        ? ['label' => 'Projet déjà financé', 'color' => 'success']
        : ($aFundingPropose
            ? ['label' => 'Financement proposé', 'color' => 'info']
            : dashboard02_project_status_badge($p->statut ?? 'draft'));
    return [
        'id' => $p->id,
        'titre' => $p->titre,
        'secteur' => $p->secteur ?? '',
        'montant_demande' => (float) ($p->montant_demande ?? 0),
        'montant_finance' => (float) ($p->montant_finance ?? 0),
        'statut' => $p->statut,
        'statut_label' => $status['label'],
        'statut_color' => $status['color'],
        'description' => $p->description ?? '',
        'created_at' => $p->created_at ? $p->created_at->format('d M Y') : '',
        'updated_at' => $p->updated_at ? $p->updated_at->format('d M Y') : '',
        'institution_nom' => $p->financements->first()?->institution?->nom ?? '',
        'progression' => ($p->montant_demande ?? 0) > 0 ? (int) round((($p->montant_finance ?? 0) / ($p->montant_demande ?? 1)) * 100) : 0,
        'can_edit' => (\App\Enums\ProjectStatus::fromStorage($p->statut))?->canOwnerModify() ?? false,
    ];
})->values();
?>
<script id="dashboardProjectData" type="application/json"><?php echo json_encode($projectsJson, JSON_UNESCAPED_UNICODE); ?></script>
<script id="dashboardChartData" type="application/json"><?php echo json_encode([
    'labels' => $dashboardData['chart_labels'] ?? ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'],
    'demandes' => $dashboardData['chart_demandes'] ?? [],
    'financements' => $dashboardData['chart_financements'] ?? [],
], JSON_UNESCAPED_UNICODE); ?></script>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <!-- PAGE HEADER -->
            <div class="page-header">
                <h1 class="page-title text-uppercase fw-bold">Tableau de Bord Porteur</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Surveillance</li>
                    </ol>
                </div>
            </div>

            <!-- KPI CARDS -->
            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body p-0">
                            <div id="saleschart"></div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center text-dark">
                            <h6 class="mb-0 text-uppercase fw-bold">Mes Projets</h6>
                            <h5 class="mb-0 number-font"><?php echo dashboard02_format_number($totalProjects); ?></h5>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body p-0">
                            <div id="TotalOrders"></div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center text-dark">
                            <h6 class="mb-0 text-uppercase fw-bold">Financement Reçu</h6>
                            <h5 class="mb-0 number-font"><?php echo dashboard02_format_fcfa($totalFinance); ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body p-0">
                            <div id="NewUsers"></div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center text-dark">
                            <h6 class="mb-0 text-uppercase fw-bold">Remboursements (%)</h6>
                            <h5 class="mb-0 number-font"><?php echo (int) $tauxRemboursement; ?>%</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body p-0">
                            <div id="NewVisitors"></div>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center text-dark">
                            <h6 class="mb-0 text-uppercase fw-bold">Total Remboursé</h6>
                            <h5 class="mb-0 number-font"><?php echo dashboard02_format_fcfa($totalRembourse, false); ?></h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATISTICS & CHARTS -->
            <div class="row align-items-stretch">
                <div class="col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header justify-content-between">
                            <h4 class="card-title">Statistiques de Financement</h4>
                        </div>
                        <div class="card-body pt-2">
                            <div id="stats-chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RECENT PROJECTS -->
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="card">
                        <div class="card-header justify-content-between">
                            <h4 class="card-title">Projets Récents</h4>
                            <a href="mes_projets.php" class="btn btn-primary-light btn-sm">Voir tout</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table text-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">N°</th>
                                            <th scope="col">PROJET</th>
                                            <th scope="col">SECTEUR</th>
                                            <th scope="col">MONTANT</th>
                                            <th scope="col">STATUT</th>
                                            <th scope="col">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentProjectsBody">
                                        <?php if ($recentProjects->count() > 0): ?>
                                            <?php $index = 0;
                                            foreach ($recentProjects as $project):
                                                $index++;
                                                $status = dashboard02_project_status_badge($project->statut ?? 'draft');
                                                $sectorIconColor = match (strtolower($project->secteur ?? 'autre')) {
                                                    'agriculture', 'agroalimentaire' => 'warning',
                                                    'commerce', 'e-commerce' => 'primary',
                                                    'informatique', 'technologie', 'it' => 'danger',
                                                    'education', 'formation' => 'info',
                                                    'sante', 'médical' => 'success',
                                                    default => 'warning'
                                                };
                                                $projectId = $project->id ?? 0;
                                                $projectMontant = $project->montant_demande ?? 0;
                                                $projectFinance = $project->montant_finance ?? 0;
                                            ?>
                                                <tr>
                                                    <td><?php echo $index; ?></td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-md bg-primary-transparent rounded-circle me-2">
                                                                <span class="fw-bold"><?php echo strtoupper(substr($project->titre ?? 'PR', 0, 2)); ?></span>
                                                            </div>
                                                            <div class="d-flex flex-column">
                                                                <span class="fw-medium lh-1"><?php echo dashboard02_escape($project->titre ?? 'Projet'); ?></span>
                                                                <small class="text-muted"><?php echo $project->created_at ? $project->created_at->format('d M Y') : '-'; ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-<?php echo $sectorIconColor; ?>-transparent rounded-pill text-<?php echo $sectorIconColor; ?> p-2  me-3">
                                                                <i class="fe fe-tag"></i>
                                                            </span>
                                                            <?php echo dashboard02_escape($project->secteur ?? 'Autre'); ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="text-muted lh-1">
                                                            <span class="text-primary fw-medium"><?php echo dashboard02_format_fcfa($projectMontant, false); ?></span>
                                                            <?php if ($projectFinance > 0): ?>
                                                                <br><small class="text-success"><?php echo dashboard02_format_fcfa($projectFinance, false); ?> financés</small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td>
<?php
    $aFundingDecaisse = $project->financements->contains(fn($f) => in_array($f->statut, ['disbursed', 'active']));
    $aFundingPropose = $project->financements->contains(fn($f) => in_array($f->statut, ['proposed', 'awaiting_borrower_plan', 'awaiting_imf_validation']));
    $isFinanced = ($project->montant_finance > 0) || $aFundingDecaisse;
    if ($isFinanced):
        $badgeLabel = 'Projet déjà financé';
        $badgeColor = 'success';
    elseif ($aFundingPropose):
        $badgeLabel = 'Financement proposé';
        $badgeColor = 'info';
    else:
        $badgeLabel = $status['label'];
        $badgeColor = $status['color'];
    endif;
    ?>
    <span class="badge bg-<?php echo $badgeColor; ?>-transparent rounded-pill text-<?php echo $badgeColor; ?> p-2 px-3"><?php echo dashboard02_escape($badgeLabel); ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="g-1">
                                                            <a class="btn text-info btn-sm" data-bs-toggle="tooltip" data-bs-original-title="Voir les détails" href="javascript:void(0)" onclick="dashboardViewProjectDetails(<?php echo (int) $projectId; ?>)">
                                                                <i class="fe fe-eye fs-14"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <div class="empty-state">
                                                        <i class="fe fe-folder fs-50 d-block mb-3"></i>
                                                        <h6>Aucun projet récent</h6>
                                                        <p class="text-muted">Vous n'avez pas encore soumis de projet ou de demande de financement.</p>
                                                        <a href="creer_projet.php" class="btn btn-primary btn-sm">Lancer un projet</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center mt-3" id="recentProjectsPagination"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card">
                        <div class="card-header justify-content-between">
                            <h4 class="card-title">Répartition par Secteur</h4>
                        </div>
                        <div class="card-body p-1">
                            <div id="quartly-sale"></div>
                        </div>
                        <div class="card-footer border-top-0 pt-0">
                            <div class="row text-center">
                                <div class="col-6 border-end">
                                    <p class="text-muted mb-1 fs-12">Principale Branche</p>
                                    <h6 class="mb-0 fw-bold"><?php echo dashboard02_escape($dashboardData['top_sector'] ?? '—'); ?></h6>
                                </div>
                                <div class="col-6">
                                    <p class="text-muted mb-1 fs-12">Activité Totale</p>
                                    <h6 class="mb-0 fw-bold">+<?php echo (int) ($dashboardData['total_activity_pct'] ?? 0); ?>%</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REVENUE & EARNINGS -->
            <div class="row align-items-stretch">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header justify-content-between">
                            <h4 class="card-title">Flux de Trésorerie</h4>
                            <div class="btn-group earningTabs">
                                <button class="btn btn-sm btn-primary" data-type="weekly">Hebdomadaire</button>
                                <button class="btn btn-sm btn-outline-primary" data-type="monthly">Mensuel</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="sales-order"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card overflow-hidden">
                        <div class="card-header bg-primary-transparent border-0">
                            <h5 class="card-title text-primary">Santé Financière</h5>
                        </div>
                        <div class="card-body pt-0">
                            <div class="chart-container h-200">
                                <canvas id="today-revenue"></canvas>
                            </div>
                            <div class="revenue-stats py-4 d-flex justify-content-between border-top">
                                <div>
                                    <p class="text-muted mb-1 fs-12">Objectif Collecte</p>
                                    <h5 class="mb-0 fw-bold">15 M FCFA</h5>
                                </div>
                                <div>
                                    <p class="text-muted mb-1 fs-12">Réalisé</p>
                                    <h5 class="mb-0 fw-bold text-success">8.5 M FCFA</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DÉTAIL PROJET -->
<div class="modal fade" id="dashboardProjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="dashModalProjectTitle">Détail du Projet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-lg-8 col-md-7">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Titre</h6>
                        <p class="fw-medium text-break" id="dash-md-title">—</p>
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold mt-3">Description</h6>
                        <div class="text-muted text-break" id="dash-md-description">—</div>
                    </div>
                    <div class="col-lg-4 col-md-5">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body text-center py-3 d-flex flex-column justify-content-center h-100">
                                <h6 class="text-muted text-uppercase fs-11 fw-semibold">Statut</h6>
                                <span class="badge fs-13 px-3 py-2" id="dash-md-status">—</span>
                                <hr class="my-2">
                                <h6 class="text-muted text-uppercase fs-11 fw-semibold">Secteur</h6>
                                <p class="mb-0 fw-medium" id="dash-md-secteur">—</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Montant Demandé</h6>
                        <p class="fs-18 fw-bold text-primary mb-0 text-break" id="dash-md-montant-demande">—</p>
                    </div>
                    <div class="col-sm-6">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Montant Financé</h6>
                        <p class="fs-18 fw-bold text-success mb-0 text-break" id="dash-md-montant-finance">—</p>
                    </div>
                    <div class="col-12">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Progression du Financement</h6>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:10px;">
                                <div class="progress-bar" id="dash-md-progress-bar" role="progressbar" style="width:0%"></div>
                            </div>
                            <span class="fw-bold fs-14" id="dash-md-progress-text">0%</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Date de Création</h6>
                        <p class="mb-0 text-break" id="dash-md-created">—</p>
                    </div>
                    <div class="col-sm-6">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Dernière Mise à Jour</h6>
                        <p class="mb-0 text-break" id="dash-md-updated">—</p>
                    </div>
                    <div class="col-12" id="dash-md-institution-row" style="display:none;">
                        <hr>
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Institution Partenaire</h6>
                        <p class="mb-0 fw-medium text-break" id="dash-md-institution">—</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a href="#" class="btn btn-primary" id="dash-md-edit-btn"><i class="fe fe-edit me-1"></i>Modifier</a>
            </div>
        </div>
    </div>
</div>

<script src="js/dashboard-main.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        updatePagination('recentProjectsBody', 'recentProjectsPagination');
    });

    function dashboardViewProjectDetails(projectId) {
        var dataEl = document.getElementById('dashboardProjectData');
        if (!dataEl) return;
        var projects = JSON.parse(dataEl.textContent);
        var p = projects.find(function(item) { return item.id === projectId; });
        if (!p) return;

        document.getElementById('dashModalProjectTitle').textContent = p.titre;
        document.getElementById('dash-md-title').textContent = p.titre;
        document.getElementById('dash-md-description').innerHTML = p.description || 'Aucune description';
        document.getElementById('dash-md-secteur').textContent = p.secteur;

        var statusBadge = document.getElementById('dash-md-status');
        statusBadge.textContent = p.statut_label;
        statusBadge.className = 'badge fs-13 px-3 py-2 bg-' + (p.statut_color || 'secondary');

        document.getElementById('dash-md-montant-demande').textContent = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', minimumFractionDigits: 0 }).format(p.montant_demande);
        document.getElementById('dash-md-montant-finance').textContent = p.montant_finance > 0 ? new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', minimumFractionDigits: 0 }).format(p.montant_finance) : '—';

        var progress = p.progression || 0;
        document.getElementById('dash-md-progress-bar').style.width = progress + '%';
        document.getElementById('dash-md-progress-text').textContent = progress + '%';

        document.getElementById('dash-md-created').textContent = p.created_at;
        document.getElementById('dash-md-updated').textContent = p.updated_at;

        var instRow = document.getElementById('dash-md-institution-row');
        var instEl = document.getElementById('dash-md-institution');
        if (p.institution_nom) {
            instRow.style.display = '';
            instEl.textContent = p.institution_nom;
        } else {
            instRow.style.display = 'none';
        }

        var editBtn = document.getElementById('dash-md-edit-btn');
        if (p.can_edit) {
            editBtn.style.display = '';
            editBtn.href = 'creer_projet.php?id=' + p.id + '&view=1';
        } else {
            editBtn.style.display = 'none';
        }

        var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('dashboardProjectModal'));
        modal.show();
    }

</script>

<?php
$chartLabelsJson = json_encode($dashboardData['chart_labels'] ?? [], JSON_UNESCAPED_UNICODE);
$chartDemandesJson = json_encode($dashboardData['chart_demandes'] ?? []);
$chartFinancementsJson = json_encode($dashboardData['chart_financements'] ?? []);
$chartSectorsJson = json_encode($dashboardData['chart_sectors'] ?? [], JSON_UNESCAPED_UNICODE);
$chartSecteurDataJson = json_encode($dashboardData['chart_secteur_data'] ?? []);
$cashflowWeeklyJson = json_encode($dashboardData['cashflow_weekly'] ?? []);
$cashflowWeeklyLabelsJson = json_encode($dashboardData['cashflow_weekly_labels'] ?? []);
$cashflowMonthlyJson = json_encode($dashboardData['cashflow_monthly'] ?? []);
$cashflowMonthlyLabelsJson = json_encode($dashboardData['cashflow_monthly_labels'] ?? [], JSON_UNESCAPED_UNICODE);
?>

<script>
// MutationObserver : remplace le graphe stats-chart dès qu'index1.js l'ajoute dans le DOM
(function() {
    var el = document.querySelector('#stats-chart');
    if (!el) return;
    var l = <?php echo $chartLabelsJson; ?>,
        a = <?php echo $chartDemandesJson; ?>,
        f = <?php echo $chartFinancementsJson; ?>;
    if (!l || !l.length) return;
    var done = false;
    function render() {
        if (done) return;
        done = true;
        el.innerHTML = '';
        new ApexCharts(el, {
            chart: { height: 280, type: 'bar', redrawOnParentResize: true, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: false, columnWidth: '30%', endingShape: 'rounded' } },
            tooltip: { y: { formatter: function(v) { return v + ' M FCFA'; } } },
            dataLabels: { enabled: false },
            stroke: { show: false, width: 1, colors: ['transparent'] },
            grid: { show: true },
            series: [
                { name: 'Montants Demandés', data: a },
                { name: 'Montants Financés', data: f }
            ],
            xaxis: { categories: l },
            legend: { fontFamily: 'Nunito Sans, sans-serif', itemMargin: { vertical: 10, horizontal: 10 }, labels: { colors: ['#505d69', '#505d69'] } },
            colors: ['var(--primary-bg-color)', '#007F6E'],
            fill: { opacity: 1 }
        }).render();
    }
    // Attend que index1.js ait rendu le graphe statique, puis le remplace
    var obs = new MutationObserver(function() {
        if (el.children.length > 0) { obs.disconnect(); render(); }
    });
    obs.observe(el, { childList: true });
    // Fallback au cas où le DOMContentLoaded ne déclencherait pas de mutation
    setTimeout(function() { obs.disconnect(); if (!done) render(); }, 2000);
})();
</script>

<script>
// MutationObserver : attend que index1.js ait rendu les quarters, puis les remplace par les vrais secteurs
(function() {
    var el = document.querySelector('#quartly-sale');
    if (!el) return;
    var s = <?php echo $chartSectorsJson; ?>,
        d = <?php echo $chartSecteurDataJson; ?>;
    if (!s || !s.length) {
        // Pas encore de secteur : on efface et on reste neutre
        (function wait() {
            if (el.children.length > 0) {
                el.innerHTML = '<div class="d-flex align-items-center justify-content-center text-muted" style="height:280px">Aucune donnée</div>';
            } else {
                setTimeout(wait, 100);
            }
        })();
        return;
    }
    var done = false;
    function render() {
        if (done) return;
        done = true;
        el.innerHTML = '';
        new ApexCharts(el, {
            chart: { height: 280, type: 'bar', redrawOnParentResize: true, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: true, columnWidth: '50%', endingShape: 'rounded', borderRadius: 4 } },
            dataLabels: { enabled: true, formatter: function(v) { return v + ' projet' + (v > 1 ? 's' : ''); }, style: { colors: ['#fff'], fontSize: '12px', fontWeight: 600 } },
            stroke: { show: false },
            grid: { show: true, xaxis: { lines: { show: true } }, yaxis: { lines: { show: false } } },
            series: [{ name: 'Projets', data: d }],
            xaxis: { categories: s, labels: { style: { colors: '#505d69', fontSize: '12px', fontFamily: 'Nunito Sans, sans-serif' } } },
            yaxis: { labels: { style: { colors: '#505d69', fontSize: '12px', fontFamily: 'Nunito Sans, sans-serif' } } },
            colors: ['var(--primary-bg-color)'],
            legend: { show: false },
            tooltip: { y: { formatter: function(v) { return v + ' projet' + (v > 1 ? 's' : ''); } } }
        }).render();
    }
    var obs = new MutationObserver(function() {
        if (el.children.length > 0) { obs.disconnect(); render(); }
    });
    obs.observe(el, { childList: true });
    setTimeout(function() { obs.disconnect(); if (!done) render(); }, 2000);
})();
</script>

<script>
// MutationObserver : remplace le graphe sales-order (flux de trésorerie)
(function() {
    var el = document.querySelector('#sales-order');
    if (!el) return;
    var wk = <?php echo $cashflowWeeklyJson; ?>,
        wkLabels = <?php echo $cashflowWeeklyLabelsJson; ?>,
        mo = <?php echo $cashflowMonthlyJson; ?>,
        moLabels = <?php echo $cashflowMonthlyLabelsJson; ?>;
    var currentType = 'weekly';
    var chart = null;
    function buildConfig(type) {
        var data = type === 'weekly' ? wk : mo;
        var labels = type === 'weekly' ? wkLabels : moLabels;
        return {
            chart: { height: 270, type: 'area', toolbar: { show: false }, redrawOnParentResize: true },
            dataLabels: { enabled: false },
            stroke: { width: 2, curve: 'smooth' },
            series: [{ name: 'Flux net', data: data }],
            xaxis: { categories: labels, labels: { style: { colors: '#505d69', fontSize: '12px', fontFamily: 'Nunito Sans, sans-serif' } } },
            colors: ['#28a745'],
            fill: { type: 'gradient', gradient: { type: 'vertical', shadeIntensity: 1, inverseColors: false, opacityFrom: 0.45, opacityTo: 0.05, stops: [45, 100] } },
            grid: { show: true },
            tooltip: { y: { formatter: function(v) { return (v >= 0 ? '+' : '') + v + ' M FCFA'; } } },
            yaxis: { labels: { style: { colors: '#505d69', fontSize: '12px', fontFamily: 'Nunito Sans, sans-serif' }, formatter: function(v) { return v + 'M'; } } }
        };
    }
    function render() {
        if (!el) return;
        el.innerHTML = '';
        chart = new ApexCharts(el, buildConfig(currentType));
        chart.render();
    }
    // Toggle hebdo/mensuel
    function setupToggles() {
        var btns = document.querySelectorAll('.earningTabs .btn');
        btns.forEach(function(btn) {
            btn.onclick = function(e) {
                btns.forEach(function(b) { b.className = b.className.replace('btn-primary', 'btn-outline-primary').replace('active', ''); });
                this.className = this.className.replace('btn-outline-primary', 'btn-primary');
                currentType = this.getAttribute('data-type') || 'weekly';
                if (chart) {
                    var cfg = buildConfig(currentType);
                    chart.updateOptions({
                        xaxis: { categories: cfg.xaxis.categories },
                        series: cfg.series
                    });
                }
            };
        });
    }
    // Si déjà rendu par index1.js → remplacer, sinon attendre
    if (el.children.length > 0) {
        render();
        setupToggles();
    } else {
        var obs = new MutationObserver(function() {
            if (el.children.length > 0) { obs.disconnect(); render(); setupToggles(); }
        });
        obs.observe(el, { childList: true });
        setTimeout(function() { obs.disconnect(); if (!chart) render(); setupToggles(); }, 2000);
    }
})();
</script>
<?php require_once __DIR__ . '/footer.php'; ?>
<?php if (session('welcome')): ?>
<script>document.addEventListener('DOMContentLoaded', () => ALOGOTO.success('Bienvenue dans votre espace projet'));</script>
<?php endif; ?>
