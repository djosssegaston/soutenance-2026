<?php require_once __DIR__ . '/header.php'; ?>
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title text-uppercase fw-bold">Sécurité & Conformité</h1>
                    <p class="text-muted mb-0">Contrôle d'accès, sessions, authentification et politique de sécurité.</p>
                </div>
                <div><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Admin</a></li><li class="breadcrumb-item active">Sécurité</li></ol></div>
            </div>

            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Sessions Actives</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-sessions">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-primary-transparent text-primary">
                                        <i class="fe fe-monitor"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-primary fw-bold"><i class="fa fa-users"></i> connectés</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Tentatives Échouées</p>
                                    <h3 class="mb-0 number-font fw-bold text-danger" id="kpi-failed-logins">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-danger-transparent text-danger">
                                        <i class="fe fe-lock"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-danger fw-bold"><i class="fa fa-lock"></i> dernières 24h</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Comptes Bloqués</p>
                                    <h3 class="mb-0 number-font fw-bold text-warning" id="kpi-blocked">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-warning-transparent text-warning">
                                        <i class="fe fe-user-x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-warning fw-bold"><i class="fa fa-ban"></i> verrouillés</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Score Sécurité</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-score">—</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-success-transparent text-success">
                                        <i class="fe fe-shield"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-success fw-bold"><i class="fa fa-shield"></i> plateforme</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Politique de sécurité -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fe fe-shield me-2"></i>Politique de Sécurité</h3></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="sec-2fa" checked>
                                    <label class="form-check-label fw-semibold" for="sec-2fa">Authentification 2FA obligatoire (admin)</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="sec-ip-whitelist">
                                    <label class="form-check-label fw-semibold" for="sec-ip-whitelist">Whitelist IP activée</label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="sec-brute-force" checked>
                                    <label class="form-check-label fw-semibold" for="sec-brute-force">Protection brute-force</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sec-session-timeout" checked>
                                    <label class="form-check-label fw-semibold" for="sec-session-timeout">Expiration session (30 min)</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Max tentatives avant blocage</label>
                                <input type="number" class="form-control" value="5" id="sec-max-attempts">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Longueur min. mot de passe</label>
                                <input type="number" class="form-control" value="8" id="sec-min-pwd">
                            </div>
                            <button class="btn btn-primary" id="btn-save-security"><i class="fe fe-save me-1"></i>Enregistrer</button>
                        </div>
                    </div>
                </div>

                <!-- Sessions actives -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title"><i class="fe fe-monitor me-2"></i>Sessions Actives</h3>
                            <button class="btn btn-sm btn-outline-danger" id="btn-kill-all"><i class="fe fe-x-circle me-1"></i>Tout déconnecter</button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead><tr>
                                        <th class="border-bottom-0">Utilisateur</th>
                                        <th class="border-bottom-0">IP</th>
                                        <th class="border-bottom-0">Depuis</th>
                                        <th class="border-bottom-0">Action</th>
                                    </tr></thead>
                                    <tbody id="sessions-tbody">
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Chargement...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Tentatives échouées récentes -->
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fe fe-alert-triangle me-2"></i>Tentatives Échouées Récentes</h3></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead><tr>
                                        <th class="border-bottom-0">Email</th>
                                        <th class="border-bottom-0">IP</th>
                                        <th class="border-bottom-0">Date</th>
                                        <th class="border-bottom-0">Raison</th>
                                    </tr></thead>
                                    <tbody id="failed-tbody">
                                        <tr><td colspan="4" class="text-center py-4 text-muted">Chargement...</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/footer.php'; ?>
<div class="alert alert-info mx-3 mt-3">
    <i class="fe fe-info me-2"></i>
    <strong>En cours de développement :</strong> La gestion de sécurité nécessite des endpoints backend spécifiques. Les contrôles ci-dessous seront fonctionnels dans une prochaine version.
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const API_BASE = '/api/v1';
    const JSON_HEADERS = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' };

    loadSecurityData();

    async function loadSecurityData() {
        try {
            const res = await fetch(`${API_BASE}/admin/security`, { headers: JSON_HEADERS });
            if (res.ok) {
                const body = await res.json();
                const data = body.data || body;
                if (data.sessions || data.sessions === 0) document.getElementById('kpi-sessions').textContent = data.sessions;
                if (data.failed_logins || data.failed_logins === 0) document.getElementById('kpi-failed-logins').textContent = data.failed_logins;
                if (data.blocked || data.blocked === 0) document.getElementById('kpi-blocked').textContent = data.blocked;
            }
        } catch (e) {}
    }

    document.getElementById('btn-save-security').addEventListener('click', async function() {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        try {
            const res = await fetch(`${API_BASE}/admin/security`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({
                    max_attempts: document.getElementById('sec-max-attempts')?.value,
                    min_password_length: document.getElementById('sec-min-pwd')?.value
                })
            });
            if (res.ok) {
                ModalHelper.success('Politique de sécurité', 'Politique de sécurité mise à jour.');
            } else {
                ModalHelper.alert('<i class="bi bi-info-circle me-2 text-info"></i> Information', 'Fonctionnalité en cours de développement.');
            }
        } catch (e) {
            ModalHelper.alert('<i class="bi bi-info-circle me-2 text-info"></i> Information', 'Fonctionnalité en cours de développement.');
        }
    });

    document.getElementById('btn-kill-all').addEventListener('click', async function() {
        const confirmed = await ModalHelper.confirm('<i class="bi bi-shield-exclamation me-2 text-danger"></i> Déconnexion', 'Déconnecter toutes les sessions ?', 'Oui, déconnecter', 'Annuler', 'btn-danger');
        if (confirmed) {
            ModalHelper.alert('<i class="bi bi-info-circle me-2 text-info"></i> Information', 'Fonctionnalité en cours de développement.');
        }
    });
});
</script>
