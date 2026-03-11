<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$institutionData = require __DIR__ . '/components/institution_data.php';
$projects = $institutionData['projects'];

$page_title = 'Investissements';
$page_subtitle = 'Suivez les tickets engagés, leur date d’entrée et leur rentabilité estimée.';
$page_active_nav = 'investments';
$page_document_title = 'Investissements - ALOGOTO';

include __DIR__ . '/components/institution_page_start.php';
?>
        <div class="row dashboard-gap-20 mt-2 mb-5">
          <div class="col-12">
            <?php
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle">
  <thead>
    <tr>
      <th>Projet</th>
      <th>Montant investi</th>
      <th>Date investissement</th>
      <th>ROI estimé</th>
      <th>Statut</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($projects as $project): ?>
    <tr>
      <td><?php echo dashboard_escape($project['name']); ?></td>
      <td><?php echo dashboard_escape(dashboard_format_fcfa($project['amount_invested'])); ?></td>
      <td><?php echo dashboard_escape($project['date']); ?></td>
      <td><?php echo dashboard_escape((string) $project['estimated_roi']); ?>%</td>
      <td><span class="status-badge <?php echo dashboard_escape($project['status_class']); ?>"><?php echo dashboard_escape($project['status_label']); ?></span></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Investissements actifs';
            $table_id = 'institutionInvestmentsTable';
            $table_search_placeholder = 'Rechercher un investissement';
            $table_pagination = '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>
<?php include __DIR__ . '/components/institution_page_end.php'; ?>
