        </div><!-- /.page-main -->

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center flex-row-reverse">
                <div class="col-md-12 col-sm-12 text-center">
                    <div>Copyright © <span id="year"></span> <a href="javascript:void(0)">Alogoto</a>. 
                    Developpé par DJOSSE A.Gaston & GANDAHO B.Ines
                </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- FOOTER END -->

    <!-- BACK-TO-TOP -->
    <a href="javascript:void(0)" id="back-to-top"><i class="fa fa-angle-up"></i></a>   

    <!--{ JQUERY JS }-->
    <script src="../../asset/js/jquery.min.js"></script>
    <!--{ BOOTSTRAP JS }-->
    <script src="../../asset/js/plugins/bootstrap/js/popper.min.js"></script>
    <script src="../../asset/js/plugins/bootstrap/js/bootstrap.min.js"></script>
    <!--{ SPARKLINE JS }-->
    <script src="../../asset/js/jquery.sparkline.min.js"></script>
    <!--{ Sticky js }-->
    <script src="../../asset/js/sticky.js"></script>
    <!--{ CHART-CIRCLE JS }-->
    <script src="../../asset/js/circle-progress.min.js"></script>
    <!--{ PIETY CHART JS }-->
    <script src="../../asset/js/plugins/peitychart/jquery.peity.min.js"></script>
    <script src="../../asset/js/plugins/peitychart/peitychart.init.js"></script>
    <!--{ SIDEBAR JS }-->
    <script src="../../asset/js/plugins/sidebar/sidebar.js"></script>
    <!-- Perfect SCROLLBAR JS-->
    <script src="../../asset/js/plugins/p-scroll/perfect-scrollbar.js"></script>
    <script src="../../asset/js/plugins/p-scroll/pscroll.js"></script>
    <script src="../../asset/js/plugins/p-scroll/pscroll-1.js"></script>
    <!--{ INTERNAL CHARTJS CHART JS }-->
    <script src="../../asset/js/plugins/chart/Chart.bundle.js"></script>
    <script src="../../asset/js/plugins/chart/utils.js"></script>
    <!--{ Select2 js }-->
    <script src="../../asset/js/plugins/select2/select2.full.min.js"></script>
    <script src="../../asset/js/plugins/select2/select2.init.js"></script>
    <!--{  INTERNAL Data tables js }-->
    <script src="../../asset/js/plugins/datatable/jquery.dataTableaux.min.js"></script>
    <script src="../../asset/js/plugins/datatable/dataTableaux.bootstrap5.min.js"></script>
    <!--{ INTERNAL APEXCHART JS }-->
    <script src="../../asset/js/apexcharts.js"></script>
    <script src="../../asset/js/plugins/apexchart/irregular-data-series.js"></script>
    <script src="../../asset/js/plugins/flot/jquery.flot.js"></script>
    <script src="../../asset/js/plugins/flot/jquery.flot.fillbetween.js"></script>
    <script src="../../asset/js/plugins/flot/chart.flot.sampledata.js"></script>
    <script src="../../asset/js/plugins/flot/dashboard.sampledata.js"></script>
    <!--{ INTERNAL Vector js }-->
    <script src="../../asset/js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="../../asset/js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <!--{ SIDE-MENU JS }-->
    <script src="../../asset/js/plugins/sidemenu/sidemenu.js"></script>
    <!--{ INTERNAL INDEX JS }-->
    <script src="../../asset/js/index1.js"></script>
    <!--{ Thème de couleur js }-->
    <script src="../../asset/js/themeColors.js"></script>
    <!--{ CUSTOM JS }-->
    <script src="../../asset/js/custom.js"></script>
    <!--{ Sélecteur personnalisé }-->
    <script src="../../asset/js/custom-swicher.js"></script>
    <!--{ Sélecteur js }-->
    <script src="../../asset/switcher/js/switcher.js"></script>
    <!--{ SweetAlert2 + ALOGOTO Alerts }-->
    <script src="../../asset/js/plugins/sweet-alert/sweetalert.min.js"></script>
    <script>
    const MOBILE_API_HEADERS = {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    function escHtml(str) {
        return str ? String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') : '';
    }

    function resolveAvatarUrl(url) {
        if (!url) return '../../asset/images/profiles/1.jpg';
        if (url.startsWith('http') || url.startsWith('/')) return url;
        return '/storage/' + url;
    }

    async function fetchMobileHeaderData() {
        try {
            const userRes = await fetch('/api/v1/auth/me', { headers: MOBILE_API_HEADERS });
            if (userRes.ok) {
                const body = await userRes.json();
                const u = body.user || body;
                const nameEl = document.getElementById('mobile-profile-name');
                const roleEl = document.getElementById('mobile-profile-role');
                if (nameEl) nameEl.textContent = u.name;
                if (roleEl) roleEl.textContent = 'Institution';
                var hdrName = document.getElementById('header-user-name');
                if (hdrName && u.name) hdrName.textContent = u.name;
                var hdrAvatar = document.getElementById('header-user-avatar');
                if (hdrAvatar) hdrAvatar.src = resolveAvatarUrl(u.avatar_url);
            }

            const msgRes = await fetch('/api/v1/conversations', { headers: MOBILE_API_HEADERS });
            if (msgRes.ok) {
                const data = await msgRes.json();
                const conversations = data.conversations || data.data || data || [];
                const unreadCount = conversations.reduce(function(acc, conv) { return acc + (conv.unread_count || 0); }, 0);
                const badge = document.getElementById('mobile-msg-badge');
                if (badge) badge.textContent = unreadCount;
                const title = document.getElementById('mobile-msg-title');
                if (title) title.textContent = unreadCount > 0 ? 'Vous avez ' + unreadCount + ' nouveau(x) message(s)' : 'Aucun nouveau message';
                const list = document.getElementById('mobile-messages-list');
                if (list) {
                    if (!conversations.length) {
                        list.innerHTML = '<div class="dropdown-item text-center p-3 text-muted">Aucun message</div>';
                    } else {
                        list.innerHTML = conversations.slice(0, 5).map(function(conv) {
                            var otherName = conv.institution_name || conv.other_user_name || 'Utilisateur';
                            var otherAvatar = conv.institution_avatar || conv.other_user_avatar || '';
                            return '<a class="dropdown-item d-flex" href="messages.php?id=' + conv.id + '">' +
                                '<span class="avatar avatar-md me-3 align-self-center cover-image rounded-circle bg-primary-transparent">' +
                                (otherAvatar ? '<img src="' + resolveAvatarUrl(otherAvatar) + '" class="rounded-circle">' : '<span class="text-primary fw-bold">' + escHtml(otherName.substring(0, 2).toUpperCase()) + '</span>') +
                                '</span>' +
                                '<div class="wd-90p">' +
                                '<div class="d-flex">' +
                                '<h5 class="mb-1">' + escHtml(otherName) + '</h5>' +
                                '<small class="text-muted ms-auto">' + (conv.last_message_at || '') + '</small>' +
                                '</div>' +
                                '<span class="fs-12 text-muted text-truncate d-block" style="max-width: 200px;">' + escHtml(conv.last_message || 'Nouvelle conversation') + '</span>' +
                                '</div></a>';
                        }).join('');
                    }
                }
            }

            const notifRes = await fetch('/api/v1/notifications', { headers: MOBILE_API_HEADERS });
            if (notifRes.ok) {
                const notifications = await notifRes.json();
                const notifList = notifications.data || notifications || [];
                const unreadNotifs = notifList.filter(function(n) { return !n.is_read; }).length;
                const badge = document.getElementById('mobile-notif-badge');
                if (badge) badge.textContent = unreadNotifs;
                const list = document.getElementById('mobile-notifications-list');
                if (list) {
                    if (!notifList.length) {
                        list.innerHTML = '<div class="dropdown-item text-center p-3 text-muted">Aucune notification</div>';
                    } else {
                        list.innerHTML = notifList.slice(0, 5).map(function(n) {
                            return '<div class="d-flex align-items-start dropdown-item">' +
                                '<div class="flex-grow-1">' +
                                '<div class="d-flex align-items-start justify-content-between mb-1">' +
                                '<h5 class="mb-0 fs-13 fw-semibold">' + escHtml(n.title || n.type || 'Notification') + '</h5>' +
                                '<small class="text-muted">' + (n.created_at_human || '') + '</small>' +
                                '</div>' +
                                '<p class="mb-0 text-muted fs-12">' + escHtml(n.content || n.message || '') + '</p>' +
                                '</div></div>';
                        }).join('');
                    }
                }
            }
        } catch (e) {
            console.error('Error fetching mobile header data:', e);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        fetchMobileHeaderData();

        const body = document.body;
        const sidebarToggleBtn = document.querySelector('.sidebar-toggle-btn');
        const sidebarOverlay = document.querySelector('.app-sidebar__overlay');
        const mobileNavItems = document.querySelectorAll('.mobile-bottom-nav__item');
        const mobileDropdownSelectors = '.mobile-search-dropdown, .mobile-notifications-dropdown, .mobile-messages-dropdown, .mobile-profile-dropdown';

        function closeMobileDropdowns() {
            document.querySelectorAll(mobileDropdownSelectors).forEach(function(menu) {
                menu.classList.remove('show');
            });
        }

        function resetMobileActiveState() {
            mobileNavItems.forEach(function(item) {
                item.classList.remove('active');
            });
        }

        function syncMenuButtonState() {
            if (!sidebarToggleBtn) {
                return;
            }

            if (window.innerWidth > 991) {
                sidebarToggleBtn.classList.remove('active');
                return;
            }

            if (body.classList.contains('sidenav-toggled')) {
                sidebarToggleBtn.classList.add('active');
            } else {
                sidebarToggleBtn.classList.remove('active');
            }
        }

        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                document.querySelectorAll('.mobile-bottom-nav .dropdown-menu.show').forEach(function(menu) {
                    let toggler = menu.previousElementSibling;
                    if (toggler && toggler.classList.contains('dropdown-toggle')) {
                        toggler.click();
                    } else {
                        menu.classList.remove('show');
                        if (menu.parentElement) menu.parentElement.classList.remove('show');
                    }
                });

                resetMobileActiveState();
                body.classList.toggle('sidenav-toggled');
                body.classList.remove('sidenav-toggled-open');
                syncMenuButtonState();
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                window.setTimeout(syncMenuButtonState, 0);
            });
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.mobile-bottom-nav')) {
                closeMobileDropdowns();
                resetMobileActiveState();
                syncMenuButtonState();
            }
        });

        window.addEventListener('resize', function() {
            closeMobileDropdowns();
            resetMobileActiveState();
            syncMenuButtonState();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileDropdowns();
                body.classList.remove('sidenav-toggled');
                body.classList.remove('sidenav-toggled-open');
                resetMobileActiveState();
                syncMenuButtonState();
            }
        });

        syncMenuButtonState();
    });
    </script>
    
    <!-- BARRE DE NAVIGATION MOBILE/TABLETTE -->
    <nav class="mobile-bottom-nav" aria-label="Navigation mobile">
        <div class="mobile-bottom-nav__container">
            <div class="dropdown mobile-bottom-nav__slot">
                <a href="javascript:void(0)" class="mobile-bottom-nav__item" id="mobile-search-toggle" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside">
                    <div class="mobile-bottom-nav__icon">
                        <i class="fe fe-search"></i>
                    </div>
                    <span class="mobile-bottom-nav__label">Recherche</span>
                </a>
                <div class="dropdown-menu dropdown-menu-start mobile-search-dropdown">
                    <div class="input-group w-100 p-2">
                        <input type="text" class="form-control" placeholder="Rechercher.....">
                        <div class="input-group-text btn btn-primary">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="dropdown mobile-bottom-nav__slot">
                <a href="javascript:void(0)" class="mobile-bottom-nav__item" id="mobile-notif-toggle" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside">
                    <div class="mobile-bottom-nav__icon">
                        <i class="fe fe-bell"></i>
                        <span class="mobile-bottom-nav__badge" id="mobile-notif-badge">0</span>
                    </div>
                    <span class="mobile-bottom-nav__label">Alertes</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow mobile-notifications-dropdown">
                    <div class="drop-heading border-bottom">
                        <div class="d-flex">
                            <h6 class="mt-1 mb-0 fs-16 fw-semibold text-dark">Notifications</h6>
                        </div>
                    </div>
                    <div class="notifications-menu" id="mobile-notifications-list">
                        <!-- Chargement... -->
                    </div>
                    <div class="dropdown-divider m-0"></div>
                    <a href="notifications.php" class="dropdown-item text-center p-3 text-muted">Voir toutes les notifications</a>
                </div>
            </div>
            
            <div class="mobile-bottom-nav__slot">
                <a href="javascript:void(0)" class="mobile-bottom-nav__item mobile-bottom-nav__item--menu sidebar-toggle-btn" aria-label="Ouvrir le menu">
                    <div class="mobile-bottom-nav__icon">
                        <i class="fe fe-grid"></i>
                    </div>
                    <span class="mobile-bottom-nav__label">Menu</span>
                </a>
            </div>
            
            <div class="dropdown mobile-bottom-nav__slot">
                <a href="javascript:void(0)" class="mobile-bottom-nav__item" id="mobile-msg-toggle" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside">
                    <div class="mobile-bottom-nav__icon">
                        <i class="fe fe-message-square"></i>
                        <span class="mobile-bottom-nav__badge" id="mobile-msg-badge">0</span>
                    </div>
                    <span class="mobile-bottom-nav__label">Messages</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow mobile-messages-dropdown">
                    <div class="drop-heading border-bottom">
                        <div class="d-flex">
                            <h6 class="mt-1 mb-0 fs-16 fw-semibold text-dark" id="mobile-msg-title">Aucun nouveau message</h6>
                        </div>
                    </div>
                    <div class="message-menu message-menu-scroll" id="mobile-messages-list">
                        <!-- Chargement... -->
                    </div>
                    <div class="dropdown-divider m-0"></div>
                    <a href="messages.php" class="dropdown-item text-center p-3 text-muted">Voir tous les messages</a>
                </div>
            </div>
            
            <div class="dropdown mobile-bottom-nav__slot">
                <a href="javascript:void(0)" class="mobile-bottom-nav__item" id="mobile-profile-toggle" data-bs-toggle="dropdown" data-bs-display="static" data-bs-auto-close="outside">
                    <div class="mobile-bottom-nav__icon">
                        <i class="fe fe-user"></i>
                    </div>
                    <span class="mobile-bottom-nav__label">Profil</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow mobile-profile-dropdown">
                    <div class="drop-heading">
                        <div class="text-center">
                            <h5 class="text-dark mb-0 fs-14 fw-semibold" id="mobile-profile-name">Institution</h5>
                            <small class="text-muted" id="mobile-profile-role">Utilisateur</small>
                        </div>
                    </div>
                    <div class="dropdown-divider m-0"></div>
                    <a class="dropdown-item" href="profils.php">
                        <i class="dropdown-icon fe fe-user"></i> Mon Profil
                    </a>
                    <a class="dropdown-item" href="securite.php">
                        <i class="dropdown-icon fe fe-shield"></i> Sécurité
                    </a>
                    <div class="dropdown-divider m-0"></div>
                    <a class="dropdown-item" href="/logout" onclick="event.preventDefault();ALOGOTO.logout();">
                        <i class="dropdown-icon fe fe-alert-circle"></i> Déconnexion
                    </a>
                    <form id="mobile-logout-form" action="/logout" method="POST" class="d-none">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- TOAST CONTAINER TEMPS RÉEL -->
    <div id="realtime-toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

    <!-- ALOGOTO Helpers (toasts, confirm, loader) -->
    <script src="../shared/js/alogoto-helpers.js"></script>

    <?php
    $reverbAvailable = false;
    try {
        $conn = @fsockopen('127.0.0.1', 8080, $errno, $errstr, 0.1);
        if ($conn !== false) {
            $reverbAvailable = true;
            fclose($conn);
        }
    } catch (\Throwable $e) {}
    ?>
    <script>window.REVERB_AVAILABLE = <?php echo $reverbAvailable ? 'true' : 'false'; ?>;</script>

    <!-- ECHO / TEMPS RÉEL (chargé conditionnellement) -->
    <script src="../shared/js/realtime-init.js"></script>

    <!-- PROTECTION BOTTOM-NAV : empêche toute perte de style sur mobile/tablette -->
    <style>
      /* Seules les propriétés structurelles critiques – pas de sizing qui
         serait écrasé par les breakpoints de header.css */
      @media (max-width: 991px) {
        html body nav.mobile-bottom-nav {
          display: block !important;
          position: fixed !important;
          bottom: 0 !important; left: 0 !important; right: 0 !important;
          z-index: 1035 !important;
        }
        html body nav.mobile-bottom-nav .mobile-bottom-nav__container {
          display: grid !important;
          grid-template-columns: repeat(5,minmax(0,1fr)) !important;
          margin: 0 auto !important;
        }
        html body nav.mobile-bottom-nav .mobile-bottom-nav__item {
          display: flex !important;
          flex-direction: column !important;
          align-items: center !important;
          justify-content: center !important;
          text-decoration: none !important;
          color: #6c757d !important;
          border: none !important;
          font-weight: 600 !important;
        }
        html body nav.mobile-bottom-nav .mobile-bottom-nav__item--menu {
          background: #FCA028 !important;
          color: #fff !important;
        }
        html body .app-header .logo-horizontal {
          display: flex !important;
          align-items: center !important;
          justify-content: center !important;
        }
        html body .app-header .app-sidebar__toggle,
        html body .app-header .header-right-icons,
        html body .app-header .responsive-navbar,
        html body .app-header .main-header-center,
        html body .app-header .header-brand,
        html body .app-header .header-brand1 {
          display: none !important;
        }
        html body footer.footer,
        html body #back-to-top {
          display: none !important;
        }
      }
    </style>
    <script>
    (function() {
      'use strict';
      function protectMobileNav() {
        var nav = document.querySelector('.mobile-bottom-nav');
        if (!nav) return;

        if (window.innerWidth > 991) {
          var inlineProps = ['display','position','bottom','left','right','z-index'];
          inlineProps.forEach(function(p) { nav.style.removeProperty(p); });
          var c = nav.querySelector('.mobile-bottom-nav__container');
          if (c) {
            c.style.removeProperty('display');
            c.style.removeProperty('grid-template-columns');
          }
          return;
        }

        var props = [
          ['display','block'],
          ['position','fixed'],
          ['bottom','0px'],
          ['left','0px'],
          ['right','0px'],
          ['z-index','10000']
        ];
        props.forEach(function(p) { nav.style.setProperty(p[0], p[1], 'important'); });
        var c = nav.querySelector('.mobile-bottom-nav__container');
        if (c) {
          [['display','grid'],['grid-template-columns','repeat(5,minmax(0,1fr))']]
            .forEach(function(p) { c.style.setProperty(p[0], p[1], 'important'); });
        }
      }
      ['load','resize'].forEach(function(e) { window.addEventListener(e, protectMobileNav); });
      if (document.readyState==='complete') { setTimeout(protectMobileNav,100); }
      else { window.addEventListener('load',function(){ setTimeout(protectMobileNav,200); }); }
      setTimeout(protectMobileNav,500);
    })();
    </script>

<?php require_once __DIR__ . '/../shared/modal-helper.php'; ?>

</div><!-- /.page -->

</body>

</html>
