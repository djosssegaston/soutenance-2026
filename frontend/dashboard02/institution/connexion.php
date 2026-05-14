<!doctype html>
<html lang="fr" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Alogoto – Connexion Institution">
    <meta name="author" content="Alogoto">
    <meta name="keywords" content="microfinance, connexion, institution, Alogoto">

    <link rel="shortcut icon" type="image/x-icon" href="../../asset/images/brand/favicon.ico">
    <title>Connexion Institution - Alogoto</title>

    <link id="style" href="../../asset/css/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../asset/css/style.css" rel="stylesheet">
    <link href="../../asset/css/plugins.css" rel="stylesheet">
    <link href="../../asset/css/icons.css" rel="stylesheet">
    <link href="../../asset/switcher/css/switcher.css" rel="stylesheet">
    <link href="../../asset/switcher/demo.css" rel="stylesheet">
    <link href="../../asset/css/porteur-theme.css" rel="stylesheet">

    <style>
        :root {
            --login-accent: #FCA028;
            --login-accent-rgb: 252, 160, 40;
            --login-accent-soft: rgba(252, 160, 40, 0.08);
            --login-bg-start: #FFF8F0;
            --login-bg-end: #FFFFFF;
            --login-card-shadow: 0 20px 60px rgba(0, 0, 0, 0.08), 0 8px 20px rgba(252, 160, 40, 0.06);
            --login-card-radius: 24px;
            --login-input-radius: 14px;
            --login-btn-radius: 14px;
        }

        * {
            box-sizing: border-box;
        }

        body.login-page {
            background: linear-gradient(145deg, var(--login-bg-start) 0%, var(--login-bg-end) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 20px;
            margin: 0;
        }

        .login-wrapper {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
        }

        .login-card {
            background: #ffffff;
            border-radius: var(--login-card-radius);
            box-shadow: var(--login-card-shadow);
            padding: 40px 36px 44px;
            border: 1px solid rgba(252, 160, 40, 0.08);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, var(--login-accent), #FFB74D, var(--login-accent));
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { background-position: 0% 0%; }
            50% { background-position: 100% 0%; }
        }

        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-logo img {
            max-width: 180px;
            height: auto;
        }

        .login-title {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-title h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0 0 6px 0;
        }

        .login-title p {
            font-size: 14px;
            color: #6c757d;
            margin: 0;
        }

        .login-tabs {
            display: flex;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 28px;
            gap: 4px;
        }

        .login-tabs .tab-btn {
            flex: 1;
            padding: 10px 16px;
            border: none;
            background: transparent;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }

        .login-tabs .tab-btn:hover {
            color: var(--login-accent);
            background: rgba(252, 160, 40, 0.06);
        }

        .login-tabs .tab-btn.active {
            background: #ffffff;
            color: var(--login-accent);
            box-shadow: 0 2px 8px rgba(252, 160, 40, 0.15);
        }

        .login-tab-content {
            display: none;
        }

        .login-tab-content.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
        }

        .input-group-custom {
            display: flex;
            align-items: center;
            border: 2px solid #e9ecef;
            border-radius: var(--login-input-radius);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background: #fafbfc;
        }

        .input-group-custom:focus-within {
            border-color: var(--login-accent);
            box-shadow: 0 0 0 4px rgba(252, 160, 40, 0.1);
            background: #ffffff;
        }

        .input-group-custom .input-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            min-width: 48px;
            height: 48px;
            color: #adb5bd;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .input-group-custom:focus-within .input-icon {
            color: var(--login-accent);
        }

        .input-group-custom .input-icon .fe {
            font-size: 20px;
        }

        .input-group-custom input {
            flex: 1;
            border: none;
            background: transparent;
            padding: 12px 16px 12px 0;
            font-size: 15px;
            color: #1a1a2e;
            outline: none;
            min-height: 48px;
        }

        .input-group-custom input::placeholder {
            color: #adb5bd;
        }

        .input-group-custom .toggle-password {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            min-width: 48px;
            height: 48px;
            color: #adb5bd;
            cursor: pointer;
            font-size: 18px;
            transition: color 0.3s ease;
            border: none;
            background: transparent;
            padding: 0;
        }

        .input-group-custom .toggle-password:hover {
            color: var(--login-accent);
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .form-options .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #6c757d;
            cursor: pointer;
        }

        .form-options .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--login-accent);
            cursor: pointer;
        }

        .form-options .forgot-link {
            font-size: 14px;
            color: var(--login-accent);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .form-options .forgot-link:hover {
            color: #e08900;
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px 24px;
            border: none;
            border-radius: var(--login-btn-radius);
            background: linear-gradient(135deg, var(--login-accent), #FFB74D);
            color: #ffffff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 24px rgba(252, 160, 40, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 32px rgba(252, 160, 40, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-login .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin: 0 auto;
        }

        .btn-login.loading .spinner {
            display: block;
        }

        .btn-login.loading .btn-text {
            display: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }

        .login-footer p {
            font-size: 14px;
            color: #6c757d;
            margin: 0;
        }

        .login-footer a {
            color: var(--login-accent);
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .social-login-section {
            margin-top: 20px;
            text-align: center;
        }

        .social-login-section .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .social-login-section .divider::before,
        .social-login-section .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
        }

        .social-login-section .divider span {
            font-size: 13px;
            color: #6c757d;
            white-space: nowrap;
        }

        .social-login-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .social-login-buttons .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            border: 2px solid #e9ecef;
            color: #6c757d;
            font-size: 20px;
            text-decoration: none;
            transition: all 0.3s ease;
            background: #ffffff;
        }

        .social-login-buttons .social-btn:hover {
            border-color: var(--login-accent);
            color: var(--login-accent);
            background: var(--login-accent-soft);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(252, 160, 40, 0.15);
        }

        /* OTP */
        .otp-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 24px 0;
        }

        .otp-container input {
            width: 52px;
            height: 58px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            border: 2px solid #e9ecef;
            border-radius: 14px;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            color: #1a1a2e;
        }

        .otp-container input:focus {
            border-color: var(--login-accent);
            box-shadow: 0 0 0 4px rgba(252, 160, 40, 0.1);
        }

        .otp-note {
            text-align: center;
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 20px;
        }

        .mobile-input-wrapper .country-code {
            display: flex;
            align-items: center;
            padding: 0 12px;
            font-weight: 600;
            color: #1a1a2e;
            font-size: 15px;
            border-right: 2px solid #e9ecef;
            min-height: 48px;
        }

        /* Responsive */
        @media (max-width: 576px) {
            body.login-page {
                padding: 12px;
            }

            .login-card {
                padding: 28px 20px 32px;
                border-radius: 20px;
            }

            .login-title h2 {
                font-size: 20px;
            }

            .login-tabs .tab-btn {
                font-size: 13px;
                padding: 8px 12px;
            }

            .form-options {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }

            .otp-container {
                gap: 6px;
            }

            .otp-container input {
                width: 44px;
                height: 50px;
                font-size: 20px;
            }
        }

        @media (max-width: 380px) {
            .otp-container input {
                width: 38px;
                height: 44px;
                font-size: 18px;
            }
        }
    </style>
</head>

<body class="login-page">

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-logo">
                <a href="index.php">
                    <img src="../../asset/images/brand/logo-dark.png" class="header-brand-img dark-logo" alt="Alogoto">
                </a>
            </div>

            <div class="login-title">
                <h2>Connexion Institution</h2>
                <p>Accédez à votre tableau de bord institutionnel</p>
            </div>

            <form class="login-form" id="loginForm" novalidate>
                <!-- Tabs -->
                <div class="login-tabs" role="tablist">
                    <button class="tab-btn active" id="tab-email-btn" data-tab="tab5" type="button">E-mail</button>
                    <button class="tab-btn" id="tab-mobile-btn" data-tab="tab6" type="button">Mobile</button>
                </div>

                <!-- Tab: Email -->
                <div class="login-tab-content active" id="tab5">
                    <div class="form-group">
                        <label for="login-email">Adresse e-mail</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="fe fe-mail"></i></span>
                            <input type="email" id="login-email" class="form-control-custom" placeholder="exemple@institution.com" autocomplete="email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="login-password">Mot de passe</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="fe fe-lock"></i></span>
                            <input type="password" id="login-password" class="form-control-custom" placeholder="••••••••" autocomplete="current-password" required>
                            <button type="button" class="toggle-password" id="togglePassword" tabindex="-1" aria-label="Afficher le mot de passe">
                                <i class="fe fe-eye-off"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" id="remember-me"> Se souvenir de moi
                        </label>
                        <a href="forgot-password.html" class="forgot-link">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="btn-login" id="login-submit">
                        <span class="btn-text">Se connecter</span>
                        <span class="spinner"></span>
                    </button>
                </div>

                <!-- Tab: Mobile -->
                <div class="login-tab-content" id="tab6">
                    <div class="form-group">
                        <label for="mobile-num">Numéro de téléphone</label>
                        <div class="input-group-custom">
                            <span class="country-code">+226</span>
                            <input type="tel" id="mobile-num" class="form-control-custom" placeholder="XX XX XX XX" autocomplete="tel">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Code de vérification (OTP)</label>
                        <div class="otp-container" id="login-otp">
                            <input type="text" id="txt1" maxlength="1" inputmode="numeric" pattern="[0-9]">
                            <input type="text" id="txt2" maxlength="1" inputmode="numeric" pattern="[0-9]">
                            <input type="text" id="txt3" maxlength="1" inputmode="numeric" pattern="[0-9]">
                            <input type="text" id="txt4" maxlength="1" inputmode="numeric" pattern="[0-9]">
                        </div>
                    </div>

                    <p class="otp-note">Connectez-vous avec votre numéro mobile enregistré pour recevoir un code OTP.</p>

                    <button type="button" class="btn-login" id="generate-otp">
                        <span class="btn-text">Recevoir le code</span>
                        <span class="spinner"></span>
                    </button>
                </div>
            </form>

            <div class="social-login-section">
                <div class="divider">
                    <span>Ou connectez-vous avec</span>
                </div>
                <div class="social-login-buttons">
                    <a href="javascript:void(0)" class="social-btn" aria-label="Twitter">
                        <i class="fa fa-twitter"></i>
                    </a>
                    <a href="javascript:void(0)" class="social-btn" aria-label="Google">
                        <i class="fa fa-google"></i>
                    </a>
                    <a href="javascript:void(0)" class="social-btn" aria-label="Facebook">
                        <i class="fa fa-facebook"></i>
                    </a>
                </div>
            </div>

            <div class="login-footer">
                <p>Vous n'avez pas de compte ? <a href="register.html">Créer un compte</a></p>
            </div>
        </div>
    </div>

    <script src="../../asset/js/jquery.min.js"></script>
    <script src="../../asset/js/plugins/bootstrap/js/popper.min.js"></script>
    <script src="../../asset/js/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../asset/js/plugins/p-scroll/perfect-scrollbar.js"></script>
    <script src="../../asset/js/themeColors.js"></script>
    <script src="../../asset/js/show-password.min.js"></script>
    <script src="../../asset/js/generate-otp.js"></script>
    <script src="../../asset/js/custom-swicher.js"></script>
    <script src="../../asset/switcher/js/switcher.js"></script>

    <script>
        // Tabs
        document.querySelectorAll('.tab-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var target = this.getAttribute('data-tab');
                document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
                document.querySelectorAll('.login-tab-content').forEach(function(t) { t.classList.remove('active'); });
                this.classList.add('active');
                document.getElementById(target).classList.add('active');
            });
        });

        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            var pw = document.getElementById('login-password');
            var icon = this.querySelector('i');
            if (pw.type === 'password') {
                pw.type = 'text';
                icon.className = 'fe fe-eye';
            } else {
                pw.type = 'password';
                icon.className = 'fe fe-eye-off';
            }
        });

        // OTP auto-advance
        document.querySelectorAll('#login-otp input').forEach(function(input, idx, arr) {
            input.addEventListener('input', function() {
                if (this.value.length === 1 && idx < arr.length - 1) {
                    arr[idx + 1].focus();
                }
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value.length === 0 && idx > 0) {
                    arr[idx - 1].focus();
                }
            });
            input.addEventListener('focus', function() { this.select(); });
        });

        // Login form submit
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var btn = document.getElementById('login-submit');
            btn.classList.add('loading');
            btn.disabled = true;
            // Simulate - actual auth logic is handled by the application
            setTimeout(function() {
                btn.classList.remove('loading');
                btn.disabled = false;
            }, 2000);
        });

        // Generate OTP
        document.getElementById('generate-otp').addEventListener('click', function() {
            var btn = this;
            btn.classList.add('loading');
            btn.disabled = true;
            // Simulate - actual OTP logic is handled by the application
            setTimeout(function() {
                btn.classList.remove('loading');
                btn.disabled = false;
            }, 2000);
        });
    </script>
</body>
</html>
