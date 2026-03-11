<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$institutionData = require __DIR__ . '/components/institution_data.php';
$repayments = $institutionData['repayments'];

$paid = 0;
$pending = 0;
$late = 0;
foreach ($repayments as $repayment) {
  if ($repayment['status_label'] === 'Payé') {
    $paid++;
  } elseif ($repayment['status_label'] === 'En attente') {
    $pending++;
  } else {
    $late++;
  }
}

$page_title = 'Remboursements';
$page_subtitle = 'Suivez les encaissements reçus, les échéances à confirmer et les retards.';
$page_active_nav = 'repayments';
$page_document_title = 'Remboursements institution - ALOGOTO';

include __DIR__ . '/components/institution_page_start.php';
?>
        <div class="row dashboard-gap-16 mt-2 mb-4 institution-page-kpi-row">
          <div class="col-12 col-md-6 col-xl-4">
            <?php
            $stat_icon = 'bi-check2-circle';
            $stat_title = 'Payés';
            $stat_value = (string) $paid;
            $stat_trend = 'Encaissements confirmés';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-arrow-up-right';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.15)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-4">
            <?php
            $stat_icon = 'bi-hourglass-split';
            $stat_title = 'En attente';
            $stat_value = (string) $pending;
            $stat_trend = 'À rapprocher';
            $stat_trend_class = 'is-neutral';
            $stat_trend_icon = 'bi-clock-history';
            $stat_icon_bg = 'rgba(252, 160, 40, 0.18)';
            $stat_icon_color = 'var(--primary-color-3)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-4">
            <?php
            $stat_icon = 'bi-exclamation-triangle';
            $stat_title = 'Retards';
            $stat_value = (string) $late;
            $stat_trend = 'À relancer';
            $stat_trend_class = 'is-down';
            $stat_trend_icon = 'bi-arrow-down-right';
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
      <th>Projet</th>
      <th>Porteur</th>
      <th>Montant</th>
      <th>Date paiement</th>
      <th>Statut</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($repayments as $repayment): ?>
    <tr>
      <td><?php echo dashboard_escape($repayment['project']); ?></td>
      <td><?php echo dashboard_escape($repayment['carrier']); ?></td>
      <td><?php echo dashboard_escape(dashboard_format_fcfa($repayment['amount'], false)); ?></td>
      <td><?php echo dashboard_escape($repayment['date']); ?></td>
      <td><span class="status-badge <?php echo dashboard_escape($repayment['status_class']); ?>"><?php echo dashboard_escape($repayment['status_label']); ?></span></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Remboursements reçus';
            $table_id = 'institutionRepayments';
            $table_search_placeholder = 'Rechercher un remboursement';
            $table_pagination = '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>
<?php include __DIR__ . '/components/institution_page_end.php'; ?>
