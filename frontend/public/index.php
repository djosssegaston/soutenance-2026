<?php
include 'header.php';
?>

<!-- Preloader 5s -->
<div id="preloader">
    <div class="preloader-inner">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 140" class="preloader-svg">
            <rect fill="#FF7F2D" stroke="#FF7F2D" stroke-width="6" width="22" height="22" rx="3" x="38" y="60">
                <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.4"/>
            </rect>
            <rect fill="#FF7F2D" stroke="#FF7F2D" stroke-width="6" width="22" height="22" rx="3" x="89" y="60">
                <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="-.2"/>
            </rect>
            <rect fill="#FF7F2D" stroke="#FF7F2D" stroke-width="6" width="22" height="22" rx="3" x="140" y="60">
                <animate attributeName="opacity" calcMode="spline" dur="2" values="1;0;1;" keySplines=".5 0 .5 1;.5 0 .5 1" repeatCount="indefinite" begin="0"/>
            </rect>
        </svg>
    </div>
</div>
<style>
#preloader {
    position: fixed;
    inset: 0;
    z-index: 999999;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.5s ease, visibility 0.5s ease;
}
#preloader.hidden {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}
.preloader-inner {
    text-align: center;
}
.preloader-svg {
    display: block;
    width: 160px;
    height: auto;
    animation: preloaderPulse 2s ease-in-out infinite;
}
@keyframes preloaderPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.04); }
}
</style>
<script>
(function() {
    var preloader = document.getElementById('preloader');
    if (preloader) {
        setTimeout(function() {
            preloader.classList.add('hidden');
        }, 3000);
    }
})();
</script>

<style>
    /* ── Hero Section – Width / Height / Layout ── */
    .banner__three-single-slide {
        padding: 120px 0 !important;
        padding-bottom: 140px !important;
        background-size: cover !important;
        background-position: center center !important;
        background-repeat: no-repeat !important;
        position: relative;
        overflow: hidden;
    }
    .banner__three-single-slide .container {
        max-width: 1100px;
    }
    .banner__three-content {
        max-width: 700px;
    }
    .banner__three-content .subtitle-three {
        display: inline-block;
        opacity: 0;
    }
    .banner__three-content h2 {
        font-size: 64px !important;
        line-height: 1.15;
        font-weight: 700;
        opacity: 0;
    }
    .banner__three-content p {
        width: 100% !important;
        max-width: 600px;
        font-size: 16px;
        line-height: 1.7;
        opacity: 0;
    }
    .banner__three-content .btn-three {
        opacity: 0;
    }
    .banner__three-shape img {
        position: absolute;
        pointer-events: none;
        opacity: 0;
        filter: drop-shadow(0 0 18px rgba(252,160,40,0.35));
        will-change: transform, opacity;
    }
    .banner__three .banner__three-shape .shape-1 {
        bottom: 0;
        left: 0;
        max-width: 14%;
    }
    .banner__three .banner__three-shape .shape-2 {
        bottom: 0;
        right: 0;
        max-width: 12%;
    }

    /* ── Custom ZoomIn Keyframes (for yellow shapes) ── */
    @keyframes zoomIn {
        from {
            opacity: 0;
            transform: scale(0.3);
        }
        60% {
            opacity: 0.85;
            transform: scale(1.08);
        }
        to {
            opacity: 0.7;
            transform: scale(1);
        }
    }
    .zoomIn {
        animation-name: zoomIn;
    }

    /* ── Slider Arrows – right extremity, opposite of text ── */
    .banner__three .slider-arrow {
        position: absolute;
        right: 30px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .banner__three .slider-arrow i {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: rgba(255,255,255,0.15);
        color: #fff;
        font-size: 18px;
        cursor: pointer;
        transition: background 0.4s ease, transform 0.4s ease, box-shadow 0.4s ease;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255,255,255,0.25);
        position: static !important;
        margin: 0 !important;
        left: auto !important;
        right: auto !important;
        top: auto !important;
        transform: none !important;
    }
    .banner__three .slider-arrow i:hover {
        background: var(--primary-color-3, #FCA028);
        border-color: var(--primary-color-3, #FCA028);
        transform: scale(1.12) !important;
        box-shadow: 0 0 20px rgba(252,160,40,0.4);
    }

    /* ── Smooth text crossfade ── */
    .banner__three [data-animation] {
        transition: opacity 0.5s ease;
    }

    /* ── Brand Message ── */
    .brand-message-wrapper {
        text-align: center;
        padding: 40px 20px;
    }
    .brand-message {
        font-size: 20px;
        line-height: 1.8;
        color: var(--color-1, #333);
        max-width: 700px;
        margin: 0 auto;
        opacity: 0;
        font-style: italic;
        letter-spacing: 0.3px;
    }
    @media (max-width: 767px) {
        .brand-message {
            font-size: 16px;
            padding: 0 10px;
        }
    }

    /* ── Responsive Breakpoints ── */

    /* ≥ 1800 (ultra-large / projection) */
    @media (min-width: 1800px) {
        .banner__three-single-slide {
            padding: 160px 0 !important;
            padding-bottom: 180px !important;
        }
        .banner__three-content h2 {
            font-size: 72px !important;
        }
        .banner__three-content {
            max-width: 800px;
        }
        .banner__three-content p {
            max-width: 680px;
            font-size: 18px;
        }
        .banner__three-single-slide .container {
            max-width: 1400px;
        }
    }

    @media (min-width: 2200px) {
        .banner__three-single-slide {
            padding: 200px 0 !important;
            padding-bottom: 220px !important;
        }
        .banner__three-content h2 {
            font-size: 84px !important;
        }
        .banner__three-content {
            max-width: 900px;
        }
        .banner__three-single-slide .container {
            max-width: 1600px;
        }
    }

    /* ≤ 1399 */
    @media (max-width: 1399px) {
        .banner__three-content h2 {
            font-size: 56px !important;
        }
    }

    /* ≤ 1199 */
    @media (max-width: 1199px) {
        .banner__three-content h2 {
            font-size: 48px !important;
        }
        .banner__three-content {
            max-width: 600px;
        }
        .banner__three-single-slide {
            padding: 100px 0 !important;
            padding-bottom: 130px !important;
        }
    }

    /* ≤ 991 */
    @media (max-width: 991px) {
        .banner__three-single-slide {
            padding: 90px 0 !important;
            padding-bottom: 120px !important;
        }
        .banner__three-content h2 {
            font-size: 40px !important;
        }
        .banner__three-content {
            max-width: 100%;
        }
        .banner__three-content p {
            max-width: 100%;
        }
        .banner__three .slider-arrow {
            right: 20px;
            gap: 12px;
        }
        .banner__three .slider-arrow i {
            width: 40px;
            height: 40px;
            font-size: 15px;
        }
    }

    /* ≤ 767 */
    @media (max-width: 767px) {
        .banner__three-single-slide {
            padding: 70px 0 !important;
            padding-bottom: 110px !important;
        }
        .banner__three-content h2 {
            font-size: 32px !important;
        }
        .banner__three-content p {
            font-size: 15px;
        }
        .banner__three .slider-arrow {
            right: 16px;
            gap: 10px;
        }
        .banner__three .slider-arrow i {
            width: 36px;
            height: 36px;
            font-size: 13px;
        }
        .banner__three-shape .shape-1 {
            max-width: 20%;
        }
        .banner__three-shape .shape-2 {
            max-width: 18%;
        }
    }

    /* ≤ 575 */
    @media (max-width: 575px) {
        .banner__three-single-slide {
            padding: 50px 0 !important;
            padding-bottom: 100px !important;
        }
        .banner__three-content h2 {
            font-size: 26px !important;
        }
        .banner__three-content p {
            font-size: 14px;
        }
        .banner__three .slider-arrow {
            right: 12px;
            gap: 8px;
        }
        .banner__three .slider-arrow i {
            width: 32px;
            height: 32px;
            font-size: 12px;
        }
        .banner__three-shape .shape-1 {
            max-width: 26%;
        }
        .banner__three-shape .shape-2 {
            display: none;
        }
    }

    /* ≤ 375 */
    @media (max-width: 375px) {
        .banner__three-single-slide {
            padding: 40px 0 !important;
            padding-bottom: 90px !important;
        }
        .banner__three-content h2 {
            font-size: 22px !important;
        }
        .banner__three-content p {
            font-size: 13px;
        }
        .banner__three .slider-arrow {
            right: 10px;
            gap: 6px;
        }
        .banner__three .slider-arrow i {
            width: 28px;
            height: 28px;
            font-size: 10px;
        }
    }
</style>
<body>
    <!-- Banner Three Area Start -->
	<div class="banner__three">
		<div class="swiper banner__three-slider">
			<div class="swiper-wrapper">
				<div class="banner__three-single-slide swiper-slide" style="background-image: url(assets/img/banner/image-1.png);">
					<div class="banner__three-shape"> <img class="shape-1" data-animation="zoomIn" data-delay="1.5s" data-duration="1.4s" src="assets/img/shapes/banner-shape-1.png" alt=""> <img class="shape-2" data-animation="zoomIn" data-delay="1.8s" data-duration="1.6s" src="assets/img/shapes/banner-shape-2.png" alt=""> </div>
					<div class="container">
						<div class="row">
							<div class="col-xl-7 col-lg-8 col-md-10 col-sm-11">
								<div class="banner__three-content"> <span class="subtitle-three" data-animation="fadeInUp" data-delay=".3s">Solutions de Financement</span>
									<h2 data-animation="fadeInUp" data-delay=".6s">Transformez vos projets en réalité <br class="d-none d-sm-inline"> en un clic.</h2>
									<p data-animation="fadeInUp" data-delay=".8s">Avec ALOGOTO, obtenez facilement le financement dont vous avez besoin pour concrétiser vos projets entrepreneuriaux.</p> <a href="/login" class="btn-three" data-animation="fadeInUp" data-delay="1s">Je suis un porteur de projet
                                            <i class="fas fa-plus"></i>
                                        </a> </div>
								</div>
							</div>
					</div>
				</div>
		
				<div class="banner__three-single-slide swiper-slide" style="background-image: url(assets/img/banner/image-2.png);">
					<div class="banner__three-shape"> <img class="shape-1" data-animation="zoomIn" data-delay="1.5s" data-duration="1.4s" src="assets/img/shapes/banner-shape-1.png" alt=""> <img class="shape-2" data-animation="zoomIn" data-delay="1.8s" data-duration="1.6s" src="assets/img/shapes/banner-shape-2.png" alt=""> </div>
					<div class="container">
						<div class="row">
							<div class="col-xl-7 col-lg-8 col-md-10 col-sm-11">
								<div class="banner__three-content"> <span class="subtitle-three" data-animation="fadeInUp" data-delay=".3s">Opportunités d'Investissement</span>
									<h2 data-animation="fadeInUp" data-delay=".6s">Investisez, finançez, frutifiez votre reseau.</h2>
									<p data-animation="fadeInUp" data-delay=".8s">ALOGOTO connecte les investisseurs avec des projets à fort potentiel, offrant des opportunités de croissance et de rendement.</p> <a href="/login" class="btn-three" data-animation="fadeInUp" data-delay="1s">Je suis un investisseur
                                            <i class="fas fa-plus"></i>
                                        </a> </div>
							</div>
						</div>
					</div>
				</div>
		
			</div>
			<div class="slider-arrow"> <i class="swiper-button-prev fal fa-long-arrow-left"></i> <i class="swiper-button-next fal fa-long-arrow-right"></i> </div>
		</div>
	</div>
	<!-- Banner Three Area End -->
	<!-- Services Five Start -->
	<div class="service__five">
		<div class="container">
			<div class="row justify-content-center gy-4">
				<div class="col-xl-4 col-lg-6">
					<div class="service__five-card">
						<div class="icon"> <i class="flaticon-cyber-security"></i> </div>
						<div class="content">
							<h5>100% Securisé</h5>
							<p>Tous vos projets sont sécurisés et protégés par nos experts.</p>
						</div>
					</div>
				</div>
				<div class="col-xl-4 col-lg-6">
					<div class="service__five-card">
						<div class="icon"> <i class="flaticon-consultant"></i> </div>
						<div class="content">
							<h5>Accompagnement personnalisé</h5>
							<p>Chaque projet est accompagné par nos experts pour garantir son succès.</p>
						</div>
					</div>
				</div>
				<div class="col-xl-4 col-lg-6">
					<div class="service__five-card">
						<div class="icon"> <i class="flaticon-monitoring-software"></i> </div>
						<div class="content">
							<h5>Suivi en temps reel</h5>
							<p>Suivez l'évolution de vos projets en temps réel grâce à notre système de suivi avancé.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Services Five End -->
	<!-- About Three Area Start -->
	<div class="about__three section-padding">
		<div class="container">
			<div class="row align-items-center flex-wrap-reverse gy-4">
				<div class="col-xl-6 col-lg-7 col-md-8">
					<div class="about__three-image"> <img src="assets/img/about/image4.png" alt="" class="animate-y-axis">
						<div class="about__three-image-shape"><img src="assets/img/shapes/about-three-shape.png" alt=""></div>
					</div>
				</div>
				<div class="col-xl-6 col-lg-12">
					<div class="about__three-content"> <span class="subtitle-three">A propos de Alogoto</span>
						<h2>Construire un avenir financier pour tous</h2>
						<p>ALOGOTO vous aide à atteindre la paix financière grâce à des stratégies personnalisées et des conseils experts. Notre équipe est dédiée à comprendre vos besoins uniques et à vous fournir des solutions adaptées.</p>
						<div class="about__three-content-service">
							<div class="about__three-content-service-single"> <i class="fas fa-check-circle"></i>
								<h6>Votre succès est notre mission</h6> </div>
							<div class="about__three-content-service-single"> <i class="fas fa-check-circle"></i>
								<h6>Faciliter l'acces au financement.</h6> </div>
							<div class="about__three-content-service-single"> <i class="fas fa-check-circle"></i>
								<h6>Accompagner les porteurs de projets.</h6> </div>
							<div class="about__three-content-service-single"> <i class="fas fa-check-circle"></i>
								<h6>Transformant les entreprises en plus brillantes</h6> </div>
						</div> <a href="about.php" class="btn-three">
                                Apprendre encore plus
                                <i class="fas fa-plus"></i>
                            </a> </div>
				</div>
			</div>
		</div>
	</div>
	<!-- About Three Area End -->
	<!-- Services Three Area Start -->
	<div class="services__three section-padding">
		<div class="container">
			<div class="row mb-50 gy-2">
				<div class="col-xl-5 col-lg-7"> <span class="subtitle-three">Mission</span>
					<h2>Notre approche</h2> </div>
				<div class="col-xl-5 offset-xl-2 col-lg-5 d-flex justify-content-end">
					<p>Notre mission est de faciliter l'acces au financement pour les porteurs de projets en Afrique de l'Ouest.</p>
				</div>
			</div>
			<div class="row gy-4 justify-content-center">
				<div class="col-xl-4 col-lg-6 col-md-12">
					<div class="single-service">
						<div class="single-service__image"> <img src="assets/img/service/image1.png" alt=""> </div>
						<div class="single-service__content">
							<div class="single-service__icon"> <i class="flaticon-good-feedback"></i> </div>
							<h5>Engagement</h5>
							<p>Nous nous engageons à accompagner les porteurs de projets dans leur parcours de développement et de financement.</p> <a href="#" class="btn-three">
                                 
                    
                                </a> </div>
					</div>
				</div>
				<div class="col-xl-4 col-lg-6 col-md-12">
					<div class="single-service">
						<div class="single-service__image"> <img src="assets/img/service/image2.png" alt=""> </div>
						<div class="single-service__content">
							<div class="single-service__icon"> <i class="flaticon-analytics"></i> </div>
							<h5>Precision</h5>
							<p>Nous adaptons nos solutions aux objectifs financiers et au niveau de risque de chaque porteur de projet, pour garantir un portefeuille adapté à ses besoins.</p> <a href="#" class="btn-three">
                                    
                                   
                                </a> </div>
					</div>
				</div>
				<div class="col-xl-4 col-lg-6 col-md-12">
					<div class="single-service">
						<div class="single-service__image"> <img src="assets/img/service/image3.png" alt=""> </div>
						<div class="single-service__content">
							<div class="single-service__icon"> <i class="flaticon-growth"></i> </div>
							<h5>Impact</h5>
							<p>Nous créons un impact positif sur les porteurs de projets en Afrique de l'Ouest, en leur fournissant les ressources financières nécessaires pour concrétiser leurs idées.</p> <a href="#" class="btn-three">
                                
                                </a> </div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Services Three Area End -->
	<!-- Work Process Area Start -->
	<div class="work-process__one section-padding">
		<div class="container">
			<div class="row justify-content-center text-center">
				<div class="col-xl-6 col-lg-6">
					<div class="work-process__one-title"> <span class="subtitle-three m-auto">Processus</span>
						<h2>Comment ça marche</h2> </div>
				</div>
			</div>
			<div class="work-process-wrapper">
				<div class="single-item">
					<div class="single-item__icon"> <span class="single-item__number">01</span> <i class="flaticon-newspaper"></i> </div>
					<h5>Soumettre</h5>
					<p>Créez votre compte et soumettez votre projet avec tous les details necessaires : description, montant, duree.</p>
					<div class="next-arrow"> <img src="assets/img/shapes/work-process-arrow.png" alt=""> </div>
				</div>
				<div class="single-item">
					<div class="single-item__icon"> <span class="single-item__number">02</span> <i class="flaticon-web-research"></i> </div>
					<h5>Analyser</h5>
					<p>Notre équipe et nos partenaires analysent votre projet selon des critères rigoureux de viabilité et de risque.</p>
					<div class="next-arrow"> <img src="assets/img/shapes/work-process-arrow.png" alt=""> </div>
				</div>
				<div class="single-item">
					<div class="single-item__icon"> <span class="single-item__number">03</span> <i class="flaticon-global-network"></i> </div>
					<h5>Financer</h5>
					<p>Une fois approuvé, votre projet est mis en relation avec les institutions financières adaptées à vos besoins.</p>
					<div class="next-arrow"> <img src="assets/img/shapes/work-process-arrow.png" alt=""> </div>
				</div>
				<div class="single-item">
					<div class="single-item__icon"> <span class="single-item__number">04</span> <i class="flaticon-check-1"></i> </div>
					<h5>Rembourser</h5>
					<p>Les remboursements sont effectués selon les termes convenus avec les institutions financières.</p>
				</div>
			</div>
		</div>
	</div>
	<!-- Work Process Area End -->
     <!-- Why Choose Us Three Area Start -->
	<div class="why-choose-us__three section-padding">
		<div class="container">
			<div class="row align-items-center flex-wrap-reverse gy-4">
				<div class="col-xl-6 col-lg-6 col-md-10">
					<div class="why-choose-us__three-content"> <span class="subtitle-three">Passez à l'action</span>
						<h2>Prêt à démarrer votre projet ?</h2>
						<p>Rejoignez notre communauté d'entrepreneurs et obtenez le soutien dont vous avez besoin pour réussir.</p>
						<div class="why-choose-us__three-content-service">
							<div class="why-choose-us__three-content-service-single">
								<div class="service-top"> <i class="flaticon-consultant"></i>
									<h6>Porteur de projet</h6> </div>
								<p>Connectez-vous et soummettre votre projet.</p>
							</div>
							<div class="why-choose-us__three-content-service-single">
								<div class="service-top"> <i class="flaticon-growth"></i>
									<h6>Insvestisseur</h6> </div>
								<p>Connectez-vous et investissez dans les projets qui vous passionnent.</p>
							</div>
						</div> <a href="/login" class="btn-three">
                                Rejoignez-nous
                                <i class="fas fa-plus"></i>
                            </a> </div>
				</div>
				<div class="col-xl-6 col-lg-6 col-md-12">
					<div class="why-choose-us__three-image"> <img src="assets/img/why/why-three-1.jpg" alt="">
						<a href="https://www.youtube.com/watch?v=SZEflIVnhH8" class="video-btn video-popup video-pulse"> <i class="fas fa-play"></i> </a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Why Choose Us Three Area End -->
	
	
	<!-- Project Three Area Start -->
	<!-- <div class="project__three">
		<div class="project__three-card-wrapper">
			<div class="project__three-single-card">
				<div class="project__three-image" style="background-image: url(assets/img/project/project-2.jpg);"></div>
				<a href="portfolio-details.html" class="project__three-single-card-inner"> <span>Collaboration</span>
					<h6>Partnership Program</h6> </a>
			</div>
			<div class="project__three-single-card active">
				<div class="project__three-image" style="background-image: url(assets/img/project/project-1.jpg);"></div>
				<a href="portfolio-details.html" class="project__three-single-card-inner"> <span>Media</span>
					<h6>Video Production</h6> </a>
			</div>
			<div class="project__three-single-card">
				<div class="project__three-image" style="background-image: url(assets/img/project/project-3.jpg);"></div>
				<a href="portfolio-details.html" class="project__three-single-card-inner"> <span>Reach</span>
					<h6>Audience Targeting</h6> </a>
			</div>
			<div class="project__three-single-card">
				<div class="project__three-image" style="background-image: url(assets/img/project/project-4.jpg);"></div>
				<a href="portfolio-details.html" class="project__three-single-card-inner"> <span>Loyalty</span>
					<h6>Customer Retention</h6> </a>
			</div>
		</div>
	</div> -->
	<!-- Project Three Area End -->
      <!-- Brand Area Start -->
	<div class="brand-area">
		<div class="container">
            <div class="row justify-content-center text-center mb-60 gy-4 mt-4">
				<div class="col-xl-6 col-lg-6">
					<div class="work-process__one-title"> <span class="subtitle-three m-auto">Nos partenaires</span>
						<h2>Institutions de microfinance partenaires.</h2> </div>
				</div>
			</div>
			<div class="brand-message-wrapper">
				<p class="brand-message" data-animation="fadeInUp" data-delay=".3s" data-duration="1.2s">
					Nos services seront bientôt disponibles ici. Vous pouvez en faire partie si cela vous plaît bien......
				</p>
			</div>
		</div>
	</div>
	<!-- Brand Area End -->
     <!-- Contact One Area End -->
	<div class="contact__one">
		<div class="contact__one-wrapper">
			<div class="contact__one-image"> <img src="assets/img/contact/contact.png" alt=""> </div>
			<div class="contact__one-content"> <span class="subtitle-three">On vous écoute</span>
				<h2>Que voulez-vous nous dire ?</h2>
				<form action="#" class="contact__one-form">
					<input type="text" placeholder="Nom">
					<input type="text" placeholder="Email">
					<input type="text" placeholder="Telephone">
					<input type="text" placeholder="Objet">
					<textarea placeholder="Message"></textarea>
					<button class="btn-three" type="submit">Soumettre</button>
				</form>
			</div>
		</div>
	</div>
	<!-- Contact One Area End -->
     <!-- Testimonial Two Area Start -->
	<div class="testimonial__three section-padding">
		<div class="container">
			<div class="row align-items-center mb-60 gy-5">
				<div class="col-xl-6 col-lg-6 col-md-8">
					<div class="testimonial__three-title"> <span class="subtitle-three">Historique des clients</span>
						<h2>Temoignages</h2> </div>
				</div>
				<div class="col-xl-6 col-lg-6 col-md-4">
					<div class="slider-arrow justify-content-md-end"> <i class="swiper-button-prev fas fa-chevron-left"></i> <i class="swiper-button-next fas fa-chevron-right"></i> </div>
				</div>
			</div>
			<div class="row">
				<div class="col-xl-12">
					<div class="swiper testimonial__three-slider-active">
						<div class="swiper-wrapper">
							<div class="testimonial__three-single-slider swiper-slide">
								<div class="testimonial__three-single-slider-user">
									<div class="testimonial__three-single-slider-user-image"> <img src="assets/img/testimonial/avatar-1.jpg" alt=""> </div>
									<div class="testimonial__three-single-slider-user-name">
										<h4>Gerome FACHOLA</h4> <span>Entrepreneur Agricole</span> </div>
								</div>
								<p>"Grâce à ALOGOTO, j'ai pu obtenir le financement nécessaire pour moderniser mon exploitation agricole. Le processus était simple et rapide."</p>
								<div class="testimonial__three-single-slider-user-rating"> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star not-rated"></i> </div>
								<div class="slider-shape"> <i class="fas fa-quote-right"></i> </div>
							</div>
							<div class="testimonial__three-single-slider swiper-slide">
								<div class="testimonial__three-single-slider-user">
									<div class="testimonial__three-single-slider-user-image"> <img src="assets/img/pdgntech1.jpeg" alt=""> </div>
									<div class="testimonial__three-single-slider-user-name">
										<h4>Jean Martin</h4> <span> PDG TechStart</span> </div>
								</div>
								<p>"En tant que startup dans le domaine de la tech, trouver un financement adapté était un défi. ALOGOTO est devenu solution efficace."</p>
								<div class="testimonial__three-single-slider-user-rating"> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star not-rated"></i> </div>
								<div class="slider-shape"> <i class="fas fa-quote-right"></i> </div>
							</div>
							<div class="testimonial__three-single-slider swiper-slide">
								<div class="testimonial__three-single-slider-user">
									<div class="testimonial__three-single-slider-user-image"> <img src="assets/img/ino.jpeg" alt=""> </div>
									<div class="testimonial__three-single-slider-user-name">
										<h4>Fatima Diallo</h4> <span>Artisane textile</span> </div>
								</div>
								<p>"Le financement reçu à travers ALOGOTO m'a permis de développer mon entreprise artisanale. Les conseils personnalisés ont été précieux."</p>
								<div class="testimonial__three-single-slider-user-rating"> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star not-rated"></i> </div>
								<div class="slider-shape"> <i class="fas fa-quote-right"></i> </div>
							</div>
							<div class="testimonial__three-single-slider swiper-slide">
								<div class="testimonial__three-single-slider-user">
									<div class="testimonial__three-single-slider-user-image"> <img src="assets/img/ade.jpeg" alt=""> </div>
									<div class="testimonial__three-single-slider-user-name">
										<h4>Sophie Lambert</h4> <span>Fondatrice EcoSolutions</span> </div>
								</div>
								<p>"ALOGOTO a cru en notre projet environnemental. Grâce à leur expertise, nous avons pu concrétiser notre vision de développement durable."</p>
								<div class="testimonial__three-single-slider-user-rating"> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star"></i> <i class="fas fa-star not-rated"></i> </div>
								<div class="slider-shape"> <i class="fas fa-quote-right"></i> </div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
    <!-- Team One Area Start -->
        <div class="team__one style-two section-padding pb-100">
            <div class="container">
                <div class="row justify-content-center text-center mb-60 gy-4">
				<div class="col-xl-6 col-lg-6">
					<div class="work-process__one-title"> <span class="subtitle-three m-auto">Equipe</span>
						<h2>Les visages qui se cachent derriere Alogoto.</h2> </div>
				</div>
			</div>
                <div class="row justify-content-center">
                    <div class="col-xl-4 col-lg-6">
                        <div class="team__one-single-slider">
                            <div class="team__one-image">
                                <img src="assets/img/pdgntech1.jpeg" alt="">
                            </div>
                            <div class="team__one-content">
                                <h5>Nawane-dine SANNY</h5>
                                <span>CEO NTECH DIGIT</span>
                                <div class="social">
                                    <a href="https://www.facebook.com"><i class="fab fa-facebook-f"></i></a>
                                    <a href="https://www.twitter.com"><i class="fa-brands fa-x-twitter"></i></a>
                                    <a href="https://www.linkedin.com"><i class="fab fa-linkedin-in"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6">
                        <div class="team__one-single-slider">
                            <div class="team__one-image">
                                <img src="assets/img/ino.jpeg" alt="">
                            </div>
                            <div class="team__one-content">
                                <h5>Inès B. GANDAHO</h5>
                                <span>Développeuse web</span>
                                <div class="social">
                                    <a href="https://www.facebook.com"><i class="fab fa-facebook-f"></i></a>
                                    <a href="https://www.twitter.com"><i class="fa-brands fa-x-twitter"></i></a>
                                    <a href="https://www.linkedin.com"><i class="fab fa-linkedin-in"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6">
                        <div class="team__one-single-slider">
                            <div class="team__one-image">
                                <img src="assets/img/ade.jpeg" alt="">
                            </div>
                            <div class="team__one-content">
                                <h5>Gaston A. DJOSSE</h5>
                                <span>Développeur web</span>
                                <div class="social">
                                    <a href="https://www.facebook.com"><i class="fab fa-facebook-f"></i></a>
                                    <a href="https://www.twitter.com"><i class="fa-brands fa-x-twitter"></i></a>
                                    <a href="https://www.linkedin.com"><i class="fab fa-linkedin-in"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
         </div>
        <!-- Team One Area End -->
	<!-- Testimonial Two Area End -->
	<!-- Why Choose Us Three Area Start -->
	<!-- <div class="why-choose-us__three section-padding">
		<div class="container">
			<div class="row align-items-center flex-wrap-reverse gy-4">
				<div class="col-xl-6 col-lg-6 col-md-10">
					<div class="why-choose-us__three-content"> <span class="subtitle-three">Passez à l'action</span>
						<h2>Prêt à démarrer votre projet ?</h2>
						<p>Rejoignez notre communauté d'entrepreneurs et obtenez le soutien dont vous avez besoin pour réussir.</p>
						<div class="why-choose-us__three-content-service">
							<div class="why-choose-us__three-content-service-single">
								<div class="service-top"> <i class="flaticon-consultant"></i>
									<h6>Porteur de projet</h6> </div>
								<p>Connectez-vous et soummettre votre projet.</p>
							</div>
							<div class="why-choose-us__three-content-service-single">
								<div class="service-top"> <i class="flaticon-growth"></i>
									<h6>Insvestisseur</h6> </div>
								<p>Connectez-vous et investissez dans les projets qui vous passionnent.</p>
							</div>
						</div> <a href="about.html" class="btn-three">
                                Rejoignez-nous
                                <i class="fas fa-plus"></i>
                            </a> </div>
				</div>
				<div class="col-xl-6 col-lg-6 col-md-12">
					<div class="why-choose-us__three-image"> <img src="assets/img/why/why-three-1.jpg" alt="">
						<a href="https://www.youtube.com/watch?v=SZEflIVnhH8" class="video-btn video-popup video-pulse"> <i class="fas fa-play"></i> </a>
					</div>
				</div>
			</div>
		</div>
	</div> -->
	<!-- Why Choose Us Three Area End -->
	<!-- Blog Three Area Start -->
	<!-- <div class="blog__three section-padding">
		<div class="container">
			<div class="row justify-content-center text-center">
				<div class="col-xl-6 col-lg-6">
					<div class="blog__three-title"> <span class="subtitle-three">Latest Blog</span>
						<h2>Explore Blogs & Insights</h2> </div>
				</div>
			</div>
			<div class="swiper blog__three-slide-active">
				<div class="swiper-wrapper">
					<div class="blog__three-single-blog swiper-slide">
						<div class="blog__three-single-blog-image"> <img src="assets/img/blog/blog-4.jpg" alt=""> </div>
						<div class="blog__three-single-blog-date"> <span>10 Sep</span> </div>
						<div class="blog__three-single-blog-content">
							<div class="blog__three-single-blog-content-top"> <span>
                                        <i class="far fa-user"></i>
                                        wpboss
                                    </span> <span>
                                        <i class="far fa-comment-dots"></i>
                                        Comments (05)
                                    </span> </div>
							<h5><a href="blog-details-right-sidebar.html" class="blog-heading">The Power of Storytelling in Digital Marketing</a></h5> <a href="blog-details-right-sidebar.html" class="blog-btn">Read More
                                    <i class="fas fa-plus"></i>
                                </a> </div>
					</div>
					<div class="blog__three-single-blog swiper-slide">
						<div class="blog__three-single-blog-image"> <img src="assets/img/blog/blog-5.jpg" alt=""> </div>
						<div class="blog__three-single-blog-date"> <span>10 Sep</span> </div>
						<div class="blog__three-single-blog-content">
							<div class="blog__three-single-blog-content-top"> <span>
                                        <i class="far fa-user"></i>
                                        wpboss
                                    </span> <span>
                                        <i class="far fa-comment-dots"></i>
                                        Comments (05)
                                    </span> </div>
							<h5><a href="blog-details-right-sidebar.html" class="blog-heading">The Role of Data in Modern Marketing Campaign</a></h5> <a href="blog-details-right-sidebar.html" class="blog-btn">Read More
                                    <i class="fas fa-plus"></i>
                                </a> </div>
					</div>
					<div class="blog__three-single-blog swiper-slide">
						<div class="blog__three-single-blog-image"> <img src="assets/img/blog/blog-6.jpg" alt=""> </div>
						<div class="blog__three-single-blog-date"> <span>10 Sep</span> </div>
						<div class="blog__three-single-blog-content">
							<div class="blog__three-single-blog-content-top"> <span>
                                        <i class="far fa-user"></i>
                                        wpboss
                                    </span> <span>
                                        <i class="far fa-comment-dots"></i>
                                        Comments (05)
                                    </span> </div>
							<h5><a href="blog-details-right-sidebar.html" class="blog-heading">5 Proven Strategies to Boost Brand Awareness</a></h5> <a href="blog-details-right-sidebar.html" class="blog-btn">Read More
                                    <i class="fas fa-plus"></i>
                                </a> </div>
					</div>
				</div>
			</div>
		</div>
	</div> -->
	<!-- Blog Three Area End -->
<!-- Contact Form Modal -->
<div class="modal fade" id="contactFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content position-relative">
            <div class="contact-modal-accent" id="contactModalAccent"></div>
            <div class="modal-header border-bottom-0 pb-1">
                <h5 class="modal-title fw-bold" id="contactModalTitle">Succès</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-1">
                <p class="mb-0" id="contactModalMessage"></p>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-primary px-4" id="contactModalBtn" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
<style>
#contactFormModal .modal-content {
    border-radius: 16px !important;
    border: none !important;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15) !important;
    overflow: hidden;
}
#contactFormModal .contact-modal-accent {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    border-radius: 16px 16px 0 0;
    z-index: 10;
    transition: background 0.3s ease;
}
#contactFormModal .modal-title {
    font-size: 15px;
    color: #334155;
}
#contactFormModal .modal-body p {
    font-size: 14px;
    color: #64748b;
    line-height: 1.6;
}
#contactFormModal .btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}
</style>
<script>
(function() {
    'use strict';
    var modalEl = document.getElementById('contactFormModal');
    if (!modalEl) return;
    var bsModal = null;

    function getModal() {
        if (!bsModal && typeof bootstrap !== 'undefined') {
            bsModal = new bootstrap.Modal(modalEl, { backdrop: 'static' });
        }
        return bsModal;
    }

    window.ContactModal = {
        success: function(message) {
            var m = getModal();
            if (!m) return alert(message || 'Opération réussie.');
            document.getElementById('contactModalAccent').style.background = '#00c486';
            document.getElementById('contactModalTitle').innerHTML = '<i class="fas fa-check-circle me-2 text-success"></i> Succès';
            document.getElementById('contactModalMessage').textContent = message || 'Opération réussie.';
            document.getElementById('contactModalBtn').className = 'btn px-4 btn-success';
            m.show();
        },
        error: function(message) {
            var m = getModal();
            if (!m) return alert(message || 'Une erreur est survenue.');
            document.getElementById('contactModalAccent').style.background = '#f35120';
            document.getElementById('contactModalTitle').innerHTML = '<i class="fas fa-times-circle me-2 text-danger"></i> Erreur';
            document.getElementById('contactModalMessage').textContent = message || 'Une erreur est survenue.';
            document.getElementById('contactModalBtn').className = 'btn px-4 btn-danger';
            m.show();
        }
    };
})();
</script>

<?php
include 'footer.php';
?>
