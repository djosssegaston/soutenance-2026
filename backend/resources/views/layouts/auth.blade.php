<!doctype html>
<html lang="fr" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Alogoto">
    <meta name="keywords" content="microfinance, Alogoto">
    @stack('meta')

    <link rel="shortcut icon" type="image/x-icon" href="{{ url('frontend/asset/images/brand/favicon.ico') }}">
    <title>@stack('title') - Alogoto</title>

    <link id="style" href="{{ url('frontend/asset/css/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('frontend/asset/css/style.css') }}" rel="stylesheet">
    <link href="{{ url('frontend/asset/css/plugins.css') }}" rel="stylesheet">
    <link href="{{ url('frontend/asset/css/icons.css') }}" rel="stylesheet">
    <link href="{{ url('frontend/asset/switcher/css/switcher.css') }}" rel="stylesheet">
    <link href="{{ url('frontend/asset/switcher/demo.css') }}" rel="stylesheet">
    <link href="{{ url('frontend/asset/css/porteur-theme.css') }}" rel="stylesheet">

    <style>
        :root {
            --login-accent: #FCA028;
            --login-accent-rgb: 252, 160, 40;
            --login-accent-soft: rgba(252, 160, 40, 0.08);
            --login-card-shadow: 0 20px 60px rgba(0, 0, 0, 0.08), 0 8px 20px rgba(252, 160, 40, 0.06);
            --login-card-radius: 24px;
            --login-input-radius: 14px;
            --login-btn-radius: 14px;
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            overflow: hidden;
        }

        .auth-split {
            display: flex;
            height: 100vh;
        }

        .auth-image {
            flex: 0 0 70%;
            position: relative;
            overflow: hidden;
        }

        .auth-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            inset: 0;
        }

        .auth-image-overlay {
            position: fixed;
            width: 70vw;
            height: 100vh;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, rgba(252, 160, 40, 0.15) 0%, rgba(252, 160, 40, 0.85) 100%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 40px 30px;
            color: #fff;
        }

        .auth-image-overlay h3 {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 8px;
            color: #fff;
        }

        .auth-image-overlay p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
            line-height: 1.6;
        }

        .auth-form {
            flex: 0 0 30%;
            display: flex;
            align-items: center;
            background: linear-gradient(145deg, #FFF8F0 0%, #FFFFFF 100%);
        }

        .auth-form-inner {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
        }

        .login-card {
            background: #ffffff;
            box-shadow: var(--login-card-shadow);
            padding: 40px 36px 44px;
            border: none;
            border-left: 1px solid rgba(252, 160, 40, 0.08);
            position: relative;
            overflow-y: auto;
            scrollbar-gutter: stable;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
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

        .back-home-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #6c757d;
            text-decoration: none;
            transition: color 0.3s ease;
            margin-bottom: 20px;
        }
        .back-home-link:hover { color: var(--login-accent); }

        .login-logo { text-align: center; margin-bottom: 32px; }
        .login-logo img { max-width: 180px; height: auto; }

        .login-title { text-align: center; margin-bottom: 28px; }
        .login-title h2 { font-size: 24px; font-weight: 700; color: #1a1a2e; margin: 0 0 6px 0; }
        .login-title p { font-size: 14px; color: #6c757d; margin: 0; }

        .form-group { margin-bottom: 20px; }

        .form-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: #495057; margin-bottom: 6px;
        }

        .input-group-custom {
            display: flex; align-items: center; border: 2px solid #e9ecef;
            border-radius: var(--login-input-radius);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background: #fafbfc;
        }

        .input-group-custom.is-invalid { border-color: #dc3545; }

        .input-group-custom:focus-within {
            border-color: var(--login-accent);
            box-shadow: 0 0 0 4px rgba(252, 160, 40, 0.1);
            background: #ffffff;
        }

        .input-group-custom.is-invalid:focus-within {
            border-color: #dc3545;
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.1);
        }

        .input-group-custom .input-icon {
            display: flex; align-items: center; justify-content: center;
            width: 48px; min-width: 48px; height: 48px;
            color: #adb5bd; font-size: 18px; transition: color 0.3s ease;
        }

        .input-group-custom:focus-within .input-icon { color: var(--login-accent); }
        .input-group-custom.is-invalid:focus-within .input-icon { color: #dc3545; }
        .input-group-custom .input-icon .fe { font-size: 20px; }

        .input-group-custom input, .input-group-custom select {
            flex: 1; border: none; background: transparent;
            padding: 12px 16px 12px 0; font-size: 15px;
            color: #1a1a2e; outline: none; min-height: 48px;
        }

        .input-group-custom select {
            cursor: pointer; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236c757d' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
        }

        .input-group-custom input::placeholder { color: #adb5bd; }

        .input-group-custom .toggle-password {
            display: flex; align-items: center; justify-content: center;
            width: 48px; min-width: 48px; height: 48px; color: #adb5bd;
            cursor: pointer; font-size: 18px; transition: color 0.3s ease;
            border: none; background: transparent; padding: 0;
        }

        .input-group-custom .toggle-password:hover { color: var(--login-accent); }

        .form-options {
            display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 24px;
        }

        .form-options .remember-me {
            display: flex; align-items: center; gap: 8px;
            font-size: 14px; color: #6c757d; cursor: pointer;
        }

        .form-options .remember-me input[type="checkbox"] {
            width: 18px; height: 18px; accent-color: var(--login-accent); cursor: pointer;
        }

        .form-options .forgot-link {
            font-size: 14px; color: var(--login-accent);
            text-decoration: none; font-weight: 500; transition: color 0.3s ease;
        }

        .form-options .forgot-link:hover { color: #e08900; text-decoration: underline; }

        .btn-login {
            width: 100%; padding: 14px 24px; border: none;
            border-radius: var(--login-btn-radius);
            background: linear-gradient(135deg, var(--login-accent), #FFB74D);
            color: #ffffff; font-size: 16px; font-weight: 700; cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 24px rgba(252, 160, 40, 0.3);
            position: relative; overflow: hidden;
        }

        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 12px 32px rgba(252, 160, 40, 0.4); }
        .btn-login:active { transform: translateY(0); }
        .btn-login:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .btn-login .spinner {
            display: none; width: 20px; height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #ffffff; border-radius: 50%;
            animation: spin 0.6s linear infinite; margin: 0 auto;
        }

        .btn-login.loading .spinner { display: block; }
        .btn-login.loading .btn-text { display: none; }

        @keyframes spin { to { transform: rotate(360deg); } }

        .login-footer { text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f0f0; }
        .login-footer p { font-size: 14px; color: #6c757d; margin: 0; }
        .login-footer a { color: var(--login-accent); text-decoration: none; font-weight: 600; }
        .login-footer a:hover { text-decoration: underline; }

        .social-login-section { margin-top: 20px; text-align: center; }

        .social-login-section .divider {
            display: flex; align-items: center; gap: 12px; margin-bottom: 20px;
        }

        .social-login-section .divider::before,
        .social-login-section .divider::after {
            content: ''; flex: 1; height: 1px; background: #e9ecef;
        }

        .social-login-section .divider span { font-size: 13px; color: #6c757d; white-space: nowrap; }

        .social-login-buttons { display: flex; justify-content: center; gap: 12px; }

        .social-login-buttons .social-btn {
            display: flex; align-items: center; justify-content: center;
            width: 48px; height: 48px; border-radius: 14px;
            border: 2px solid #e9ecef; color: #6c757d; font-size: 20px;
            text-decoration: none; transition: all 0.3s ease; background: #ffffff;
        }

        .social-login-buttons .social-btn:hover {
            border-color: var(--login-accent); color: var(--login-accent);
            background: var(--login-accent-soft); transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(252, 160, 40, 0.15);
        }

        .alert-error {
            background: #fff5f5; border: 1px solid #fecaca; border-radius: 12px;
            padding: 12px 16px; margin-bottom: 20px; font-size: 14px;
            color: #dc2626; display: flex; align-items: center; gap: 8px;
        }

        .alert-error i { font-size: 18px; flex-shrink: 0; }

        .alert-success {
            background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px;
            padding: 12px 16px; margin-bottom: 20px; font-size: 14px;
            color: #16a34a; display: flex; align-items: center; gap: 8px;
        }

        .alert-success i { font-size: 18px; flex-shrink: 0; }

        .field-error { font-size: 12px; color: #dc3545; margin-top: 4px; display: flex; align-items: center; gap: 4px; }

        .login-tabs {
            display: flex;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 28px;
            gap: 4px;
        }

        .login-tabs .tab-btn {
            flex: 1; padding: 10px 16px; border: none; background: transparent;
            border-radius: 10px; font-size: 14px; font-weight: 600; color: #6c757d;
            cursor: pointer; transition: all 0.3s ease; text-align: center;
        }

        .login-tabs .tab-btn:hover { color: var(--login-accent); background: rgba(252, 160, 40, 0.06); }

        .login-tabs .tab-btn.active {
            background: #ffffff; color: var(--login-accent);
            box-shadow: 0 2px 8px rgba(252, 160, 40, 0.15);
        }

        .login-tab-content { display: none; }
        .login-tab-content.active { display: block; }

        .otp-container { display: flex; justify-content: center; gap: 10px; margin: 24px 0; }

        .otp-container input {
            width: 52px; height: 58px; text-align: center; font-size: 24px; font-weight: 700;
            border: 2px solid #e9ecef; border-radius: 14px; outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease; color: #1a1a2e;
        }

        .otp-container input:focus {
            border-color: var(--login-accent);
            box-shadow: 0 0 0 4px rgba(252, 160, 40, 0.1);
        }

        .otp-note { text-align: center; font-size: 13px; color: #6c757d; margin-bottom: 20px; }

        .mobile-input-wrapper .country-code {
            display: flex; align-items: center; padding: 0 12px; font-weight: 600;
            color: #1a1a2e; font-size: 15px; border-right: 2px solid #e9ecef; min-height: 48px;
        }

        .form-row { display: flex; gap: 12px; }
        .form-row .form-group { flex: 1; }

        @media (max-width: 992px) {
            .auth-image { display: none; }
            .auth-image-overlay { display: none; }
            .auth-form { flex: 0 0 100%; padding: 20px; }
        }

        @media (max-width: 576px) {
            .auth-form { padding: 12px; }
            .login-card { padding: 28px 20px 32px; border-radius: 20px; }
            .login-title h2 { font-size: 20px; }
            .login-tabs .tab-btn { font-size: 13px; padding: 8px 12px; }
            .form-options { flex-direction: column; gap: 12px; align-items: flex-start; }
            .otp-container { gap: 6px; }
            .otp-container input { width: 44px; height: 50px; font-size: 20px; }
            .form-row { flex-direction: column; gap: 0; }
        }

        @stack('styles')
    </style>
</head>

<body>

<div class="auth-split">
    <div class="auth-image">
        <img src="{{ url('frontend/asset/images/media/auth-bg.jpg') }}" alt="Alogoto">
        <div class="auth-image-overlay">
            <h3>Alogoto</h3>
            <p>La plateforme qui connecte les porteurs de projet aux institutions financières pour un financement participatif sécurisé.</p>
        </div>
    </div>

    <div class="auth-form">
        <div class="auth-form-inner">
            @yield('content')
        </div>
    </div>
</div>

<script src="{{ url('frontend/asset/js/jquery.min.js') }}"></script>
<script src="{{ url('frontend/asset/js/plugins/bootstrap/js/popper.min.js') }}"></script>
<script src="{{ url('frontend/asset/js/plugins/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ url('frontend/asset/js/plugins/p-scroll/perfect-scrollbar.js') }}"></script>
<script src="{{ url('frontend/asset/js/themeColors.js') }}"></script>
<script src="{{ url('frontend/asset/js/show-password.min.js') }}"></script>
<script src="{{ url('frontend/asset/js/custom-swicher.js') }}"></script>
<script src="{{ url('frontend/asset/switcher/js/switcher.js') }}"></script>
@stack('scripts')

</body>
</html>
