<?php
require_once __DIR__ . '/header.php';

$userSessions = $userSessions ?? [];
$auditLogs = $auditLogs ?? [];
$securityStats = $securityStats ?? [
    'active_sessions' => 0,
    'password_updated' => 'Jamais',
    'failed_attempts' => 0,
    'total_logs' => 0,
];
$activeSection = $_GET['section'] ?? 'password';
?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title"><strong>SÉCURITÉ</strong></h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">ACCEUIL</a></li>
                        <li class="breadcrumb-item active" aria-current="page">SÉCURITÉ</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">SESSIONS ACTIVES</span>
                                    <h2 class="mb-0 mt-1"><?php echo $securityStats['active_sessions']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-primary">
                                        <i class="bi bi-laptop fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">DERNIER MDP</span>
                                    <h2 class="mb-0 mt-1 fs-6"><?php echo htmlspecialchars($securityStats['password_updated'], ENT_QUOTES, 'UTF-8'); ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-success">
                                        <i class="bi bi-key fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">TENTATIVES ÉCHOUÉES</span>
                                    <h2 class="mb-0 mt-1"><?php echo $securityStats['failed_attempts']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-warning">
                                        <i class="bi bi-shield-exclamation fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">ÉVÉNEMENTS</span>
                                    <h2 class="mb-0 mt-1"><?php echo $securityStats['total_logs']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-info">
                                        <i class="bi bi-clock-history fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h4 class="card-title mb-0">Paramètres de sécurité</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="d-flex flex-wrap border-bottom">
                                <a class="nav-link px-4 py-3 <?php echo $activeSection === 'password' ? 'active fw-bold text-primary border-bottom border-primary border-2' : 'text-muted'; ?>" href="?section=password">
                                    <i class="bi bi-key me-2"></i>Mot de passe
                                </a>
                                <a class="nav-link px-4 py-3 <?php echo $activeSection === 'sessions' ? 'active fw-bold text-primary border-bottom border-primary border-2' : 'text-muted'; ?>" href="?section=sessions">
                                    <i class="bi bi-laptop me-2"></i>Sessions & appareils
                                </a>
                                <a class="nav-link px-4 py-3 <?php echo $activeSection === '2fa' ? 'active fw-bold text-primary border-bottom border-primary border-2' : 'text-muted'; ?>" href="?section=2fa">
                                    <i class="bi bi-shield-lock me-2"></i>Double authentification
                                </a>
                                <a class="nav-link px-4 py-3 <?php echo $activeSection === 'documents' ? 'active fw-bold text-primary border-bottom border-primary border-2' : 'text-muted'; ?>" href="?section=documents">
                                    <i class="bi bi-file-earmark-lock me-2"></i>Sécurité documents
                                </a>
                                <a class="nav-link px-4 py-3 <?php echo $activeSection === 'historique' ? 'active fw-bold text-primary border-bottom border-primary border-2' : 'text-muted'; ?>" href="?section=historique">
                                    <i class="bi bi-clock-history me-2"></i>Historique
                                </a>
                            </div>

                            <div class="p-4">
<?php if ($activeSection === 'password'): ?>
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <h5 class="fw-bold mb-3"><i class="bi bi-key me-2 text-primary"></i>Changer le mot de passe</h5>
                                        <div class="mb-3">
                                            <label class="form-label">Mot de passe actuel</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="currentPassword" autocomplete="current-password">
                                                <button class="btn btn-outline-secondary toggle-pwd" type="button"><i class="bi bi-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nouveau mot de passe</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="newPassword" autocomplete="new-password">
                                                <button class="btn btn-outline-secondary toggle-pwd" type="button"><i class="bi bi-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Confirmer le mot de passe</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="confirmPassword" autocomplete="new-password">
                                                <button class="btn btn-outline-secondary toggle-pwd" type="button"><i class="bi bi-eye"></i></button>
                                            </div>
                                        </div>
                                        <div id="passwordError" class="alert alert-danger d-none"></div>
                                        <div id="passwordSuccess" class="alert alert-success d-none"></div>
                                        <button class="btn btn-primary" id="changePasswordBtn"><i class="bi bi-check-lg me-2"></i>Modifier le mot de passe</button>
                                    </div>
                                    <div class="col-lg-6">
                                        <h5 class="fw-bold mb-3"><i class="bi bi-shield-check me-2 text-success"></i>Exigences mot de passe fort</h5>
                                        <div id="passwordStrength">
                                            <div class="progress mb-3" style="height:8px;">
                                                <div class="progress-bar" id="strengthBar" role="progressbar" style="width:0%;background:#dc3545;"></div>
                                            </div>
                                            <p class="mb-2 fw-semibold" id="strengthLabel">Entrez un mot de passe</p>
                                            <ul class="list-unstyled mb-4" id="strengthChecks">
                                                <li class="mb-1 text-muted" data-check="length"><i class="bi bi-circle me-2"></i>8 caractères minimum</li>
                                                <li class="mb-1 text-muted" data-check="upper"><i class="bi bi-circle me-2"></i>Une majuscule</li>
                                                <li class="mb-1 text-muted" data-check="lower"><i class="bi bi-circle me-2"></i>Une minuscule</li>
                                                <li class="mb-1 text-muted" data-check="number"><i class="bi bi-circle me-2"></i>Un chiffre</li>
                                                <li class="mb-1 text-muted" data-check="special"><i class="bi bi-circle me-2"></i>Un caractère spécial</li>
                                            </ul>
                                        </div>
                                        <h5 class="fw-bold mb-3 mt-4"><i class="bi bi-clock-history me-2 text-info"></i>Historique des changements</h5>
                                        <div id="passwordHistory"><div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></div></div>
                                    </div>
                                </div>

<?php elseif ($activeSection === 'sessions'): ?>
                                <div>
                                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                                        <h5 class="fw-bold mb-0"><i class="bi bi-laptop me-2 text-primary"></i>Appareils connectés</h5>
                                        <button class="btn btn-sm btn-outline-danger" id="killAllSessionsBtn"><i class="bi bi-x-circle me-1"></i>Déconnecter tout</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead><tr><th>Appareil</th><th>Navigateur</th><th>IP</th><th>Dernière activité</th><th>Action</th></tr></thead>
                                            <tbody id="sessionsTableBody">
                                            <?php foreach ($userSessions as $session): ?>
                                                <tr data-session-id="<?php echo htmlspecialchars($session['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                    <td><i class="bi bi-<?php echo $session['device'] === 'Mobile' ? 'phone' : ($session['device'] === 'Tablette' ? 'tablet' : 'laptop'); ?> me-2"></i><?php echo htmlspecialchars($session['device'] ?? 'Inconnu', ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($session['browser'] ?? 'Inconnu', ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($session['platform'] ?? '?', ENT_QUOTES, 'UTF-8'); ?>)</td>
                                                    <td><?php echo htmlspecialchars($session['ip_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td>
                                                        <?php echo htmlspecialchars($session['last_activity_ago'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                                        <?php if ($session['is_current'] ?? false): ?><span class="badge bg-success-transparent text-success ms-2">Actuelle</span><?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!($session['is_current'] ?? false)): ?>
                                                        <button class="btn btn-sm btn-outline-danger kill-session" data-session-id="<?php echo htmlspecialchars($session['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-x-lg"></i></button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($userSessions)): ?><tr><td colspan="5" class="text-center py-4 text-muted">Aucune session trouvée</td></tr><?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-center mt-3" id="sessionsPagination"></div>
                                </div>

<?php elseif ($activeSection === '2fa'): ?>
                                <div>
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>La double authentification ajoute une couche de sécurité supplémentaire à votre compte. Activez-la pour protéger vos projets et documents sensibles.
                                    </div>
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="card border">
                                                <div class="card-body text-center">
                                                    <i class="bi bi-chat-dots fs-1 text-primary mb-3 d-block"></i>
                                                    <h5 class="fw-bold">OTP par SMS</h5>
                                                    <p class="text-muted small">Recevez un code unique par SMS à chaque connexion.</p>
                                                    <button class="btn btn-outline-primary btn-sm disabled" disabled><i class="bi bi-hourglass-split me-1"></i>Bientôt disponible</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="card border">
                                                <div class="card-body text-center">
                                                    <i class="bi bi-envelope fs-1 text-warning mb-3 d-block"></i>
                                                    <h5 class="fw-bold">OTP par Email</h5>
                                                    <p class="text-muted small">Recevez un code unique par email à chaque connexion.</p>
                                                    <button class="btn btn-outline-primary btn-sm" id="enableOtpEmail"><i class="bi bi-envelope-check me-1"></i>Activer</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="card border">
                                                <div class="card-body text-center">
                                                    <i class="bi bi-google fs-1 text-danger mb-3 d-block"></i>
                                                    <h5 class="fw-bold">Google Authenticator</h5>
                                                    <p class="text-muted small">Utilisez l'application Google Authenticator pour générer des codes.</p>
                                                    <button class="btn btn-outline-primary btn-sm disabled" disabled><i class="bi bi-hourglass-split me-1"></i>Bientôt disponible</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="card border">
                                                <div class="card-body text-center">
                                                    <i class="bi bi-key fs-1 text-success mb-3 d-block"></i>
                                                    <h5 class="fw-bold">Codes de récupération</h5>
                                                    <p class="text-muted small">Générez des codes de secours au cas où vous perdriez l'accès à votre 2FA.</p>
                                                    <button class="btn btn-outline-secondary btn-sm disabled" disabled><i class="bi bi-hourglass-split me-1"></i>Bientôt disponible</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

<?php elseif ($activeSection === 'documents'): ?>
                                <div>
                                    <div class="alert alert-warning">
                                        <i class="bi bi-shield-exclamation me-2"></i>Vos documents sont protégés par plusieurs couches de sécurité. Seul vous (le porteur de projet) pouvez y accéder et les télécharger.
                                    </div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2"><i class="bi bi-lock-fill text-success fs-4 me-3"></i><div><h6 class="fw-bold mb-0">Chiffrement des documents</h6><small class="text-muted">Tous les documents sont chiffrés au repos</small></div></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2"><i class="bi bi-person-lock text-primary fs-4 me-3"></i><div><h6 class="fw-bold mb-0">Accès propriétaire</h6><small class="text-muted">Seul le porteur du projet peut voir ses documents</small></div></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2"><i class="bi bi-download-off text-danger fs-4 me-3"></i><div><h6 class="fw-bold mb-0">Téléchargement protégé</h6><small class="text-muted">Téléchargement interdit aux tiers non autorisés</small></div></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2"><i class="bi bi-water text-info fs-4 me-3"></i><div><h6 class="fw-bold mb-0">Watermark dynamique</h6><small class="text-muted">Les documents prévisualisés contiennent un filigrane</small></div></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2"><i class="bi bi-journal-text text-warning fs-4 me-3"></i><div><h6 class="fw-bold mb-0">Journalisation complète</h6><small class="text-muted">Chaque ouverture de document est enregistrée</small></div></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2"><i class="bi bi-clock text-secondary fs-4 me-3"></i><div><h6 class="fw-bold mb-0">Expiration de partage</h6><small class="text-muted">Les liens de partage expirent automatiquement</small></div></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2"><i class="bi bi-building text-info fs-4 me-3"></i><div><h6 class="fw-bold mb-0">Contrôle accès IMF</h6><small class="text-muted">Les institutions voient uniquement les documents validés</small></div></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-2"><i class="bi bi-eye-slash text-danger fs-4 me-3"></i><div><h6 class="fw-bold mb-0">Alerte consultation suspecte</h6><small class="text-muted">Vous êtes notifié en cas d'accès anormal</small></div></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

<?php elseif ($activeSection === 'historique'): ?>
                                <div>
                                    <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Journal d'activité</h5>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead><tr><th>Action</th><th>IP</th><th>Date</th></tr></thead>
                                            <tbody id="auditLogsBody">
                                            <?php foreach ($auditLogs as $log): ?>
                                                <tr>
                                                    <td><i class="bi <?php echo htmlspecialchars($log['icon'] ?? 'bi-clock', ENT_QUOTES, 'UTF-8'); ?> text-<?php echo htmlspecialchars($log['color'] ?? 'secondary', ENT_QUOTES, 'UTF-8'); ?> me-2"></i><?php echo htmlspecialchars($log['action'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($log['ip'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($log['date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if (empty($auditLogs)): ?><tr><td colspan="3" class="text-center py-4 text-muted">Aucun événement enregistré</td></tr><?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-center mt-3" id="auditLogsPagination"></div>
                                </div>
<?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalTitle"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Confirmer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"><p id="confirmModalMessage"></p></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn" id="confirmModalBtn">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<script>
const csrfToken = '<?php echo $csrf_token ?? ""; ?>';
var confirmModal = null;
var confirmCallback = null;

function execAction(url, method, body, cb) {
    fetch(url, {
        method: method,
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        credentials: 'same-origin',
        body: body ? JSON.stringify(body) : null
    })
    .then(function(r) { return r.json().catch(function(){return {};}).then(function(d){return {status:r.status,data:d};}); })
    .then(function(resp) {
        if (resp.data && resp.data.message) { if (cb) cb(resp.data); }
        else if (resp.status >= 400) { showError(resp.data && resp.data.message ? resp.data.message : 'Erreur ' + resp.status); }
    })
    .catch(function() { showError('Erreur de connexion'); });
}

function stripHtml(str) {
    return String(str || '').replace(/<[^>]*>/g, '');
}
function showConfirm(title, message, btnClass, btnText, callback) {
    document.getElementById('confirmModalTitle').innerHTML = title;
    document.getElementById('confirmModalMessage').textContent = stripHtml(message);
    var btn = document.getElementById('confirmModalBtn');
    btn.className = 'btn ' + btnClass;
    btn.textContent = btnText;
    confirmCallback = callback;
    confirmModal.show();
}

function showError(msg) {
    var el = document.getElementById('passwordError');
    if (el) { el.textContent = stripHtml(msg); el.classList.remove('d-none'); setTimeout(function(){el.classList.add('d-none');},5000); }
    else { ModalHelper.alert('<i class="bi bi-exclamation-circle me-2 text-danger"></i> Erreur', stripHtml(msg)); }
}

function showSuccess(msg) {
    var el = document.getElementById('passwordSuccess');
    if (el) { el.textContent = stripHtml(msg); el.classList.remove('d-none'); setTimeout(function(){el.classList.add('d-none');},5000); }
}

function loadPasswordHistory() {
    fetch('/dashboard/porteur/securite/password-history', {
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var el = document.getElementById('passwordHistory');
        if (!el) return;
        if (data.length === 0) { el.innerHTML = '<p class="text-muted small">Aucun changement enregistré</p>'; return; }
        var html = '<ul class="list-unstyled mb-0">';
        data.forEach(function(log) {
            html += '<li class="mb-2"><i class="bi bi-arrow-right-circle text-info me-2"></i>' + escHtml(log.date) + ' <small class="text-muted">(' + escHtml(log.ip) + ')</small></li>';
        });
        html += '</ul>';
        el.innerHTML = html;
    })
    .catch(function() {});
}

function checkPasswordStrength(password) {
    var checks = [
        { key: 'length', pass: password.length >= 8 },
        { key: 'upper', pass: /[A-Z]/.test(password) },
        { key: 'lower', pass: /[a-z]/.test(password) },
        { key: 'number', pass: /[0-9]/.test(password) },
        { key: 'special', pass: /[^a-zA-Z0-9]/.test(password) },
    ];
    var score = checks.filter(function(c) { return c.pass; }).length * 20;
    var bar = document.getElementById('strengthBar');
    var label = document.getElementById('strengthLabel');
    if (password.length === 0) {
        bar.style.width = '0%'; label.textContent = 'Entrez un mot de passe';
        return;
    }
    var color = score >= 80 ? '#198754' : score >= 60 ? '#0d6efd' : score >= 40 ? '#ffc107' : '#dc3545';
    var level = score >= 80 ? 'Très fort' : score >= 60 ? 'Fort' : score >= 40 ? 'Moyen' : 'Faible';
    bar.style.width = score + '%';
    bar.style.background = color;
    label.textContent = 'Niveau : ' + level;
    label.className = 'mb-2 fw-semibold text-' + (score >= 80 ? 'success' : score >= 60 ? 'primary' : score >= 40 ? 'warning' : 'danger');

    checks.forEach(function(c) {
        var li = document.querySelector('#strengthChecks li[data-check="' + c.key + '"]');
        if (li) {
            li.className = 'mb-1 ' + (c.pass ? 'text-success' : 'text-muted');
            li.innerHTML = (c.pass ? '<i class="bi bi-check-circle-fill me-2"></i>' : '<i class="bi bi-circle me-2"></i>') + li.textContent.trim();
        }
    });
}

// Pagination — defined at top level for global access
const PAGINATION_ROWS = 10;
function paginationGo(tbl, pag, page) {
    var nav = document.getElementById(pag); if (nav) nav.dataset.currentPage = page;
    updatePagination(tbl, pag); return false;
}
function resetPagination(tbl, pag) {
    var nav = document.getElementById(pag); if (nav) nav.dataset.currentPage = 1;
    updatePagination(tbl, pag);
}
function updatePagination(tbl, pag) {
    var tbody = document.getElementById(tbl);
    var nav = document.getElementById(pag);
    if (!tbody || !nav) return;
    var rows = Array.from(tbody.children).filter(function(r) {
        if (r.dataset && r.dataset.filtered === 'true') return false;
        if (r.querySelector('td[colspan]')) return false;
        return true;
    });
    var totalPages = Math.max(1, Math.ceil(rows.length / PAGINATION_ROWS));
    var cur = parseInt(nav.dataset.currentPage || '1', 10);
    if (cur > totalPages) cur = totalPages;
    nav.dataset.currentPage = cur;
    Array.from(tbody.children).forEach(function(r) {
        if (r.dataset && r.dataset.filtered === 'true') {
            r.style.display = 'none';
        } else if (!r.querySelector('td[colspan]')) {
            var idx = rows.indexOf(r);
            if (idx !== -1) {
                r.style.display = (idx >= (cur-1)*PAGINATION_ROWS && idx < cur*PAGINATION_ROWS) ? '' : 'none';
            }
        }
    });
    if (totalPages <= 1) { nav.innerHTML = ''; return; }
    var h = '<nav aria-label="Pagination"><ul class="pagination pagination-sm justify-content-center mb-0">';
    h += '<li class="page-item '+(cur<=1?'disabled':'')+'" data-page="'+(cur-1)+'"><a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a></li>';
    for (var i=1; i<=totalPages; i++) { h += '<li class="page-item '+(i===cur?'active':'')+'" data-page="'+i+'"><a class="page-link" href="#">'+i+'</a></li>'; }
    h += '<li class="page-item '+(cur>=totalPages?'disabled':'')+'" data-page="'+(cur+1)+'"><a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a></li>';
    h += '</ul></nav>';
    nav.innerHTML = h;
    nav.onclick = function(e) {
        var li = e.target.closest('[data-page]');
        if (!li || li.classList.contains('disabled')) return;
        var p = parseInt(li.getAttribute('data-page'), 10);
        if (isNaN(p)) return;
        e.preventDefault();
        paginationGo(tbl, pag, p);
    };
}

document.addEventListener('DOMContentLoaded', function() {
    // Tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });

    confirmModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('confirmModal'));

    updatePagination('auditLogsBody', 'auditLogsPagination');
    updatePagination('sessionsTableBody', 'sessionsPagination');

    // Toggle password visibility
    document.querySelectorAll('.toggle-pwd').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = this.parentElement.querySelector('input');
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
                this.querySelector('i').className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
            }
        });
    });

    var newPwd = document.getElementById('newPassword');
    if (newPwd) {
        newPwd.addEventListener('input', function() { checkPasswordStrength(this.value); });
    }

    document.getElementById('changePasswordBtn')?.addEventListener('click', function() {
        var current = document.getElementById('currentPassword').value;
        var password = document.getElementById('newPassword').value;
        var confirm = document.getElementById('confirmPassword').value;

        if (!current || !password || !confirm) { showError('Veuillez remplir tous les champs'); return; }
        if (password !== confirm) { showError('Les mots de passe ne correspondent pas'); return; }
        if (password.length < 8) { showError('Le mot de passe doit contenir au moins 8 caractères'); return; }

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Modification...';

        execAction('/dashboard/porteur/securite/password', 'POST', {
            current_password: current,
            password: password,
            password_confirmation: confirm
        }, function(data) {
            showSuccess(data.message || 'Mot de passe modifié avec succès');
            document.getElementById('currentPassword').value = '';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmPassword').value = '';
            checkPasswordStrength('');
            loadPasswordHistory();
            document.getElementById('changePasswordBtn').disabled = false;
            document.getElementById('changePasswordBtn').innerHTML = '<i class="bi bi-check-lg me-2"></i>Modifier le mot de passe';
        });
    });

    loadPasswordHistory();

    document.querySelectorAll('.kill-session').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var sessionId = this.getAttribute('data-session-id');
            var row = this.closest('tr');
            showConfirm('<i class="bi bi-x-circle me-2 text-danger"></i> Déconnecter', 'Déconnecter cet appareil ?', 'btn-danger', 'Déconnecter', function() {
                execAction('/dashboard/porteur/securite/sessions/' + sessionId, 'DELETE', null, function() {
                    if (row) row.remove();
                });
            });
        });
    });

    document.getElementById('killAllSessionsBtn')?.addEventListener('click', function() {
        var rows = document.querySelectorAll('#sessionsTableBody tr[data-session-id]');
        showConfirm('<i class="bi bi-x-circle me-2 text-danger"></i> Tout déconnecter', 'Déconnecter tous les appareils (sauf celui-ci) ?', 'btn-danger', 'Tout déconnecter', function() {
            rows.forEach(function(row) {
                var sid = row.getAttribute('data-session-id');
                if (sid) {
                    execAction('/dashboard/porteur/securite/sessions/' + sid, 'DELETE', null, function() {
                        row.remove();
                    });
                }
            });
        });
    });

    document.getElementById('enableOtpEmail')?.addEventListener('click', function() {
        showConfirm(
            '<i class="bi bi-envelope-check me-2 text-primary"></i> Activer OTP Email',
            'Activer la vérification par code email ? Vous recevrez un code à chaque connexion.',
            'btn-primary',
            'Activer',
            function() {
                ModalHelper.alert('<i class="bi bi-info-circle me-2 text-info"></i> Information', 'Fonctionnalité OTP Email sera disponible prochainement.');
            }
        );
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
