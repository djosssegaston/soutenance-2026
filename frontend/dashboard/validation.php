<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$institutionData = require __DIR__ . '/components/institution_data.php';
$projects = $institutionData['projects'];

$page_title = 'Validation';
$page_subtitle = 'Arbitrez les projets en revue et formalisez les décisions de comité.';
$page_active_nav = 'validation';
$page_document_title = 'Validation - ALOGOTO';

include __DIR__ . '/components/institution_page_start.php';
?>
        <div class="row dashboard-gap-16 mt-2 mb-4 institution-page-kpi-row">
          <div class="col-12 col-md-6 col-xl-4">
            <?php
            $stat_icon = 'bi-file-earmark-check';
            $stat_title = 'Dossiers à valider';
            $stat_value = '3';
            $stat_trend = 'Comité actif';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-arrow-up-right';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.15)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-4">
            <?php
            $stat_icon = 'bi-clipboard-data';
            $stat_title = 'Risque moyen';
            $stat_value = '39/100';
            $stat_trend = 'Avant arbitrage';
            $stat_trend_class = 'is-neutral';
            $stat_trend_icon = 'bi-hourglass-split';
            $stat_icon_bg = 'rgba(252, 160, 40, 0.18)';
            $stat_icon_color = 'var(--primary-color-3)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-4">
            <?php
            $stat_icon = 'bi-patch-question';
            $stat_title = 'Décisions en attente';
            $stat_value = '2';
            $stat_trend = 'Avant 18h';
            $stat_trend_class = 'is-down';
            $stat_trend_icon = 'bi-exclamation-circle';
            $stat_icon_bg = 'rgba(243, 81, 32, 0.12)';
            $stat_icon_color = 'var(--primary-color-4)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20 mt-4 mb-5">
          <div class="col-12">
            <section class="dashboard-card content-card dashboard-table-card">
              <div class="dashboard-table-card__header card-title">
                <h6>Projets à valider</h6>
                <div class="dashboard-table-card__actions">
                  <form id="validationSearch" class="dashboard-inline-search" data-ajax="true" action="#" method="get">
                    <i class="bi bi-search"></i>
                    <input type="search" name="table-search" placeholder="Rechercher un projet" aria-label="Rechercher un projet" />
                  </form>
                </div>
              </div>
              <div class="dashboard-table-wrapper table-responsive">
                <table class="table dashboard-table data-table align-middle">
                  <thead>
                    <tr>
                      <th>Projet</th>
                      <th>Institution</th>
                      <th>Montant</th>
                      <th>Score risque</th>
                      <th>Statut validation</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
<?php foreach ($projects as $project): ?>
                    <tr>
                      <td><?php echo dashboard_escape($project['name']); ?></td>
                      <td><?php echo dashboard_escape($project['institution']); ?></td>
                      <td><?php echo dashboard_escape(dashboard_format_fcfa($project['requested_amount'])); ?></td>
                      <td><?php echo dashboard_escape((string) $project['risk_score']); ?>/100</td>
                      <td><span class="status-badge <?php echo dashboard_escape($project['validation_class']); ?>"><?php echo dashboard_escape($project['validation_label']); ?></span></td>
                      <td>
                        <div class="institution-action-group">
                          <button type="button" class="dashboard-btn-primary btn-dashboard primary sm">
                            <i class="bi bi-check2"></i>
                            <span>Valider</span>
                          </button>
                          <button type="button" class="btn-dashboard outline sm text-danger">
                            <i class="bi bi-x-circle"></i>
                            <span>Refuser</span>
                          </button>
                        </div>
                      </td>
                    </tr>
<?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <div class="dashboard-pagination pagination-dashboard">
                <button class="dashboard-page-btn page-btn active">1</button>
                <button class="dashboard-page-btn page-btn">2</button>
                <button class="dashboard-page-btn page-btn">3</button>
              </div>
            </section>
          </div>
        </div>
<?php include __DIR__ . '/components/institution_page_end.php'; ?>
