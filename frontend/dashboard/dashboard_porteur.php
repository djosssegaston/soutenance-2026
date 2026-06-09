<?php
require_once __DIR__ . '/../includes/path_helpers.php';

// Dashboard accessible sans authentification
$porteur = null;

// Données utilisateur dynamiques (remplace les données hardcoded)
$page_title = "Tableau de bord";
$page_subtitle = $porteur ? $porteur->name . " - Porteur de projet" : "Porteur de projet";
$page_active_nav = "dashboard";
$mobile_nav_context = "porteur";
$mobile_nav_messages_badge = 0; // Sera mis à jour via API
$mobile_nav_notifications_badge = 0; // Sera mis à jour via API
$dashboard_user_name = $porteur ? $porteur->name : "Porteur";
$dashboard_user_id = $porteur ? "PRT-" . $porteur->id : "PRT-000";
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
        <!-- Ligne 1: Cartes stats - DONNÉES DYNAMIQUES via API -->
        <div class="row dashboard-gap-16 mt-2 mb-4 porteur-kpi-row" id="porteur-kpi-container">
          <?php 
          // KPIs avec placeholders pour chargement dynamique
          $porteur_kpis = [
            [
              'icon' => 'bi-folder2',
              'title' => 'Mes Projets',
              'value' => '<span id="kpi-projects-count">--</span>',
              'trend' => '<span id="kpi-projects-trend">--</span>',
              'trend_class' => 'is-neutral',
              'trend_icon' => 'bi-dash',
              'icon_bg' => 'rgba(0, 196, 134, 0.15)',
              'icon_color' => 'var(--primary-color-1)',
            ],
            [
              'icon' => 'bi-cash-coin',
              'title' => 'Finance total recu',
              'value' => '<span id="kpi-total-finance">--</span>',
              'trend' => '<span id="kpi-finance-trend">--</span>',
              'trend_class' => 'is-neutral',
              'trend_icon' => 'bi-dash',
              'icon_bg' => 'rgba(252, 160, 40, 0.18)',
              'icon_color' => 'var(--primary-color-3)',
            ],
            [
              'icon' => 'bi-graph-up-arrow',
              'title' => 'Pourcentage total de remboursement',
              'value' => '<span id="kpi-repayment-percentage">--</span>',
              'trend' => '<span id="kpi-repayment-trend">--</span>',
              'trend_class' => 'is-neutral',
              'trend_icon' => 'bi-dash',
              'icon_bg' => 'rgba(0, 72, 220, 0.12)',
              'icon_color' => 'var(--primary-color-2)',
            ],
            [
              'icon' => 'bi-credit-card-2-front',
              'title' => 'Remboursement total effectue',
              'value' => '<span id="kpi-total-repayment">--</span>',
              'trend' => '<span id="kpi-repayment-frequency">--</span>',
              'trend_class' => 'is-neutral',
              'trend_icon' => 'bi-dash',
              'icon_bg' => 'rgba(243, 81, 32, 0.12)',
              'icon_color' => 'var(--primary-color-4)',
            ],
          ];
          ?>
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
            
            // Définir les projets par défaut ou utiliser ceux passés par le contrôleur
            $porteur_projects = $porteur_projects ?? [
              [
                'code' => 'PRJ-001',
                'title' => 'Cooperative Cacao',
                'sector' => 'Agriculture',
                'amount' => '15M FCFA',
                'validation_label' => 'validé',
                'validation_class' => 'status-approved approved',
                'funding_label' => 'en cours de traitement',
                'funding_class' => 'status-submitted submitted',
              ],
              [
                'code' => 'PRJ-002',
                'title' => 'Ferme Bio Bassam',
                'sector' => 'Agriculture',
                'amount' => '8M FCFA',
                'validation_label' => 'validé',
                'validation_class' => 'status-approved approved',
                'funding_label' => 'financé',
                'funding_class' => 'status-funded funded',
              ],
              [
                'code' => 'PRJ-003',
                'title' => 'Boutique Digitale',
                'sector' => 'Commerce',
                'amount' => '5M FCFA',
                'validation_label' => 'en attente',
                'validation_class' => 'status-submitted submitted',
                'funding_label' => 'en cours de traitement',
                'funding_class' => 'status-draft draft',
              ],
            ];
            
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle" data-items-per-page="10">
  <thead>
    <tr>
      <th>ID</th>
      <th>Projet</th>
      <th>Secteur</th>
      <th>Montant</th>
      <th>Statut validation</th>
      <th>Action</th>
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
            $table_show_filter = false;
            $table_auto_paginate = true;
            include 'components/data_table.html';
            ?>
          </div>

          <div class="col-12">
            <section class="dashboard-card next-payment-card porteur-next-payment-card">
              <div class="dashboard-card__head text-center mb-3">
                <h6 class="mb-0">Prochain remboursement</h6>
              </div>
              <div class="porteur-next-payment-card__content container-fluid px-0 ps-md-2 mt-3 mb-3">
                <?php
                // Définir next_payment par défaut ou utiliser celui passé par le contrôleur
                $next_payment = $next_payment ?? [
                  'project' => 'Cooperative Cacao Abidjan',
                  'date' => '15 Mars 2026',
                  'amount' => '850 000 FCFA',
                ];
                ?>
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <div class="d-grid gap-2">
                      <p class="porteur-next-payment-card__item mb-0">
                        <span class="porteur-next-payment-card__label fw-semibold">Projet:</span>
                        <strong><?php echo htmlspecialchars($next_payment['project'], ENT_QUOTES, 'UTF-8'); ?></strong>
                      </p>
                      <p class="porteur-next-payment-card__item mb-0">
                        <span class="porteur-next-payment-card__label fw-semibold">Date:</span>
                        <?php echo htmlspecialchars($next_payment['date'], ENT_QUOTES, 'UTF-8'); ?>
                      </p>
                    </div>
                  </div>
                  <div class="col-12 col-md-6">
                    <div class="porteur-next-payment-card__amount">
                      <p class="porteur-next-payment-card__item mb-1">
                        <span class="porteur-next-payment-card__label fw-semibold">Montant à payer:</span>
                      </p>
                      <h4 class="next-payment-amount mb-0"><?php echo htmlspecialchars($next_payment['amount'], ENT_QUOTES, 'UTF-8'); ?></h4>
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
                <?php
                // Définir activity_timeline par défaut ou utiliser celle passée par le contrôleur
                $activity_timeline = $activity_timeline ?? [
                  [
                    'title' => 'Paiement recu',
                    'message' => 'Remboursement mensuel effectue',
                    'time' => 'Il y a 2 jours',
                    'variant' => 'success',
                  ],
                  [
                    'title' => 'Document valide',
                    'message' => 'Business plan approuve par l\'admin',
                    'time' => 'Il y a 5 jours',
                    'variant' => 'info',
                  ],
                  [
                    'title' => 'Nouveau financement',
                    'message' => 'Banque Atlantique a investi 5M FCFA',
                    'time' => 'Il y a 1 semaine',
                    'variant' => 'warning',
                  ],
                  [
                    'title' => 'Projet soumis',
                    'message' => 'Boutique Digitale envoye pour revue',
                    'time' => 'Il y a 2 semaines',
                    'variant' => 'muted',
                  ],
                ];
                ?>
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
  <?php include 'components/mobile_bottom_nav.html'; ?>
  <?php include 'components/dashboard_logout_modal.php'; ?>

  <!-- Bootstrap 5 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    $(document).ready(function() {
      const apiBaseUrl = '<?php echo htmlspecialchars(backend_url('api/v1/'), ENT_QUOTES, 'UTF-8'); ?>';
      const token = localStorage.getItem('alogoto_api_token');

      if (!token) {
        window.location.href = '<?php echo htmlspecialchars(frontend_public_url('login.php'), ENT_QUOTES, 'UTF-8'); ?>';
        return;
      }

      const headers = {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json'
      };

      // Load KPIs
      $.ajax({
        url: apiBaseUrl + 'dashboard/porteur/kpis',
        headers: headers,
        success: function(data) {
          updateKPIs(data);
        },
        error: function(xhr) {
          console.error('Error loading KPIs:', xhr);
        }
      });

      // Load projects
      $.ajax({
        url: apiBaseUrl + 'dashboard/porteur/projects',
        headers: headers,
        success: function(data) {
          updateProjects(data);
        },
        error: function(xhr) {
          console.error('Error loading projects:', xhr);
        }
      });

      // Load next repayment
      $.ajax({
        url: apiBaseUrl + 'dashboard/porteur/next-repayment',
        headers: headers,
        success: function(data) {
          updateNextRepayment(data);
        },
        error: function(xhr) {
          console.error('Error loading next repayment:', xhr);
        }
      });

      // Load repayment history
      $.ajax({
        url: apiBaseUrl + 'dashboard/porteur/repayment-history',
        headers: headers,
        success: function(data) {
          updateRepaymentChart(data);
        },
        error: function(xhr) {
          console.error('Error loading repayment history:', xhr);
        }
      });

      // Load activities
      $.ajax({
        url: apiBaseUrl + 'dashboard/porteur/activities',
        headers: headers,
        success: function(data) {
          updateActivities(data);
        },
        error: function(xhr) {
          console.error('Error loading activities:', xhr);
        }
      });

      // Load counts for badges
      $.ajax({
        url: apiBaseUrl + 'dashboard/counts',
        headers: headers,
        success: function(data) {
          updateCounts(data);
        },
        error: function(xhr) {
          console.error('Error loading counts:', xhr);
        }
      });

      function updateKPIs(data) {
        // Update KPI cards
        const kpiCards = $('.porteur-kpi-row .stat-card');
        if (kpiCards.length >= 4) {
          // Projects count
          $(kpiCards[0]).find('.stat-value').text(data.projects_count);
          
          // Total finance
          $(kpiCards[1]).find('.stat-value').text((data.total_finance_received / 1000000).toFixed(1) + 'M FCFA');
          
          // Repayment percentage
          $(kpiCards[2]).find('.stat-value').text(data.repayment_percentage.toFixed(1) + '%');
          
          // Total repayment done
          $(kpiCards[3]).find('.stat-value').text((data.total_repayment_done / 1000000).toFixed(1) + 'M FCFA');
        }
      }

      function updateProjects(data) {
        const tbody = $('#porteurProjects tbody');
        tbody.empty();
        
        data.projects.forEach(function(project) {
          const row = `
            <tr>
              <td>${project.code}</td>
              <td>${project.title}</td>
              <td>${project.sector}</td>
              <td>${project.amount}</td>
              <td><span class="status-badge ${project.validation_class}">${project.validation_label}</span></td>
              <td><span class="status-badge ${project.funding_class}">${project.funding_label}</span></td>
            </tr>
          `;
          tbody.append(row);
        });

        // Update pagination if needed
        // For now, keep static pagination
      }

      function updateNextRepayment(data) {
        const container = $('.porteur-next-payment-card__content');
        if (data.next_payment) {
          container.html(`
            <div class="row g-3">
              <div class="col-12 col-md-6">
                <div class="d-grid gap-2">
                  <p class="porteur-next-payment-card__item mb-0">
                    <span class="porteur-next-payment-card__label fw-semibold">Projet:</span>
                    <strong>${data.next_payment.project}</strong>
                  </p>
                  <p class="porteur-next-payment-card__item mb-0">
                    <span class="porteur-next-payment-card__label fw-semibold">Date:</span>
                    ${data.next_payment.date}
                  </p>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="porteur-next-payment-card__amount">
                  <p class="porteur-next-payment-card__item mb-1">
                    <span class="porteur-next-payment-card__label fw-semibold">Montant à payer:</span>
                  </p>
                  <h4 class="next-payment-amount mb-0">${data.next_payment.amount}</h4>
                </div>
              </div>
            </div>
          `);
        } else {
          container.html('<p class="text-center">Aucun remboursement prévu</p>');
        }
      }

      function updateRepaymentChart(data) {
        window.dashboardCharts.porteurRepayment = {
          labels: data.repayment_history.map(item => item.month),
          values: data.repayment_history.map(item => item.amount)
        };
        
        // Update chart if it exists
        if (window.porteurRepaymentChart) {
          const payload = getChartPayload("porteurRepayment", window.dashboardCharts.porteurRepayment);
          window.porteurRepaymentChart.data.labels = payload.labels;
          window.porteurRepaymentChart.data.datasets[0].data = payload.values;
          window.porteurRepaymentChart.update();
        }
      }

      function updateActivities(data) {
        const timeline = $('.dashboard-timeline');
        timeline.empty();
        
        data.activities.forEach(function(activity) {
          const item = `
            <li>
              <span class="dashboard-timeline-dot ${activity.variant}"></span>
              <h6>${activity.title}</h6>
              <p>${activity.message}</p>
              <time>${activity.time}</time>
            </li>
          `;
          timeline.append(item);
        });
      }

      function updateCounts(data) {
        // Update mobile nav badges
        $('.mobile-nav__badge--messages').text(data.messages_count);
        $('.mobile-nav__badge--notifications').text(data.notifications_count);
      }
    });

    window.dashboardCharts = window.dashboardCharts || {};
    <?php
    // Définir porteur_repayment_chart par défaut ou utiliser celui passé par le contrôleur
    $porteur_repayment_chart = $porteur_repayment_chart ?? [
      'labels' => ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jun'],
      'values' => [850, 850, 850, 850, 850, 0],
    ];
    ?>
    window.dashboardCharts.porteurRepayment = <?php echo json_encode($porteur_repayment_chart, JSON_UNESCAPED_UNICODE); ?>;
  </script>
  <!-- Scripts dashboard -->
  <script src="<?php echo htmlspecialchars(dashboard_asset('js/api-client.js'), ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
