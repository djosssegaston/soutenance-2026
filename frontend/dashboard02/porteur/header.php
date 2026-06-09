<?php 
require_once __DIR__ . "/../path_helpers.php"; 
// L'authentification est gérée par Laravel via le middleware auth et role
// Le contrôleur DashboardPagesController::ensureDashboardRoleAccess() s'en charge
?>

<?php require __DIR__ . '/../shared/header_meta.php'; ?>

</head>

<body class="app sidebar-mini ltr light-mode" data-user-id="<?php echo auth()->id(); ?>" data-user-name="<?php echo auth()->user()?->name ?? ''; ?>">

    <!--{ Pre-loder start }-->
    <div id="global-loader">
        <img src="../../asset/images/loader.svg" class="loader-img" alt="Chargement">
    </div>
    <!--{ Pre-loder end }-->
<?php require __DIR__ . '/../shared/switcher.php'; ?>
    <!-- PAGE -->
    <div class="page">
        <div class="page-main">
           
             <!--{ app header start }-->
             <div class="app-header header sticky">
                <div class="container-fluid main-container">
                    <div class="d-flex">
                        <a aria-label="Hide Sidebar" class="app-sidebar__toggle" data-bs-toggle="sidebar"
                            href="javascript:void(0)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="feather feather-grid status_toggle middle sidebar-toggle">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                        </a>
                        <!-- sidebar-toggle-->
        <a class="logo-horizontal " href="index.php">
            <img src="../../asset/images/brand/logo-white.png" class="header-brand-img desktop-logo"
                alt="logo">
            <img src="../../asset/images/brand/logo-dark.png" class="header-brand-img light-logo1"
                alt="logo">
        </a>
                        <!-- LOGO -->
                        <div class="d-flex order-lg-2 ms-auto header-right-icons">
                            <!-- SEARCH -->
                            <button class="navbar-toggler navresponsive-toggler d-lg-none ms-auto" type="button"
                                data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-4"
                                aria-controls="navbarSupportedContent-4" aria-expanded="false"
                                aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon fe fe-more-vertical"></span>
                            </button>
                            <div class="navbar navbar-collapse responsive-navbar p-0">
                                <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
                                    <div class="d-flex order-lg-2">
                                        <div class="dropdown d-lg-none d-flex">
                                            <a href="javascript:void(0)" class="nav-link icon"
                                                data-bs-toggle="dropdown">
                                                <i class="fe fe-search"></i>
                                            </a>
                                            <div class="dropdown-menu header-search dropdown-menu-start">
                                                <div class="input-group w-100 p-2">
                                                    <input type="text" class="form-control" placeholder="Rechercher.....">
                                                    <div class="input-group-text btn btn-primary">
                                                        <i class="fa fa-search" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                       
                                        <!-- COUNTRY -->
                                        <div class="d-flex">
                                            <a class="nav-link icon theme-layout nav-link-bg layout-setting">
                                                <span class="dark-layout"><i class="ti ti-moon-stars"></i></span>
                                                <span class="light-layout"><i class="fe fe-sun"></i></span>
                                            </a>
                                        </div>
                                        <!-- Theme-Layout -->

                                        <!-- CART -->

                                        <!-- NOTIFICATIONS -->
                                        <div class="dropdown  d-flex notifications">
                                            <a class="nav-link icon" data-bs-toggle="dropdown"><i
                                                    class="fe fe-bell"></i><span class="header-count" id="header-notifications-pulse">0</span>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <div class="drop-heading border-bottom">
                                                    <div class="d-flex">
                                                        <h6 class="mt-1 mb-0 fs-16 fw-semibold text-dark">Notifications
                                                        </h6>
                                                    </div>
                                                </div>
                                                <div class="notifications-menu" id="header-notifications-list">
                                                    <!-- Dynamique -->
                                                </div>
                                                <div class="dropdown-divider m-0"></div>
                                                <a href="notifications.php"
                                                    class="dropdown-item text-center p-3 text-muted">Voir toutes les
                                                    notifications</a>
                                            </div>
                                            
                                        </div>
                                        <!-- NOTIFICATIONS -->
                                        <div class="dropdown  d-flex message">
                                            <a class="nav-link icon text-center" data-bs-toggle="dropdown">
                                                <i class="fe fe-message-square"></i><span class="header-count" id="header-messages-pulse">0</span>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <div class="drop-heading border-bottom">
                                                    <div class="d-flex">
                                                        <h6 class="mt-1 mb-0 fs-16 fw-semibold text-dark">Messages</h6>
                                                    </div>
                                                </div>
                                                <div class="message-menu message-menu-scroll" id="header-messages-list">
                                                    <!-- Dynamique -->
                                                </div>
                                                <div class="dropdown-divider m-0"></div>
                                                <a href="messages.php"
                                                    class="dropdown-item text-center p-3 text-muted">Voir tous les
                                                    messages</a>
                                            </div>
                                        </div>

                                        <div class="demo-icon nav-link icon">
                                            <i class="fe fe-settings text_primary"></i>
                                        </div>
                                        <!-- SIDE-MENU -->
                                        <div class="dropdown d-flex">
                                            <a class="nav-link icon full-screen-link nav-link-bg">
                                                <i class="fe fe-minimize fullscreen-button"></i>
                                            </a>
                                        </div>
                                        <!-- FULL-SCREEN -->
                                        <div class="dropdown d-flex profile-1">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" class="nav-link user-dropdown">
                                                <?php $__av = $user->avatar_url ?? ''; $__avSrc = !empty($__av) ? (str_starts_with($__av,'http')||str_starts_with($__av,'/')?$__av:'/storage/'.$__av) : '../../asset/images/profiles/1.jpg'; ?>
                                                <img src="<?php echo htmlspecialchars($__avSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="profile-user" class="profile-user avatar cover-image" id="header-user-avatar">
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <div class="drop-heading">
                                                    <div class="text-center">
                                                        <h5 class="text-dark mb-0 fs-14 fw-semibold" id="header-user-name"><?php echo htmlspecialchars($user->name ?? 'Porteur', ENT_QUOTES, 'UTF-8'); ?></h5>
                                                        <small class="text-muted" id="header-user-role">Porteur de Projet</small>
                                                    </div>
                                                </div>
                                                <div class="dropdown-divider m-0"></div>
                                                <a class="dropdown-item" href="profils.php">
                                                    <i class="dropdown-icon fe fe-user"></i> Profil
                                                </a>
                                                <a class="dropdown-item" href="securite.php">
                                                    <i class="dropdown-icon fe fe-shield"></i> Sécurité
                                                </a>
                                                <a class="dropdown-item" href="/logout" onclick="event.preventDefault();ALOGOTO.logout();">
                                                    <i class="dropdown-icon fe fe-alert-circle"></i> Déconnexion
                                                </a>
                                                <form id="logout-form" action="/logout" method="POST" class="d-none">
                                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                                </form>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <!--{ app header end }-->
             <!--{ app sidebar start }-->
             <div class="sticky">
                <div class="app-sidebar__overlay" data-bs-toggle="sidebar"></div>
                <div class="app-sidebar">
             <div class="side-header">
                     <a class="header-brand1" href="index.php">
                         <img src="../../asset/images/brand/logo-white.png" class="header-brand-img desktop-logo"
                             alt="logo">
                         <img src="../../asset/images/brand/icon-dark.png" class="header-brand-img toggle-logo"
                             alt="logo">
                         <img src="../../asset/images/brand/icon-dark.png" class="header-brand-img light-logo"
                             alt="logo">
                         <img src="../../asset/images/brand/logo-dark.png" class="header-brand-img light-logo1"
                             alt="logo">
                     </a>
                     <!-- LOGO -->
                 </div>
                 <div class="main-sidemenu">
                     <div class="slide-left disabled" id="slide-left"><svg xmlns="http://www.w3.org/2000/svg"
                             fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
                             <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z" />
                         </svg></div>
                     <ul class="side-menu">
                         <li class="sub-category">
                             <!-- <h3>Main</h3> -->
                         </li>
                         <li class="slide" >
                             <a class="sidenav-menu-item" data-bs-toggle="slide" href="index.php">
                                 <i class="side-menu__icon  typcn typcn-home"></i>
                                 <span class="side-menu__label">Tableau de bord</span>
                                 <!-- <i class="angle fe fe-chevron-right"></i> -->
                             </a>
                             <!-- <ul class="slide-menu">
                                 <li class="panel sidetab-menu">
                                     <div class="panel-body tabs-menu-body p-0 border-0">
                                         <div class="tab-content">
                                             <div class="tab-pane active" id="side1">
                                                 <ul class="sidemenu-list">
                                                     <li class="side-menu-label1"><a href="javascript:void(0)">Tableau de bords</a></li>
                                                     <li><a href="dashboard_porteur.php" class="slide-item">Mes Projets</a></li>
                                                     <li><a href="dashboard-ecommerce.html" class="slide-item">Créer un Projets</a></li>
                                                 </ul>
                                             </div>
                                         </div>
                                     </div>
                                 </li>
                             </ul> -->
                         </li>

                         <li class="sub-category">
                             <h3>PROJET & DOCUMENTATION</h3>
                         </li>
            
                         <li class="slide">
                             <a class="sidenav-menu-item" data-bs-toggle="slide" href="mes_projets.php">
                                 <i class="side-menu__icon  ion-plus-circled"></i><span
                                         class="side-menu__label">Mes Projets</span>
                             </a>
                         </li>
                         <li class="slide">
                             <a class="sidenav-menu-item" href="creer_projet.php"><i
                                         class="side-menu__icon  ion-plus-circled"></i><span
                                         class="side-menu__label">Créer un projet</span>
                             </a>
                         </li>
                          <li class="slide">
                             <a class="sidenav-menu-item" data-bs-toggle="slide" href="document.php"><i
                                         class="side-menu__icon  ion-filing"></i><span
                                         class="side-menu__label">Documents</span>
                             </a>
                         </li> 

                         <li class="sub-category">
                             <h3>RENDEZ-VOUS & MESSAGES</h3>
                         </li>
            
                          <li class="slide">
                             <a class="sidenav-menu-item" href="rendez_vous.php"><i
                                         class="side-menu__icon   fa fa-calendar"></i><span
                                         class="side-menu__label">Rendez-vous</span>
                             </a>
                         </li>
            
                         <li class="slide">
                             <a class="sidenav-menu-item" href="messages.php"><i
                                         class="side-menu__icon   ion-chatbubbles"></i><span
                                         class="side-menu__label">Messages</span>
                             </a>
                         </li>
            
                          

                         <li class="sub-category">
                             <h3>FINANCEMENT & ÉCHÉANCES</h3>
                         </li>
            
                         <li class="slide">
                             <a class="sidenav-menu-item" href="financement.php"><i
                                         class="side-menu__icon fa fa-line-chart"></i><span
                                         class="side-menu__label">Financement</span>
                             </a>
                         </li>
                         <li class="slide">
                             <a class="sidenav-menu-item" href="remboursement.php"><i
                                         class="side-menu__icon fa fa-area-chart"></i><span
                                         class="side-menu__label">Remboursement</span>
                             </a>
                         </li>
                         <li class="slide">
                             <a class="sidenav-menu-item" href="echances.php"><i
                                         class="side-menu__icon ti ti-calculator"></i><span
                                         class="side-menu__label">Échéances</span>
                             </a>
                         </li>
                         <li class="sub-category">
                             <h3>NOTIFICATIONS & PARAMÈTRES</h3>
                         </li>
                         <li class="slide">
                            <a class="sidenav-menu-item" href="notifications.php"><i
                                        class="side-menu__icon  fe fe-bell"></i><span
                                        class="side-menu__label">Notifications</span>
                            </a>
                        </li>
                         <li class="slide">
                             <a class="sidenav-menu-item" href="securite.php"><i
                                         class="side-menu__icon  fe fe-lock"></i><span
                                         class="side-menu__label">Sécurité</span>
                             </a>
                         </li>
                         <li class="slide">
                             <a class="sidenav-menu-item" href="profils.php"><i
                                         class="side-menu__icon   fe fe-user"></i><span
                                         class="side-menu__label">Profil</span>
                             </a>
                         </li>
                     </ul>
                    <!-- <ul class="side-menu">
                        <li class="sub-category">
                            <h3>EXTRA</h3>
                        </li>
                        <li class="slide">
                            <a class="sidenav-menu-item" data-bs-toggle="slide" href="javascript:void(0)">
                                <i class="side-menu__icon fe fe-sliders"></i>
                                <span class="side-menu__label">Level items</span><i
                                    class="angle fe fe-chevron-right"></i>
                            </a>
                            <ul class="slide-menu">
                                <li class="panel sidetab-menu">
                                    <div class="panel-body tabs-menu-body p-0 border-0">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="side1">
                                                <ul class="sidemenu-list">
                                                    <li class="side-menu-label1"><a href="javascript:void(0)">Level
                                                            items</a></li>
                                                    <li><a href="javascript:void(0)" class="slide-item">Level-1</a>
                                                    </li>
                                                    <li class="sub-slide">
                                                        <a class="sub-sidenav-menu-item" data-bs-toggle="sub-slide"
                                                                href="javascript:void(0)"><span
                                                                    class="sub-side-menu__label">Level-2</span><i
                                                                    class="sub-angle fe fe-chevron-right"></i></a>
                                                        <ul class="sub-slide-menu">
                                                            <li><a class="sub-slide-item"
                                                                    href="javascript:void(0)">Level-2.1</a> 
                                                            </li>
                                                            <li><a class="sub-slide-item"
                                                                    href="javascript:void(0)">Level-2.2</a>
                                                            </li>
                                                            <li class="sub-slide2">
                                                                <a class="sub-sidenav-menu-item"
                                                                        href="javascript:void(0)"
                                                                        data-bs-toggle="sub-slide2"><span
                                                                            class="sub-side-menu__label">Level-2.3</span><i
                                                                            class="sub-angle2 fe fe-chevron-right"></i></a>
                                                                <ul class="sub-slide-menu2">
                                                                    <li><a href="javascript:void(0)"
                                                                            class="sub-slide-item2">Level-2.3.1</a> 
                                                                    </li>
                                                                    <li><a href="javascript:void(0)"
                                                                            class="sub-slide-item2">Level-2.3.2</a>
                                                                    </li>
                                                                    <li><a href="javascript:void(0)"
                                                                            class="sub-slide-item2">Level-2.3.3</a>
                                                                    </li>
                                                                </ul>
                                                            </li>
                                                            <li><a class="sub-slide-item"
                                                                    href="javascript:void(0)">Level-2.4</a></li>
                                                            <li><a class="sub-slide-item"
                                                                    href="javascript:void(0)">Level-2.5</a> 
                                                            </li>
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul> -->
                 </div>
                 <div class="sidebar-logout">
                    <form id="dashboard02-logout-form" method="POST"
                            action="<?php echo htmlspecialchars($logout_url ?? '/logout', ENT_QUOTES, 'UTF-8'); ?>"
                            class="d-none">
                        <?php if (function_exists('csrf_field')): ?>
                            <?php echo csrf_field(); ?>
                        <?php endif; ?>
                    </form>
                    <a class="sidebar-logout__link"
                            href="<?php echo htmlspecialchars($logout_url ?? '/logout', ENT_QUOTES, 'UTF-8'); ?>"
                            onclick="event.preventDefault();ALOGOTO.logout();">
                        <i class="side-menu__icon fa fa-sign-out"></i>
                        <span class="side-menu__label">Déconnexion</span>
                    </a>
                </div>

                 <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191"
                         width="24" height="24" viewBox="0 0 24 24">
                         <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z" />
                     </svg></div>
             </div>
             
         </div>
     </div>
     <!--{ app sidebar end }-->
<script src="js/header-dynamic.js"></script> 
