<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$adminData = require __DIR__ . '/components/admin_data.php';
$disputes = $adminData['disputes'];

$page_title = 'Litiges';
$page_subtitle = 'Suivez les incidents, les médiations et les clôtures de dossiers sensibles.';
$page_active_nav = 'disputes';
$page_document_title = 'Litiges - ALOGOTO';

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
      <th>Projet</th>
      <th>Utilisateur</th>
      <th>Type litige</th>
      <th>Statut</th>
      <th>Date</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($disputes as $dispute): ?>
    <tr>
      <td><?php echo dashboard_escape($dispute['project']); ?></td>
      <td><?php echo dashboard_escape($dispute['user']); ?></td>
      <td><?php echo dashboard_escape($dispute['type']); ?></td>
      <td><span class="status-badge <?php echo dashboard_escape($dispute['status_class']); ?>"><?php echo dashboard_escape($dispute['status_label']); ?></span></td>
      <td><?php echo dashboard_escape($dispute['date']); ?></td>
      <td>
        <div class="admin-action-group">
          <button type="button" class="dashboard-btn-primary btn-dashboard primary sm"><i class="bi bi-check2-square"></i><span>Résoudre</span></button>
          <button type="button" class="btn-dashboard outline sm"><i class="bi bi-lock"></i><span>Fermer</span></button>
        </div>
      </td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Registre des litiges';
            $table_id = 'adminDisputes';
            $table_search_placeholder = 'Rechercher un litige';
            $table_pagination = '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>
<?php include __DIR__ . '/components/admin_page_end.php'; ?>
