<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement sécurisé - Alogoto</title>
    <link href="{{ url('frontend/asset/css/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ url('frontend/asset/css/plugins.css') }}" rel="stylesheet">
    <link href="{{ url('frontend/asset/css/icons.css') }}" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #e67e22 0%, #d35400 50%, #e67e22 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .checkout-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15), 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 440px;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-5px); }
            80% { transform: translateX(5px); }
        }

        .checkout-header {
            background: linear-gradient(135deg, #e67e22, #d35400);
            padding: 30px 30px 24px;
            text-align: center;
            position: relative;
            animation: fadeIn 0.6s ease-out 0.1s both;
        }

        .checkout-header img {
            height: 36px;
            width: auto;
            filter: brightness(0) invert(1);
        }

        .checkout-header .lock-icon {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            margin-top: 8px;
        }

        .checkout-body {
            padding: 28px 30px 30px;
            animation: fadeIn 0.6s ease-out 0.2s both;
        }

        .amount-display {
            background: #fff8f0;
            border: 2px solid #fde4c8;
            border-radius: 12px;
            padding: 18px 20px;
            text-align: center;
            margin-bottom: 24px;
            transition: border-color 0.3s;
        }

        .amount-display.has-error {
            border-color: #dc3545;
            animation: shake 0.4s ease-out;
        }

        .amount-display .label {
            font-size: 13px;
            color: #6b7a8f;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .amount-display .amount {
            font-size: 28px;
            font-weight: 700;
            color: #d35400;
        }

        .amount-display .amount .currency {
            font-size: 16px;
            color: #6b7a8f;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #3d4a5c;
            margin-bottom: 6px;
        }

        .form-group label .required {
            color: #dc3545;
        }

        .form-group .input-wrapper {
            position: relative;
        }

        .form-group .input-wrapper .prefix {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7a8f;
            font-size: 14px;
            font-weight: 500;
            pointer-events: none;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e0e5ec;
            border-radius: 10px;
            font-size: 16px;
            color: #2d3748;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #fff;
        }

        .form-group input:focus {
            border-color: #e67e22;
            box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.12);
        }

        .form-group input.has-error {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .form-group input.phone-input {
            padding-left: 52px;
        }

        .form-group .hint {
            font-size: 12px;
            color: #6b7a8f;
            margin-top: 5px;
        }

        .form-group .hint.success-hint { color: #1a7d36; }
        .form-group .hint.fail-hint { color: #dc3545; }

        .btn-group {
            display: flex;
            gap: 12px;
        }

        .btn-group .submit-btn {
            flex: 2;
        }

        .btn-group .cancel-btn {
            flex: 1;
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, #e67e22, #d35400);
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s, opacity 0.3s;
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(211, 84, 0, 0.3);
        }

        .submit-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .submit-btn .btn-text {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: opacity 0.2s;
        }

        .submit-btn .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            position: absolute;
            top: 50%;
            left: 50%;
            margin: -10px 0 0 -10px;
        }

        .submit-btn.loading .btn-text { opacity: 0; }
        .submit-btn.loading .spinner { display: block; }

        .submit-btn.success {
            background: linear-gradient(135deg, #1a7d36, #155d27);
        }

        .submit-btn.error {
            background: linear-gradient(135deg, #dc3545, #a71d2a);
        }

        .submit-btn.error:hover,
        .submit-btn.success:hover {
            transform: none;
            box-shadow: none;
        }

        .cancel-btn {
            width: 100%;
            padding: 14px;
            border: 2px solid #e0e5ec;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            color: #6b7a8f;
            background: #fff;
            cursor: pointer;
            transition: border-color 0.2s, color 0.2s, background 0.2s;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .cancel-btn:hover {
            border-color: #dc3545;
            color: #dc3545;
            background: #fff5f5;
        }

        .checkout-footer {
            text-align: center;
            padding: 0 30px 24px;
            animation: fadeIn 0.6s ease-out 0.3s both;
        }

        .checkout-footer .secured-by {
            font-size: 11px;
            color: #9aa9bb;
            margin-bottom: 4px;
        }

        .checkout-footer .secured-by i {
            font-size: 10px;
        }

        .checkout-footer .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #6b7a8f;
            background: #f0f2f5;
            padding: 4px 12px;
            border-radius: 20px;
        }

        /* Status messages */
        .status-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease-out;
        }

        .status-overlay.active {
            display: flex;
        }

        .status-modal {
            background: #fff;
            border-radius: 16px;
            padding: 40px 36px 32px;
            text-align: center;
            max-width: 380px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            animation: slideUp 0.4s ease-out;
        }

        .status-modal .icon {
            margin-bottom: 16px;
        }

        .status-modal .icon svg {
            width: 56px;
            height: 56px;
        }

        .status-modal h3 {
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 6px;
        }

        .status-modal p {
            font-size: 14px;
            color: #6b7a8f;
            margin-bottom: 20px;
        }

        .status-modal .btn-close-modal {
            padding: 10px 28px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .status-modal .btn-close-modal:hover {
            opacity: 0.85;
        }

        .status-modal.success .btn-close-modal {
            background: #1a7d36;
            color: #fff;
        }

        .status-modal.error .btn-close-modal {
            background: #dc3545;
            color: #fff;
        }

        @media (max-width: 480px) {
            body { padding: 12px; }
            .checkout-header { padding: 24px 20px 18px; }
            .checkout-body { padding: 22px 20px 24px; }
            .checkout-footer { padding: 0 20px 20px; }
            .amount-display .amount { font-size: 24px; }
            .btn-group { flex-direction: column; }
        }
    </style>
</head>
<body>

    <div class="checkout-card">
        <div class="checkout-header">
            <img src="{{ url('frontend/asset/images/brand/logo-white.png') }}" alt="Alogoto" onerror="this.style.display='none'">
            <div class="lock-icon">
                <i class="bi bi-lock-fill"></i>
                <span>Paiement sécurisé</span>
            </div>
        </div>

        <div class="checkout-body">
            <div class="amount-display" id="amountDisplay">
                <div class="label">Montant à payer</div>
                <div class="amount">{{ number_format($transaction->amount, 0, ',', ' ') }} <span class="currency">XOF</span></div>
            </div>

            <form id="paymentForm" method="POST" action="{{ request()->url() }}">
                @csrf
                <input type="hidden" name="amount" value="{{ $transaction->amount }}">

                <div class="form-group">
                    <label>Numéro de téléphone <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <span class="prefix">+229</span>
                        <input type="tel" name="phone" class="phone-input" id="phoneInput"
                               value="0168552584"
                               placeholder="XX XX XX XX"
                               maxlength="10" required autocomplete="off">
                    </div>
                    <div class="hint" id="phoneHint">
                        Test : <strong class="success-hint">0168552584</strong> (succès) —
                        <strong class="fail-hint">0143440858</strong> (solde insuffisant)
                    </div>
                </div>

                <div class="btn-group">
                    <button type="submit" class="submit-btn" id="submitBtn">
                        <span class="btn-text">
                            <i class="bi bi-check-circle"></i>
                            Payer {{ number_format($transaction->amount, 0, ',', ' ') }} XOF
                        </span>
                        <span class="spinner"></span>
                    </button>
                    <a href="{{ route('payment.bypass.cancel', ['transaction' => $transaction->id]) }}" class="cancel-btn" id="cancelBtn">
                        <i class="bi bi-x-circle"></i> Annuler
                    </a>
                </div>
            </form>
        </div>

        <div class="checkout-footer">
            <div class="secured-by">
                <i class="bi bi-shield-check"></i> Connexion sécurisée
            </div>
            <div class="badge">
                <i class="bi bi-lightning-fill"></i>
                Mode test (Sandbox)
            </div>
        </div>
    </div>

    <!-- Status overlay -->
    <div class="status-overlay" id="statusOverlay">
        <div class="status-modal" id="statusModal">
            <div class="icon" id="statusIcon">
            </div>
            <h3 id="statusTitle">Traitement...</h3>
            <p id="statusMessage">Veuillez patienter</p>
            <button class="btn-close-modal" id="statusBtn" style="display:none;">Fermer</button>
        </div>
    </div>

    <script>
        const form = document.getElementById('paymentForm');
        const submitBtn = document.getElementById('submitBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const phoneInput = document.getElementById('phoneInput');
        const phoneHint = document.getElementById('phoneHint');
        const amountDisplay = document.getElementById('amountDisplay');
        const statusOverlay = document.getElementById('statusOverlay');
        const statusModal = document.getElementById('statusModal');
        const statusIcon = document.getElementById('statusIcon');
        const statusTitle = document.getElementById('statusTitle');
        const statusMessage = document.getElementById('statusMessage');
        const statusBtn = document.getElementById('statusBtn');

        const SUCCESS_PHONE = '0168552584';
        const FAIL_PHONE = '0143440858';

        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            amountDisplay.classList.remove('has-error');
            this.classList.remove('has-error');

            if (this.value === FAIL_PHONE) {
                phoneHint.innerHTML = 'Test : <strong class="success-hint">0168552584</strong> (succès) — <strong class="fail-hint">0143440858</strong> (solde insuffisant) — <span class="fail-hint">solde insuffisant</span>';
            } else if (this.value === SUCCESS_PHONE) {
                phoneHint.innerHTML = 'Test : <strong class="success-hint">0168552584</strong> (succès) — <strong class="fail-hint">0143440858</strong> (solde insuffisant) — <span class="success-hint">paiement réussi</span>';
            } else {
                phoneHint.innerHTML = 'Test : <strong class="success-hint">0168552584</strong> (succès) — <strong class="fail-hint">0143440858</strong> (solde insuffisant)';
            }
        });

        phoneInput.addEventListener('blur', function() {
            if (this.value.length > 0 && this.value.length < 10) {
                this.classList.add('has-error');
                amountDisplay.classList.add('has-error');
            } else {
                this.classList.remove('has-error');
                amountDisplay.classList.remove('has-error');
            }
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const phone = phoneInput.value.replace(/\D/g, '');

            if (phone.length < 10) {
                phoneInput.classList.add('has-error');
                amountDisplay.classList.add('has-error');
                phoneInput.focus();
                return;
            }

            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            cancelBtn.style.pointerEvents = 'none';
            cancelBtn.style.opacity = '0.5';

            statusOverlay.classList.add('active');
            statusModal.className = 'status-modal';
            statusIcon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="#e67e22" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-dasharray="50" stroke-dashoffset="50"><animate attributeName="stroke-dashoffset" values="50;0" dur="1.5s" repeatCount="indefinite"/></circle></svg>';
            statusTitle.textContent = 'Traitement en cours...';
            statusMessage.textContent = 'Veuillez patienter pendant le traitement de votre paiement.';
            statusBtn.style.display = 'none';

            setTimeout(function() {
                if (phone === FAIL_PHONE) {
                    statusModal.className = 'status-modal error';
                    statusIcon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6" stroke-linecap="round"/></svg>';
                    statusTitle.textContent = 'Paiement échoué';
                    statusMessage.textContent = 'Solde insuffisant. Veuillez réessayer avec un autre numéro ou moyen de paiement.';
                    statusBtn.textContent = 'Réessayer';
                    statusBtn.style.display = 'inline-block';
                    statusBtn.className = 'btn-close-modal';
                    statusBtn.onclick = function() {
                        statusOverlay.classList.remove('active');
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                        cancelBtn.style.pointerEvents = '';
                        cancelBtn.style.opacity = '';
                        submitBtn.className = 'submit-btn error';
                        setTimeout(function() {
                            submitBtn.className = 'submit-btn';
                        }, 1000);
                    };

                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                    cancelBtn.style.pointerEvents = '';
                    cancelBtn.style.opacity = '';
                    submitBtn.className = 'submit-btn error';
                    setTimeout(function() {
                        submitBtn.className = 'submit-btn';
                    }, 2000);
                } else {
                    statusModal.className = 'status-modal success';
                    statusIcon.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="#1a7d36" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12l3 3 5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                    statusTitle.textContent = 'Paiement réussi !';
                    statusMessage.textContent = 'Votre paiement de {{ number_format($transaction->amount, 0, ",", " ") }} XOF a été confirmé. Vous allez être redirigé...';
                    statusBtn.style.display = 'none';

                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                    submitBtn.className = 'submit-btn success';

                    setTimeout(function() {
                        const realSubmit = document.createElement('button');
                        realSubmit.type = 'submit';
                        realSubmit.style.display = 'none';
                        form.appendChild(realSubmit);
                        submitBtn.classList.remove('loading', 'success', 'error');
                        submitBtn.disabled = true;
                        form.submit();
                    }, 1500);
                }
            }, 1500);
        });

        window.addEventListener('load', function() {
            setTimeout(function() {
                amountDisplay.style.borderColor = '#e67e22';
            }, 300);
            setTimeout(function() {
                amountDisplay.style.borderColor = '#fde4c8';
            }, 800);
        });
    </script>
</body>
</html>