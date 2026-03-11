<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$page_title = 'Finance';
$page_subtitle = 'Consolidez les flux financiers, remboursements, défauts et revenus de plateforme.';
$page_active_nav = 'finance';
$page_document_title = 'Finance admin - ALOGOTO';

include __DIR__ . '/components/admin_page_start.php';
?>
        <div class="row dashboard-gap-16 mt-2 mb-4 admin-page-kpi-row">
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-cash-coin';
            $stat_title = 'Volume total financé';
            $stat_value = '2.4Mrd FCFA';
            $stat_trend = '+18%';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-arrow-up-right';
            $stat_icon_bg = 'rgba(252, 160, 40, 0.18)';
            $stat_icon_color = 'var(--primary-color-3)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-credit-card-2-front';
            $stat_title = 'Remboursements';
            $stat_value = '1.1Mrd FCFA';
            $stat_trend = '+9%';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-arrow-up-right';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.15)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-exclamation-circle';
            $stat_title = 'Défauts';
            $stat_value = '3.1%';
            $stat_trend = '-0,5 point';
            $stat_trend_class = 'is-down';
            $stat_trend_icon = 'bi-arrow-down-right';
            $stat_icon_bg = 'rgba(243, 81, 32, 0.12)';
            $stat_icon_color = 'var(--primary-color-4)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-wallet2';
            $stat_title = 'Revenus plateforme';
            $stat_value = '86M FCFA';
            $stat_trend = '+11%';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-graph-up-arrow';
            $stat_icon_bg = 'rgba(0, 72, 220, 0.12)';
            $stat_icon_color = 'var(--primary-color-2)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20 mt-4 mb-5">
          <div class="col-12">
            <section class="dashboard-card chart-card">
              <div class="dashboard-card__head">
                <h6>Vue financière consolidée</h6>
              </div>
              <div class="chart-holder">
                <canvas id="adminFundingChart"></canvas>
              </div>
            </section>
          </div>
        </div>
<?php include __DIR__ . '/components/admin_page_end.php'; ?>
