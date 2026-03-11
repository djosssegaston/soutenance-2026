<?php
require_once __DIR__ . '/../includes/path_helpers.php';

$page_title = "Tableau de bord";
$page_subtitle = "Administration générale";
$page_active_nav = "dashboard";
$mobile_nav_context = "admin";
$mobile_nav_messages_badge = 3;
$mobile_nav_notifications_badge = 3;
$dashboard_user_name = "Admin principal";
$dashboard_user_id = "ADM-2026-001";
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
        <!-- Ligne 1: Cartes stats -->
        <div class="row dashboard-gap-16 mt-2 mb-4 admin-kpi-row">
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = "bi-folder2";
            $stat_title = "Projets Totaux";
            $stat_value = "156";
            $stat_trend = "+12";
            $stat_trend_class = "is-up";
            $stat_trend_icon = "bi-arrow-up-right";
            $stat_icon_bg = "rgba(0, 196, 134, 0.15)";
            $stat_icon_color = "var(--primary-color-1)";
            include 'components/stat_card.html';
            ?>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = "bi-cash-coin";
            $stat_title = "Volume Financé";
            $stat_value = "2.4Mrd FCFA";
            $stat_trend = "+18%";
            $stat_trend_class = "is-up";
            $stat_trend_icon = "bi-arrow-up-right";
            $stat_icon_bg = "rgba(252, 160, 40, 0.18)";
            $stat_icon_color = "var(--primary-color-3)";
            include 'components/stat_card.html';
            ?>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = "bi-graph-up-arrow";
            $stat_title = "Taux Remboursement";
            $stat_value = "94.2%";
            $stat_trend = "+2.1%";
            $stat_trend_class = "is-up";
            $stat_trend_icon = "bi-arrow-up-right";
            $stat_icon_bg = "rgba(0, 72, 220, 0.12)";
            $stat_icon_color = "var(--primary-color-2)";
            include 'components/stat_card.html';
            ?>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = "bi-exclamation-triangle";
            $stat_title = "Taux Défaut";
            $stat_value = "3.1%";
            $stat_trend = "-0.5%";
            $stat_trend_class = "is-down";
            $stat_trend_icon = "bi-arrow-down-right";
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
            $table_title = "Projets récents";
            $table_id = "adminRecentProjects";
            $table_search_placeholder = "Rechercher un projet";
            $table_html = <<<'HTML'
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
    <tr>
      <td>PRJ-001</td>
      <td>Coopérative Cacao Abidjan</td>
      <td>Kouame A.</td>
      <td>15M FCFA</td>
      <td><span class="status-badge status-approved">Approuvé</span></td>
    </tr>
    <tr>
      <td>PRJ-002</td>
      <td>Clinique Mobile Bamako</td>
      <td>Diallo M.</td>
      <td>25M FCFA</td>
      <td><span class="status-badge status-funding">En financement</span></td>
    </tr>
    <tr>
      <td>PRJ-003</td>
      <td>EdTech Plateforme Dakar</td>
      <td>Ndiaye S.</td>
      <td>8M FCFA</td>
      <td><span class="status-badge status-submitted">Soumis</span></td>
    </tr>
    <tr>
      <td>PRJ-004</td>
      <td>Ferme Solaire Lomé</td>
      <td>Agbeko K.</td>
      <td>45M FCFA</td>
      <td><span class="status-badge status-rejected">Rejeté</span></td>
    </tr>
    <tr>
      <td>PRJ-005</td>
      <td>Transport Électrique</td>
      <td>Mensah P.</td>
      <td>30M FCFA</td>
      <td><span class="status-badge status-late">En retard</span></td>
    </tr>
  </tbody>
</table>
HTML;
            $table_pagination = '<button class="dashboard-page-btn active">1</button><button class="dashboard-page-btn">2</button><button class="dashboard-page-btn">3</button>';
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

                <ul class="dashboard-legend">
                  <li><span><i class="bg-success-token"></i>Agriculture</span><strong>35%</strong></li>
                  <li><span><i class="bg-info-token"></i>Technologie</span><strong>25%</strong></li>
                  <li><span><i class="bg-warning-token"></i>Santé</span><strong>20%</strong></li>
                  <li><span><i class="bg-danger-token"></i>Transport</span><strong>15%</strong></li>
                  <li><span><i style="background:#6A726F;"></i>Autre</span><strong>5%</strong></li>
                </ul>
              </div>
            </section>
          </div>
        </div>

        <!-- Ligne 4: Classement institutions -->
        <div class="row dashboard-gap-20">
          <div class="col-12 col-md-6 col-lg-4">
            <section class="dashboard-card institution-card hover-lift">
              <p class="institution-card__meta mb-2">Banque Atlantique</p>
              <h4 class="mb-2">450M FCFA</h4>
              <p class="institution-card__meta mb-3">23 projets</p>
              <div class="dashboard-progress">
                <div class="dashboard-progress__bar bg-success-token" style="width: 92%;"></div>
              </div>
              <p class="institution-card__meta mt-2">92%</p>
            </section>
          </div>

          <div class="col-12 col-md-6 col-lg-4">
            <section class="dashboard-card institution-card hover-lift">
              <p class="institution-card__meta mb-2">Ecobank</p>
              <h4 class="mb-2">380M FCFA</h4>
              <p class="institution-card__meta mb-3">18 projets</p>
              <div class="dashboard-progress">
                <div class="dashboard-progress__bar bg-warning-token" style="width: 87%;"></div>
              </div>
              <p class="institution-card__meta mt-2">87%</p>
            </section>
          </div>

          <div class="col-12 col-md-6 col-lg-4">
            <section class="dashboard-card institution-card hover-lift">
              <p class="institution-card__meta mb-2">BOAD</p>
              <h4 class="mb-2">290M FCFA</h4>
              <p class="institution-card__meta mb-3">12 projets</p>
              <div class="dashboard-progress">
                <div class="dashboard-progress__bar bg-danger-token" style="width: 78%;"></div>
              </div>
              <p class="institution-card__meta mt-2">78%</p>
            </section>
          </div>
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
  <script src="<?php echo htmlspecialchars(dashboard_asset('js/dashboard.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
  <script src="<?php echo htmlspecialchars(dashboard_asset('js/dashboard-enhancements.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
