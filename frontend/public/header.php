<?php require_once __DIR__ . '/../includes/path_helpers.php'; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<!-- Start Meta -->
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="Business Consulting HTML5 Template">
	<meta name="keywords" content="Creative, Digital, multipage, landing, freelancer template">
	<meta name="author" content="ThemeOri">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Title of Site -->
	<title>Alogoto-soutenance-2026</title>
	<!-- Favicons -->
	<link rel="icon" type="image/png" href="<?php echo htmlspecialchars(frontend_public_asset('img/favicon-1.png'), ENT_QUOTES, 'UTF-8'); ?>">
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="<?php echo htmlspecialchars(frontend_public_asset('css/bootstrap.min.css'), ENT_QUOTES, 'UTF-8'); ?>">
	<!-- Font Awesome CSS -->
	<link rel="stylesheet" href="<?php echo htmlspecialchars(frontend_public_asset('css/fontawesome.css'), ENT_QUOTES, 'UTF-8'); ?>">
	<!-- Flat Icon CSS -->
	<link rel="stylesheet" href="<?php echo htmlspecialchars(frontend_public_asset('webfonts/flat-icon/flaticon_bantec.css'), ENT_QUOTES, 'UTF-8'); ?>">
	<!-- Animate CSS -->
	<link rel="stylesheet" href="<?php echo htmlspecialchars(frontend_public_asset('css/animate.css'), ENT_QUOTES, 'UTF-8'); ?>">
	<!-- Swiper Bundle CSS -->
	<link rel="stylesheet" href="<?php echo htmlspecialchars(frontend_public_asset('css/swiper-bundle.min.css'), ENT_QUOTES, 'UTF-8'); ?>">
	<!-- Slick CSS -->
	<link rel="stylesheet" href="<?php echo htmlspecialchars(frontend_public_asset('css/slick.css'), ENT_QUOTES, 'UTF-8'); ?>">
	<!-- Magnific Popup CSS -->
	<link rel="stylesheet" href="<?php echo htmlspecialchars(frontend_public_asset('css/magnific-popup.css'), ENT_QUOTES, 'UTF-8'); ?>">
	<!-- Custom CSS -->
	<link rel="stylesheet" href="<?php echo htmlspecialchars(frontend_public_asset('sass/style.css?v=20260303-1'), ENT_QUOTES, 'UTF-8'); ?>">
	<?php if (isset($csrf_token)): ?>
	<meta name="csrf-token" content="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
	<?php endif; ?>
	<style>
		@media (min-width: 1800px) {
			.section-padding { padding: 140px 0; }
			.container { max-width: 1400px; }
		}
		@media (min-width: 2200px) {
			.section-padding { padding: 160px 0; }
			.container { max-width: 1600px; }
		}
	</style>
</head>

<body>
	<!-- Header Area Start -->
	<div class="header__area three">
		<div class="header__sticky">
			<div class="container">
				<div class="header__area-menubar">
					<div class="header__area-menubar-left">
						<div class="header__area-menubar-left-logo">
							<a href="<?php echo htmlspecialchars(frontend_public_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>"><img class="dark-n" src="<?php echo htmlspecialchars(frontend_public_asset('img/logo.png'), ENT_QUOTES, 'UTF-8'); ?>?v=<?php echo time(); ?>" alt=""></a>
						</div>
					</div>
					<div class="header__area-menubar-center">
						<div class="header__area-menubar-center-menu menu-responsive">
							<ul id="mobilemenu">
							<li><a href="<?php echo htmlspecialchars(frontend_public_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>">Accueil</a></li>
							<li><a href="<?php echo htmlspecialchars(frontend_public_url('about.php'), ENT_QUOTES, 'UTF-8'); ?>">A propos</a></li>
							<li><a href="<?php echo htmlspecialchars(frontend_public_url('services.php'), ENT_QUOTES, 'UTF-8'); ?>">Services</a></li>
							<li><a href="<?php echo htmlspecialchars(frontend_public_url('company-history.php'), ENT_QUOTES, 'UTF-8'); ?>">Historique</a></li>
							<li><a href="<?php echo htmlspecialchars(frontend_public_url('contact.php'), ENT_QUOTES, 'UTF-8'); ?>">Contactez-Nous</a></li>
							<li class="menu-item-has-children">
								<a href="javascript:void(0)" style="color: var(--primary-color-3);">Connexion <i class="fas fa-chevron-down"></i></a>
								<ul class="sub-menu">
									<li><a href="<?php echo htmlspecialchars('/login/porteur', ENT_QUOTES, 'UTF-8'); ?>">Espace Porteur</a></li>
									<li><a href="<?php echo htmlspecialchars('/login/institution', ENT_QUOTES, 'UTF-8'); ?>">Espace Institution</a></li>
								</ul>
							</li>
							</ul>
						</div>
					</div>
					<div class="header__area-menubar-right">
						<div class="header__area-menubar-right-box">
						
							<div class="header__area-menubar-right-box-sidebar">
								<div class="header__area-menubar-right-box-sidebar-popup-icon"> <i class="flaticon-menu"></i> </div>
							</div>
							
							<!-- sidebar Menu Start -->
							<div class="header__area-menubar-right-sidebar-popup home-three">
								<div class="sidebar-close-btn"> <i class="fal fa-times"></i> </div>
								<div class="header__area-menubar-right-sidebar-popup-logo">
									<a href="<?php echo htmlspecialchars(frontend_public_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>"> <img src="<?php echo htmlspecialchars(frontend_public_asset('img/logo.png'), ENT_QUOTES, 'UTF-8'); ?>?v=<?php echo time(); ?>" alt=""> </a>
								</div>
								<p> ALOGOTO vous aide à atteindre la paix financière grâce à des stratégies personnalisées et des conseils experts. Notre équipe est dédiée à comprendre vos besoins uniques et à vous fournir des solutions adaptées.</p>
								<div class="header__area-menubar-right-sidebar-popup-contact">
									<h4 class="mb-30">Prenez contact avec nous</h4>
									<div class="header__area-menubar-right-sidebar-popup-contact-item">
										<div class="header__area-menubar-right-sidebar-popup-contact-item-icon"> <i class="fal fa-phone-alt icon-animation"></i> </div>
										<div class="header__area-menubar-right-sidebar-popup-contact-item-content"> <span>Téléphone</span>
											<h6>
                                                    <a href="tel:+2290168552584"
                                                        >+229 01 68 55 25 84</a
                                                    >
                                                </h6> </div>
									</div>
									<div class="header__area-menubar-right-sidebar-popup-contact-item">
										<div class="header__area-menubar-right-sidebar-popup-contact-item-icon"> <i class="fal fa-envelope"></i> </div>
										<div class="header__area-menubar-right-sidebar-popup-contact-item-content"> <span>Adresse e-mail</span>
											<h6>
                                                    <a
                                                        href="mailto:alogoto03@gmail.com"
                                                        >alogoto03@gmail.com</a
                                                    >
                                                </h6> </div>
									</div>
									<div class="header__area-menubar-right-sidebar-popup-contact-item">
										<div class="header__area-menubar-right-sidebar-popup-contact-item-icon"> <i class="fal fa-map-marker-alt"></i> </div>
										<div class="header__area-menubar-right-sidebar-popup-contact-item-content"> <span>Adresse</span>
											<h6>
                                                    <a href="https://www.google.com/maps"
                                                        >Benin, Cotonou,Porto-NOvo</a
                                                    >
                                                </h6> </div>
									</div>
								</div>
								<div class="header__area-menubar-right-sidebar-popup-social">
									<ul>
										<li><a href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
										<li><a href="https://x.com" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
										<li><a href="https://behance.net" target="_blank"><i class="fab fa-behance"></i></a></li>
										<li><a href="https://linkedin.com" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
									</ul>
								</div>
							</div>
							<div class="sidebar-overlay"></div>
							<div class="menu-overlay"></div>
							<!-- sidebar Menu Start -->
						</div>
						<div class="responsive-menu_popup-icon  sidebar-menu-show-hide"> <i class="fas fa-bars"></i> </div>
						<!-- ================== Responsive Menu ================== -->
						<div class="responsive__menu">
							<div class="responsive__menu-wrapper">
								<div class="responsive__menu_wrap text-start mb-5">
									<div class="logo-wrapper">
										<a href="<?php echo htmlspecialchars(frontend_public_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>"> <img src="<?php echo htmlspecialchars(frontend_public_asset('img/logo.png'), ENT_QUOTES, 'UTF-8'); ?>?v=<?php echo time(); ?>" alt="Logo"></a>
									</div> <i class="fas fa-times close-hide-show"></i> </div>
								<ul class="responsive-sidebar-menu-list">
							<li class="responsive-sidebar-menu-list__item"> <a href="<?php echo htmlspecialchars(frontend_public_url('index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="responsive-sidebar-menu-list__link">
                                                Accueil
                                            </a> </li>
											<li class="responsive-sidebar-menu-list__item"> <a href="<?php echo htmlspecialchars(frontend_public_url('about.php'), ENT_QUOTES, 'UTF-8'); ?>" class="responsive-sidebar-menu-list__link">
												A propos
											</a> </li>
											<li class="responsive-sidebar-menu-list__item"> <a href="<?php echo htmlspecialchars(frontend_public_url('services.php'), ENT_QUOTES, 'UTF-8'); ?>" class="responsive-sidebar-menu-list__link">
												Services
											</a> </li>
											<li class="responsive-sidebar-menu-list__item"> <a href="<?php echo htmlspecialchars(frontend_public_url('faq.php'), ENT_QUOTES, 'UTF-8'); ?>" class="responsive-sidebar-menu-list__link">
												FAQ
											</a> </li>
											<li class="responsive-sidebar-menu-list__item"> <a href="<?php echo htmlspecialchars(frontend_public_url('company-history.php'), ENT_QUOTES, 'UTF-8'); ?>" class="responsive-sidebar-menu-list__link">
												Historique
											</a> </li>
									
									        <li class="responsive-sidebar-menu-list__item"> <a href="<?php echo htmlspecialchars(frontend_public_url('contact.php'), ENT_QUOTES, 'UTF-8'); ?>" class="responsive-sidebar-menu-list__link">
                                                Contactez-Nous 
                                            </a> </li>
											<li class="responsive-sidebar-menu-list__item"> <a href="<?php echo htmlspecialchars('/login/porteur', ENT_QUOTES, 'UTF-8'); ?>" class="responsive-sidebar-menu-list__link">
												Connexion Porteur
											</a> </li>
											<li class="responsive-sidebar-menu-list__item"> <a href="<?php echo htmlspecialchars('/login/institution', ENT_QUOTES, 'UTF-8'); ?>" class="responsive-sidebar-menu-list__link">
												Connexion Institution
											</a> </li>

								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Header Area End -->
</body>

