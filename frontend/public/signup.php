<?php
include 'header.php';
$apiRegisterUrl = backend_url('api/v1/auth/register');
$apiResendUrl = backend_url('api/v1/auth/resend-verification');
?>
<style>
    * {
        box-sizing: border-box;
    }
    .alogoto-signup {
        background: linear-gradient(180deg, #edf4f2 0%, #ffffff 100%);
    }
    .alogoto-signup.section-padding {
        padding: 40px 0;
    }
    .alogoto-signup__panel {
        background-color: var(--white);
        border: 1px solid var(--border-color-2);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 12px 32px rgba(6, 42, 38, 0.10);
        max-width: 1000px;
        margin: 0 auto;
    }
    .alogoto-signup__media {
        position: relative;
        min-height: 100%;
        height: 100%;
    }
    .alogoto-signup__media img {
        width: 100%;
        height: 100%;
        min-height: 400px;
        object-fit: cover;
    }
    .alogoto-signup__media-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(6, 42, 38, 0.18) 0%, rgba(6, 42, 38, 0.82) 100%);
        color: var(--white);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 20px;
        gap: 8px;
    }
    .alogoto-signup__media-overlay h3 {
        color: var(--white);
        margin-bottom: 6px;
    }
    .alogoto-signup__form-wrap {
        padding: 20px 22px;
    }
    .alogoto-signup__form {
        max-width: 440px;
        margin: 0 auto;
        display: none;
        animation: fadeInUp 0.35s ease;
    }
    .alogoto-signup__form.is-active {
        display: block;
    }
    .alogoto-signup__form-wrap h2 {
        margin-bottom: 4px;
        font-size: 28px;
    }
    .alogoto-signup__form-wrap > p {
        margin-bottom: 10px;
        font-size: 13px;
    }
    .alogoto-signup__role-toggle {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin: 12px 0 14px;
    }
    .alogoto-signup__role-btn {
        border: 1px solid var(--border-color-3);
        background: #fff;
        color: var(--color-1);
        padding: 8px 10px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        transition: 0.2s ease;
    }
    .alogoto-signup__role-btn.active {
        background: var(--primary-color-3);
        color: #fff;
        border-color: var(--primary-color-3);
        box-shadow: 0 10px 22px rgba(252, 160, 40, 0.22);
    }
    .alogoto-signup__group {
        margin-bottom: 10px;
    }
    .alogoto-signup__group label {
        display: block;
        margin-bottom: 4px;
        font-weight: 600;
        color: var(--color-1);
        font-size: 13px;
    }
    .alogoto-signup__group input,
    .alogoto-signup__group select,
    .alogoto-signup__group textarea {
        width: 100%;
        height: 42px;
        border: 1px solid var(--border-color-3);
        border-radius: 8px;
        padding: 0 12px;
        transition: 0.25s ease;
        background-color: #fff;
        font-size: 13px;
    }
    .alogoto-signup__group textarea {
        padding: 10px 12px;
        height: auto;
        min-height: 80px;
        resize: vertical;
    }
    .alogoto-signup__group input:focus,
    .alogoto-signup__group select:focus,
    .alogoto-signup__group textarea:focus {
        outline: none;
        border-color: var(--primary-color-3);
        box-shadow: 0 0 0 4px rgba(252, 160, 40, 0.16);
    }
    .alogoto-signup__row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .alogoto-signup__submit {
        width: 100%;
        border: 0;
        height: 42px;
        border-radius: 8px;
        color: var(--white);
        font-weight: 700;
        font-size: 14px;
        background-color: var(--primary-color-3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .alogoto-signup__submit:hover:not(:disabled) {
        background-color: #f08e08;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(252, 160, 40, 0.35);
    }
    .alogoto-signup__submit:active:not(:disabled) {
        transform: translateY(0);
    }
    .alogoto-signup__submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    .alogoto-signup__resend-wrap {
        display: none;
        margin-top: 12px;
        animation: fadeInUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .alogoto-signup__resend {
        width: 100%;
        border: 2px solid var(--primary-color-3);
        background: transparent;
        color: var(--primary-color-3);
        height: 42px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .alogoto-signup__resend:hover:not(:disabled) {
        background-color: var(--primary-color-3);
        color: var(--white);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(252, 160, 40, 0.35);
    }
    .alogoto-signup__resend:active:not(:disabled) {
        transform: translateY(0);
    }
    .alogoto-signup__resend:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }
    .alogoto-signup__submit:hover {
        background-color: #f08e08;
    }
    .alogoto-signup__form-status {
        margin-top: 6px;
        min-height: 18px;
        font-size: 12px;
        font-weight: 500;
    }
    .alogoto-signup__form-status.ok {
        color: #228b5d;
    }
    .alogoto-signup__form-status.err {
        color: #d64545;
    }
    .alogoto-signup__login {
        margin-top: 8px;
        margin-bottom: 0;
        font-size: 13px;
        color: var(--p-color);
        text-align: center;
    }
    .alogoto-signup__login a {
        color: var(--primary-color-3);
        font-weight: 600;
    }
    .alogoto-signup__login a:hover {
        color: #f08e08;
    }
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @media (max-width: 991px) {
        .alogoto-signup.section-padding {
            padding: 30px 0;
        }
        .alogoto-signup__media img {
            min-height: 240px;
        }
        .alogoto-signup__form-wrap {
            padding: 18px 16px;
        }
        .alogoto-signup__form-wrap h2 {
            font-size: 24px;
        }
        .alogoto-signup__row {
            grid-template-columns: 1fr;
            gap: 10px;
        }
        .alogoto-signup__role-toggle {
            grid-template-columns: 1fr;
        }
        .alogoto-signup__group input,
        .alogoto-signup__group select {
            font-size: 12px;
        }
    }
    @media (max-width: 576px) {
        .alogoto-signup.section-padding {
            padding: 20px 0;
        }
        .alogoto-signup__panel {
            border-radius: 12px;
        }
        .alogoto-signup__form-wrap {
            padding: 16px 14px;
        }
        .alogoto-signup__form-wrap h2 {
            font-size: 22px;
        }
        .alogoto-signup__form-wrap > p {
            font-size: 12px;
        }
        .alogoto-signup__media img {
            min-height: 180px;
        }
        .alogoto-signup__group {
            margin-bottom: 8px;
        }
        .alogoto-signup__group input,
        .alogoto-signup__group select {
            height: 40px;
            font-size: 12px;
            padding: 0 10px;
        }
        .alogoto-signup__submit,
        .alogoto-signup__resend {
            height: 40px;
            font-size: 13px;
        }
        .alogoto-signup__role-btn {
            padding: 7px 8px;
            font-size: 12px;
        }
        .alogoto-signup__row {
            gap: 8px;
        }
    }
    @media (max-width: 480px) {
        .alogoto-signup.section-padding {
            padding: 14px 0;
        }
        .alogoto-signup__form-wrap {
            padding: 14px 12px;
        }
        .alogoto-signup__form-wrap h2 {
            font-size: 20px;
        }
        .alogoto-signup__media img {
            min-height: 150px;
        }
        .alogoto-signup__group input,
        .alogoto-signup__group select {
            height: 38px;
            font-size: 11px;
            padding: 0 8px;
        }
        .alogoto-signup__submit,
        .alogoto-signup__resend {
            height: 38px;
            font-size: 12px;
        }
        .alogoto-signup__group label {
            font-size: 12px;
        }
    }
    @media (max-width: 400px) {
        .alogoto-signup__form-wrap {
            padding: 12px 10px;
        }
        .alogoto-signup__form-wrap h2 {
            font-size: 18px;
        }
        .alogoto-signup__media img {
            min-height: 120px;
        }
        .alogoto-signup__group input,
        .alogoto-signup__group select {
            height: 36px;
            font-size: 11px;
        }
        .alogoto-signup__submit,
        .alogoto-signup__resend {
            height: 36px;
            font-size: 11px;
        }
    }
    @media (max-width: 375px) {
        .alogoto-signup__form-wrap {
            padding: 10px 8px;
        }
        .alogoto-signup__form-wrap h2 {
            font-size: 17px;
        }
        .alogoto-signup__media img {
            min-height: 100px;
        }
        .alogoto-signup__group input,
        .alogoto-signup__group select {
            height: 34px;
            font-size: 10px;
        }
        .alogoto-signup__submit,
        .alogoto-signup__resend {
            height: 34px;
            font-size: 10px;
        }
        .alogoto-signup__role-btn {
            font-size: 11px;
            padding: 6px 6px;
        }
    }
</style>

<body>
    <main class="alogoto-signup section-padding">
        <div class="container">
            <div class="alogoto-signup__panel">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <div class="alogoto-signup__media">
                            <img src="assets/img/team/team-3.jpg" alt="Visuel inscription (680 x 820 px)">
                            <div class="alogoto-signup__media-overlay">
                                <h3>Rejoignez la plateforme</h3>
                                <p>Choisissez votre profil et completez le formulaire adapte pour lancer votre experience.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="alogoto-signup__form-wrap">
                            <h2 style="text-align: center; color: var(--primary-color-3);">Inscription</h2>
                            <p style="text-align: center;">Selectionnez votre role pour afficher le bon formulaire.</p>
                            <p class="alogoto-signup__note">Apres confirmation de votre compte, vous pourrez soumettre votre projet.</p>

                            <div class="alogoto-signup__role-toggle" role="tablist">
                                <button type="button" class="alogoto-signup__role-btn active" data-role="porteur">
                                    Je suis porteur de projet
                                </button>
                                <button type="button" class="alogoto-signup__role-btn" data-role="institution">
                                    Je represente une institution
                                </button>
                            </div>

                            <form id="porteurForm" class="alogoto-signup__form is-active" novalidate>
                                <div class="alogoto-signup__group">
                                    <label for="porteurName">Nom complet</label>
                                    <input id="porteurName" name="name" type="text" required placeholder="ex: Samira Adelo">
                                </div>
                                <div class="alogoto-signup__row">
                                    <div class="alogoto-signup__group">
                                        <label for="porteurEmail">Email</label>
                                        <input id="porteurEmail" name="email" type="email" required placeholder="vous@email.com">
                                    </div>
                                    <div class="alogoto-signup__group">
                                        <label for="porteurPhone">Telephone</label>
                                        <input id="porteurPhone" name="telephone" type="tel" placeholder="+2290100000000">
                                    </div>
                                </div>
                                <div class="alogoto-signup__row">
                                    <div class="alogoto-signup__group">
                                        <label for="porteurPassword">Mot de passe</label>
                                        <input id="porteurPassword" name="password" type="password" required minlength="8" placeholder="8 caracteres min.">
                                    </div>
                                    <div class="alogoto-signup__group">
                                        <label for="porteurPasswordConfirm">Confirmation</label>
                                        <input id="porteurPasswordConfirm" name="password_confirmation" type="password" required minlength="8" placeholder="Confirmez le mot de passe">
                                    </div>
                                </div>

                                <button type="submit" class="alogoto-signup__submit" id="porteurSubmitBtn">
                                    <i class="fas fa-user-plus"></i> Creer mon compte
                                </button>
                                <p id="porteurStatus" class="alogoto-signup__form-status"></p>
                                <div id="porteurResendWrap" class="alogoto-signup__resend-wrap">
                                    <button type="button" id="porteurResendBtn" class="alogoto-signup__resend">
                                        <i class="fas fa-envelope"></i> Renvoyer l'email de confirmation
                                    </button>
                                </div>
                            </form>

                            <form id="institutionForm" class="alogoto-signup__form" novalidate>
                                <div class="alogoto-signup__group">
                                    <label for="institutionRep">Nom du representant</label>
                                    <input id="institutionRep" name="name" type="text" required placeholder="ex: Marc Yao">
                                </div>
                                <input id="institutionEmail" name="email" type="hidden">
                                <input id="institutionPhone" name="telephone" type="hidden">
                                <div class="alogoto-signup__row">
                                    <div class="alogoto-signup__group">
                                        <label for="institutionPassword">Mot de passe</label>
                                        <input id="institutionPassword" name="password" type="password" required minlength="8" placeholder="8 caracteres min.">
                                    </div>
                                    <div class="alogoto-signup__group">
                                        <label for="institutionPasswordConfirm">Confirmation</label>
                                        <input id="institutionPasswordConfirm" name="password_confirmation" type="password" required minlength="8" placeholder="Confirmez le mot de passe">
                                    </div>
                                </div>

                                <div class="alogoto-signup__group">
                                    <label for="institutionName">Nom de l'institution</label>
                                    <input id="institutionName" name="institution_name" type="text" required placeholder="ex: Banque Alogoto">
                                </div>
                                <div class="alogoto-signup__row">
                                    <div class="alogoto-signup__group">
                                    <label for="institutionMail">Email de l'institution (connexion)</label>
                                    <input id="institutionMail" name="institution_email" type="email" required placeholder="contact@institution.com">
                                </div>
                                <div class="alogoto-signup__group">
                                    <label for="institutionTel">Telephone de l'institution</label>
                                    <input id="institutionTel" name="institution_phone" type="tel" required placeholder="+2290100000000">
                                </div>
                            </div>
                                <div class="alogoto-signup__group">
                                    <label for="institutionAddress">Adresse</label>
                                    <input id="institutionAddress" name="institution_address" type="text" required placeholder="Quartier, ville, pays">
                                </div>

                                <button type="submit" class="alogoto-signup__submit" id="institutionSubmitBtn">
                                    <i class="fas fa-building"></i> Creer le compte institution
                                </button>
                                <p id="institutionStatus" class="alogoto-signup__form-status"></p>
                                <div id="institutionResendWrap" class="alogoto-signup__resend-wrap">
                                    <button type="button" id="institutionResendBtn" class="alogoto-signup__resend">
                                        <i class="fas fa-envelope"></i> Renvoyer l'email de confirmation
                                    </button>
                                </div>
                            </form>

                            <p class="alogoto-signup__login">
                                Deja un compte ?
                                <a href="login.php">Se connecter</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        (function () {
            const apiRegisterUrl = <?php echo json_encode($apiRegisterUrl); ?>;
            const apiResendUrl = <?php echo json_encode($apiResendUrl); ?>;
            const roleButtons = document.querySelectorAll('.alogoto-signup__role-btn');
            const porteurForm = document.getElementById('porteurForm');
            const institutionForm = document.getElementById('institutionForm');
            const porteurStatus = document.getElementById('porteurStatus');
            const institutionStatus = document.getElementById('institutionStatus');
            const porteurResendWrap = document.getElementById('porteurResendWrap');
            const institutionResendWrap = document.getElementById('institutionResendWrap');
            const porteurResendBtn = document.getElementById('porteurResendBtn');
            const institutionResendBtn = document.getElementById('institutionResendBtn');
            const porteurSubmitBtn = document.getElementById('porteurSubmitBtn');
            const institutionSubmitBtn = document.getElementById('institutionSubmitBtn');

            let lastEmail = '';
            let currentRole = 'porteur';

            function setRole(role) {
                currentRole = role;
                roleButtons.forEach((btn) => {
                    btn.classList.toggle('active', btn.dataset.role === role);
                });
                porteurForm.classList.toggle('is-active', role === 'porteur');
                institutionForm.classList.toggle('is-active', role === 'institution');
                porteurStatus.textContent = '';
                institutionStatus.textContent = '';
                porteurResendWrap.style.display = 'none';
                institutionResendWrap.style.display = 'none';
                lastEmail = '';
            }

            roleButtons.forEach((btn) => {
                btn.addEventListener('click', () => setRole(btn.dataset.role));
            });

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
            const phoneRegex = /^\+?[0-9]{8,15}$/;

            function normalizePhone(value) {
                const trimmed = value.trim();
                if (!trimmed) {
                    return trimmed;
                }
                const hasPlus = trimmed.startsWith("+");
                const digits = trimmed.replace(/[^\d]/g, "");
                return hasPlus ? "+" + digits : digits;
            }

            function applyPhoneMask(input) {
                input.addEventListener("input", () => {
                    const normalized = normalizePhone(input.value);
                    input.value = normalized;
                });
            }

            function setStatus(target, message, ok) {
                target.classList.remove('ok', 'err');
                if (ok === true) {
                    target.classList.add('ok');
                } else if (ok === false) {
                    target.classList.add('err');
                }
                target.textContent = message;
            }

            function formToJson(form) {
                const data = {};
                new FormData(form).forEach((value, key) => {
                    data[key] = value;
                });
                return data;
            }

            function validateClient(payload, statusEl) {
                if (!payload.name || payload.name.trim().length < 3) {
                    setStatus(statusEl, 'Veuillez saisir un nom complet valide.', false);
                    return false;
                }
                if (!emailRegex.test(payload.email || '')) {
                    setStatus(statusEl, 'Veuillez saisir un email valide.', false);
                    return false;
                }
                if (!payload.password || payload.password.length < 8) {
                    setStatus(statusEl, 'Le mot de passe doit contenir au moins 8 caracteres.', false);
                    return false;
                }
                if (!/[A-Z]/.test(payload.password) || !/[a-z]/.test(payload.password) || !/[0-9]/.test(payload.password)) {
                    setStatus(statusEl, 'Le mot de passe doit contenir une majuscule, une minuscule et un chiffre.', false);
                    return false;
                }
                if (payload.password !== payload.password_confirmation) {
                    setStatus(statusEl, 'La confirmation du mot de passe ne correspond pas.', false);
                    return false;
                }
                if (payload.telephone && !phoneRegex.test(payload.telephone)) {
                    setStatus(statusEl, 'Numero de telephone invalide (8 a 15 chiffres).', false);
                    return false;
                }
                if (payload.institution_phone && !phoneRegex.test(payload.institution_phone)) {
                    setStatus(statusEl, 'Telephone institution invalide (8 a 15 chiffres).', false);
                    return false;
                }
                return true;
            }

            async function submitForm(form, role, statusEl, resendWrap, submitBtn) {
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const payload = formToJson(form);
                payload.role = role;
                if (role === 'institution') {
                    payload.email = (payload.institution_email || '').trim();
                    payload.telephone = (payload.institution_phone || '').trim();
                }
                if (payload.telephone) {
                    payload.telephone = normalizePhone(payload.telephone);
                }
                if (payload.institution_phone) {
                    payload.institution_phone = normalizePhone(payload.institution_phone);
                }

                if (!validateClient(payload, statusEl)) {
                    return;
                }

                // Disable submit button and show loading
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creation en cours...';
                
                setStatus(statusEl, 'Envoi en cours...', null);

                try {
                    const response = await fetch(apiRegisterUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    let data = {};
                    try {
                        data = await response.json();
                    } catch (e) {
                        data = {};
                    }

                    if (!response.ok) {
                        let message = data.message || 'Une erreur est survenue. Verifiez les champs.';
                        if (data.errors) {
                            const allErrors = Object.values(data.errors).flat();
                            if (allErrors.length) {
                                message = allErrors.join(' ');
                            }
                        }
                        setStatus(statusEl, message, false);
                        // Re-enable submit button on error
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                        return;
                    }

                    lastEmail = payload.email || '';
                    setStatus(statusEl, data.message || 'Inscription reussie. Verifiez votre email.', true);
                    
                    // Show resend button immediately after successful registration
                    resendWrap.style.display = 'block';
                    resendWrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    
                    // Reset submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check"></i> Compte cree avec succes !';
                    
                    // Reset form
                    form.reset();
                    
                    // Restore button after 3 seconds
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                    }, 3000);
                } catch (err) {
                    setStatus(statusEl, 'Impossible de contacter le serveur. Reessayez.', false);
                    // Re-enable submit button on error
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }

            porteurForm.addEventListener('submit', (event) => {
                event.preventDefault();
                submitForm(porteurForm, 'porteur', porteurStatus, porteurResendWrap, porteurSubmitBtn);
            });

            institutionForm.addEventListener('submit', (event) => {
                event.preventDefault();
                submitForm(institutionForm, 'institution', institutionStatus, institutionResendWrap, institutionSubmitBtn);
            });

            async function resendVerification(statusEl, resendBtn, resendWrapEl) {
                const emailToUse = lastEmail;
                if (!emailRegex.test(emailToUse)) {
                    setStatus(statusEl, "Veuillez d'abord completer l'inscription avec un email valide.", false);
                    return;
                }
                
                // Disable resend button and show loading
                resendBtn.disabled = true;
                const originalResendText = resendBtn.innerHTML;
                resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
                setStatus(statusEl, "Envoi de l'email de confirmation...", null);
                
                try {
                    const response = await fetch(apiResendUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email: emailToUse })
                    });
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        setStatus(statusEl, data.message || "Impossible de renvoyer l'email.", false);
                        resendBtn.disabled = false;
                        resendBtn.innerHTML = originalResendText;
                        return;
                    }
                    setStatus(statusEl, data.message || 'Email de confirmation renvoye.', true);
                    
                    // Show success state
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = '<i class="fas fa-check"></i> Email envoye !';
                    
                    // Restore button after 3 seconds
                    setTimeout(() => {
                        resendBtn.innerHTML = originalResendText;
                    }, 3000);
                } catch (err) {
                    setStatus(statusEl, 'Erreur reseau. Reessayez.', false);
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = originalResendText;
                }
            }

            porteurResendBtn.addEventListener('click', () => resendVerification(porteurStatus, porteurResendBtn, porteurResendWrap));
            institutionResendBtn.addEventListener('click', () => resendVerification(institutionStatus, institutionResendBtn, institutionResendWrap));

            applyPhoneMask(document.getElementById('porteurPhone'));
            applyPhoneMask(document.getElementById('institutionTel'));
        })();
    </script>
</body>
<?php
include 'footer.php';
?>
<style>
    .alogoto-signup__note {
        margin-top: 6px;
        font-size: 12px;
        text-align: center;
        color: #6a726f;
    }
    .alogoto-signup__resend {
        display: none;
        margin-top: 8px;
        text-align: center;
    }
    .alogoto-signup__resend button {
        border: 1px solid var(--border-color-3);
        background: #fff;
        color: var(--color-1);
        font-size: 13px;
        font-weight: 600;
        padding: 8px 12px;
        border-radius: 10px;
        transition: 0.2s ease;
    }
    .alogoto-signup__resend button:hover {
        border-color: var(--primary-color-3);
        color: var(--primary-color-3);
    }
</style>
