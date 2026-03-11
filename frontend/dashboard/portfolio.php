<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$institutionData = require __DIR__ . '/components/institution_data.php';
$projects = $institutionData['projects'];

$totalInvested = 0;
foreach ($projects as $project) {
  $totalInvested += $project['amount_invested'];
}

$page_title = 'Portfolio';
$page_subtitle = 'Mesurez la performance du portefeuille et la qualité des actifs financés.';
$page_active_nav = 'portfolio';
$page_document_title = 'Portfolio - ALOGOTO';

include __DIR__ . '/components/institution_page_start.php';
?>
        <div class="row dashboard-gap-16 mt-2 mb-4 institution-page-kpi-row">
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-cash-stack';
            $stat_title = 'Investissement total';
            $stat_value = dashboard_format_fcfa($totalInvested);
            $stat_trend = '+14% en glissement';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-arrow-up-right';
            $stat_icon_bg = 'rgba(252, 160, 40, 0.18)';
            $stat_icon_color = 'var(--primary-color-3)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-percent';
            $stat_title = 'ROI moyen';
            $stat_value = '10,8%';
            $stat_trend = '+1,2 point';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-graph-up-arrow';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.15)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-briefcase';
            $stat_title = 'Projets financés';
            $stat_value = (string) count($projects);
            $stat_trend = 'Lignes actives';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-check2-circle';
            $stat_icon_bg = 'rgba(0, 72, 220, 0.12)';
            $stat_icon_color = 'var(--primary-color-2)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-speedometer2';
            $stat_title = 'Score portefeuille';
            $stat_value = '91/100';
            $stat_trend = 'Qualité globale';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-stars';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.12)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20 mt-4 mb-4">
          <div class="col-12">
            <section class="dashboard-card chart-card">
              <div class="dashboard-card__head">
                <h6>Performance du portefeuille</h6>
              </div>
              <div class="chart-holder">
                <canvas id="institutionPerformanceChart"></canvas>
              </div>
            </section>
          </div>
        </div>

        <div class="row dashboard-gap-20 mt-4 mb-5">
<?php foreach ($projects as $project): ?>
          <div class="col-12 col-md-6 col-xl-3">
            <section class="dashboard-card portfolio-track-card hover-lift h-100">
              <p class="portfolio-track-card__meta mb-1"><?php echo dashboard_escape($project['name']); ?></p>
              <h4 class="mb-1"><?php echo dashboard_escape(dashboard_format_fcfa($project['amount_invested'])); ?></h4>
              <p class="portfolio-track-card__meta mb-3">Score qualité <?php echo dashboard_escape((string) $project['quality_score']); ?>/100</p>
              <div class="dashboard-progress">
                <div class="dashboard-progress__bar bg-success-token" style="width: <?php echo dashboard_escape((string) $project['quality_score']); ?>%;"></div>
              </div>
              <p class="portfolio-track-card__meta mt-2"><?php echo dashboard_escape($project['risk_label']); ?> • ROI <?php echo dashboard_escape((string) $project['estimated_roi']); ?>%</p>
            </section>
          </div>
<?php endforeach; ?>
        </div>
<?php include __DIR__ . '/components/institution_page_end.php'; ?>
