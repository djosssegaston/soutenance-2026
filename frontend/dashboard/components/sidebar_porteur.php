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
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="dashboard" href="<?php echo htmlspecialchars(dashboard_url('dashboard_porteur.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-grid"></i><span>Dashboard</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="my-projects" href="<?php echo htmlspecialchars(dashboard_url('mes_projets.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-folder2"></i><span>Mes projets</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="create-project" href="<?php echo htmlspecialchars(dashboard_url('creer_projet.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-plus-circle"></i><span>Cr&eacute;er un projet</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="documents" href="<?php echo htmlspecialchars(dashboard_url('documents.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-file-earmark-text"></i><span>Documents</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="funding" href="<?php echo htmlspecialchars(dashboard_url('financement.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-graph-up-arrow"></i><span>Financement</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="repayments" href="<?php echo htmlspecialchars(dashboard_url('remboursements.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-credit-card-2-front"></i><span>Remboursements</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="messages" href="<?php echo htmlspecialchars(dashboard_url('messages.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-chat-dots"></i><span>Messages</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="notifications" href="<?php echo htmlspecialchars(dashboard_url('notifications.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-bell"></i><span>Notifications</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="stats" href="<?php echo htmlspecialchars(dashboard_url('statistiques.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-bar-chart"></i><span>Statistiques</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="profile" href="<?php echo htmlspecialchars(dashboard_url('profil.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-person"></i><span>Profil</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="security" href="<?php echo htmlspecialchars(dashboard_url('securite.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-shield-lock"></i><span>S&eacute;curit&eacute;</span></a>
    </nav>
  </div>

  <div class="dashboard-sidebar__bottom">
    <button type="button" class="dashboard-logout-btn btn-dashboard outline sm js-dashboard-logout">
      <i class="bi bi-box-arrow-right"></i>
      <span>D&eacute;connexion</span>
    </button>
  </div>
</aside>
