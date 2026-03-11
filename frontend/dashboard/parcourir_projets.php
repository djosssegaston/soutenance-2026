<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$institutionData = require __DIR__ . '/components/institution_data.php';
$projects = $institutionData['projects'];

$lowRiskProjects = 0;
$totalRequested = 0;
foreach ($projects as $project) {
  $totalRequested += $project['requested_amount'];
  if ($project['risk_label'] === 'Faible') {
    $lowRiskProjects++;
  }
}
$averageTicket = count($projects) > 0 ? (int) round($totalRequested / count($projects)) : 0;

$page_title = 'Parcourir les projets';
$page_subtitle = 'Explorez les projets disponibles et priorisez les opportunités d’investissement.';
$page_active_nav = 'browse';
$page_document_title = 'Parcourir les projets - ALOGOTO';

include __DIR__ . '/components/institution_page_start.php';
?>
        <div class="row dashboard-gap-16 mt-2 mb-4 institution-page-kpi-row">
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-folder2-open';
            $stat_title = 'Projets disponibles';
            $stat_value = (string) count($projects);
            $stat_trend = '+2 cette semaine';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-arrow-up-right';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.15)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-cash-coin';
            $stat_title = 'Ticket moyen';
            $stat_value = dashboard_format_fcfa($averageTicket);
            $stat_trend = 'Montant cible';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-graph-up-arrow';
            $stat_icon_bg = 'rgba(252, 160, 40, 0.18)';
            $stat_icon_color = 'var(--primary-color-3)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-shield-check';
            $stat_title = 'Faible risque';
            $stat_value = (string) $lowRiskProjects;
            $stat_trend = 'À forte priorité';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-check2-circle';
            $stat_icon_bg = 'rgba(0, 72, 220, 0.12)';
            $stat_icon_color = 'var(--primary-color-2)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-calendar-check';
            $stat_title = 'Comités en cours';
            $stat_value = '3';
            $stat_trend = 'Décisions attendues';
            $stat_trend_class = 'is-neutral';
            $stat_trend_icon = 'bi-hourglass-split';
            $stat_icon_bg = 'rgba(243, 81, 32, 0.12)';
            $stat_icon_color = 'var(--primary-color-4)';
            include __DIR__ . '/components/stat_card.html';
            ?>
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
      <th>ID</th>
      <th>Nom du projet</th>
      <th>Secteur</th>
      <th>Montant demandé</th>
      <th>Score de risque</th>
      <th>Statut</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($projects as $project): ?>
    <tr>
      <td><?php echo dashboard_escape($project['id']); ?></td>
      <td><?php echo dashboard_escape($project['name']); ?></td>
      <td><?php echo dashboard_escape($project['sector']); ?></td>
      <td><?php echo dashboard_escape(dashboard_format_fcfa($project['requested_amount'])); ?></td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <span class="<?php echo dashboard_escape($project['risk_class']); ?>"><?php echo dashboard_escape($project['risk_label']); ?></span>
          <small><?php echo dashboard_escape((string) $project['risk_score']); ?>/100</small>
        </div>
      </td>
      <td><span class="status-badge <?php echo dashboard_escape($project['status_class']); ?>"><?php echo dashboard_escape($project['status_label']); ?></span></td>
      <td>
        <a href="analyse_risque.php" class="dashboard-btn-primary btn-dashboard primary sm">
          <i class="bi bi-eye"></i>
          <span>Voir le projet</span>
        </a>
      </td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Projets disponibles';
            $table_id = 'browseProjects';
            $table_search_placeholder = 'Rechercher un projet';
            $table_pagination = '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20 mb-5">
          <div class="col-12 col-md-6 col-xl-4">
            <section class="dashboard-card institution-soft-card h-100">
              <p class="institution-page-subtle mb-1">Priorité d’analyse</p>
              <h6 class="mb-2">Plateforme EdTech Dakar</h6>
              <p class="mb-0">Score qualité élevé et rentabilité estimée supérieure à 16%.</p>
            </section>
          </div>
          <div class="col-12 col-md-6 col-xl-4">
            <section class="dashboard-card institution-soft-card h-100">
              <p class="institution-page-subtle mb-1">Secteur dominant</p>
              <h6 class="mb-2">Agriculture</h6>
              <p class="mb-0">Le pipeline actuel reste tiré par les projets agricoles structurés.</p>
            </section>
          </div>
          <div class="col-12 col-md-6 col-xl-4">
            <section class="dashboard-card institution-soft-card h-100">
              <p class="institution-page-subtle mb-1">Action recommandée</p>
              <h6 class="mb-2">Préparer le comité</h6>
              <p class="mb-0">Trois dossiers sont prêts pour arbitrage cette semaine.</p>
            </section>
          </div>
        </div>
<?php include __DIR__ . '/components/institution_page_end.php'; ?>
