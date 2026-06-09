<?php
/* Composant header partage pour les 3 dashboards. */
$headerTitle = isset($page_title) ? $page_title : "Tableau de bord";
$headerSubtitle = isset($page_subtitle) ? $page_subtitle : "";
$headerUserName = isset($header_user_name) ? (string) $header_user_name : "Mon compte";
$headerUserInitials = isset($header_user_initials) ? (string) $header_user_initials : "--";
$headerNotifications = isset($header_notifications) ? (array) $header_notifications : [];
?>
<header class="dashboard-topbar top-header">
  <div class="d-flex align-items-start align-items-md-center gap-3 page-header mb-0">
    <button type="button" class="dashboard-menu-toggle sidebar-toggle d-none d-md-inline-flex" aria-label="Ouvrir le menu">
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

        <?php if (count($headerNotifications) === 0): ?>
          <div class="dashboard-notification-item">
            <div class="dashboard-notification-content">
              <strong>Aucune notification</strong>
              <span>Votre flux est &agrave; jour.</span>
              <span>Maintenant</span>
            </div>
          </div>
        <?php else: ?>
          <?php foreach ($headerNotifications as $notification): ?>
            <div class="dashboard-notification-item">
              <?php if (empty($notification['read'])): ?>
                <span class="dashboard-notification-unread"></span>
              <?php endif; ?>
              <div class="dashboard-notification-content">
                <strong><?php echo htmlspecialchars($notification['title'] ?? 'Notification', ENT_QUOTES, 'UTF-8'); ?></strong>
                <span><?php echo htmlspecialchars($notification['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                <span><?php echo htmlspecialchars($notification['time'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <div class="dashboard-dropdown-wrap">
      <button type="button" class="dashboard-user-btn header-icon-btn" data-dropdown-target="userDropdown" aria-label="Menu utilisateur">
        <span class="dashboard-avatar avatar"><?php echo htmlspecialchars($headerUserInitials, ENT_QUOTES, 'UTF-8'); ?></span>
        <span class="dashboard-user-name"><?php echo htmlspecialchars($headerUserName, ENT_QUOTES, 'UTF-8'); ?></span>
        <i class="bi bi-chevron-down"></i>
      </button>
      <div class="dashboard-dropdown-panel dashboard-user-menu dropdown-dashboard" id="userDropdown">
        <a href="/dashboard/profil" class="dropdown-item"><i class="bi bi-person"></i><span>Mon profil</span></a>
        <a href="/dashboard/parametres" class="dropdown-item"><i class="bi bi-gear"></i><span>Param&egrave;tres</span></a>
        <hr class="dropdown-divider" />
        <a href="#" class="dropdown-item text-danger js-dashboard-logout"><i class="bi bi-box-arrow-right"></i><span>D&eacute;connexion</span></a>
      </div>
    </div>
  </div>
</header>
