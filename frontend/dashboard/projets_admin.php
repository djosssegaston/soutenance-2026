<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$adminData = require __DIR__ . '/components/admin_data.php';
$projects = $adminData['projects'];

$page_title = 'Projets';
$page_subtitle = 'Pilotez les validations, refus et contrôles sur l’ensemble des dossiers.';
$page_active_nav = 'projects';
$page_document_title = 'Projets admin - ALOGOTO';

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
      <th>ID</th>
      <th>Nom</th>
      <th>Porteur</th>
      <th>Secteur</th>
      <th>Montant</th>
      <th>Statut</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($projects as $project): ?>
    <tr>
      <td><?php echo dashboard_escape($project['id']); ?></td>
      <td><?php echo dashboard_escape($project['name']); ?></td>
      <td><?php echo dashboard_escape($project['carrier']); ?></td>
      <td><?php echo dashboard_escape($project['sector']); ?></td>
      <td><?php echo dashboard_escape(dashboard_format_fcfa($project['amount'])); ?></td>
      <td><span class="status-badge <?php echo dashboard_escape($project['status_class']); ?>"><?php echo dashboard_escape($project['status_label']); ?></span></td>
      <td>
        <div class="admin-action-group">
          <button type="button" class="dashboard-btn-primary btn-dashboard primary sm"><i class="bi bi-check2"></i><span>Valider</span></button>
          <button type="button" class="btn-dashboard outline sm text-danger"><i class="bi bi-x-circle"></i><span>Refuser</span></button>
          <button type="button" class="btn-dashboard outline sm"><i class="bi bi-eye"></i><span>Voir détail</span></button>
        </div>
      </td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Pipeline projets';
            $table_id = 'adminProjects';
            $table_search_placeholder = 'Rechercher un projet';
            $table_pagination = '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>
<?php include __DIR__ . '/components/admin_page_end.php'; ?>
