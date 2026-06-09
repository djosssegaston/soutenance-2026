<?php
require_once __DIR__ . '/components/porteur_helpers.php';
$porteurData = require __DIR__ . '/components/porteur_data.php';
$repayments = $porteurData['repayments'];

$paidCount = 0;
$pendingCount = 0;
$lateCount = 0;
$repaidTotal = 0;
$latestPaid = null;
$nextPending = null;

foreach ($repayments as $repayment) {
  if ($repayment['status_label'] === 'Paye') {
    $paidCount++;
    $repaidTotal += $repayment['amount'];
    $latestPaid = $repayment;
  } elseif ($repayment['status_label'] === 'En attente') {
    $pendingCount++;
    if ($nextPending === null) {
      $nextPending = $repayment;
    }
  } elseif ($repayment['status_label'] === 'En retard') {
    $lateCount++;
  }
}

$latestPaidAmount = $latestPaid ? porteur_format_fcfa($latestPaid['amount'], false) : '0 FCFA';
$latestPaidDate = $latestPaid ? $latestPaid['date'] : 'N/A';
$nextPendingAmount = $nextPending ? porteur_format_fcfa($nextPending['amount'], false) : '0 FCFA';
$nextPendingDate = $nextPending ? $nextPending['date'] : 'N/A';

$page_title = 'Remboursements';
$page_subtitle = 'Visualisez l historique des paiements et les echeances sous surveillance.';
$page_active_nav = 'repayments';
$page_document_title = 'Remboursements - ALOGOTO';

include __DIR__ . '/components/porteur_page_start.php';
?>
        <div class="row dashboard-gap-16 mt-2 mb-4 porteur-page-kpi-row">
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-wallet2';
            $stat_title = 'Montant total rembourse';
            $stat_value = porteur_format_fcfa($repaidTotal);
            $stat_trend = 'Flux encaisses';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-arrow-up-right';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.15)';
            $stat_icon_color = 'var(--primary-color-1)';
            $stat_compact_header = true;
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-cash-coin';
            $stat_title = 'Remboursement recent';
            $stat_value = $latestPaidAmount;
            $stat_trend = $latestPaidDate;
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-check-circle';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.12)';
            $stat_icon_color = 'var(--primary-color-1)';
            $stat_compact_header = true;
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-calendar-check';
            $stat_title = 'Prochain remboursement';
            $stat_value = $nextPendingAmount;
            $stat_trend = $nextPendingDate;
            $stat_trend_class = 'is-neutral';
            $stat_trend_icon = 'bi-clock-history';
            $stat_icon_bg = 'rgba(252, 160, 40, 0.18)';
            $stat_icon_color = 'var(--primary-color-3)';
            $stat_compact_header = true;
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-exclamation-octagon';
            $stat_title = 'Retard de remboursement';
            $stat_value = (string) $lateCount;
            $stat_trend = 'Action requise';
            $stat_trend_class = 'is-down';
            $stat_trend_icon = 'bi-exclamation-triangle';
            $stat_icon_bg = 'rgba(243, 81, 32, 0.12)';
            $stat_icon_color = 'var(--primary-color-4)';
            $stat_compact_header = true;
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20 mt-3 mb-4">
          <div class="col-12">
            <section class="dashboard-card chart-card">
              <div class="dashboard-card__head">
                <h6>Evolution des remboursements</h6>
              </div>
              <div class="chart-holder">
                <canvas id="repaymentsEvolutionChart"></canvas>
              </div>
            </section>
          </div>
        </div>

        <div class="row dashboard-gap-20">
          <div class="col-12">
            <?php
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle" data-items-per-page="10">
  <thead>
    <tr>
      <th>Projet</th>
      <th>Montant</th>
      <th>Date paiement</th>
      <th>Statut</th>
      <th>Institution</th>
      <th>Confirmation institution</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($repayments as $repayment): ?>
<?php
  $confirmationInstitution = $repayment['status_label'] === 'Paye'
    ? 'Paiement enregistre par institution'
    : 'En attente de confirmation';
?>
    <tr>
      <td><?php echo porteur_escape($repayment['project']); ?></td>
      <td><?php echo porteur_escape(porteur_format_fcfa($repayment['amount'], false)); ?></td>
      <td><?php echo porteur_escape($repayment['date']); ?></td>
      <td><span class="status-badge <?php echo porteur_escape($repayment['status_class']); ?>"><?php echo porteur_escape($repayment['status_label']); ?></span></td>
      <td><?php echo porteur_escape($repayment['institution']); ?></td>
      <td><?php echo porteur_escape($confirmationInstitution); ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Historique remboursements';
            $table_id = 'porteurRepaymentsList';
            $table_search_placeholder = 'Rechercher un paiement';
            $table_show_filter = false;
            $table_auto_paginate = true;
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>
<?php include __DIR__ . '/components/porteur_page_end.php'; ?>
