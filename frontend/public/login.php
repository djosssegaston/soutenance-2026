<?php
require_once __DIR__ . '/../includes/path_helpers.php';

// Rediriger vers le login Laravel si accès direct (standalone)
// Le login Laravel utilise l'authentification par session (web guard)
// compatible avec les dashboards dashboard02
if (!isset($isLaravel) || !$isLaravel) {
    header('Location: ' . backend_url('login'));
    exit;
}

include __DIR__ . '/header.php';
?>
<style>
    * {
        box-sizing: border-box;
    }
    .alogoto-login {
        background: linear-gradient(180deg, #edf4f2 0%, #ffffff 100%);
    }
    .alogoto-login.section-padding {
        padding: 30px 0;
    }
    .alogoto-login__panel {
        background-color: var(--white);
        border: 1px solid var(--border-color-2);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 12px 32px rgba(6, 42, 38, 0.10);
        max-width: 1000px;
        margin: 0 auto;
    }
    .alogoto-login__media {
        position: relative;
        min-height: 100%;
        height: 100%;
    }
    .alogoto-login__media img {
        width: 100%;
        height: 100%;
        min-height: 400px;
        object-fit: cover;
    }
    .alogoto-login__image-size {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        background-color: rgba(252, 160, 40, 0.18);
        border: 1px solid rgba(252, 160, 40, 0.55);
        border-radius: 999px;
        padding: 7px 14px;
        font-size: 13px;
        font-weight: 600;
        color: #ffd9a6;
    }
    .alogoto-login__form-wrap {
        padding: 18px 20px;
    }
    .alogoto-login__form {
        max-width: 400px;
        margin: 0 auto;
    }
    .alogoto-login__form-wrap h2 {
        margin-bottom: 3px;
        font-size: 26px;
    }
    .alogoto-login__form-wrap > p {
        margin-bottom: 10px;
        font-size: 12px;
    }
    .alogoto-login__group {
        margin-bottom: 8px;
    }
    .alogoto-login__group label {
        display: block;
        margin-bottom: 3px;
        font-weight: 600;
        color: var(--color-1);
        font-size: 12px;
    }
    .alogoto-login__group input {
        width: 100%;
        height: 40px;
        border: 1px solid var(--border-color-3);
        border-radius: 8px;
        padding: 0 10px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: #fff;
        font-size: 13px;
    }
    .alogoto-login__group input:focus {
        outline: none;
        border-color: var(--primary-color-3);
        box-shadow: 0 0 0 4px rgba(252, 160, 40, 0.16);
        transform: translateY(-1px);
    }
    .alogoto-login__group input.is-valid {
        border-color: #30a46c;
        box-shadow: 0 0 0 4px rgba(48, 164, 108, 0.14);
    }
    .alogoto-login__group input.is-invalid {
        border-color: #d64545;
        box-shadow: 0 0 0 4px rgba(214, 69, 69, 0.14);
    }
    .alogoto-login__feedback {
        display: block;
        margin-top: 3px;
        min-height: 14px;
        font-size: 11px;
        line-height: 1.4;
        color: #6a726f;
    }
    .alogoto-login__feedback.is-valid {
        color: #228b5d;
    }
    .alogoto-login__feedback.is-invalid {
        color: #d64545;
    }
    .alogoto-login__password-wrap {
        position: relative;
    }
    .alogoto-login__toggle-pass {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: var(--p-color);
        width: 28px;
        height: 28px;
        border-radius: 50%;
        transition: 0.2s;
    }
    .alogoto-login__toggle-pass:hover {
        background-color: rgba(6, 42, 38, 0.08);
        color: var(--color-1);
    }
    .alogoto-login__rules {
        margin: 4px 0 4px;
        padding: 0;
        list-style: none;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3px 8px;
    }
    .alogoto-login__rules li {
        font-size: 10px;
        display: flex;
        align-items: center;
        gap: 5px;
        color: #7c8482;
    }
    .alogoto-login__rules li i {
        font-size: 10px;
        color: #d64545;
        min-width: 10px;
    }
    .alogoto-login__rules li span {
        line-height: 1.35;
    }
    .alogoto-login__rules li.ok {
        color: #1f915f;
    }
    .alogoto-login__rules li.ok i {
        color: #1f915f;
    }
    .alogoto-login__strength {
        background-color: #e9eeec;
        border-radius: 999px;
        height: 5px;
        overflow: hidden;
        margin-bottom: 3px;
    }
    .alogoto-login__strength > span {
        display: block;
        width: 0;
        height: 100%;
        background-color: #d64545;
        transition: width 0.25s ease, background-color 0.25s ease;
    }
    .alogoto-login__extra {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin: 4px 0 8px;
        flex-wrap: wrap;
    }
    .alogoto-login__extra label {
        margin: 0;
        display: inline-flex;
        gap: 5px;
        align-items: center;
        font-size: 12px;
    }
    .alogoto-login__submit {
        width: 100%;
        border: 0;
        height: 40px;
        border-radius: 8px;
        color: var(--white);
        font-weight: 700;
        font-size: 13px;
        background-color: var(--primary-color-3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .alogoto-login__submit:hover:not(:disabled) {
        background-color: #f08e08;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(252, 160, 40, 0.35);
    }
    .alogoto-login__submit:active:not(:disabled) {
        transform: translateY(0);
    }
    .alogoto-login__submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    .alogoto-login__register {
        margin-top: 6px;
        margin-bottom: 0;
        font-size: 12px;
        color: var(--p-color);
        text-align: center;
    }
    .alogoto-login__register a {
        color: var(--primary-color-3);
        font-weight: 600;
    }
    .alogoto-login__register a:hover {
        color: #f08e08;
    }
    .alogoto-login__form-status {
        margin-top: 4px;
        min-height: 16px;
        font-size: 11px;
        font-weight: 500;
    }
    .alogoto-login__form-status.ok {
        color: #228b5d;
    }
    .alogoto-login__form-status.err {
        color: #d64545;
    }
    @media (min-width: 1800px) {
        .alogoto-login__panel {
            max-width: 1100px;
        }
        .alogoto-login__form-wrap {
            padding: 28px 36px;
        }
        .alogoto-login__form-wrap h2 {
            font-size: 30px;
        }
        .alogoto-login__form {
            max-width: 440px;
        }
        .alogoto-login__group input {
            height: 44px;
            font-size: 14px;
        }
        .alogoto-login__submit {
            height: 44px;
            font-size: 14px;
        }
        .alogoto-login__media img {
            min-height: 500px;
        }
    }
    @media (max-width: 1400px) {
        .alogoto-login__panel {
            max-width: 960px;
        }
        .alogoto-login__form {
            max-width: 380px;
        }
    }
    @media (max-width: 1200px) {
        .alogoto-login__panel {
            max-width: 900px;
        }
        .alogoto-login__form-wrap {
            padding: 16px 18px;
        }
        .alogoto-login__form {
            max-width: 360px;
        }
    }
    @media (max-width: 991px) {
        .alogoto-login__panel {
            max-width: 100%;
            margin: 0 10px;
        }
        .alogoto-login__media img {
            min-height: 220px;
        }
        .alogoto-login__form-wrap {
            padding: 20px 18px;
        }
        .alogoto-login__form-wrap h2 {
            font-size: 24px;
        }
        .alogoto-login__form {
            max-width: 100%;
        }
        .alogoto-login__rules {
            grid-template-columns: 1fr;
        }
        .alogoto-login__group input {
            font-size: 14px;
        }
    }
    @media (max-width: 768px) {
        .alogoto-login.section-padding {
            padding: 20px 0;
        }
        .alogoto-login__panel {
            border-radius: 14px;
            margin: 0 8px;
        }
        .alogoto-login__media img {
            min-height: 180px;
        }
        .alogoto-login__form-wrap {
            padding: 18px 16px;
        }
        .alogoto-login__form-wrap h2 {
            font-size: 22px;
        }
        .alogoto-login__form-wrap > p {
            font-size: 13px;
        }
    }
    @media (max-width: 576px) {
        .alogoto-login.section-padding {
            padding: 12px 0;
        }
        .alogoto-login__panel {
            border-radius: 12px;
            margin: 0 6px;
        }
        .alogoto-login__form-wrap {
            padding: 16px 14px;
        }
        .alogoto-login__form-wrap h2 {
            font-size: 20px;
        }
        .alogoto-login__media img {
            min-height: 160px;
        }
        .alogoto-login__group input {
            height: 40px;
            font-size: 14px;
            padding: 0 10px;
        }
        .alogoto-login__submit {
            height: 42px;
            font-size: 13px;
        }
        .alogoto-login__extra {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }
        .alogoto-login__rules {
            gap: 2px 6px;
        }
        .alogoto-login__rules li {
            font-size: 11px;
        }
    }
    @media (max-width: 480px) {
        .alogoto-login.section-padding {
            padding: 8px 0;
        }
        .alogoto-login__panel {
            border-radius: 10px;
            margin: 0 4px;
        }
        .alogoto-login__form-wrap {
            padding: 14px 12px;
        }
        .alogoto-login__form-wrap h2 {
            font-size: 18px;
        }
        .alogoto-login__media img {
            min-height: 140px;
        }
        .alogoto-login__group {
            margin-bottom: 6px;
        }
        .alogoto-login__group label {
            font-size: 11px;
        }
        .alogoto-login__group input {
            height: 38px;
            font-size: 13px;
            padding: 0 8px;
        }
        .alogoto-login__submit {
            height: 38px;
            font-size: 12px;
        }
        .alogoto-login__extra label {
            font-size: 11px;
        }
        .alogoto-login__register {
            font-size: 11px;
        }
        .alogoto-login__register a {
            font-size: 11px;
        }
    }
    @media (max-width: 400px) {
        .alogoto-login__form-wrap {
            padding: 12px 10px;
        }
        .alogoto-login__form-wrap h2 {
            font-size: 17px;
        }
        .alogoto-login__media img {
            min-height: 120px;
        }
        .alogoto-login__group input {
            height: 36px;
            font-size: 12px;
            padding: 0 8px;
        }
        .alogoto-login__submit {
            height: 36px;
            font-size: 11px;
        }
        .alogoto-login__toggle-pass {
            width: 24px;
            height: 24px;
            right: 6px;
            font-size: 12px;
        }
        .alogoto-login__extra {
            gap: 4px;
        }
        .alogoto-login__extra label {
            font-size: 10px;
        }
        .alogoto-login__extra a {
            font-size: 11px;
        }
        .alogoto-login__rules li {
            font-size: 10px;
        }
        .alogoto-login__register {
            font-size: 10px;
        }
    }
    @media (max-width: 375px) {
        .alogoto-login__form-wrap {
            padding: 10px 8px;
        }
        .alogoto-login__form-wrap h2 {
            font-size: 16px;
        }
        .alogoto-login__media img {
            min-height: 100px;
        }
        .alogoto-login__group label {
            font-size: 10px;
        }
        .alogoto-login__group input {
            height: 34px;
            font-size: 11px;
            border-radius: 6px;
        }
        .alogoto-login__submit {
            height: 34px;
            font-size: 11px;
            border-radius: 6px;
        }
        .alogoto-login__password-wrap input {
            padding-right: 30px !important;
        }
    }
    @media (max-width: 360px) {
        .alogoto-login.section-padding {
            padding: 4px 0;
        }
        .alogoto-login__form-wrap {
            padding: 8px 6px;
        }
        .alogoto-login__form-wrap h2 {
            font-size: 15px;
        }
        .alogoto-login__form-wrap > p {
            font-size: 10px;
        }
        .alogoto-login__group input {
            height: 32px;
            font-size: 11px;
        }
        .alogoto-login__submit {
            height: 32px;
            font-size: 10px;
        }
        .alogoto-login__group label {
            font-size: 9px;
        }
        .alogoto-login__register {
            font-size: 9px;
        }
    }
    @media (max-width: 320px) {
        .alogoto-login__form-wrap {
            padding: 6px 4px;
        }
        .alogoto-login__form-wrap h2 {
            font-size: 14px;
        }
        .alogoto-login__media img {
            min-height: 80px;
        }
        .alogoto-login__group {
            margin-bottom: 4px;
        }
        .alogoto-login__group label {
            font-size: 9px;
            margin-bottom: 2px;
        }
        .alogoto-login__group input {
            height: 30px;
            font-size: 10px;
            padding: 0 6px;
            border-radius: 5px;
        }
        .alogoto-login__submit {
            height: 30px;
            font-size: 10px;
            border-radius: 5px;
        }
        .alogoto-login__toggle-pass {
            width: 20px;
            height: 20px;
            right: 4px;
            font-size: 10px;
        }
        .alogoto-login__extra label {
            font-size: 9px;
        }
        .alogoto-login__extra a {
            font-size: 9px;
        }
        .alogoto-login__register {
            font-size: 8px;
        }
        .alogoto-login__register a {
            font-size: 8px;
        }
        .alogoto-login__rules li {
            font-size: 9px;
        }
        .alogoto-login__feedback {
            font-size: 9px;
            min-height: 12px;
        }
    }
</style>

<body>
    <main class="alogoto-login section-padding">
        <div class="container">
            <div class="alogoto-login__panel">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <div class="alogoto-login__media">
                            <img src="" alt="Visuel connexion (680 x 820 px)">

                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="alogoto-login__form-wrap">
                            <h2 style="text-align: center; color: var(--primary-color-3);">Connexion</h2>
                            <!-- <p>Renseignez vos acces. Les controles se valident en direct sous chaque champ.</p> -->
                            <form id="alogotoLoginForm" class="alogoto-login__form" novalidate method="POST" action="<?php echo htmlspecialchars($apiLoginUrl, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php if (!empty($csrfToken)) { ?>
                                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php } ?>
                                <div class="alogoto-login__group">
                                    <label for="loginIdentifier">Email ou telephone</label>
                                    <input
                                        id="loginIdentifier"
                                        name="identifier"
                                        type="text"
                                        inputmode="email"
                                        autocomplete="username"
                                        placeholder="ex: vous@email.com ou +2290100000000"
                                        required
                                    >
                                    <small id="identifierFeedback" class="alogoto-login__feedback">
                                        <!-- Saisissez un email valide ou un numero de telephone (8 a 15 chiffres). -->
                                    </small>
                                </div>
                                <div class="alogoto-login__group">
                                    <label for="loginPassword">Mot de passe</label>
                                    <div class="alogoto-login__password-wrap">
                                        <input
                                            id="loginPassword"
                                            name="password"
                                            type="password"
                                            autocomplete="current-password"
                                            placeholder="Votre mot de passe"
                                            required
                                        >
                                        <button type="button" id="togglePassword" class="alogoto-login__toggle-pass" aria-label="Afficher le mot de passe">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <small id="passwordFeedback" class="alogoto-login__feedback">
                                        Le mot de passe doit respecter les criteres ci-dessous.
                                    </small>
                                </div>
                                <div class="alogoto-login__extra">
                                    <label for="rememberMe">
                                        <input id="rememberMe" type="checkbox" name="remember_me">
                                        Se souvenir de moi
                                    </label>
                                    <a href="reset-password.php">Mot de passe oublie ?</a>
                                </div>
                                <button type="submit" class="alogoto-login__submit">
                                    <i class="fas fa-sign-in-alt"></i> Se connecter
                                </button>
                                <p id="verifiedBanner" class="alogoto-login__form-status ok" style="display:none;"></p>
                                <p id="formStatus" class="alogoto-login__form-status"></p>
                                <p class="alogoto-login__register">
                                    Nouveau sur Alogoto ?
                                    <a href="<?php echo htmlspecialchars(frontend_public_url('signup.php'), ENT_QUOTES, 'UTF-8'); ?>">Creer un compte</a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        (function () {
            // Ne pas intercepter le formulaire si Laravel gère l'authentification
            const isLaravelContext = <?php echo isset($isLaravel) && $isLaravel ? 'true' : 'false'; ?>;
            if (isLaravelContext) {
                // Laisser Laravel gérer la soumission du formulaire avec les sessions
                return;
            }

            const apiLoginUrl = <?php echo json_encode($apiLoginUrl); ?>;
            const apiResendUrl = <?php echo json_encode($apiResendUrl); ?>;
            const form = document.getElementById("alogotoLoginForm");
            const csrfToken = "<?php echo htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8'); ?>";
            const identifier = document.getElementById("loginIdentifier");
            const password = document.getElementById("loginPassword");
            const togglePassword = document.getElementById("togglePassword");
            const identifierFeedback = document.getElementById("identifierFeedback");
            const passwordFeedback = document.getElementById("passwordFeedback");
            const formStatus = document.getElementById("formStatus");
            const verifiedBanner = document.getElementById("verifiedBanner");
            const submitButton = form ? form.querySelector(".alogoto-login__submit") : null;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
            const phoneRegex = /^\+?[0-9]{8,15}$/;

            function setState(input, feedbackEl, message, valid) {
                input.classList.remove("is-valid", "is-invalid");
                feedbackEl.classList.remove("is-valid", "is-invalid");
                if (valid === true) {
                    input.classList.add("is-valid");
                    feedbackEl.classList.add("is-valid");
                } else if (valid === false) {
                    input.classList.add("is-invalid");
                    feedbackEl.classList.add("is-invalid");
                }
                feedbackEl.textContent = message;
            }

            function normalizePhone(value) {
                const trimmed = value.trim();
                if (trimmed.includes("@")) {
                    return trimmed;
                }
                const hasPlus = trimmed.startsWith("+");
                const digits = trimmed.replace(/[^\d]/g, "");
                return hasPlus ? "+" + digits : digits;
            }

            function validateIdentifier() {
                const value = identifier.value.trim();
                if (!value) {
                    setState(identifier, identifierFeedback, "Saisissez votre email ou votre numero de telephone.", null);
                    return false;
                }
                if (emailRegex.test(value)) {
                    setState(identifier, identifierFeedback, "Email valide.", true);
                    return true;
                }
                const compactValue = normalizePhone(value);
                if (phoneRegex.test(compactValue)) {
                    identifier.value = compactValue;
                    setState(identifier, identifierFeedback, "Numero de telephone valide.", true);
                    return true;
                }
                setState(identifier, identifierFeedback, "Format invalide: utilisez un email ou un numero (8 a 15 chiffres).", false);
                return false;
            }

            function updatePassword() {
                const value = password.value;
                if (!value) {
                    setState(password, passwordFeedback, 'Le mot de passe est requis.', null);
                    return false;
                }
                setState(password, passwordFeedback, 'Mot de passe valide.', true);
                return true;
            }

            identifier.addEventListener("input", validateIdentifier);
            password.addEventListener("input", updatePassword);

            togglePassword.addEventListener("click", function () {
                const isPassword = password.getAttribute("type") === "password";
                password.setAttribute("type", isPassword ? "text" : "password");
                this.innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
            });

            function setLoading(state) {
                if (!submitButton) {
                    return;
                }
                submitButton.disabled = state;
                const originalHTML = '<i class="fas fa-sign-in-alt"></i> Se connecter';
                submitButton.innerHTML = state ? '<i class="fas fa-spinner fa-spin"></i> Connexion...' : originalHTML;
            }

            form.addEventListener("submit", async function (e) {
                e.preventDefault();
                const idOk = validateIdentifier();
                const passOk = updatePassword();

                formStatus.classList.remove("ok", "err");
                if (idOk && passOk) {
                    setLoading(true);
                    
                    try {
                        const response = await fetch(apiLoginUrl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                ...(csrfToken ? { "X-CSRF-TOKEN": csrfToken } : {})
                            },
                            body: JSON.stringify({
                                identifier: identifier.value.trim(),
                                password: password.value
                            })
                        });

                        const data = await response.json().catch(() => ({}));
                        if (!response.ok) {
                            formStatus.classList.add("err");
                            formStatus.textContent = data.message || "Connexion impossible. Verifiez vos identifiants.";
                            return;
                        }

                        if (data.token) {
                            localStorage.setItem("alogoto_api_token", data.token);
                        }
                        
                        // Redirect based on user role
                        if (data.user && data.user.role) {
                            const role = data.user.role;
                            const userName = data.user.name || 'Utilisateur';
                            const roleLabels = { admin: 'Administrateur', institution: 'Institution Financière', porteur: 'Porteur de Projet' };
                            const roleLabel = roleLabels[role] || role;
                            const loadingTexts = { admin: 'Chargement du centre de contrôle...', institution: 'Synchronisation du portefeuille...', porteur: 'Chargement de vos financements...' };
                            const icons = { admin: '🛡️', institution: '🏦', porteur: '📋' };
                            
                            let dashboardUrl;
                            switch(role) {
                                case 'admin': dashboardUrl = backend_url('dashboard/admin'); break;
                                case 'institution': dashboardUrl = backend_url('dashboard/institution'); break;
                                default: dashboardUrl = backend_url('dashboard/porteur'); break;
                            }
                            
                            await Swal.fire({
                                title: `Bienvenue ${userName.split(' ')[0]} ${icons[role] || '👋'}`,
                                html: `<div style="font-size:1.1rem;color:#64748b;margin-bottom:0.5rem;">Connexion sécurisée réussie</div>
                                       <div style="display:inline-block;padding:0.3rem 1rem;border-radius:20px;background:#fca02820;color:#fca028;font-weight:600;font-size:0.9rem;">${roleLabel}</div>
                                       <div style="margin-top:1.2rem;font-size:0.95rem;color:#94a3b8;">${loadingTexts[role] || 'Chargement de votre espace...'}</div>`,
                                icon: 'success',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                timer: 2800,
                                timerProgressBar: true,
                                background: '#ffffff',
                                customClass: {
                                    popup: 'animated fadeInDown faster',
                                    title: 'fs-24 fw-bold',
                                },
                                didOpen: () => {
                                    const popup = Swal.getPopup();
                                    popup.style.borderRadius = '20px';
                                    popup.style.boxShadow = '0 20px 60px rgba(0,0,0,0.12)';
                                    popup.style.padding = '2rem';
                                }
                            });
                            
                            window.location.href = dashboardUrl;
                            return;
                        }
                        
                        if (data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }
                        
                        formStatus.classList.add("ok");
                        formStatus.textContent = "Connexion reussie. Bienvenue !";
                    } catch (err) {
                        formStatus.classList.add("err");
                        formStatus.textContent = "Erreur reseau. Reessayez.";
                    } finally {
                        setLoading(false);
                    }
                    return;
                }

                formStatus.classList.add("err");
                formStatus.textContent = "Veuillez corriger les champs signales avant de continuer.";
            });

            const params = new URLSearchParams(window.location.search);
            if (params.get("verified") === "1" || params.get("verified") === "true") {
                verifiedBanner.style.display = "block";
                verifiedBanner.textContent = "Votre email a ete confirme. Vous pouvez vous connecter.";
            }
        })();
    </script>


</body>
<?php
include __DIR__ . '/footer.php';
?>


