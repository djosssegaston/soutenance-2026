<?php require_once __DIR__ . '/header.php'; ?>
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title text-uppercase fw-bold">Paramètres de la Plateforme</h1>
                    <p class="text-muted mb-0">Configuration générale, seuils, maintenance et préférences système.</p>
                </div>
                <div><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Admin</a></li><li class="breadcrumb-item active">Paramètres</li></ol></div>
            </div>

            <div class="row">
                <!-- Paramètres généraux -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fe fe-settings me-2"></i>Configuration Générale</h3></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nom de la Plateforme</label>
                                <input type="text" id="setting-platform-name" name="platform_name" class="form-control" value="Alogoto">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email de Contact</label>
                                <input type="email" id="setting-contact-email" name="contact_email" class="form-control" value="contact@alogoto.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Devise par Défaut</label>
                                <select id="setting-currency" name="currency" class="form-control">
                                    <option value="XOF" selected>FCFA (Franc CFA)</option>
                                    <option value="EUR">EUR (Euro)</option>
                                    <option value="USD">USD (Dollar)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Langue par Défaut</label>
                                <select id="setting-language" name="language" class="form-control">
                                    <option value="fr" selected>Français</option>
                                    <option value="en">English</option>
                                </select>
                            </div>
                            <button class="btn btn-primary" id="btn-save-general"><i class="fe fe-save me-1"></i>Enregistrer</button>
                        </div>
                    </div>
                </div>

                <!-- Seuils financiers -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fe fe-sliders me-2"></i>Seuils et Limites</h3></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Montant Min. Financement (FCFA)</label>
                                <input type="number" id="setting-min-amount" name="min_amount" class="form-control" value="100000">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Montant Max. Financement (FCFA)</label>
                                <input type="number" id="setting-max-amount" name="max_amount" class="form-control" value="50000000">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Taux d'Intérêt par Défaut (%)</label>
                                <input type="number" id="setting-interest-rate" name="interest_rate" class="form-control" step="0.1" value="5.0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Durée Max. Remboursement (mois)</label>
                                <input type="number" id="setting-max-duration" name="max_duration" class="form-control" value="60">
                            </div>
                            <button class="btn btn-primary" id="btn-save-thresholds"><i class="fe fe-save me-1"></i>Enregistrer</button>
                        </div>
                    </div>
                </div>

                <!-- Mode maintenance -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fe fe-tool me-2"></i>Mode Maintenance</h3></div>
                        <div class="card-body">
                            <div class="alert alert-warning mb-3">
                                <i class="fe fe-alert-triangle me-2"></i>Activer le mode maintenance rendra la plateforme inaccessible aux utilisateurs.
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="setting-maintenance-mode" name="maintenance_mode">
                                    <label class="form-check-label fw-semibold" for="setting-maintenance-mode">Activer le Mode Maintenance</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Message de Maintenance</label>
                                <textarea id="setting-maintenance-msg" name="maintenance_msg" class="form-control" rows="3">La plateforme est en maintenance. Veuillez réessayer ultérieurement.</textarea>
                            </div>
                            <button class="btn btn-warning" id="btn-save-maintenance"><i class="fe fe-save me-1"></i>Appliquer</button>
                        </div>
                    </div>
                </div>

                <!-- Notifications système -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fe fe-bell me-2"></i>Notifications Système</h3></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="setting-notif-email" name="notif_email" checked>
                                    <label class="form-check-label" for="setting-notif-email">Notifications par email</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="setting-notif-sms" name="notif_sms">
                                    <label class="form-check-label" for="setting-notif-sms">Notifications par SMS</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="setting-notif-push" name="notif_push" checked>
                                    <label class="form-check-label" for="setting-notif-push">Notifications push</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="setting-notif-audit" name="notif_audit" checked>
                                    <label class="form-check-label" for="setting-notif-audit">Alertes d'audit critiques</label>
                                </div>
                            </div>
                            <button class="btn btn-primary" id="btn-save-notif"><i class="fe fe-save me-1"></i>Enregistrer</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const API_BASE = '/api/v1';
    const JSON_HEADERS = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' };

    loadSettings();

    async function loadSettings() {
        try {
            const res = await fetch(`${API_BASE}/admin/settings`, { headers: JSON_HEADERS });
            if (!res.ok) return;
            const body = await res.json();
            const d = body.data || body;
            const map = {
                'setting-platform-name': 'platform_name',
                'setting-contact-email': 'contact_email',
                'setting-currency': 'currency',
                'setting-language': 'language',
                'setting-min-amount': 'min_amount',
                'setting-max-amount': 'max_amount',
                'setting-interest-rate': 'interest_rate',
                'setting-max-duration': 'max_duration',
                'setting-maintenance-mode': 'maintenance_mode',
                'setting-maintenance-msg': 'maintenance_msg',
                'setting-notif-email': 'notif_email',
                'setting-notif-sms': 'notif_sms',
                'setting-notif-push': 'notif_push',
                'setting-notif-audit': 'notif_audit',
            };
            for (const [elId, key] of Object.entries(map)) {
                const el = document.getElementById(elId);
                if (!el || d[key] === undefined) continue;
                if (el.type === 'checkbox') el.checked = d[key] === '1' || d[key] === true;
                else el.value = d[key];
            }
        } catch (e) {
            console.warn('Settings load error:', e);
        }
    }

    function collectCard(formId) {
        const card = document.getElementById(formId)?.closest('.card') || document;
        const data = {};
        card.querySelectorAll('input, select, textarea').forEach(el => {
            if (el.name && !el.disabled) {
                data[el.name] = el.type === 'checkbox' ? (el.checked ? '1' : '0') : el.value;
            }
        });
        return data;
    }

    async function saveSettings(data, btn) {
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Enregistrement...';
        }
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch(`${API_BASE}/admin/settings`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if (result.success) {
                if (typeof ALOGOTO !== 'undefined') ALOGOTO.success('Paramètres enregistrés.');
                else ModalHelper.success('Paramètres enregistrés', 'Paramètres enregistrés avec succès.');
            } else {
                if (typeof ALOGOTO !== 'undefined') ALOGOTO.error(result.message || 'Erreur de sauvegarde.');
                else ModalHelper.error('Erreur de sauvegarde', result.message || 'Erreur lors de la sauvegarde.');
            }
        } catch (e) {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur réseau : ' + e.message);
            else ModalHelper.error('Erreur réseau', 'Erreur réseau : ' + e.message);
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = btn.dataset.originalText || 'Enregistrer';
            }
        }
    }

    document.getElementById('btn-save-general')?.addEventListener('click', function() {
        const data = collectCard('setting-platform-name');
        saveSettings(data, this);
    });
    document.getElementById('btn-save-thresholds')?.addEventListener('click', function() {
        const data = collectCard('setting-min-amount');
        saveSettings(data, this);
    });
    document.getElementById('btn-save-maintenance')?.addEventListener('click', function() {
        const data = collectCard('setting-maintenance-mode');
        saveSettings(data, this);
    });
    document.getElementById('btn-save-notif')?.addEventListener('click', function() {
        const data = collectCard('setting-notif-email');
        saveSettings(data, this);
    });
});
</script>
