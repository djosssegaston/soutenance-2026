<?php
require_once __DIR__ . '/components/porteur_helpers.php';
$porteurData = require __DIR__ . '/components/porteur_data.php';
$repayments = $porteurData['repayments'];

$paidCount = 0;
$pendingCount = 0;
$lateCount = 0;
$repaidTotal = 0;

foreach ($repayments as $repayment) {
  if ($repayment['status_label'] === 'Paye') {
    $paidCount++;
    $repaidTotal += $repayment['amount'];
  } elseif ($repayment['status_label'] === 'En attente') {
    $pendingCount++;
  } elseif ($repayment['status_label'] === 'En retard') {
    $lateCount++;
  }
}

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
            $stat_title = 'Montant rembourse';
            $stat_value = porteur_format_fcfa($repaidTotal);
            $stat_trend = 'Flux encaisses';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-arrow-up-right';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.15)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-check2-all';
            $stat_title = 'Paiements payes';
            $stat_value = (string) $paidCount;
            $stat_trend = 'A jour';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-check-circle';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.12)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-hourglass';
            $stat_title = 'En attente';
            $stat_value = (string) $pendingCount;
            $stat_trend = 'Validation bancaire';
            $stat_trend_class = 'is-neutral';
            $stat_trend_icon = 'bi-clock-history';
            $stat_icon_bg = 'rgba(252, 160, 40, 0.18)';
            $stat_icon_color = 'var(--primary-color-3)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-exclamation-octagon';
            $stat_title = 'En retard';
            $stat_value = (string) $lateCount;
            $stat_trend = 'Action requise';
            $stat_trend_class = 'is-down';
            $stat_trend_icon = 'bi-exclamation-triangle';
            $stat_icon_bg = 'rgba(243, 81, 32, 0.12)';
            $stat_icon_color = 'var(--primary-color-4)';
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
<table class="table dashboard-table data-table align-middle">
  <thead>
    <tr>
      <th>Projet</th>
      <th>Montant</th>
      <th>Date paiement</th>
      <th>Statut</th>
      <th>Institution</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($repayments as $repayment): ?>
    <tr>
      <td><?php echo porteur_escape($repayment['project']); ?></td>
      <td><?php echo porteur_escape(porteur_format_fcfa($repayment['amount'], false)); ?></td>
      <td><?php echo porteur_escape($repayment['date']); ?></td>
      <td><span class="status-badge <?php echo porteur_escape($repayment['status_class']); ?>"><?php echo porteur_escape($repayment['status_label']); ?></span></td>
      <td><?php echo porteur_escape($repayment['institution']); ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Historique remboursements';
            $table_id = 'porteurRepaymentsList';
            $table_search_placeholder = 'Rechercher un paiement';
            $table_pagination = '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>
<?php include __DIR__ . '/components/porteur_page_end.php'; ?>
