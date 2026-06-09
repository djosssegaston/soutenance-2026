<?php
require_once base_path('../frontend/includes/path_helpers.php');

$page_title = $page_title ?? "Tableau de bord";
$page_subtitle = $page_subtitle ?? "Vue d'ensemble";
$page_active_nav = "dashboard";
$mobile_nav_context = "institution";
$mobile_nav_messages_badge = $mobile_nav_messages_badge ?? 0;
$mobile_nav_notifications_badge = $mobile_nav_notifications_badge ?? 0;
$dashboard_user_name = $dashboard_user_name ?? "Institution";
$dashboard_user_id = $dashboard_user_id ?? "";
$dashboard_user_role = $dashboard_user_role ?? "Institution financière";
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
  <title>Dashboard Institution - ALOGOTO</title>
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
    .institution-kpi-row .stat-card {
      min-height: 146px;
      padding-top: 14px;
      padding-bottom: 14px;
    }

    .institution-kpi-card__header {
      margin-bottom: 10px;
    }

    .institution-kpi-row .stat-card__label {
      margin: 0;
    }

    .institution-kpi-row .stat-card__value {
      margin: 0;
      text-align: center;
    }

    .institution-kpi-row .stat-card__trend {
      margin: 8px auto 0;
      display: flex;
      width: fit-content;
    }

    @media (max-width: 767.98px) {
      .institution-kpi-row .stat-card {
        min-height: auto;
      }
    }
  </style>
</head>
<body class="dashboard-body" data-active-nav="dashboard">
  @include('dashboard.components.sidebar_institution')
  <div class="sidebar-overlay"></div>

  <div class="dashboard-wrapper">
    @include('dashboard.components.header')

    <main class="dashboard-content main-content">
      <div class="container-fluid px-0">
        <!-- Ligne 1: Cartes stats -->
        <div class="row dashboard-gap-16 mt-2 mb-4 institution-kpi-row">
          <div class="col-12 col-md-6 col-xl-3">
            <div class="dashboard-card content-card stat-card hover-lift">
              <div class="institution-kpi-card__header d-flex align-items-center gap-2">
                <span class="stat-card__icon icon-box" style="background: rgba(0, 196, 134, 0.15); color: var(--primary-color-1);">
                  <i class="bi bi-briefcase"></i>
                </span>
                <p class="stat-card__label stat-label">Portfolio Actif</p>
              </div>
              <h4 class="stat-card__value stat-value text-center"><?php echo htmlspecialchars($institution_kpis["portfolio_active"], ENT_QUOTES, "UTF-8"); ?></h4>
              <p class="stat-card__trend stat-trend is-up">
                <i class="bi bi-arrow-up-right"></i>
                <span><?php echo htmlspecialchars($institution_kpis["portfolio_trend"], ENT_QUOTES, "UTF-8"); ?></span>
              </p>
            </div>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <div class="dashboard-card content-card stat-card hover-lift">
              <div class="institution-kpi-card__header d-flex align-items-center gap-2">
                <span class="stat-card__icon icon-box" style="background: rgba(252, 160, 40, 0.18); color: var(--primary-color-3);">
                  <i class="bi bi-cash-coin"></i>
                </span>
                <p class="stat-card__label stat-label">Volume Investi</p>
              </div>
              <h4 class="stat-card__value stat-value text-center"><?php echo htmlspecialchars($institution_kpis["volume_invested"], ENT_QUOTES, "UTF-8"); ?></h4>
              <p class="stat-card__trend stat-trend is-up">
                <i class="bi bi-arrow-up-right"></i>
                <span><?php echo htmlspecialchars($institution_kpis["volume_trend"], ENT_QUOTES, "UTF-8"); ?></span>
              </p>
            </div>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <div class="dashboard-card content-card stat-card hover-lift">
              <div class="institution-kpi-card__header d-flex align-items-center gap-2">
                <span class="stat-card__icon icon-box" style="background: rgba(0, 72, 220, 0.12); color: var(--primary-color-2);">
                  <i class="bi bi-graph-up-arrow"></i>
                </span>
                <p class="stat-card__label stat-label">ROI Moyen</p>
              </div>
              <h4 class="stat-card__value stat-value text-center"><?php echo htmlspecialchars($institution_kpis["roi_avg"], ENT_QUOTES, "UTF-8"); ?></h4>
              <p class="stat-card__trend stat-trend is-up">
                <i class="bi bi-arrow-up-right"></i>
                <span><?php echo htmlspecialchars($institution_kpis["roi_trend"], ENT_QUOTES, "UTF-8"); ?></span>
              </p>
            </div>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <div class="dashboard-card content-card stat-card hover-lift">
              <div class="institution-kpi-card__header d-flex align-items-center gap-2">
                <span class="stat-card__icon icon-box" style="background: rgba(243, 81, 32, 0.12); color: var(--primary-color-4);">
                  <i class="bi bi-speedometer2"></i>
                </span>
                <p class="stat-card__label stat-label">Score Performance</p>
              </div>
              <h4 class="stat-card__value stat-value text-center"><?php echo htmlspecialchars($institution_kpis["performance_score"], ENT_QUOTES, "UTF-8"); ?></h4>
              <p class="stat-card__trend stat-trend is-up">
                <i class="bi bi-arrow-up-right"></i>
                <span><?php echo htmlspecialchars($institution_kpis["performance_trend"], ENT_QUOTES, "UTF-8"); ?></span>
              </p>
            </div>
          </div>
        </div>

        <!-- Ligne 2: Graphique aire -->
        <div class="row dashboard-gap-20 mt-4 mb-4">
          <div class="col-12">
            <section class="dashboard-card chart-card">
              <div class="dashboard-card__head">
                <h6>Performance du portfolio</h6>
              </div>
              <div class="chart-holder">
                <canvas id="institutionPerformanceChart"></canvas>
              </div>
            </section>
          </div>
        </div>

        <!-- Ligne 3: Table projets disponibles -->
        <div class="row dashboard-gap-20 mt-4 mb-4">
          <div class="col-12">
            <?php
            $table_title = "Projets disponibles";
            $table_id = "institutionProjects";
            $table_search_placeholder = "Rechercher un projet";
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle">
  <thead>
    <tr>
      <th>ID</th>
      <th>Projet</th>
      <th>Secteur</th>
      <th>Montant</th>
      <th>Risque</th>
      <th>Score</th>
      <th>Statut</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($available_projects as $project): ?>
      <tr>
        <td><?php echo htmlspecialchars($project['code'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($project['sector'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($project['amount'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><span class="risk-badge <?php echo htmlspecialchars($project['risk_class'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($project['risk_label'], ENT_QUOTES, 'UTF-8'); ?></span></td>
        <td>
          <div class="d-flex align-items-center gap-2">
            <div class="mini-progress mini-progress--score"><div class="mini-progress__bar <?php echo htmlspecialchars($project['score_class'], ENT_QUOTES, 'UTF-8'); ?>" style="width:<?php echo htmlspecialchars($project['score'], ENT_QUOTES, 'UTF-8'); ?>%;"></div></div>
            <small><?php echo htmlspecialchars($project['score'], ENT_QUOTES, 'UTF-8'); ?></small>
          </div>
        </td>
        <td><span class="status-badge <?php echo htmlspecialchars($project['status_class'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($project['status_label'], ENT_QUOTES, 'UTF-8'); ?></span></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_pagination = $table_pagination ?? '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include resource_path('views/dashboard/components/data_table.blade.php');
            ?>
          </div>
        </div>

        <!-- Ligne 4: Cartes suivi -->
        <div class="row dashboard-gap-20 mt-4">
          <div class="col-12 col-md-6 col-xl-4">
            <section class="dashboard-card portfolio-track-card hover-lift">
              <p class="portfolio-track-card__meta mb-1">Remboursements à jour</p>
              <h4 class="mb-1"><?php echo htmlspecialchars($institution_portfolio["on_time_count"], ENT_QUOTES, "UTF-8"); ?> <small class="portfolio-track-card__meta">/<?php echo htmlspecialchars($institution_portfolio["total"], ENT_QUOTES, "UTF-8"); ?></small></h4>
              <p class="portfolio-track-card__meta mb-3"><?php echo htmlspecialchars($institution_portfolio["on_time_percent"], ENT_QUOTES, "UTF-8"); ?></p>
              <div class="dashboard-progress">
                <div class="dashboard-progress__bar bg-success-token" style="width:<?php echo htmlspecialchars($institution_portfolio["on_time_percent"], ENT_QUOTES, "UTF-8"); ?>%;"></div>
              </div>
            </section>
          </div>

          <div class="col-12 col-md-6 col-xl-4">
            <section class="dashboard-card portfolio-track-card hover-lift">
              <p class="portfolio-track-card__meta mb-1">En retard</p>
              <h4 class="mb-1"><?php echo htmlspecialchars($institution_portfolio["late_count"], ENT_QUOTES, "UTF-8"); ?> <small class="portfolio-track-card__meta">/<?php echo htmlspecialchars($institution_portfolio["total"], ENT_QUOTES, "UTF-8"); ?></small></h4>
              <p class="portfolio-track-card__meta mb-3"><?php echo htmlspecialchars($institution_portfolio["late_percent"], ENT_QUOTES, "UTF-8"); ?></p>
              <div class="dashboard-progress">
                <div class="dashboard-progress__bar bg-warning-token" style="width:<?php echo htmlspecialchars($institution_portfolio["late_percent"], ENT_QUOTES, "UTF-8"); ?>%;"></div>
              </div>
            </section>
          </div>

          <div class="col-12 col-md-6 col-xl-4">
            <section class="dashboard-card portfolio-track-card hover-lift">
              <p class="portfolio-track-card__meta mb-1">En défaut</p>
              <h4 class="mb-1"><?php echo htmlspecialchars($institution_portfolio["default_count"], ENT_QUOTES, "UTF-8"); ?> <small class="portfolio-track-card__meta">/<?php echo htmlspecialchars($institution_portfolio["total"], ENT_QUOTES, "UTF-8"); ?></small></h4>
              <p class="portfolio-track-card__meta mb-3"><?php echo htmlspecialchars($institution_portfolio["default_percent"], ENT_QUOTES, "UTF-8"); ?></p>
              <div class="dashboard-progress">
                <div class="dashboard-progress__bar bg-danger-token" style="width:<?php echo htmlspecialchars($institution_portfolio["default_percent"], ENT_QUOTES, "UTF-8"); ?>%;"></div>
              </div>
            </section>
          </div>
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
    window.dashboardCharts.institutionPerformance = <?php echo json_encode($institution_performance_chart, JSON_UNESCAPED_UNICODE); ?>;
  </script>
  <!-- Scripts dashboard -->
  <script src="<?php echo htmlspecialchars(dashboard_asset('js/dashboard.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
  <script src="<?php echo htmlspecialchars(dashboard_asset('js/dashboard-enhancements.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>











