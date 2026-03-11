<?php
/* Composant header partage pour les 3 dashboards. */
$headerTitle = isset($page_title) ? $page_title : "Tableau de bord";
$headerSubtitle = isset($page_subtitle) ? $page_subtitle : "";
?>
<header class="dashboard-topbar top-header">
  <div class="d-flex align-items-start align-items-md-center gap-3 page-header mb-0">
    <button type="button" class="dashboard-menu-toggle sidebar-toggle" aria-label="Ouvrir le menu">
      <i class="bi bi-list"></i>
    </button>

    <div>
      <h5 class="dashboard-topbar-title"><?php echo htmlspecialchars($headerTitle, ENT_QUOTES, 'UTF-8'); ?></h5>
      <?php if ($headerSubtitle !== ""): ?>
        <p class="dashboard-topbar-subtitle subtitle d-none d-md-block"><?php echo htmlspecialchars($headerSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
      <?php endif; ?>
    </div>
  </div>

  <div class="dashboard-topbar-actions header-actions d-none d-md-flex">
    <div class="dashboard-search-shell">
      <form id="headerSearchForm" class="dashboard-search-form search-box js-dashboard-search" data-ajax="true" data-search-scope="page" action="#" method="get">
        <i class="bi bi-search"></i>
        <input type="search" name="search" placeholder="Rechercher..." aria-label="Rechercher" autocomplete="off" />
      </form>
      <div class="dashboard-search-suggestions" hidden></div>
    </div>

    <div class="dashboard-dropdown-wrap">
      <button type="button" class="dashboard-icon-btn header-icon-btn" data-dropdown-target="notificationDropdown" aria-label="Notifications">
        <i class="bi bi-bell"></i>
        <span class="dashboard-dot notification-dot"></span>
      </button>
      <div class="dashboard-dropdown-panel dropdown-dashboard" id="notificationDropdown">
        <p class="dashboard-dropdown-title">Notifications</p>

        <div class="dashboard-notification-item">
          <span class="dashboard-notification-unread"></span>
          <div class="dashboard-notification-content">
            <strong>Nouveau projet soumis</strong>
            <span>PRJ-018 est en attente de validation</span>
            <span>Il y a 12 min</span>
          </div>
        </div>

        <div class="dashboard-notification-item">
          <span class="dashboard-notification-unread"></span>
          <div class="dashboard-notification-content">
            <strong>Paiement re&ccedil;u</strong>
            <span>Remboursement mensuel confirm&eacute;</span>
            <span>Il y a 2 h</span>
          </div>
        </div>

        <div class="dashboard-notification-item">
          <span class="dashboard-notification-unread"></span>
          <div class="dashboard-notification-content">
            <strong>Document valid&eacute;</strong>
            <span>Le business plan a &eacute;t&eacute; approuv&eacute;</span>
            <span>Aujourd&rsquo;hui</span>
          </div>
        </div>
      </div>
    </div>

    <div class="dashboard-dropdown-wrap">
      <button type="button" class="dashboard-user-btn header-icon-btn" data-dropdown-target="userDropdown" aria-label="Menu utilisateur">
        <span class="dashboard-avatar avatar">KA</span>
        <span class="dashboard-user-name">Mon compte</span>
        <i class="bi bi-chevron-down"></i>
      </button>
      <div class="dashboard-dropdown-panel dashboard-user-menu dropdown-dashboard" id="userDropdown">
        <a href="#" class="dropdown-item"><i class="bi bi-person"></i><span>Mon profil</span></a>
        <a href="#" class="dropdown-item"><i class="bi bi-gear"></i><span>Param&egrave;tres</span></a>
        <hr class="dropdown-divider" />
        <a href="#" class="dropdown-item text-danger js-dashboard-logout"><i class="bi bi-box-arrow-right"></i><span>D&eacute;connexion</span></a>
      </div>
    </div>
  </div>
</header>
