<?php
require_once __DIR__ . '/components/porteur_helpers.php';
$porteurData = require __DIR__ . '/components/porteur_data.php';
$projects = $porteurData['projects'];
$repayments = $porteurData['repayments'];

$requestedTotal = 0;
$fundedTotal = 0;
$repaidTotal = 0;
foreach ($projects as $project) {
  $requestedTotal += $project['requested_amount'];
  $fundedTotal += $project['funded_amount'];
}
foreach ($repayments as $repayment) {
  if ($repayment['status_label'] === 'Paye') {
    $repaidTotal += $repayment['amount'];
  }
}
$progressRate = $requestedTotal > 0 ? (int) round(($fundedTotal / $requestedTotal) * 100) : 0;

$page_title = 'Statistiques';
$page_subtitle = 'Analysez vos indicateurs de financement, remboursement et performance projet.';
$page_active_nav = 'stats';
$page_document_title = 'Statistiques - ALOGOTO';

include __DIR__ . '/components/porteur_page_start.php';
?>
        <div class="dashboard-page-stack">
        <div class="row dashboard-gap-16 porteur-page-kpi-row">
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-folder2';
            $stat_title = 'Nombre de projets';
            $stat_value = (string) count($projects);
            $stat_trend = 'Portefeuille actif';
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
            $stat_title = 'Montant finance';
            $stat_value = porteur_format_fcfa($fundedTotal);
            $stat_trend = 'Total consolide';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-graph-up-arrow';
            $stat_icon_bg = 'rgba(252, 160, 40, 0.18)';
            $stat_icon_color = 'var(--primary-color-3)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-credit-card-2-front';
            $stat_title = 'Montant rembourse';
            $stat_value = porteur_format_fcfa($repaidTotal);
            $stat_trend = 'Encaissements';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-wallet2';
            $stat_icon_bg = 'rgba(0, 72, 220, 0.12)';
            $stat_icon_color = 'var(--primary-color-2)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-speedometer2';
            $stat_title = 'Taux progression';
            $stat_value = $progressRate . '%';
            $stat_trend = 'Couverture de financement';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-activity';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.12)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20">
          <div class="col-12 col-xl-6">
            <section class="dashboard-card chart-card">
              <div class="dashboard-card__head">
                <h6>Financement mensuel</h6>
              </div>
              <div class="chart-holder chart-holder-sm">
                <canvas id="statsFundingChart"></canvas>
              </div>
            </section>
          </div>
          <div class="col-12 col-xl-6">
            <section class="dashboard-card chart-card">
              <div class="dashboard-card__head">
                <h6>Remboursements mensuels</h6>
              </div>
              <div class="chart-holder chart-holder-sm">
                <canvas id="statsRepaymentsChart"></canvas>
              </div>
            </section>
          </div>
        </div>

        <div class="row dashboard-gap-20">
          <div class="col-12">
            <section class="dashboard-card chart-card">
              <div class="dashboard-card__head">
                <h6>Evolution du projet</h6>
              </div>
              <div class="chart-holder">
                <canvas id="statsProjectEvolutionChart"></canvas>
              </div>
            </section>
          </div>
        </div>
        </div>
<?php include __DIR__ . '/components/porteur_page_end.php'; ?>
