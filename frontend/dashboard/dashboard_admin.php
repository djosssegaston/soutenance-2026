<?php
require_once __DIR__ . '/../includes/path_helpers.php';

$totalProjects = \App\Models\Project::count();
$totalFunding = (float) \App\Models\Funding::sum('montant');
$totalRepayments = (float) \App\Models\Repayment::sum('montant');
$paidRepayments = (float) \App\Models\Repayment::where('statut', 'paye')->sum('montant');
$repaymentRate = $totalRepayments > 0 ? round(($paidRepayments / $totalRepayments) * 100, 1) : 0;
$activeDisputes = \App\Models\Dispute::whereIn('statut', ['ouvert', 'en_mediation'])->count();
$projectsThisMonth = \App\Models\Project::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
$projectsLastMonth = \App\Models\Project::whereBetween('created_at', [now()->copy()->subMonth()->startOfMonth(), now()->copy()->subMonth()->endOfMonth()])->count();
$projectTrend = $projectsLastMonth > 0 ? round((($projectsThisMonth - $projectsLastMonth) / $projectsLastMonth) * 100, 1) : 0;
$totalUsers = \App\Models\User::count();

$recentProjects = \App\Models\Project::with('owner')->latest()->take(5)->get();

$sectorData = \App\Models\Project::select('secteur', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
    ->whereNotNull('secteur')
    ->groupBy('secteur')
    ->orderByDesc('count')
    ->get();
$totalSectorProjects = $sectorData->sum('count');

$institutionRanking = \App\Models\Institution::withSum('financements', 'montant')
    ->withCount('financements as projets_count')
    ->orderByDesc('financements_sum_montant')
    ->take(3)
    ->get();

$page_title = "Tableau de bord";
$page_subtitle = "Administration générale";
$page_active_nav = "dashboard";
$mobile_nav_context = "admin";
$mobile_nav_messages_badge = 0;
$mobile_nav_notifications_badge = 0;
$dashboard_user_name = "Administrateur";
$dashboard_user_id = "ADM-000";
$dashboard_user_role = "Administrateur plateforme";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Admin - ALOGOTO</title>
  <link rel="icon" type="image/png" href="<?php echo htmlspecialchars(frontend_public_asset('img/favicon-1.png'), ENT_QUOTES, 'UTF-8'); ?>" />

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

  <!-- Styles existants du projet -->
  <link rel="stylesheet" href="<?php echo htmlspecialchars(frontend_public_asset('sass/style.css?v=20260303-1'), ENT_QUOTES, 'UTF-8'); ?>" />

  <!-- Styles dashboard -->
  <link rel="stylesheet" href="<?php echo htmlspecialchars(dashboard_asset('css/dashboard.css'), ENT_QUOTES, 'UTF-8'); ?>" />
  <link rel="stylesheet" href="<?php echo htmlspecialchars(dashboard_asset('css/dashboard-fix.css'), ENT_QUOTES, 'UTF-8'); ?>" />
  <link rel="stylesheet" href="<?php echo htmlspecialchars(dashboard_asset('css/dashboard-enhancements.css'), ENT_QUOTES, 'UTF-8'); ?>" />
  <style>
    .admin-kpi-row .stat-card {
      min-height: 150px;
      padding-top: 14px;
      padding-bottom: 14px;
      display: grid;
      grid-template-columns: auto 1fr;
      grid-template-rows: auto 1fr auto;
      column-gap: 12px;
      row-gap: 6px;
      align-items: center;
    }

    .admin-kpi-row .stat-card__icon {
      grid-column: 1;
      grid-row: 1;
      margin: 0;
    }

    .admin-kpi-row .stat-card__label {
      grid-column: 2;
      grid-row: 1;
      margin: 0;
      align-self: center;
    }

    .admin-kpi-row .stat-card__value {
      grid-column: 1 / -1;
      grid-row: 2;
      width: 100%;
      margin: 0;
      align-self: center;
      justify-self: center;
      text-align: center;
    }

    .admin-kpi-row .stat-card__trend {
      grid-column: 1 / -1;
      grid-row: 3;
      margin: 0;
      justify-self: center;
    }

    .admin-sector-card {
      margin-top: 16px;
      padding-top: 22px;
      padding-left: 20px;
      padding-bottom: 22px;
    }

    .admin-sector-card .dashboard-card__head {
      justify-content: center;
      margin-bottom: 18px;
      text-align: center;
    }

    .admin-sector-card .chart-holder-sm {
      max-width: 320px;
      height: 280px;
      margin: 0;
    }

    .admin-sector-card__content {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 40px;
    }

    .admin-sector-card__chart {
      flex: 0 0 320px;
      display: flex;
      justify-content: flex-start;
    }

    .admin-sector-card .dashboard-legend {
      flex: 1 1 auto;
      max-width: 360px;
      margin: 0 0 0 auto;
    }

    @media (max-width: 767.98px) {
      .admin-kpi-row .stat-card {
        min-height: auto;
      }

      .admin-sector-card__content {
        display: block;
      }

      .admin-sector-card__chart {
        display: block;
      }

      .admin-sector-card .chart-holder-sm {
        max-width: 260px;
        height: 240px;
        margin: 0 auto;
      }

      .admin-sector-card .dashboard-legend {
        margin: 18px auto 0;
      }

      .admin-sector-card {
        padding-left: 14px;
      }
    }
  </style>
</head>
<body class="dashboard-body" data-active-nav="dashboard">
  <?php include 'components/sidebar_admin.php'; ?>
  <div class="sidebar-overlay"></div>

  <div class="dashboard-wrapper">
    <?php include 'components/header.php'; ?>

    <main class="dashboard-content main-content">
      <div class="container-fluid px-0">
        <!-- Ligne 1: Cartes stats - DONNÉES DYNAMIQUES -->
        <div class="row dashboard-gap-16 mt-2 mb-4 admin-kpi-row" id="admin-kpi-container">
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = "bi-folder2";
            $stat_title = "Projets Totaux";
            $stat_value = (string) $totalProjects;
            $stat_trend = ($projectTrend >= 0 ? '+' : '') . $projectTrend . '% vs mois dernier';
            $stat_trend_class = $projectTrend >= 0 ? 'is-up' : 'is-down';
            $stat_trend_icon = $projectTrend >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right';
            $stat_icon_bg = "rgba(0, 196, 134, 0.15)";
            $stat_icon_color = "var(--primary-color-1)";
            include 'components/stat_card.html';
            ?>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = "bi-cash-coin";
            $stat_title = "Volume Financé";
            $stat_value = number_format($totalFunding, 0, ',', ' ') . ' FCFA';
            $stat_trend = $totalUsers . ' utilisateurs';
            $stat_trend_class = "is-up";
            $stat_trend_icon = "bi-people";
            $stat_icon_bg = "rgba(252, 160, 40, 0.18)";
            $stat_icon_color = "var(--primary-color-3)";
            include 'components/stat_card.html';
            ?>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = "bi-graph-up-arrow";
            $stat_title = "Taux Remboursement";
            $stat_value = $repaymentRate . '%';
            $stat_trend = number_format($paidRepayments, 0, ',', ' ') . ' FCFA remboursés';
            $stat_trend_class = "is-up";
            $stat_trend_icon = "bi-check2-circle";
            $stat_icon_bg = "rgba(0, 72, 220, 0.12)";
            $stat_icon_color = "var(--primary-color-2)";
            include 'components/stat_card.html';
            ?>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = "bi-exclamation-triangle";
            $stat_title = "Litiges Actifs";
            $stat_value = (string) $activeDisputes;
            $stat_trend = 'En cours de résolution';
            $stat_trend_class = $activeDisputes > 0 ? "is-down" : "is-up";
            $stat_trend_icon = $activeDisputes > 0 ? "bi-exclamation-circle" : "bi-check-circle";
            $stat_icon_bg = "rgba(243, 81, 32, 0.12)";
            $stat_icon_color = "var(--primary-color-4)";
            include 'components/stat_card.html';
            ?>
          </div>
        </div>

        <!-- Ligne 2: Graphique aire -->
        <div class="row dashboard-gap-20 mt-4 mb-4">
          <div class="col-12">
            <section class="dashboard-card chart-card">
              <div class="dashboard-card__head">
                <h6>Volume de financement</h6>
              </div>
              <div class="chart-holder">
                <canvas id="adminFundingChart"></canvas>
              </div>
            </section>
          </div>
        </div>

        <!-- Ligne 3: Table + camembert -->
        <div class="row dashboard-gap-20 mt-4 mb-4">
          <div class="col-12">
            <?php
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle" data-items-per-page="10">
  <thead>
    <tr>
      <th>ID</th>
      <th>Projet</th>
      <th>Porteur</th>
      <th>Montant</th>
      <th>Statut</th>
    </tr>
  </thead>
  <tbody>
<?php if ($recentProjects->count() > 0): ?>
<?php foreach ($recentProjects as $p): ?>
<?php $statusEnum = $p->statusEnum(); ?>
    <tr>
      <td>PRJ-<?php echo str_pad($p->id, 3, '0', STR_PAD_LEFT); ?></td>
      <td><?php echo htmlspecialchars($p->titre); ?></td>
      <td><?php echo htmlspecialchars($p->owner->name ?? 'N/A'); ?></td>
      <td><?php echo number_format($p->montant_demande, 0, ',', ' '); ?> FCFA</td>
      <td><span class="status-badge <?php echo $statusEnum->badgeClass(); ?>"><?php echo $statusEnum->label(); ?></span></td>
    </tr>
<?php endforeach; ?>
<?php else: ?>
    <tr>
      <td colspan="5" class="text-center text-muted">Aucun projet pour le moment</td>
    </tr>
<?php endif; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = "Projets récents";
            $table_id = "adminRecentProjects";
            $table_search_placeholder = "Rechercher un projet";
            $table_auto_paginate = true;
            include 'components/data_table.html';
            ?>
          </div>

          <div class="col-12 mt-4">
            <section class="dashboard-card chart-card admin-sector-card">
              <div class="dashboard-card__head">
                <h6>Répartition par secteur</h6>
              </div>

              <div class="admin-sector-card__content">
                <div class="admin-sector-card__chart">
                  <div class="chart-holder chart-holder-sm">
                    <canvas id="adminSectorChart"></canvas>
                  </div>
                </div>

                <ul class="dashboard-legend" id="adminSectorLegend">
<?php foreach ($sectorData as $sector): ?>
<?php $pct = $totalSectorProjects > 0 ? round(($sector->count / $totalSectorProjects) * 100) : 0; ?>
                  <li data-sector="<?php echo htmlspecialchars($sector->secteur); ?>" data-pct="<?php echo $pct; ?>">
                    <span><i style="background:<?php echo 'hsl(' . (crc32($sector->secteur) % 360) . ', 60%, 50%)'; ?>;"></i><?php echo htmlspecialchars($sector->secteur); ?></span>
                    <strong><?php echo $pct; ?>%</strong>
                  </li>
<?php endforeach; ?>
<?php if ($sectorData->count() === 0): ?>
                  <li><span class="text-muted">Aucune donnée sectorielle</span></li>
<?php endif; ?>
                </ul>
              </div>
            </section>
          </div>
        </div>

        <!-- Ligne 4: Classement institutions -->
        <div class="row dashboard-gap-20">
<?php if ($institutionRanking->count() > 0): ?>
<?php
$maxFinance = $institutionRanking->first()->financements_sum_montant ?? 1;
$barColors = ['bg-success-token', 'bg-warning-token', 'bg-danger-token'];
$i = 0;
?>
<?php foreach ($institutionRanking as $inst): ?>
<?php $pct = $maxFinance > 0 ? round(($inst->financements_sum_montant / $maxFinance) * 100) : 0; ?>
          <div class="col-12 col-md-6 col-lg-4">
            <section class="dashboard-card institution-card hover-lift">
              <p class="institution-card__meta mb-2"><?php echo htmlspecialchars($inst->nom ?? $inst->name ?? 'Institution'); ?></p>
              <h4 class="mb-2"><?php echo number_format($inst->financements_sum_montant ?? 0, 0, ',', ' '); ?> FCFA</h4>
              <p class="institution-card__meta mb-3"><?php echo (int) $inst->projets_count; ?> projets</p>
              <div class="dashboard-progress">
                <div class="dashboard-progress__bar <?php echo $barColors[$i % 3]; ?>" style="width: <?php echo $pct; ?>%;"></div>
              </div>
              <p class="institution-card__meta mt-2"><?php echo $pct; ?>%</p>
            </section>
          </div>
<?php $i++; ?>
<?php endforeach; ?>
<?php else: ?>
          <div class="col-12">
            <section class="dashboard-card institution-card">
              <p class="text-center text-muted mb-0">Aucune institution avec financement pour le moment</p>
            </section>
          </div>
<?php endif; ?>
        </div>
      </div>
    </main>
  </div>
  <?php include 'components/mobile_bottom_nav.html'; ?>
  <?php include 'components/dashboard_logout_modal.php'; ?>

  <!-- Bootstrap 5 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <!-- Scripts dashboard -->
  <script src="<?php echo htmlspecialchars(dashboard_asset('js/api-client.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    initAdminSectorChart();
  });

  function initAdminSectorChart() {
    const canvas = document.getElementById('adminSectorChart');
    if (!canvas) return;

    const legend = document.getElementById('adminSectorLegend');
    if (!legend) return;

    const items = legend.querySelectorAll('li[data-sector]');
    if (items.length === 0) return;

    const labels = [];
    const data = [];
    const colors = [];

    items.forEach(function(item) {
      labels.push(item.getAttribute('data-sector'));
      data.push(parseFloat(item.getAttribute('data-pct')));
      const color = item.querySelector('i')?.style?.background || '#6A726F';
      colors.push(color);
    });

    new Chart(canvas, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: colors,
          borderWidth: 1,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        }
      }
    });
  }
  </script>
</body>
</html>
