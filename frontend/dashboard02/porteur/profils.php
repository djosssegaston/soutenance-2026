<?php
require_once __DIR__ . '/header.php';

$profileHistory = $profileHistory ?? collect();
$avatarUrl = !empty($user['avatar_url'])
    ? (str_starts_with($user['avatar_url'], 'http') || str_starts_with($user['avatar_url'], '/')
        ? $user['avatar_url']
        : '/storage/'.$user['avatar_url'])
    : '../../asset/images/profiles/1.jpg';
?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title"><strong>PROFIL</strong></h1>
                    <p class="text-muted mb-0">Assurez-vous que vos informations personnelles et professionnelles restent exploitables par les partenaires financiers.</p>
                </div>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">ACCEUIL</a></li>
                        <li class="breadcrumb-item active" aria-current="page">PROFIL</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fs-12 fw-semibold">Identité vérifiée</p>
                                    <h3 class="mb-1 number-font" id="identityStatus">Oui</h3>
                                    <span class="text-muted fs-12" id="identityHint">Pièces à jour</span>
                                </div>
                                <div class="avatar avatar-lg bg-success-transparent rounded-circle text-success">
                                    <i class="bi bi-person-check fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fs-12 fw-semibold">Téléphone actif</p>
                                    <h3 class="mb-1 number-font" id="phoneStatus">01</h3>
                                    <span class="text-muted fs-12">Canal principal</span>
                                </div>
                                <div class="avatar avatar-lg bg-primary-transparent rounded-circle text-primary">
                                    <i class="bi bi-phone fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fs-12 fw-semibold">Canaux de contact</p>
                                    <h3 class="mb-1 number-font">03</h3>
                                    <span class="text-muted fs-12">Email, téléphone, Mobile Money</span>
                                </div>
                                <div class="avatar avatar-lg bg-info-transparent rounded-circle text-info">
                                    <i class="bi bi-broadcast fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fs-12 fw-semibold">Points à compléter</p>
                                    <h3 class="mb-1 number-font" id="incompleteCount">02</h3>
                                    <span class="text-muted fs-12">Avant prochaine revue</span>
                                </div>
                                <div class="avatar avatar-lg bg-warning-transparent rounded-circle text-warning">
                                    <i class="bi bi-pencil fs-20"></i>
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
                            <h4 class="card-title mb-0"><i class="bi bi-person-circle me-2"></i>Informations du profil</h4>
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-sm btn-outline-primary" id="toggleEditBtn"><i class="bi bi-pencil me-1"></i>Modifier</button>
                                <button class="btn btn-sm btn-primary d-none" id="saveAllBtn"><i class="bi bi-check-lg me-1"></i>Enregistrer</button>
                                <button class="btn btn-sm btn-outline-secondary d-none" id="cancelEditBtn"><i class="bi bi-x-lg me-1"></i>Annuler</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="profileAlert" class="alert d-none"></div>

                            <div class="row g-4">
                                <div class="col-lg-4 text-center">
                                    <div class="mb-3">
                                        <div class="avatar avatar-xxl brround d-inline-flex align-items-center justify-content-center overflow-hidden border" id="avatarPreview" style="width:150px;height:150px;">
                                            <img src="<?php echo htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Photo" class="w-100 h-100 object-fit-cover" id="avatarImg">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="avatarUpload" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-camera me-1"></i>Changer photo
                                        </label>
                                        <input type="file" id="avatarUpload" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp">
                                    </div>
                                    <div id="avatarSuccess" class="alert alert-success py-2 small d-none"></div>
                                    <div id="avatarError" class="alert alert-danger py-2 small d-none"></div>
                                </div>

                                <div class="col-lg-8">
                                    <div id="profileForm">
                                        <div class="row g-3">
                                            <div class="col-sm-6">
                                                <label class="form-label fw-semibold">Nom</label>
                                                <input type="text" class="form-control" id="inputName" value="<?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label fw-semibold">Prénom</label>
                                                <input type="text" class="form-control" id="inputPrenom" value="<?php echo htmlspecialchars($user['prenom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label fw-semibold">Téléphone</label>
                                                <div class="input-group">
                                                    <input type="tel" class="form-control" id="inputTelephone" value="<?php echo htmlspecialchars($user['telephone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                                                    <span class="input-group-text" id="phoneVerifyBadge">
                                                        <span class="badge bg-success-transparent text-success"><i class="bi bi-check-circle-fill me-1"></i>Vérifié</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label fw-semibold">Email</label>
                                                <div class="input-group">
                                                    <input type="email" class="form-control" id="inputEmail" value="<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                                                    <span class="input-group-text" id="emailVerifyBadge">
                                                        <?php if (! is_null($user['email_verified_at'] ?? null)): ?>
                                                            <span class="badge bg-success-transparent text-success"><i class="bi bi-check-circle-fill me-1"></i>Vérifié</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning-transparent text-warning"><i class="bi bi-exclamation-circle me-1"></i>Non vérifié</span>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label fw-semibold">Adresse</label>
                                                <input type="text" class="form-control" id="inputAdresse" value="<?php echo htmlspecialchars($user['adresse'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label fw-semibold">Activité</label>
                                                <input type="text" class="form-control" id="inputActivite" value="<?php echo htmlspecialchars($user['activite'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label fw-semibold">Entreprise</label>
                                                <input type="text" class="form-control" id="inputEntrepriseNom" value="<?php echo htmlspecialchars($user['entreprise_nom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label fw-semibold">Secteur d'activité</label>
                                                <input type="text" class="form-control" id="inputEntrepriseSecteur" value="<?php echo htmlspecialchars($user['entreprise_secteur'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><i class="bi bi-shield-check me-2"></i>Vérifications</h4>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush">
                                <div class="d-flex align-items-center justify-content-between py-3 border-bottom flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="avatar avatar-md bg-success-transparent text-success rounded-circle"><i class="bi bi-envelope fs-5"></i></span>
                                        <div>
                                            <h6 class="mb-1">Email</h6>
                                            <small class="text-muted" id="emailDisplay"><?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></small>
                                        </div>
                                    </div>
                                    <div id="emailVerifyActions">
                                        <?php if (! is_null($user['email_verified_at'] ?? null)): ?>
                                            <span class="badge bg-success-transparent text-success fs-12 px-3 py-2"><i class="bi bi-check-circle me-1"></i>Vérifié</span>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-warning" id="resendVerificationBtn"><i class="bi bi-send me-1"></i>Renvoyer la vérification</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between py-3 border-bottom flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="avatar avatar-md bg-primary-transparent text-primary rounded-circle"><i class="bi bi-phone fs-5"></i></span>
                                        <div>
                                            <h6 class="mb-1">Téléphone</h6>
                                            <small class="text-muted" id="phoneDisplay"><?php echo htmlspecialchars($user['telephone'] ?? 'Non renseigné', ENT_QUOTES, 'UTF-8'); ?></small>
                                        </div>
                                    </div>
                                    <div id="phoneVerifyActions">
                                        <?php if (! is_null($user['telephone_verified_at'] ?? null)): ?>
                                            <span class="badge bg-success-transparent text-success fs-12 px-3 py-2"><i class="bi bi-check-circle me-1"></i>Vérifié</span>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-primary" id="verifyPhoneBtn"><i class="bi bi-shield me-1"></i>Vérifier</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><i class="bi bi-clock-history me-2"></i>Historique des modifications</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>IP</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="historyBody">
                                        <?php if ($profileHistory->isNotEmpty()): ?>
                                            <?php foreach ($profileHistory as $log): ?>
                                                <tr>
                                                    <td><i class="bi <?php echo htmlspecialchars($log['icon'] ?? 'bi-clock', ENT_QUOTES, 'UTF-8'); ?> text-<?php echo htmlspecialchars($log['color'] ?? 'secondary', ENT_QUOTES, 'UTF-8'); ?> me-2"></i><?php echo htmlspecialchars($log['action'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($log['ip'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($log['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="3" class="text-center py-4 text-muted">Aucune modification enregistrée</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3" id="historyPagination"></div>
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

<div class="modal fade" id="emailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-envelope me-2 text-warning"></i>Changer d'email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="emailModalAlert" class="alert d-none"></div>
                <div class="mb-3">
                    <label class="form-label">Nouvel email</label>
                    <input type="email" class="form-control" id="newEmailInput">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmer avec votre mot de passe</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="emailPasswordInput">
                        <button class="btn btn-outline-secondary toggle-pwd" type="button" data-target="emailPasswordInput"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-warning" id="confirmEmailBtn"><i class="bi bi-check-lg me-1"></i>Changer l'email</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="phoneVerifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-shield me-2 text-primary"></i>Vérifier le téléphone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="phoneModalAlert" class="alert d-none"></div>
                <p class="text-muted">Un code de vérification a été envoyé à <strong id="verifyPhoneNumber"></strong>.</p>
                <div class="mb-3">
                    <label class="form-label">Code de vérification</label>
                    <input type="text" class="form-control" id="phoneCodeInput" maxlength="6" placeholder="000000">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmPhoneBtn"><i class="bi bi-check-lg me-1"></i>Vérifier</button>
            </div>
        </div>
    </div>
</div>

<script>
function resolveAvatarUrl(url) { if (!url) return '../../asset/images/profiles/1.jpg'; if (url.startsWith('http') || url.startsWith('/')) return url; return '/storage/' + url; }
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

const csrfToken = '<?php echo $csrf_token ?? ""; ?>';
var confirmModal = null;
var confirmCallback = null;

function showAlert(el, msg, type) {
    el.textContent = msg;
    el.className = 'alert alert-' + type;
    el.classList.remove('d-none');
}

function hideAlert(el) {
    el.classList.add('d-none');
}

function showConfirm(title, message, btnClass, btnText, callback) {
    document.getElementById('confirmModalTitle').innerHTML = title;
    document.getElementById('confirmModalMessage').textContent = message;
    var btn = document.getElementById('confirmModalBtn');
    btn.className = 'btn ' + btnClass;
    btn.textContent = btnText;
    confirmCallback = callback;
    confirmModal.show();
}

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
        else if (resp.status >= 400) {
            var msg = resp.data && resp.data.message ? resp.data.message : 'Erreur ' + resp.status;
            var alertEl = document.getElementById('profileAlert');
            showAlert(alertEl, msg, 'danger');
        }
    })
    .catch(function() {
        var alertEl = document.getElementById('profileAlert');
        showAlert(alertEl, 'Erreur de connexion', 'danger');
    });
}

function updateProfileUI(data) {
    var u = data.user || data;
    if (u.name) document.getElementById('inputName').value = u.name;
    if (u.prenom) document.getElementById('inputPrenom').value = u.prenom;
    if (u.telephone) document.getElementById('inputTelephone').value = u.telephone;
    if (u.email) document.getElementById('inputEmail').value = u.email;
    if (u.adresse !== undefined) document.getElementById('inputAdresse').value = u.adresse || '';
    if (u.activite !== undefined) document.getElementById('inputActivite').value = u.activite || '';
    if (u.entreprise_nom !== undefined) document.getElementById('inputEntrepriseNom').value = u.entreprise_nom || '';
    if (u.entreprise_secteur !== undefined) document.getElementById('inputEntrepriseSecteur').value = u.entreprise_secteur || '';

    if (u.avatar_url) {
        document.getElementById('avatarImg').src = resolveAvatarUrl(u.avatar_url);
    }

    document.getElementById('emailDisplay').textContent = u.email || '';
    document.getElementById('phoneDisplay').textContent = u.telephone || 'Non renseigné';

    var emailBadge = document.getElementById('emailVerifyBadge');
    if (u.email_verified) {
        emailBadge.innerHTML = '<span class="badge bg-success-transparent text-success"><i class="bi bi-check-circle-fill me-1"></i>Vérifié</span>';
    } else {
        emailBadge.innerHTML = '<span class="badge bg-warning-transparent text-warning"><i class="bi bi-exclamation-circle me-1"></i>Non vérifié</span>';
    }

    var phoneBadge = document.getElementById('phoneVerifyBadge');
    if (u.telephone_verified) {
        phoneBadge.innerHTML = '<span class="badge bg-success-transparent text-success"><i class="bi bi-check-circle-fill me-1"></i>Vérifié</span>';
    } else {
        phoneBadge.innerHTML = '<span class="badge bg-secondary-transparent text-secondary"><i class="bi bi-dash-circle me-1"></i>Non vérifié</span>';
    }

    var emailActions = document.getElementById('emailVerifyActions');
    if (u.email_verified) {
        emailActions.innerHTML = '<span class="badge bg-success-transparent text-success fs-12 px-3 py-2"><i class="bi bi-check-circle me-1"></i>Vérifié</span>';
    } else {
        emailActions.innerHTML = '<button class="btn btn-sm btn-outline-warning" id="resendVerificationBtn"><i class="bi bi-send me-1"></i>Renvoyer la vérification</button>';
    }

    var phoneActions = document.getElementById('phoneVerifyActions');
    if (u.telephone_verified) {
        phoneActions.innerHTML = '<span class="badge bg-success-transparent text-success fs-12 px-3 py-2"><i class="bi bi-check-circle me-1"></i>Vérifié</span>';
    } else {
        phoneActions.innerHTML = '<button class="btn btn-sm btn-outline-primary" id="verifyPhoneBtn"><i class="bi bi-shield me-1"></i>Vérifier</button>';
    }

    var identityEl = document.getElementById('identityStatus');
    if (u.email_verified && u.telephone_verified) {
        identityEl.textContent = 'Oui';
        document.getElementById('identityHint').textContent = 'Pièces à jour';
    } else {
        identityEl.textContent = 'Partiel';
        document.getElementById('identityHint').textContent = 'Vérifications en attente';
    }

    document.getElementById('phoneStatus').textContent = u.telephone ? u.telephone : '--';
}

document.addEventListener('DOMContentLoaded', function() {
    confirmModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('confirmModal'));

    document.getElementById('confirmModalBtn').addEventListener('click', function() {
        if (confirmCallback) { confirmCallback(); confirmCallback = null; }
        confirmModal.hide();
    });

    // Initialiser la pagination
    updatePagination('historyBody', 'historyPagination');

    // Toggle edit mode
    var isEditing = false;
    var editBtn = document.getElementById('toggleEditBtn');
    var saveBtn = document.getElementById('saveAllBtn');
    var cancelBtn = document.getElementById('cancelEditBtn');

    function setEditMode(enabled) {
        isEditing = enabled;
        var inputs = document.querySelectorAll('#profileForm input');
        inputs.forEach(function(inp) { inp.disabled = !enabled; });
        editBtn.classList.toggle('d-none', enabled);
        saveBtn.classList.toggle('d-none', !enabled);
        cancelBtn.classList.toggle('d-none', !enabled);
    }

    editBtn.addEventListener('click', function() { setEditMode(true); });

    cancelBtn.addEventListener('click', function() {
        setEditMode(false);
        hideAlert(document.getElementById('profileAlert'));
    });

    saveBtn.addEventListener('click', function() {
        var data = {
            name: document.getElementById('inputName').value,
            prenom: document.getElementById('inputPrenom').value,
            telephone: document.getElementById('inputTelephone').value,
            adresse: document.getElementById('inputAdresse').value,
            activite: document.getElementById('inputActivite').value,
            entreprise_nom: document.getElementById('inputEntrepriseNom').value,
            entreprise_secteur: document.getElementById('inputEntrepriseSecteur').value,
        };

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement...';

        execAction('/dashboard/porteur/profil/update', 'POST', data, function(resp) {
            var alertEl = document.getElementById('profileAlert');
            showAlert(alertEl, resp.message || 'Profil mis à jour', 'success');
            setEditMode(false);
            if (resp.user) updateProfileUI(resp.user);
            loadHistory();
            document.getElementById('saveAllBtn').disabled = false;
            document.getElementById('saveAllBtn').innerHTML = '<i class="bi bi-check-lg me-1"></i>Enregistrer';
        });
    });

    // Avatar upload
    document.getElementById('avatarUpload').addEventListener('change', function() {
        var file = this.files[0];
        if (!file) return;

        var formData = new FormData();
        formData.append('avatar', file);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/dashboard/porteur/profil/photo', true);
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Accept', 'application/json');

    xhr.onload = function() {
        var resp;
        try { resp = JSON.parse(xhr.responseText); } catch(e) { resp = {}; }
        if (xhr.status >= 200 && xhr.status < 300) {
            if (resp.avatar_url) {
                document.getElementById('avatarImg').src = resolveAvatarUrl(resp.avatar_url);
                var hdrAvatar = document.getElementById('header-user-avatar');
                if (hdrAvatar) hdrAvatar.src = resolveAvatarUrl(resp.avatar_url);
            }
            var succEl = document.getElementById('avatarSuccess');
            succEl.textContent = resp.message || 'Photo mise à jour';
            succEl.classList.remove('d-none');
            setTimeout(function() { succEl.classList.add('d-none'); }, 3000);
            if (resp.user) updateProfileUI(resp.user);
            loadHistory();
        } else {
            var errEl = document.getElementById('avatarError');
            errEl.textContent = resp && resp.message ? resp.message : 'Erreur lors du téléchargement';
            errEl.classList.remove('d-none');
            setTimeout(function() { errEl.classList.add('d-none'); }, 5000);
        }
    };

    xhr.onerror = function() {
        var errEl = document.getElementById('avatarError');
        errEl.textContent = 'Erreur réseau lors du téléchargement';
        errEl.classList.remove('d-none');
        setTimeout(function() { errEl.classList.add('d-none'); }, 5000);
    };

    xhr.send(formData);
    });

    // Email change
    var emailModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('emailModal'));

    document.getElementById('inputEmail').addEventListener('dblclick', function() {
        document.getElementById('newEmailInput').value = this.value;
        document.getElementById('emailPasswordInput').value = '';
        hideAlert(document.getElementById('emailModalAlert'));
        emailModal.show();
    });

    document.getElementById('confirmEmailBtn').addEventListener('click', function() {
        var email = document.getElementById('newEmailInput').value;
        var password = document.getElementById('emailPasswordInput').value;
        var btn = this;

        if (!email || !password) {
            showAlert(document.getElementById('emailModalAlert'), 'Veuillez remplir tous les champs', 'danger');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Modification...';

        execAction('/dashboard/porteur/profil/email', 'POST', { email: email, password: password }, function(resp) {
            showAlert(document.getElementById('emailModalAlert'), resp.message || 'Email modifié', 'success');
            if (resp.user) updateProfileUI(resp.user);
            loadHistory();
            setTimeout(function() { emailModal.hide(); }, 2000);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Changer l\'email';
        });
    });

    // Resend verification email
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'resendVerificationBtn') {
            execAction('/dashboard/porteur/profil/resend-verification', 'POST', null, function(resp) {
                var alertEl = document.getElementById('profileAlert');
                showAlert(alertEl, resp.message || 'Lien de vérification renvoyé', 'success');
            });
        }
    });

    // Phone verification
    var phoneVerifyModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('phoneVerifyModal'));

    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'verifyPhoneBtn') {
            document.getElementById('verifyPhoneNumber').textContent = document.getElementById('inputTelephone').value || 'Non renseigné';
            document.getElementById('phoneCodeInput').value = '';
            hideAlert(document.getElementById('phoneModalAlert'));
            phoneVerifyModal.show();
        }
    });

    document.getElementById('confirmPhoneBtn').addEventListener('click', function() {
        var code = document.getElementById('phoneCodeInput').value;
        var btn = this;

        if (!code || code.length !== 6) {
            showAlert(document.getElementById('phoneModalAlert'), 'Veuillez entrer le code à 6 chiffres', 'danger');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Vérification...';

        execAction('/dashboard/porteur/profil/verify-telephone', 'POST', { code: code }, function(resp) {
            showAlert(document.getElementById('phoneModalAlert'), resp.message || 'Téléphone vérifié', 'success');
            if (resp.user) updateProfileUI(resp.user);
            loadHistory();
            setTimeout(function() { phoneVerifyModal.hide(); }, 2000);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Vérifier';
        });
    });

    // Toggle password visibility (used in email modal)
    document.querySelectorAll('.toggle-pwd').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
                this.querySelector('i').className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
            }
        });
    });
});

function loadHistory() {
    fetch('/dashboard/porteur/profil/history', {
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var tbody = document.getElementById('historyBody');
        if (!tbody) return;
        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted">Aucune modification enregistrée</td></tr>';
            return;
        }
        var html = '';
        data.forEach(function(log) {
            html += '<tr><td><i class="bi ' + (log.icon || 'bi-clock') + ' text-' + (log.color || 'secondary') + ' me-2"></i>' + (log.action || '') + '</td><td>' + (log.ip || '') + '</td><td>' + (log.date || '') + '</td></tr>';
        });
        tbody.innerHTML = html;
        updatePagination('historyBody', 'historyPagination');
    })
    .catch(function() {});
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
