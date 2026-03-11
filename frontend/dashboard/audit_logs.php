<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$adminData = require __DIR__ . '/components/admin_data.php';
$logs = $adminData['audit_logs'];

$page_title = 'Audit Logs';
$page_subtitle = 'Consultez l’historique des actions système et les traces d’administration.';
$page_active_nav = 'audit';
$page_document_title = 'Audit Logs - ALOGOTO';

include __DIR__ . '/components/admin_page_start.php';
?>
        <div class="row dashboard-gap-20 mt-2 mb-5">
          <div class="col-12">
            <?php
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle">
  <thead>
    <tr>
      <th>Utilisateur</th>
      <th>Action</th>
      <th>Date</th>
      <th>Adresse IP</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($logs as $log): ?>
    <tr>
      <td><?php echo dashboard_escape($log['user']); ?></td>
      <td><?php echo dashboard_escape($log['action']); ?></td>
      <td><?php echo dashboard_escape($log['date']); ?></td>
      <td><?php echo dashboard_escape($log['ip']); ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Historique système';
            $table_id = 'adminAuditLogs';
            $table_search_placeholder = 'Rechercher une action';
            $table_pagination = '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>
<?php include __DIR__ . '/components/admin_page_end.php'; ?>
