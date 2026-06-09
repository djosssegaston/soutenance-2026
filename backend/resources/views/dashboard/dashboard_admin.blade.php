<?php
require_once base_path('../frontend/includes/path_helpers.php');

$page_title = $page_title ?? "Tableau de bord";
$page_subtitle = $page_subtitle ?? "Administration générale";
$page_active_nav = "dashboard";
$mobile_nav_context = "admin";
$mobile_nav_messages_badge = $mobile_nav_messages_badge ?? 0;
$mobile_nav_notifications_badge = $mobile_nav_notifications_badge ?? 0;
$dashboard_user_name = $dashboard_user_name ?? "Admin";
$dashboard_user_id = $dashboard_user_id ?? "";
$dashboard_user_role = $dashboard_user_role ?? "Administrateur plateforme";
$header_user_name = $header_user_name ?? $dashboard_user_name;
$header_user_initials = $header_user_initials ?? "--";
$header_notifications = $header_notifications ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token ?? csrf_token(), ENT_QUOTES, 'UTF-8'); ?>" />
  <script>window.csrfToken = "<?php echo htmlspecialchars($csrf_token ?? csrf_token(), ENT_QUOTES, 'UTF-8'); ?>";</script>
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
  @include('dashboard.components.sidebar_admin')
  <div class="sidebar-overlay"></div>

  <div class="dashboard-wrapper">
    @include('dashboard.components.header')

    <main class="dashboard-content main-content">
      <div class="container-fluid px-0">
        <!-- Ligne 1: Cartes stats -->
        <div class="row dashboard-gap-16 mt-2 mb-4 admin-kpi-row">
          <?php foreach ($admin_kpis as $kpi): ?>
            <div class="col-12 col-md-6 col-xl-3">
              <?php
              $stat_icon = $kpi['icon'];
              $stat_title = $kpi['title'];
              $stat_value = $kpi['value'];
              $stat_trend = $kpi['trend'];
              $stat_trend_class = $kpi['trend_class'];
              $stat_trend_icon = $kpi['trend_icon'];
              $stat_icon_bg = $kpi['icon_bg'];
              $stat_icon_color = $kpi['icon_color'];
              include resource_path('views/dashboard/components/stat_card.blade.php');
              ?>
            </div>
          <?php endforeach; ?>
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
            $table_title = "Projets récents";
            $table_id = "adminRecentProjects";
            $table_search_placeholder = "Rechercher un projet";
            ob_start();
            ?>
<table class="table dashboard-table align-middle">
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
    <?php foreach ($recent_projects as $project): ?>
      <tr>
        <td><?php echo htmlspecialchars($project['code'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($project['owner'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($project['amount'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><span class="status-badge <?php echo htmlspecialchars($project['status_class'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($project['status_label'], ENT_QUOTES, 'UTF-8'); ?></span></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_pagination = $table_pagination ?? '<button class="dashboard-page-btn active">1</button><button class="dashboard-page-btn">2</button><button class="dashboard-page-btn">3</button>';
            include resource_path('views/dashboard/components/data_table.blade.php');
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

                <ul class="dashboard-legend">
                  <?php foreach ($sector_breakdown as $sector): ?>
                    <li><span><i style="background: <?php echo htmlspecialchars($sector['color'], ENT_QUOTES, 'UTF-8'); ?>;"></i><?php echo htmlspecialchars($sector['label'], ENT_QUOTES, 'UTF-8'); ?></span><strong><?php echo htmlspecialchars($sector['percent'], ENT_QUOTES, 'UTF-8'); ?>%</strong></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </section>
          </div>
        </div>

        <!-- Ligne 4: Classement institutions -->
        <div class="row dashboard-gap-20">
          <?php foreach ($top_institutions as $institution): ?>
            <div class="col-12 col-md-6 col-lg-4">
              <section class="dashboard-card institution-card hover-lift">
                <p class="institution-card__meta mb-2"><?php echo htmlspecialchars($institution['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                <h4 class="mb-2"><?php echo htmlspecialchars($institution['amount'], ENT_QUOTES, 'UTF-8'); ?></h4>
                <p class="institution-card__meta mb-3"><?php echo htmlspecialchars($institution['projects'], ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="dashboard-progress">
                  <div class="dashboard-progress__bar <?php echo htmlspecialchars($institution['bar_class'], ENT_QUOTES, 'UTF-8'); ?>" style="width: <?php echo htmlspecialchars($institution['score'], ENT_QUOTES, 'UTF-8'); ?>%;"></div>
                </div>
                <p class="institution-card__meta mt-2"><?php echo htmlspecialchars($institution['score'], ENT_QUOTES, 'UTF-8'); ?>%</p>
              </section>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </main>
  </div>
  @include('dashboard.components.mobile_bottom_nav')
  @include('dashboard.components.dashboard_logout_modal')

  <!-- Bootstrap 5 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    window.dashboardCharts = window.dashboardCharts || {};
    window.dashboardCharts.adminFunding = <?php echo json_encode($admin_funding_chart, JSON_UNESCAPED_UNICODE); ?>;
    window.dashboardCharts.adminSector = <?php echo json_encode($admin_sector_chart, JSON_UNESCAPED_UNICODE); ?>;
  </script>
  <!-- Scripts dashboard -->
  <script src="<?php echo htmlspecialchars(dashboard_asset('js/dashboard.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
  <script src="<?php echo htmlspecialchars(dashboard_asset('js/dashboard-enhancements.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>








