<aside class="dashboard-sidebar sidebar" id="dashboardSidebar" aria-label="Navigation porteur de projet">
  <div class="dashboard-sidebar__top">
    <button type="button" class="dashboard-sidebar-close d-lg-none" aria-label="Fermer le menu">
      <i class="bi bi-x-lg"></i>
    </button>

    <div class="logo-section">
      <a class="dashboard-brand" href="<?php echo htmlspecialchars(dashboard_url('dashboard_porteur.php'), ENT_QUOTES, 'UTF-8'); ?>">
        <span class="dashboard-brand__icon"><i class="bi bi-grid-1x2-fill"></i></span>
        <span>ALOGOTO</span>
      </a>
      <span class="dashboard-role-badge">Porteur de projet</span>
    </div>

    <p class="section-title">Navigation</p>

    <nav class="dashboard-nav nav flex-column">
      <a class="dashboard-nav__link nav-link sidebar-link <?php echo ($page_active_nav ?? '') === 'dashboard' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(dashboard_url('dashboard_porteur.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-grid"></i><span>Dashboard</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link <?php echo ($page_active_nav ?? '') === 'my-projects' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(dashboard_url('mes_projets.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-folder2"></i><span>Mes projets</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link <?php echo ($page_active_nav ?? '') === 'create-project' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(dashboard_url('creer_projet.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-plus-circle"></i><span>Cr&eacute;er un projet</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link <?php echo ($page_active_nav ?? '') === 'documents' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(dashboard_url('documents.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-file-earmark-text"></i><span>Documents</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link <?php echo ($page_active_nav ?? '') === 'funding' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(dashboard_url('financement.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-graph-up-arrow"></i><span>Financement</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link <?php echo ($page_active_nav ?? '') === 'repayments' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(dashboard_url('remboursements.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-credit-card-2-front"></i><span>Remboursements</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link <?php echo ($page_active_nav ?? '') === 'repayment-schedule' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(dashboard_url('echeancier_remboursement.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-calendar3"></i><span>&Eacute;ch&eacute;ancier</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link <?php echo ($page_active_nav ?? '') === 'messages' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(dashboard_url('messages.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-chat-dots"></i><span>Messages</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link <?php echo ($page_active_nav ?? '') === 'notifications' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(dashboard_url('notifications.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-bell"></i><span>Notifications</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link <?php echo ($page_active_nav ?? '') === 'profile' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(dashboard_url('profil.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-person"></i><span>Profil</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link <?php echo ($page_active_nav ?? '') === 'security' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars(dashboard_url('securite.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-shield-lock"></i><span>S&eacute;curit&eacute;</span></a>
    </nav>
  </div>

  <div class="dashboard-sidebar__bottom">
    <button type="button" class="dashboard-logout-btn btn-dashboard outline sm js-dashboard-logout">
      <i class="bi bi-box-arrow-right"></i>
      <span>D&eacute;connexion</span>
    </button>
  </div>
</aside>
