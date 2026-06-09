<?php
include 'header.php';
?>
<style>
    * {
        box-sizing: border-box;
    }
    .login-form {
        max-width: 420px;
        margin: 0 auto;
        background: var(--white);
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
    .login-form h2 {
        text-align: center;
        margin-bottom: 16px;
        color: var(--primary-color-3);
        font-size: 24px;
    }
    .login-form label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        font-size: 13px;
        color: var(--color-1);
    }
    .login-form label span {
        color: #d64545;
    }
    .login-form input {
        width: 100%;
        height: 42px;
        border: 1px solid var(--border-color-3);
        border-radius: 8px;
        padding: 0 12px;
        margin-bottom: 16px;
        transition: 0.25s ease;
        background-color: #fff;
        font-size: 13px;
    }
    .login-form input:focus {
        outline: none;
        border-color: var(--primary-color-3);
        box-shadow: 0 0 0 4px rgba(252, 160, 40, 0.16);
    }
    .login-form button {
        width: 100%;
        height: 42px;
        border: 0;
        border-radius: 8px;
        color: var(--white);
        font-weight: 700;
        font-size: 14px;
        background-color: var(--primary-color-3);
        transition: 0.3s ease;
        cursor: pointer;
    }
    .login-form button:hover {
        background-color: #f08e08;
    }
    @media (max-width: 768px) {
        .login-form {
            padding: 20px 18px;
        }
        .login-form h2 {
            font-size: 22px;
        }
        .login-form input,
        .login-form button {
            height: 40px;
            font-size: 12px;
        }
    }
    @media (max-width: 576px) {
        .login-form {
            padding: 18px 15px;
            border-radius: 10px;
        }
        .login-form h2 {
            font-size: 20px;
        }
        .login-form input,
        .login-form button {
            height: 38px;
            font-size: 12px;
        }
    }
    @media (max-width: 480px) {
        .login-form {
            padding: 16px 12px;
        }
        .login-form h2 {
            font-size: 18px;
        }
        .login-form input,
        .login-form button {
            height: 36px;
            font-size: 11px;
        }
        .login-form label {
            font-size: 12px;
        }
    }
    @media (max-width: 400px) {
        .login-form {
            padding: 14px 10px;
        }
        .login-form h2 {
            font-size: 17px;
        }
        .login-form input,
        .login-form button {
            height: 34px;
            font-size: 11px;
        }
    }
    @media (max-width: 375px) {
        .login-form {
            padding: 12px 8px;
        }
        .login-form h2 {
            font-size: 16px;
        }
        .login-form input,
        .login-form button {
            height: 32px;
            font-size: 10px;
        }
    }
</style>
<body>
      <div class="banner__three-single-slide swiper-slide" style="background-image: url(assets/img/banner/banner-three-1.jpg);">
		<div class="container">
			<div class="breadcrumb__area-content">
				<h2>Réinitialisation</h2> <span>
                        <a href="index.php">
                            <i class="fas fa-home"></i>
                            Accueil
                        </a>
                        <i class="fas fa-chevron-right"></i>
                        Réinitialisation
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
                    <button type="submit" class="btn-one">Réinitialiser le mot de passe</button>
                </form>
            </div>
         </div>
        <!-- Login Area End -->

</body>
<?php
include 'footer.php';
?>