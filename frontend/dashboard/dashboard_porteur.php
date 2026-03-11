<?php
require_once __DIR__ . '/../includes/path_helpers.php';

$page_title = "Tableau de bord";
$page_subtitle = "Kouame Assi - Porteur de projet";
$page_active_nav = "dashboard";
$mobile_nav_context = "porteur";
$mobile_nav_messages_badge = 3;
$mobile_nav_notifications_badge = 4;
$dashboard_user_name = "Kouame Assi";
$dashboard_user_id = "PRT-2026-001";
$dashboard_user_role = "Porteur de projet";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
  <?php include 'components/sidebar_porteur.php'; ?>
  <div class="sidebar-overlay"></div>

  <div class="dashboard-wrapper">
    <?php include 'components/header.php'; ?>

    <main class="dashboard-content main-content">
      <div class="container-fluid px-0">
        <!-- Ligne 1: Cartes stats -->
        <div class="row dashboard-gap-16 mt-2 mb-4 porteur-kpi-row">
          <div class="col-12 col-md-6 col-xl-3">
            <div class="dashboard-card content-card stat-card hover-lift">
              <div class="porteur-kpi-card__header d-flex align-items-center gap-2">
                <span class="stat-card__icon icon-box" style="background: rgba(0, 196, 134, 0.15); color: var(--primary-color-1);">
                  <i class="bi bi-folder2"></i>
                </span>
                <p class="stat-card__label stat-label">Mes Projets</p>
              </div>
              <h4 class="stat-card__value stat-value text-center">3</h4>
              <p class="stat-card__trend stat-trend is-up">
                <i class="bi bi-arrow-up-right"></i>
                <span>+1</span>
              </p>
            </div>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <div class="dashboard-card content-card stat-card hover-lift">
              <div class="porteur-kpi-card__header d-flex align-items-center gap-2">
                <span class="stat-card__icon icon-box" style="background: rgba(252, 160, 40, 0.18); color: var(--primary-color-3);">
                  <i class="bi bi-cash-coin"></i>
                </span>
                <p class="stat-card__label stat-label">Finance</p>
              </div>
              <h4 class="stat-card__value stat-value text-center">28M FCFA</h4>
              <p class="stat-card__trend stat-trend is-up">
                <i class="bi bi-arrow-up-right"></i>
                <span>+15%</span>
              </p>
            </div>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <div class="dashboard-card content-card stat-card hover-lift">
              <div class="porteur-kpi-card__header d-flex align-items-center gap-2">
                <span class="stat-card__icon icon-box" style="background: rgba(0, 72, 220, 0.12); color: var(--primary-color-2);">
                  <i class="bi bi-graph-up-arrow"></i>
                </span>
                <p class="stat-card__label stat-label">Progression</p>
              </div>
              <h4 class="stat-card__value stat-value text-center">67%</h4>
              <p class="stat-card__trend stat-trend is-up">
                <i class="bi bi-arrow-up-right"></i>
                <span>+5%</span>
              </p>
            </div>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <div class="dashboard-card content-card stat-card hover-lift">
              <div class="porteur-kpi-card__header d-flex align-items-center gap-2">
                <span class="stat-card__icon icon-box" style="background: rgba(243, 81, 32, 0.12); color: var(--primary-color-4);">
                  <i class="bi bi-credit-card-2-front"></i>
                </span>
                <p class="stat-card__label stat-label">Rembourse</p>
              </div>
              <h4 class="stat-card__value stat-value text-center">8.5M FCFA</h4>
              <p class="stat-card__trend stat-trend is-neutral">
                <i class="bi bi-calendar-check"></i>
                <span>Mensuel</span>
              </p>
            </div>
          </div>
        </div>

        <!-- Ligne 2: Table + prochain paiement -->
        <div class="row dashboard-gap-20 mt-4 mb-4">
          <div class="col-12">
            <?php
            $table_title = "Mes projets";
            $table_id = "porteurProjects";
            $table_search_placeholder = "Rechercher un projet";
            $table_html = <<<'HTML'
<table class="table dashboard-table data-table align-middle">
  <thead>
    <tr>
      <th>ID</th>
      <th>Projet</th>
      <th>Secteur</th>
      <th>Montant</th>
      <th>Progression</th>
      <th>Statut</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>PRJ-001</td>
      <td>Cooperative Cacao</td>
      <td>Agriculture</td>
      <td>15M FCFA</td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="mini-progress"><div class="mini-progress__bar" style="width:75%;"></div></div>
          <small>75%</small>
        </div>
      </td>
      <td><span class="status-badge status-funding funding">En financement</span></td>
    </tr>
    <tr>
      <td>PRJ-002</td>
      <td>Ferme Bio Bassam</td>
      <td>Agriculture</td>
      <td>8M FCFA</td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="mini-progress"><div class="mini-progress__bar" style="width:100%;"></div></div>
          <small>100%</small>
        </div>
      </td>
      <td><span class="status-badge status-funded funded">Finance</span></td>
    </tr>
    <tr>
      <td>PRJ-003</td>
      <td>Boutique Digitale</td>
      <td>Commerce</td>
      <td>5M FCFA</td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <div class="mini-progress"><div class="mini-progress__bar" style="width:0%;"></div></div>
          <small>0%</small>
        </div>
      </td>
      <td><span class="status-badge status-draft draft">Brouillon</span></td>
    </tr>
  </tbody>
</table>
HTML;
            $table_pagination = '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include 'components/data_table.html';
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
                        <strong>Cooperative Cacao Abidjan</strong>
                      </p>
                      <p class="porteur-next-payment-card__item mb-0">
                        <span class="porteur-next-payment-card__label fw-semibold">Date:</span>
                        15 Mars 2026
                      </p>
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <div class="porteur-next-payment-card__amount">
                      <p class="porteur-next-payment-card__item mb-1">
                        <span class="porteur-next-payment-card__label fw-semibold">Montant à payer:</span>
                      </p>
                      <h4 class="next-payment-amount mb-0">850 000 FCFA</h4>
                    </div>
                  </div>
                </div>
              </div>
              <div class="text-center mt-3">
                <button type="button" class="dashboard-btn-primary btn-dashboard primary">
                  <i class="bi bi-eye"></i>
                  <span>Voir détail</span>
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
                <li>
                  <span class="dashboard-timeline-dot success"></span>
                  <h6>Paiement recu</h6>
                  <p>Remboursement mensuel effectue</p>
                  <time>Il y a 2 jours</time>
                </li>
                <li>
                  <span class="dashboard-timeline-dot info"></span>
                  <h6>Document valide</h6>
                  <p>Business plan approuve par l'admin</p>
                  <time>Il y a 5 jours</time>
                </li>
                <li>
                  <span class="dashboard-timeline-dot warning"></span>
                  <h6>Nouveau financement</h6>
                  <p>Banque Atlantique a investi 5M FCFA</p>
                  <time>Il y a 1 semaine</time>
                </li>
                <li>
                  <span class="dashboard-timeline-dot muted"></span>
                  <h6>Projet soumis</h6>
                  <p>Boutique Digitale envoye pour revue</p>
                  <time>Il y a 2 semaines</time>
                </li>
              </ul>
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
