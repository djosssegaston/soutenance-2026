<?php
require_once __DIR__ . '/components/porteur_helpers.php';
$porteurData = require __DIR__ . '/components/porteur_data.php';
$fundings = $porteurData['fundings'];
$projects = $porteurData['projects'];

$totalFinanced = 0;
$distinctInstitutions = [];
$confirmedTickets = 0;
$requestedTotal = 0;
$fundedTotal = 0;

foreach ($fundings as $funding) {
  $totalFinanced += $funding['amount'];
  $distinctInstitutions[$funding['institution']] = true;
  if ($funding['status_label'] === 'Decaisse' || $funding['status_label'] === 'Confirme' || $funding['status_label'] === 'Finance') {
    $confirmedTickets++;
  }
}

foreach ($projects as $project) {
  $requestedTotal += $project['requested_amount'];
  $fundedTotal += $project['funded_amount'];
}

$fundingProgress = $requestedTotal > 0 ? (int) round(($fundedTotal / $requestedTotal) * 100) : 0;

$page_title = 'Financement';
$page_subtitle = 'Pilotez les tours de table et les montants deja mobilises.';
$page_active_nav = 'funding';
$page_document_title = 'Financement - ALOGOTO';

include __DIR__ . '/components/porteur_page_start.php';
?>
        <div class="row dashboard-gap-16 mt-2 mb-4 porteur-page-kpi-row">
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-cash-stack';
            $stat_title = 'Montant total finance';
            $stat_value = porteur_format_fcfa($totalFinanced);
            $stat_trend = '+2 tours recents';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-arrow-up-right';
            $stat_icon_bg = 'rgba(252, 160, 40, 0.18)';
            $stat_icon_color = 'var(--primary-color-3)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-buildings';
            $stat_title = 'Institutions actives';
            $stat_value = (string) count($distinctInstitutions);
            $stat_trend = 'Partenaires engages';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-building-check';
            $stat_icon_bg = 'rgba(0, 72, 220, 0.12)';
            $stat_icon_color = 'var(--primary-color-2)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-pie-chart';
            $stat_title = 'Progression globale';
            $stat_value = $fundingProgress . '%';
            $stat_trend = 'Des objectifs couverts';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-activity';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.15)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-patch-check';
            $stat_title = 'Tickets confirmes';
            $stat_value = (string) $confirmedTickets;
            $stat_trend = 'Deals qualifies';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-check2';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.12)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20 mb-4">
          <div class="col-12">
            <?php
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle">
  <thead>
    <tr>
      <th>Projet</th>
      <th>Institution</th>
      <th>Montant finance</th>
      <th>Date financement</th>
      <th>Statut</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($fundings as $funding): ?>
    <tr>
      <td><?php echo porteur_escape($funding['project']); ?></td>
      <td><?php echo porteur_escape($funding['institution']); ?></td>
      <td><?php echo porteur_escape(porteur_format_fcfa($funding['amount'])); ?></td>
      <td><?php echo porteur_escape($funding['date']); ?></td>
      <td><span class="status-badge <?php echo porteur_escape($funding['status_class']); ?>"><?php echo porteur_escape($funding['status_label']); ?></span></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Liste des financements recus';
            $table_id = 'porteurFundingList';
            $table_search_placeholder = 'Rechercher un financement';
            $table_pagination = '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20">
<?php foreach ($projects as $project): ?>
          <div class="col-12 col-md-6 col-xl-4">
            <section class="dashboard-card portfolio-track-card hover-lift h-100">
              <p class="portfolio-track-card__meta mb-1"><?php echo porteur_escape($project['name']); ?></p>
              <h4 class="mb-1"><?php echo porteur_escape(porteur_format_fcfa($project['funded_amount'])); ?></h4>
              <p class="portfolio-track-card__meta mb-3">sur <?php echo porteur_escape(porteur_format_fcfa($project['requested_amount'])); ?></p>
              <div class="dashboard-progress">
                <div class="dashboard-progress__bar bg-info-token" style="width: <?php echo porteur_escape((string) $project['progress']); ?>%;"></div>
              </div>
              <p class="portfolio-track-card__meta mt-2"><?php echo porteur_escape((string) $project['progress']); ?>% couvert</p>
            </section>
          </div>
<?php endforeach; ?>
        </div>
<?php include __DIR__ . '/components/porteur_page_end.php'; ?>
