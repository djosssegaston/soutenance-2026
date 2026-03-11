<?php
require_once __DIR__ . '/../includes/path_helpers.php';

$page_title = "Tableau de bord";
$page_subtitle = "Banque Atlantique - Vue d’ensemble";
$page_active_nav = "dashboard";
$mobile_nav_context = "institution";
$mobile_nav_messages_badge = 3;
$mobile_nav_notifications_badge = 3;
$dashboard_user_name = "Banque Atlantique";
$dashboard_user_id = "partenaires@banqueatlantique.ci";
$dashboard_user_role = "Institution financière";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
  <?php include 'components/sidebar_institution.php'; ?>
  <div class="sidebar-overlay"></div>

  <div class="dashboard-wrapper">
    <?php include 'components/header.php'; ?>

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
              <h4 class="stat-card__value stat-value text-center">45 projets</h4>
              <p class="stat-card__trend stat-trend is-up">
                <i class="bi bi-arrow-up-right"></i>
                <span>+3</span>
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
              <h4 class="stat-card__value stat-value text-center">245M FCFA</h4>
              <p class="stat-card__trend stat-trend is-up">
                <i class="bi bi-arrow-up-right"></i>
                <span>+12%</span>
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
              <h4 class="stat-card__value stat-value text-center">10.8%</h4>
              <p class="stat-card__trend stat-trend is-up">
                <i class="bi bi-arrow-up-right"></i>
                <span>+1.2%</span>
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
              <h4 class="stat-card__value stat-value text-center">94/100</h4>
              <p class="stat-card__trend stat-trend is-up">
                <i class="bi bi-arrow-up-right"></i>
                <span>+2</span>
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
            $table_html = <<<'HTML'
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
    <tr>
      <td>PRJ-012</td>
      <td>Coopérative Cacao Abidjan</td>
      <td>Agriculture</td>
      <td>12M FCFA</td>
      <td><span class="risk-badge risk-low">Faible</span></td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="mini-progress mini-progress--score"><div class="mini-progress__bar mini-progress__bar--success" style="width:85%;"></div></div>
          <small>85</small>
        </div>
      </td>
      <td><span class="status-badge status-approved approved">Approuvé</span></td>
    </tr>
    <tr>
      <td>PRJ-015</td>
      <td>Clinique Mobile Bamako</td>
      <td>Santé</td>
      <td>25M FCFA</td>
      <td><span class="risk-badge risk-medium">Moyen</span></td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="mini-progress mini-progress--score"><div class="mini-progress__bar" style="width:72%;"></div></div>
          <small>72</small>
        </div>
      </td>
      <td><span class="status-badge status-visible submitted">Visible</span></td>
    </tr>
    <tr>
      <td>PRJ-018</td>
      <td>EdTech Plateforme Dakar</td>
      <td>Technologie</td>
      <td>15M FCFA</td>
      <td><span class="risk-badge risk-low">Faible</span></td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="mini-progress mini-progress--score"><div class="mini-progress__bar mini-progress__bar--success" style="width:91%;"></div></div>
          <small>91</small>
        </div>
      </td>
      <td><span class="status-badge status-approved approved">Approuvé</span></td>
    </tr>
    <tr>
      <td>PRJ-021</td>
      <td>Transport Électrique Lomé</td>
      <td>Transport</td>
      <td>40M FCFA</td>
      <td><span class="risk-badge risk-high">Élevé</span></td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="mini-progress mini-progress--score"><div class="mini-progress__bar mini-progress__bar--danger" style="width:58%;"></div></div>
          <small>58</small>
        </div>
      </td>
      <td><span class="status-badge status-visible submitted">Visible</span></td>
    </tr>
  </tbody>
</table>
HTML;
            $table_pagination = '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include 'components/data_table.html';
            ?>
          </div>
        </div>

        <!-- Ligne 4: Cartes suivi -->
        <div class="row dashboard-gap-20 mt-4">
          <div class="col-12 col-md-6 col-xl-4">
            <section class="dashboard-card portfolio-track-card hover-lift">
              <p class="portfolio-track-card__meta mb-1">Remboursements à jour</p>
              <h4 class="mb-1">38 <small class="portfolio-track-card__meta">/45</small></h4>
              <p class="portfolio-track-card__meta mb-3">84%</p>
              <div class="dashboard-progress">
                <div class="dashboard-progress__bar bg-success-token" style="width:84%;"></div>
              </div>
            </section>
          </div>

          <div class="col-12 col-md-6 col-xl-4">
            <section class="dashboard-card portfolio-track-card hover-lift">
              <p class="portfolio-track-card__meta mb-1">En retard</p>
              <h4 class="mb-1">5 <small class="portfolio-track-card__meta">/45</small></h4>
              <p class="portfolio-track-card__meta mb-3">11%</p>
              <div class="dashboard-progress">
                <div class="dashboard-progress__bar bg-warning-token" style="width:11%;"></div>
              </div>
            </section>
          </div>

          <div class="col-12 col-md-6 col-xl-4">
            <section class="dashboard-card portfolio-track-card hover-lift">
              <p class="portfolio-track-card__meta mb-1">En défaut</p>
              <h4 class="mb-1">2 <small class="portfolio-track-card__meta">/45</small></h4>
              <p class="portfolio-track-card__meta mb-3">4%</p>
              <div class="dashboard-progress">
                <div class="dashboard-progress__bar bg-danger-token" style="width:4%;"></div>
              </div>
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
