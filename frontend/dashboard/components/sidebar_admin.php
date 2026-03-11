<aside class="dashboard-sidebar sidebar" id="dashboardSidebar" aria-label="Navigation administrateur">
  <div class="dashboard-sidebar__top">
    <button type="button" class="dashboard-sidebar-close d-lg-none" aria-label="Fermer le menu">
      <i class="bi bi-x-lg"></i>
    </button>

    <div class="logo-section">
      <a class="dashboard-brand" href="<?php echo htmlspecialchars(dashboard_url('dashboard_admin.php'), ENT_QUOTES, 'UTF-8'); ?>">
        <span class="dashboard-brand__icon"><i class="bi bi-grid-1x2-fill"></i></span>
        <span>ALOGOTO</span>
      </a>
      <span class="dashboard-role-badge">Admin</span>
    </div>

    <p class="section-title">Navigation</p>

    <nav class="dashboard-nav nav flex-column">
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="dashboard" href="<?php echo htmlspecialchars(dashboard_url('dashboard_admin.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-grid"></i><span>Dashboard</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="users" href="<?php echo htmlspecialchars(dashboard_url('utilisateurs.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-people"></i><span>Utilisateurs</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="projects" href="<?php echo htmlspecialchars(dashboard_url('projets_admin.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-folder2"></i><span>Projets</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="finance" href="<?php echo htmlspecialchars(dashboard_url('finance.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-graph-up-arrow"></i><span>Finance</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="disputes" href="<?php echo htmlspecialchars(dashboard_url('litiges.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-exclamation-triangle"></i><span>Litiges</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="audit" href="<?php echo htmlspecialchars(dashboard_url('audit_logs.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-list-ul"></i><span>Audit logs</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="security" href="<?php echo htmlspecialchars(dashboard_url('securite_admin.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-lock"></i><span>S&eacute;curit&eacute;</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="messages" href="<?php echo htmlspecialchars(dashboard_url('messages_admin.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-chat-dots"></i><span>Messages</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="notifications" href="<?php echo htmlspecialchars(dashboard_url('notifications_admin.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-bell"></i><span>Notifications</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="profile" href="<?php echo htmlspecialchars(dashboard_url('profil_admin.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-person"></i><span>Profil</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="settings" href="<?php echo htmlspecialchars(dashboard_url('parametres.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-gear"></i><span>Param&egrave;tres</span></a>
    </nav>
  </div>

  <div class="dashboard-sidebar__bottom">
    <button type="button" class="dashboard-logout-btn btn-dashboard outline sm js-dashboard-logout">
      <i class="bi bi-box-arrow-right"></i>
      <span>D&eacute;connexion</span>
    </button>
  </div>
</aside>
