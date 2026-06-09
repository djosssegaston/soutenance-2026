<aside class="dashboard-sidebar sidebar" id="dashboardSidebar" aria-label="Navigation institution financi&egrave;re">
  <div class="dashboard-sidebar__top">
    <button type="button" class="dashboard-sidebar-close d-lg-none" aria-label="Fermer le menu">
      <i class="bi bi-x-lg"></i>
    </button>

    <div class="logo-section">
      <a class="dashboard-brand" href="<?php echo htmlspecialchars(route('dashboard.institution'), ENT_QUOTES, 'UTF-8'); ?>">
        <span class="dashboard-brand__icon"><i class="bi bi-grid-1x2-fill"></i></span>
        <span>ALOGOTO</span>
      </a>
      <span class="dashboard-role-badge">Institution</span>
    </div>

    <p class="section-title">Navigation</p>

    <nav class="dashboard-nav nav flex-column">
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="dashboard" href="<?php echo htmlspecialchars(route('dashboard.institution'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-grid"></i><span>Dashboard</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="available-projects" href="<?php echo htmlspecialchars(dashboard_url('parcourir_projets.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-search"></i><span>Projets disponibles</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="analyzed-projects" href="<?php echo htmlspecialchars(dashboard_url('analyse_risque.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-shield-exclamation"></i><span>Projets analys&eacute;s</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="interviews" href="<?php echo htmlspecialchars(dashboard_url('entretiens_planifies.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-calendar-event"></i><span>Entretiens planifi&eacute;s</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="financed-portfolio" href="<?php echo htmlspecialchars(dashboard_url('portfolio.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-briefcase"></i><span>Portefeuille financ&eacute;</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="repayments" href="<?php echo htmlspecialchars(dashboard_url('remboursements_institution.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-credit-card-2-front"></i><span>Remboursements</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="messages" href="<?php echo htmlspecialchars(dashboard_url('messages_institution.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-chat-dots"></i><span>Messagerie</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="notifications" href="<?php echo htmlspecialchars(dashboard_url('notifications_institution.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-bell"></i><span>Notifications</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="profile" href="<?php echo htmlspecialchars(dashboard_url('profil_institution.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-building"></i><span>Profil</span></a>
      <a class="dashboard-nav__link nav-link sidebar-link" data-nav="security" href="<?php echo htmlspecialchars(dashboard_url('securite_institution.php'), ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-shield-lock"></i><span>S&eacute;curit&eacute;</span></a>
    </nav>
  </div>

  <div class="dashboard-sidebar__bottom">
    <button type="button" class="dashboard-logout-btn btn-dashboard outline sm js-dashboard-logout">
      <i class="bi bi-box-arrow-right"></i>
      <span>D&eacute;connexion</span>
    </button>
  </div>
</aside>

