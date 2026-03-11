<?php require_once __DIR__ . '/../../includes/path_helpers.php'; ?>
<?php
$logoutUserName = isset($dashboard_user_name) ? $dashboard_user_name : 'Utilisateur';
$logoutUserId = isset($dashboard_user_id) ? $dashboard_user_id : 'N/A';
$logoutUserRole = isset($dashboard_user_role) ? $dashboard_user_role : 'Utilisateur';
?>
<div class="modal fade dashboard-logout-modal" id="dashboardLogoutModal" tabindex="-1" aria-labelledby="dashboardLogoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <div>
          <span class="dashboard-logout-modal__eyebrow">Confirmation de d&eacute;connexion</span>
          <h5 class="modal-title" id="dashboardLogoutModalLabel">Quitter votre espace</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>

      <div class="modal-body pt-3">
        <p class="mb-3">Vous &ecirc;tes sur le point de vous d&eacute;connecter.<br />Voulez-vous vraiment quitter votre espace ?</p>

        <div class="dashboard-logout-modal__user">
          <div class="dashboard-logout-modal__user-item">
            <span class="dashboard-logout-modal__label">Nom de l&rsquo;utilisateur</span>
            <strong class="dashboard-logout-modal__value"><?php echo htmlspecialchars($logoutUserName, ENT_QUOTES, 'UTF-8'); ?></strong>
          </div>
          <div class="dashboard-logout-modal__user-item">
            <span class="dashboard-logout-modal__label">Identifiant utilisateur</span>
            <strong class="dashboard-logout-modal__value"><?php echo htmlspecialchars($logoutUserId, ENT_QUOTES, 'UTF-8'); ?></strong>
          </div>
          <div class="dashboard-logout-modal__user-item">
            <span class="dashboard-logout-modal__label">R&ocirc;le</span>
            <strong class="dashboard-logout-modal__value"><?php echo htmlspecialchars($logoutUserRole, ENT_QUOTES, 'UTF-8'); ?></strong>
          </div>
        </div>
      </div>

      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn-dashboard outline dashboard-logout-cancel" data-bs-dismiss="modal">Non, rester connect&eacute;</button>
        <button type="button" class="btn-dashboard dashboard-logout-confirm" data-logout-confirm data-logout-url="<?php echo htmlspecialchars(dashboard_url('logout.php'), ENT_QUOTES, 'UTF-8'); ?>">Oui, se d&eacute;connecter</button>
      </div>
    </div>
  </div>
</div>
