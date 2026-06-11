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
            $stat_compact_header = true;
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
            $stat_compact_header = true;
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-graph-up';
            $stat_title = 'Total finance';
            $stat_value = porteur_format_fcfa($fundedTotal);
            $stat_trend = 'Montants debloques';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-check2-circle';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.15)';
            $stat_icon_color = 'var(--primary-color-1)';
            $stat_compact_header = true;
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-people';
            $stat_title = 'Institutions participantes';
            $stat_value = (string) count($distinctInstitutions);
            $stat_trend = 'Partenaires actifs';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-people-check';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.12)';
            $stat_icon_color = 'var(--primary-color-1)';
            $stat_compact_header = true;
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20 mb-4">
          <div class="col-12">
            <?php
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle" data-items-per-page="10">
  <thead>
    <tr>
      <th>Projet</th>
      <th>Institution</th>
      <th>Montant finance</th>
      <th>Date financement</th>
      <th>Statut financement</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($fundings as $funding): ?>
<?php
  $statusFinancement = 'en analyse';
  if ($funding['status_label'] === 'Decaisse') {
    $statusFinancement = 'decaissé';
  } elseif ($funding['status_label'] === 'Confirme' || $funding['status_label'] === 'Finance') {
    $statusFinancement = 'accepté';
  }
?>
    <tr>
      <td><?php echo porteur_escape($funding['project']); ?></td>
      <td><?php echo porteur_escape($funding['institution']); ?></td>
      <td><?php echo porteur_escape(porteur_format_fcfa($funding['amount'])); ?></td>
      <td><?php echo porteur_escape($funding['date']); ?></td>
      <td><span class="status-badge <?php echo $statusFinancement === 'decaissé' ? 'status-approved approved' : ($statusFinancement === 'accepté' ? 'status-funded funded' : 'status-submitted submitted'); ?>"><?php echo porteur_escape($statusFinancement); ?></span></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Liste des financements recus';
            $table_id = 'porteurFundingList';
            $table_search_placeholder = 'Rechercher un financement';
            $table_show_filter = false;
            $table_auto_paginate = true;
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20 mt-4">
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

<script>
$(document).ready(function() {
  const apiBaseUrl = '<?php echo backend_url('api/v1/'); ?>';
  const token = localStorage.getItem('alogoto_api_token');
  if (!token) return;

  const headers = {
    'Authorization': 'Bearer ' + token,
    'Accept': 'application/json'
  };

  $.ajax({
    url: apiBaseUrl + 'dashboard/porteur/financing-stats',
    headers: headers,
    success: function(data) {
      if (data.en_attente_plan > 0) {
        const alertHtml = `
          <div class="alert alert-warning d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-bell"></i>
            <span>Vous avez <strong>${data.en_attente_plan} proposition(s)</strong> en attente de votre plan de remboursement.</span>
            <a href="/frontend/dashboard/porteur/financement" class="btn btn-sm btn-outline-warning ms-auto">Voir</a>
          </div>`;
        $('.porteur-page-kpi-row').before(alertHtml);
      }
    }
  });

  $.ajax({
    url: apiBaseUrl + 'porteur/financements/propositions',
    headers: headers,
    success: function(data) {
      if (data.propositions && data.propositions.length > 0) {
        var html = '';
        data.propositions.forEach(function(p) {
          html += `
            <div class="col-12">
              <section class="dashboard-card hover-lift mb-3 p-3">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="mb-1">${p.project?.titre || ''}</h6>
                    <p class="mb-1 text-muted small">
                      <strong>${p.institution?.nom || ''}</strong> propose 
                      ${(p.montant_propose || 0).toLocaleString('fr-FR')} FCFA
                      &agrave; ${p.taux_interet || 0}% sur ${p.duree || 0} mois
                    </p>
                    ${p.commentaires ? '<p class="mb-0 text-muted small">' + p.commentaires + '</p>' : ''}
                  </div>
                  <a href="/frontend/dashboard/porteur/financement" class="btn-dashboard primary sm">
                    <i class="bi bi-check2"></i> D&eacute;finir mon plan
                  </a>
                </div>
              </section>
            </div>`;
        });
        $('.dashboard-gap-20.mb-4').before('<div class="row dashboard-gap-16 mb-4"><div class="col-12"><h5 class="mb-3">Propositions en attente</h5></div>' + html + '</div>');
      }
    }
  });
});
</script>
