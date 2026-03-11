<aside class="dashboard-sidebar sidebar" id="dashboardSidebar" aria-label="Navigation institution financi&egrave;re">
  <div class="dashboard-sidebar__top">
    <button type="button" class="dashboard-sidebar-close d-lg-none" aria-label="Fermer le menu">
      <i class="bi bi-x-lg"></i>
    </button>

    <div class="logo-section">
      <a class="dashboard-brand" href="<?php echo htmlspecialchars(dashboard_url('dashboard_institution.php'), ENT_QUOTES, 'UTF-8'); ?>">
        <span class="dashboard-brand__icon"><i class="bi bi-grid-1x2-fill"></i></span>
        <span>ALOGOTO</span>
      </a>
      <span class="dashboard-role-badge">Institution</span>
    </div>

    <p class="section-title">Navigation</p>

    <nav class="dashboard-nav nav flex-column">
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="dashboard" href="<?php echo htmlspecialchars(dashboard_url('dashboard_institution.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-grid"></i><span>Dashboard</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="browse" href="<?php echo htmlspecialchars(dashboard_url('parcourir_projets.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-search"></i><span>Parcourir projets</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="risk" href="<?php echo htmlspecialchars(dashboard_url('analyse_risque.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-shield-exclamation"></i><span>Analyse de risque</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="validation" href="<?php echo htmlspecialchars(dashboard_url('validation.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-file-earmark-check"></i><span>Validation</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="portfolio" href="<?php echo htmlspecialchars(dashboard_url('portfolio.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-briefcase"></i><span>Portfolio</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="investments" href="<?php echo htmlspecialchars(dashboard_url('investissements.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-activity"></i><span>Investissements</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="reports" href="<?php echo htmlspecialchars(dashboard_url('rapports.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-pie-chart"></i><span>Rapports</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="repayments" href="<?php echo htmlspecialchars(dashboard_url('remboursements_institution.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-credit-card-2-front"></i><span>Remboursements</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="messages" href="<?php echo htmlspecialchars(dashboard_url('messages_institution.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-chat-dots"></i><span>Messages</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="notifications" href="<?php echo htmlspecialchars(dashboard_url('notifications_institution.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-bell"></i><span>Notifications</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="profile" href="<?php echo htmlspecialchars(dashboard_url('profil_institution.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-building"></i><span>Profil institution</span></a>
    </nav>
  </div>

  <div class="dashboard-sidebar__bottom">
    <button type="button" class="dashboard-logout-btn btn-dashboard outline sm js-dashboard-logout">
      <i class="bi bi-box-arrow-right"></i>
      <span>D&eacute;connexion</span>
    </button>
  </div>
</aside>
