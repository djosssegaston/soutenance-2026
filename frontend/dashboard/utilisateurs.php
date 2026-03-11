<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$adminData = require __DIR__ . '/components/admin_data.php';
$users = $adminData['users'];

$page_title = 'Utilisateurs';
$page_subtitle = 'Supervisez les comptes, leurs rôles et leurs statuts d’accès.';
$page_active_nav = 'users';
$page_document_title = 'Utilisateurs - ALOGOTO';

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
      <th>Email</th>
      <th>Rôle</th>
      <th>Statut</th>
      <th>Date inscription</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($users as $user): ?>
    <tr>
      <td><?php echo dashboard_escape($user['id']); ?></td>
      <td><?php echo dashboard_escape($user['name']); ?></td>
      <td><?php echo dashboard_escape($user['email']); ?></td>
      <td><?php echo dashboard_escape($user['role']); ?></td>
      <td><span class="status-badge <?php echo dashboard_escape($user['status_class']); ?>"><?php echo dashboard_escape($user['status_label']); ?></span></td>
      <td><?php echo dashboard_escape($user['joined_at']); ?></td>
      <td>
        <div class="admin-action-group">
          <button type="button" class="btn-dashboard outline sm"><i class="bi bi-eye"></i><span>Voir</span></button>
          <button type="button" class="btn-dashboard outline sm"><i class="bi bi-slash-circle"></i><span>Suspendre</span></button>
          <button type="button" class="btn-dashboard outline sm text-danger"><i class="bi bi-trash"></i><span>Supprimer</span></button>
        </div>
      </td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Utilisateurs plateforme';
            $table_id = 'adminUsers';
            $table_search_placeholder = 'Rechercher un utilisateur';
            $table_pagination = '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>
<?php include __DIR__ . '/components/admin_page_end.php'; ?>
