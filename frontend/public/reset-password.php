<?php
include 'header.php';
?>
<body>
      <div class="banner__three-single-slide swiper-slide" style="background-image: url(assets/img/banner/banner-three-1.jpg);">
		<div class="container">
			<div class="breadcrumb__area-content">
				<h2>Historique</h2> <span>
                        <a href="index.html">
                            <i class="fas fa-home"></i>
                            Accueil
                        </a>
                        <i class="fas fa-chevron-right"></i>
                        Historique
                    </span> </div>
		</div>
	</div>

    <!-- Login Area Start -->
         <div class="login__area section-padding">
            <div class="container">
                <form action="#" class="login-form">
                    <h2>Réinitialisation</h2>
                    <label for="email">Adresse email <span>*</span></label>
                    <input type="email" id="email">
                    <button type="submit" style= color: var(--primary-color-3); class="btn-one">Réinitialiser le mot de passe</button>
                </form>
            </div>
         </div>
        <!-- Login Area End -->

</body>
<?php
include 'footer.php';
?>