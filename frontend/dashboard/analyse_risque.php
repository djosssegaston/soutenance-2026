<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$institutionData = require __DIR__ . '/components/institution_data.php';
$projects = $institutionData['projects'];

$averageRisk = 0;
$averageRoi = 0;
$positiveRecommendations = 0;
$sensitiveProjects = 0;
foreach ($projects as $project) {
  $averageRisk += $project['risk_score'];
  $averageRoi += $project['estimated_roi'];
  if ($project['recommendation'] === 'Investir' || $project['recommendation'] === 'Prioritaire') {
    $positiveRecommendations++;
  }
  if ($project['risk_score'] >= 60) {
    $sensitiveProjects++;
  }
}
$averageRisk = count($projects) > 0 ? (int) round($averageRisk / count($projects)) : 0;
$averageRoi = count($projects) > 0 ? round($averageRoi / count($projects), 1) : 0;

$page_title = 'Analyse de risque';
$page_subtitle = 'Comparez les scores de risque, la qualité des dossiers et le rendement attendu.';
$page_active_nav = 'risk';
$page_document_title = 'Analyse de risque - ALOGOTO';

include __DIR__ . '/components/institution_page_start.php';
?>
        <div class="row dashboard-gap-16 mt-2 mb-4 institution-page-kpi-row">
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-shield-exclamation';
            $stat_title = 'Risque moyen';
            $stat_value = $averageRisk . '/100';
            $stat_trend = 'Portefeuille observé';
            $stat_trend_class = 'is-neutral';
            $stat_trend_icon = 'bi-activity';
            $stat_icon_bg = 'rgba(252, 160, 40, 0.18)';
            $stat_icon_color = 'var(--primary-color-3)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-percent';
            $stat_title = 'ROI moyen';
            $stat_value = $averageRoi . '%';
            $stat_trend = 'Rendement estimé';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-graph-up-arrow';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.15)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-patch-check';
            $stat_title = 'Recommandations positives';
            $stat_value = (string) $positiveRecommendations;
            $stat_trend = 'Investir ou prioritaire';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-check2-circle';
            $stat_icon_bg = 'rgba(0, 72, 220, 0.12)';
            $stat_icon_color = 'var(--primary-color-2)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-exclamation-triangle';
            $stat_title = 'Dossiers sensibles';
            $stat_value = (string) $sensitiveProjects;
            $stat_trend = 'Surveillance requise';
            $stat_trend_class = 'is-down';
            $stat_trend_icon = 'bi-arrow-down-right';
            $stat_icon_bg = 'rgba(243, 81, 32, 0.12)';
            $stat_icon_color = 'var(--primary-color-4)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20 mt-4 mb-4">
          <div class="col-12">
            <section class="dashboard-card chart-card">
              <div class="dashboard-card__head">
                <h6>Comparaison risque / rendement</h6>
              </div>
              <div class="chart-holder">
                <canvas id="institutionRiskReturnChart"></canvas>
              </div>
            </section>
          </div>
        </div>

        <div class="row dashboard-gap-20 mt-4 mb-5">
          <div class="col-12">
            <?php
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle">
  <thead>
    <tr>
      <th>Projet</th>
      <th>Score risque</th>
      <th>Score qualité</th>
      <th>Rentabilité estimée</th>
      <th>Recommandation</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($projects as $project): ?>
    <tr>
      <td><?php echo dashboard_escape($project['name']); ?></td>
      <td><?php echo dashboard_escape((string) $project['risk_score']); ?>/100</td>
      <td><?php echo dashboard_escape((string) $project['quality_score']); ?>/100</td>
      <td><?php echo dashboard_escape((string) $project['estimated_roi']); ?>%</td>
      <td>
<?php
      $recommendationClass = 'status-submitted submitted';
      if ($project['recommendation'] === 'Investir' || $project['recommendation'] === 'Prioritaire') {
        $recommendationClass = 'status-approved approved';
      } elseif ($project['recommendation'] === 'Refuser') {
        $recommendationClass = 'status-late late';
      }
?>
        <span class="status-badge <?php echo dashboard_escape($recommendationClass); ?>"><?php echo dashboard_escape($project['recommendation']); ?></span>
      </td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Tableau d’analyse';
            $table_id = 'institutionRiskTable';
            $table_search_placeholder = 'Rechercher une analyse';
            $table_pagination = '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>
<?php include __DIR__ . '/components/institution_page_end.php'; ?>
