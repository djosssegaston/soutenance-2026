<?php
include 'header.php';
?>
<style>
    .alogoto-login {
        background: linear-gradient(180deg, #edf4f2 0%, #ffffff 100%);
    }
    .alogoto-login.section-padding {
        padding: 78px 0;
    }
    .alogoto-login__panel {
        background-color: var(--white);
        border: 1px solid var(--border-color-2);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 18px 46px rgba(6, 42, 38, 0.11);
        max-width: 1080px;
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
        min-height: 530px;
        object-fit: cover;
    }
    .alogoto-login__media-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(6, 42, 38, 0.18) 0%, rgba(6, 42, 38, 0.82) 100%);
        color: var(--white);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 26px;
        gap: 10px;
    }
    .alogoto-login__media-overlay h3 {
        color: var(--white);
        margin-bottom: 6px;
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
        padding: 30px 28px;
        height: 100%;
    }
    .alogoto-login__form {
        max-width: 460px;
        margin: 0 auto;
    }
    .alogoto-login__form-wrap h2 {
        margin-bottom: 6px;
        font-size: 34px;
    }
    .alogoto-login__form-wrap > p {
        margin-bottom: 16px;
        font-size: 14px;
    }
    .alogoto-login__group {
        margin-bottom: 14px;
    }
    .alogoto-login__group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: var(--color-1);
        font-size: 14px;
    }
    .alogoto-login__group input {
        width: 100%;
        height: 50px;
        border: 1px solid var(--border-color-3);
        border-radius: 12px;
        padding: 0 16px;
        transition: 0.25s ease;
        background-color: #fff;
    }
    .alogoto-login__group input:focus {
        outline: none;
        border-color: var(--primary-color-3);
        box-shadow: 0 0 0 4px rgba(252, 160, 40, 0.16);
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
        margin-top: 6px;
        min-height: 18px;
        font-size: 12.5px;
        line-height: 1.45;
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
        right: 12px;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: var(--p-color);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        transition: 0.2s;
    }
    .alogoto-login__toggle-pass:hover {
        background-color: rgba(6, 42, 38, 0.08);
        color: var(--color-1);
    }
    .alogoto-login__rules {
        margin: 8px 0 8px;
        padding: 0;
        list-style: none;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px 12px;
    }
    .alogoto-login__rules li {
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #7c8482;
    }
    .alogoto-login__rules li i {
        font-size: 12px;
        color: #d64545;
        min-width: 12px;
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
        height: 8px;
        overflow: hidden;
        margin-bottom: 6px;
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
        gap: 12px;
        margin: 8px 0 14px;
        flex-wrap: wrap;
    }
    .alogoto-login__extra label {
        margin: 0;
        display: inline-flex;
        gap: 8px;
        align-items: center;
        font-size: 14px;
    }
    .alogoto-login__submit {
        width: 100%;
        border: 0;
        height: 50px;
        border-radius: 12px;
        color: var(--white);
        font-weight: 700;
        background-color: var(--primary-color-3);
        transition: 0.3s ease;
    }
    .alogoto-login__submit:hover {
        background-color: #f08e08;
    }
    .alogoto-login__register {
        margin-top: 10px;
        margin-bottom: 0;
        font-size: 14px;
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
        margin-top: 8px;
        min-height: 22px;
        font-size: 13px;
        font-weight: 500;
    }
    .alogoto-login__form-status.ok {
        color: #228b5d;
    }
    .alogoto-login__form-status.err {
        color: #d64545;
    }
    @media (max-width: 991px) {
        .alogoto-login__media img {
            min-height: 360px;
        }
        .alogoto-login__form-wrap {
            padding: 26px 20px;
        }
        .alogoto-login__rules {
            grid-template-columns: 1fr;
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
                            <img src="assets/img/team/team-2.jpg" alt="Visuel connexion (680 x 820 px)">
                            <div class="alogoto-login__media-overlay">
                                <h3>Connexion Securisee</h3>
                                <p>Accedez a votre espace pour suivre vos dossiers et vos investissements en temps reel.</p>
                                <!-- <span class="alogoto-login__image-size">
                                    Taille image conseillee : 680 x 820 px
                                </span> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="alogoto-login__form-wrap">
                            <h2 style="text-align: center; color: var(--primary-color-3);">Connexion</h2>
                            <!-- <p>Renseignez vos acces. Les controles se valident en direct sous chaque champ.</p> -->
                            <form id="alogotoLoginForm" class="alogoto-login__form" novalidate>
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
                                    <ul class="alogoto-login__rules">
                                        <li id="ruleLength"><i class="fas fa-times-circle" aria-hidden="true"></i><span>8+ caracteres</span></li>
                                        <li id="ruleUpper"><i class="fas fa-times-circle" aria-hidden="true"></i><span>1 lettre majuscule</span></li>
                                        <li id="ruleLower"><i class="fas fa-times-circle" aria-hidden="true"></i><span>1 lettre minuscule</span></li>
                                        <li id="ruleNumber"><i class="fas fa-times-circle" aria-hidden="true"></i><span>1 chiffre</span></li>
                                    </ul>
                                    <div class="alogoto-login__strength">
                                        <span id="passwordStrengthFill"></span>
                                    </div>
                                    <small id="passwordStrengthText" class="alogoto-login__feedback"></small>
                                </div>
                                <div class="alogoto-login__extra">
                                    <label for="rememberMe">
                                        <input id="rememberMe" type="checkbox" name="remember_me">
                                        Se souvenir de moi
                                    </label>
                                    <a href="reset-password.php">Mot de passe oublie ?</a>
                                </div>
                                <button type="submit" class="alogoto-login__submit">
                                    Se connecter <i class="fas fa-plus"></i>
                                </button>
                                <p id="formStatus" class="alogoto-login__form-status"></p>
                                <p class="alogoto-login__register">
                                    Nouveau sur Alogoto ?
                                    <a href="signup.php">Creer un compte</a>
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
            const form = document.getElementById("alogotoLoginForm");
            const identifier = document.getElementById("loginIdentifier");
            const password = document.getElementById("loginPassword");
            const togglePassword = document.getElementById("togglePassword");
            const identifierFeedback = document.getElementById("identifierFeedback");
            const passwordFeedback = document.getElementById("passwordFeedback");
            const strengthFill = document.getElementById("passwordStrengthFill");
            const strengthText = document.getElementById("passwordStrengthText");
            const formStatus = document.getElementById("formStatus");

            const ruleLength = document.getElementById("ruleLength");
            const ruleUpper = document.getElementById("ruleUpper");
            const ruleLower = document.getElementById("ruleLower");
            const ruleNumber = document.getElementById("ruleNumber");

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
                const compactValue = value.replace(/[\s().-]/g, "");
                if (phoneRegex.test(compactValue)) {
                    setState(identifier, identifierFeedback, "Numero de telephone valide.", true);
                    return true;
                }
                setState(identifier, identifierFeedback, "Format invalide: utilisez un email ou un numero (8 a 15 chiffres).", false);
                return false;
            }

            function evaluatePassword(value) {
                const checks = {
                    length: value.length >= 8,
                    upper: /[A-Z]/.test(value),
                    lower: /[a-z]/.test(value),
                    number: /[0-9]/.test(value)
                };
                const score = Object.values(checks).filter(Boolean).length;
                return { checks, score };
            }

            function paintRule(ruleEl, ok) {
                ruleEl.classList.toggle("ok", ok);
                const icon = ruleEl.querySelector("i");
                if (!icon) {
                    return;
                }
                icon.classList.remove("fa-check-circle", "fa-times-circle");
                icon.classList.add(ok ? "fa-check-circle" : "fa-times-circle");
            }

            function updatePassword() {
                const value = password.value;
                const { checks, score } = evaluatePassword(value);

                paintRule(ruleLength, checks.length);
                paintRule(ruleUpper, checks.upper);
                paintRule(ruleLower, checks.lower);
                paintRule(ruleNumber, checks.number);

                let width = "0%";
                let color = "#d64545";
                let label = "Niveau: faible";

                if (score === 2) {
                    width = "45%";
                    color = "#f59f00";
                    label = "Niveau: moyen";
                } else if (score === 3) {
                    width = "72%";
                    color = "#f08e08";
                    label = "Niveau: bon";
                } else if (score === 4) {
                    width = "100%";
                    color = "#1f915f";
                    label = "Niveau: fort";
                }

                strengthFill.style.width = value ? width : "0%";
                strengthFill.style.backgroundColor = value ? color : "#d64545";
                strengthText.textContent = value ? label : "";

                if (!value) {
                    setState(password, passwordFeedback, "Le mot de passe est requis.", null);
                    return false;
                }
                if (score < 4) {
                    setState(password, passwordFeedback, "Mot de passe incomplet: respectez les 4 criteres.", false);
                    return false;
                }
                setState(password, passwordFeedback, "Mot de passe valide.", true);
                return true;
            }

            identifier.addEventListener("input", validateIdentifier);
            password.addEventListener("input", updatePassword);

            togglePassword.addEventListener("click", function () {
                const isPassword = password.getAttribute("type") === "password";
                password.setAttribute("type", isPassword ? "text" : "password");
                this.innerHTML = isPassword ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
            });

            form.addEventListener("submit", function (e) {
                e.preventDefault();
                const idOk = validateIdentifier();
                const passOk = updatePassword();

                formStatus.classList.remove("ok", "err");
                if (idOk && passOk) {
                    formStatus.classList.add("ok");
                    formStatus.textContent = "Coordonnees valides. Le formulaire est pret a etre connecte au backend.";
                    return;
                }

                formStatus.classList.add("err");
                formStatus.textContent = "Veuillez corriger les champs signales avant de continuer.";
            });
        })();
    </script>


</body>
<?php
include 'footer.php';
?>
