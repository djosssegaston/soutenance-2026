<?php
require_once base_path('../frontend/includes/path_helpers.php');

$page_title = $page_title ?? "Tableau de bord";
$page_subtitle = $page_subtitle ?? "Porteur de projet";
$page_active_nav = "dashboard";
$mobile_nav_context = "porteur";
$mobile_nav_messages_badge = $mobile_nav_messages_badge ?? 0;
$mobile_nav_notifications_badge = $mobile_nav_notifications_badge ?? 0;
$dashboard_user_name = $dashboard_user_name ?? "Porteur";
$dashboard_user_id = $dashboard_user_id ?? "";
$dashboard_user_role = $dashboard_user_role ?? "Porteur de projet";
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
  <title>Dashboard Porteur - ALOGOTO</title>
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
    .porteur-kpi-row .stat-card {
      min-height: 146px;
      padding-top: 14px;
      padding-bottom: 14px;
    }

    .porteur-kpi-card__header {
      margin-bottom: 10px;
    }

    .porteur-kpi-row .stat-card__label {
      margin: 0;
    }

    .porteur-kpi-row .stat-card__value {
      margin: 0;
      text-align: center;
    }

    .porteur-kpi-row .stat-card__trend {
      margin: 8px auto 0;
      display: flex;
      width: fit-content;
    }

    .porteur-next-payment-card {
      margin-top: 16px;
      padding-top: 20px;
      padding-bottom: 20px;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      justify-content: flex-start;
    }

    .porteur-next-payment-card .dashboard-card__head,
    .porteur-next-payment-card .next-payment-amount {
      margin: 0;
    }

    .porteur-next-payment-card .dashboard-card__head {
      justify-content: center;
      width: 100%;
    }

    .porteur-next-payment-card__content {
      width: 100%;
    }

    .porteur-next-payment-card__item {
      color: var(--p-color);
      font-size: 14px;
      line-height: 1.6;
    }

    .porteur-next-payment-card__label {
      color: var(--text-heading-color);
    }

    .porteur-next-payment-card__amount {
      display: flex;
      flex-direction: column;
      justify-content: center;
      height: 100%;
    }

    .porteur-next-payment-card__amount .next-payment-amount {
      color: var(--text-heading-color);
      line-height: 1.2;
    }

    .porteur-next-payment-card .dashboard-btn-primary {
      margin-top: 4px;
    }

    @media (max-width: 767.98px) {
      .porteur-kpi-row .stat-card {
        min-height: auto;
      }
    }
  </style>
</head>
<body class="dashboard-body dashboard-body--porteur" data-active-nav="dashboard">
  @include('dashboard.components.sidebar_porteur')
  <div class="sidebar-overlay"></div>

  <div class="dashboard-wrapper">
    @include('dashboard.components.header')

    <main class="dashboard-content main-content">
      <div class="container-fluid px-0">
        <!-- Ligne 1: Cartes stats -->
        <div class="row dashboard-gap-16 mt-2 mb-4 porteur-kpi-row">
          <?php foreach ($porteur_kpis as $kpi): ?>
            <div class="col-12 col-md-6 col-xl-3">
              <div class="dashboard-card content-card stat-card hover-lift">
                <div class="porteur-kpi-card__header d-flex align-items-center gap-2">
                  <span class="stat-card__icon icon-box" style="background: <?php echo htmlspecialchars($kpi['icon_bg'], ENT_QUOTES, 'UTF-8'); ?>; color: <?php echo htmlspecialchars($kpi['icon_color'], ENT_QUOTES, 'UTF-8'); ?>;">
                    <i class="bi <?php echo htmlspecialchars($kpi['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                  </span>
                  <p class="stat-card__label stat-label"><?php echo htmlspecialchars($kpi['title'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <h4 class="stat-card__value stat-value text-center"><?php echo htmlspecialchars($kpi['value'], ENT_QUOTES, 'UTF-8'); ?></h4>
                <p class="stat-card__trend stat-trend <?php echo htmlspecialchars($kpi['trend_class'], ENT_QUOTES, 'UTF-8'); ?>">
                  <i class="bi <?php echo htmlspecialchars($kpi['trend_icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                  <span><?php echo htmlspecialchars($kpi['trend'], ENT_QUOTES, 'UTF-8'); ?></span>
                </p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Ligne 2: Table + prochain paiement -->
        <div class="row dashboard-gap-20 mt-4 mb-4">
          <div class="col-12">
            <?php
            $table_title = "Mes projets";
            $table_id = "porteurProjects";
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
      <th>Statut validation</th>
      <th>Statut financement</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($porteur_projects as $project): ?>
      <tr>
        <td><?php echo htmlspecialchars($project['code'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($project['sector'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($project['amount'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><span class="status-badge <?php echo htmlspecialchars($project['validation_class'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($project['validation_label'], ENT_QUOTES, 'UTF-8'); ?></span></td>
        <td><span class="status-badge <?php echo htmlspecialchars($project['funding_class'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($project['funding_label'], ENT_QUOTES, 'UTF-8'); ?></span></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_pagination = $table_pagination ?? '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            $table_show_filter = false;
            include resource_path('views/dashboard/components/data_table.blade.php');
            ?>
          </div>

          <div class="col-12">
            <section class="dashboard-card next-payment-card porteur-next-payment-card">
              <div class="dashboard-card__head text-center mb-3">
                <h6 class="mb-0">Prochain remboursement</h6>
              </div>
              <div class="porteur-next-payment-card__content container-fluid px-0 ps-md-2 mt-3 mb-3">
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <div class="d-grid gap-2">
                      <p class="porteur-next-payment-card__item mb-0">
                        <span class="porteur-next-payment-card__label fw-semibold">Projet:</span>
                        <strong><?php echo htmlspecialchars($next_payment["project"], ENT_QUOTES, "UTF-8"); ?></strong>
                      </p>
                      <p class="porteur-next-payment-card__item mb-0">
                        <span class="porteur-next-payment-card__label fw-semibold">Date:</span>
                        <?php echo htmlspecialchars($next_payment["date"], ENT_QUOTES, "UTF-8"); ?>
                      </p>
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <div class="porteur-next-payment-card__amount">
                      <p class="porteur-next-payment-card__item mb-1">
                        <span class="porteur-next-payment-card__label fw-semibold">Montant � payer:</span>
                      </p>
                      <h4 class="next-payment-amount mb-0"><?php echo htmlspecialchars($next_payment["amount"], ENT_QUOTES, "UTF-8"); ?></h4>
                    </div>
                  </div>
                </div>
              </div>
              <div class="text-center mt-3">
                <button type="button" class="dashboard-btn-primary btn-dashboard primary">
                  <i class="bi bi-eye"></i>
                  <span>Voir d�tail</span>
                </button>
              </div>
            </section>
          </div>
        </div>

        <!-- Ligne 3: Graphique barres -->
        <div class="row dashboard-gap-20 mt-4 mb-4">
          <div class="col-12">
            <section class="dashboard-card chart-card">
              <div class="dashboard-card__head">
                <h6>Historique des remboursements</h6>
              </div>
              <div class="chart-holder">
                <canvas id="porteurRepaymentChart"></canvas>
              </div>
            </section>
          </div>
        </div>

        <!-- Ligne 4: Timeline -->
        <div class="row dashboard-gap-20 mt-4">
          <div class="col-12">
            <section class="dashboard-card">
              <div class="dashboard-card__head">
                <h6>Activite recente</h6>
              </div>

              <ul class="dashboard-timeline">
                <?php foreach ($activity_timeline as $item): ?>
                  <li>
                    <span class="dashboard-timeline-dot <?php echo htmlspecialchars($item['variant'], ENT_QUOTES, 'UTF-8'); ?>"></span>
                    <h6><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></h6>
                    <p><?php echo htmlspecialchars($item['message'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <time><?php echo htmlspecialchars($item['time'], ENT_QUOTES, 'UTF-8'); ?></time>
                  </li>
                <?php endforeach; ?>
              </ul>
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
    window.dashboardCharts.porteurRepayment = <?php echo json_encode($porteur_repayment_chart, JSON_UNESCAPED_UNICODE); ?>;
  </script>
  <!-- Scripts dashboard -->
  <script src="<?php echo htmlspecialchars(dashboard_asset('js/dashboard.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
  <script src="<?php echo htmlspecialchars(dashboard_asset('js/dashboard-enhancements.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>








