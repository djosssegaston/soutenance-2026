<?php require_once __DIR__ . '/header.php'; ?>
<?php $__av = auth()->user()?->avatar_url ?? ''; $__avSrc = !empty($__av) ? (str_starts_with($__av,'http')||str_starts_with($__av,'/')?$__av:'/storage/'.$__av) : '../../asset/images/profiles/1.jpg'; ?>
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title text-uppercase fw-bold">Profil Administrateur</h1>
                    <p class="text-muted mb-0">Gérez vos informations personnelles et préférences.</p>
                </div>
                <div><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Admin</a></li><li class="breadcrumb-item active">Mon Profil</li></ol></div>
            </div>

            <div class="row">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="avatar avatar-xxl brround mx-auto mb-3 overflow-hidden" style="width:120px;height:120px;">
                                <img src="<?php echo htmlspecialchars($__avSrc, ENT_QUOTES, 'UTF-8'); ?>" id="profile-avatar" class="w-100 h-100 object-fit-cover">
                            </div>
                            <input type="file" id="avatar-upload" class="d-none" accept="image/jpeg,image/png,image/jpg,image/gif">
                            <div class="mb-2"><button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="document.getElementById('avatar-upload').click()"><i class="fe fe-camera me-1"></i>Changer la photo</button></div>
                            <div id="avatar-feedback" class="small d-none"></div>
                            <h4 class="mb-1" id="profile-name">Administrateur</h4>
                            <span class="badge bg-danger-transparent text-danger mb-3">Super Admin</span>
                            <div class="text-muted fs-13">
                                <p class="mb-1"><i class="fe fe-mail me-2"></i><span id="profile-email">admin@alogoto.com</span></p>
                                <p class="mb-1"><i class="fe fe-phone me-2"></i><span id="profile-phone">+229 XX XX XX XX</span></p>
                                <p class="mb-0"><i class="fe fe-calendar me-2"></i>Membre depuis <span id="profile-since">2024</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Statistiques Personnelles</h3></div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3"><span class="text-muted">Projets validés</span><span class="fw-bold" id="stat-validated">0</span></div>
                            <div class="d-flex justify-content-between mb-3"><span class="text-muted">Projets rejetés</span><span class="fw-bold" id="stat-rejected">0</span></div>
                            <div class="d-flex justify-content-between mb-3"><span class="text-muted">Dernière connexion</span><span class="fw-bold" id="stat-last-login">—</span></div>
                            <div class="d-flex justify-content-between"><span class="text-muted">Sessions actives</span><span class="fw-bold" id="stat-sessions">1</span></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fe fe-user me-2"></i>Informations Personnelles</h3></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label fw-semibold">Nom Complet</label><input type="text" id="edit-name" class="form-control" value=""></div>
                                <div class="col-md-6"><label class="form-label fw-semibold">Email</label><input type="email" id="edit-email" class="form-control" value=""></div>
                                <div class="col-md-6"><label class="form-label fw-semibold">Téléphone</label><input type="tel" id="edit-phone" class="form-control" value=""></div>
                                <div class="col-md-6"><label class="form-label fw-semibold">Ville</label><input type="text" id="edit-city" class="form-control" value=""></div>
                                <div class="col-12"><label class="form-label fw-semibold">Bio</label><textarea id="edit-bio" class="form-control" rows="3"></textarea></div>
                                <div class="col-12"><button class="btn btn-primary" id="btn-save-profile"><i class="fe fe-save me-1"></i>Mettre à jour</button></div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h3 class="card-title"><i class="fe fe-lock me-2"></i>Changer le Mot de Passe</h3></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label fw-semibold">Mot de passe actuel</label><input type="password" id="pwd-current" class="form-control"></div>
                                <div class="col-md-4"><label class="form-label fw-semibold">Nouveau mot de passe</label><input type="password" id="pwd-new" class="form-control"></div>
                                <div class="col-md-4"><label class="form-label fw-semibold">Confirmer</label><input type="password" id="pwd-confirm" class="form-control"></div>
                                <div class="col-12"><button class="btn btn-warning" id="btn-change-pwd"><i class="fe fe-key me-1"></i>Changer</button></div>
                            </div>
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

    loadProfile();

    function resolveAvatarUrl(url) {
        if (!url) return '../../asset/images/profiles/1.jpg';
        if (url.startsWith('http') || url.startsWith('/')) return url;
        return '/storage/' + url;
    }

    function updateAvatarAll(src) {
        var resolved = resolveAvatarUrl(src);
        var headerImg = document.getElementById('header-user-avatar');
        if (headerImg) headerImg.src = resolved;
        var profileImg = document.getElementById('profile-avatar');
        if (profileImg) profileImg.src = resolved;
    }

    async function loadProfile() {
        try {
            const res = await fetch(`${API_BASE}/auth/me`, { headers: JSON_HEADERS });
            const body = await res.json();
            const user = body.user || body;
            document.getElementById('profile-name').textContent = user.name || 'Administrateur';
            document.getElementById('profile-email').textContent = user.email || '—';
            document.getElementById('profile-phone').textContent = user.telephone || '—';
            document.getElementById('profile-since').textContent = user.created_at ? new Date(user.created_at).getFullYear() : '2024';
            document.getElementById('edit-name').value = user.name || '';
            document.getElementById('edit-email').value = user.email || '';
            document.getElementById('edit-phone').value = user.telephone || '';
            document.getElementById('edit-city').value = user.ville || '';
            document.getElementById('edit-bio').value = user.biographie || '';
            if (user.avatar_url) updateAvatarAll(user.avatar_url);
        } catch (e) {
            console.error('Erreur chargement profil:', e);
        }
    }

    document.getElementById('btn-save-profile').addEventListener('click', async function() {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        try {
            const res = await fetch(`${API_BASE}/profile/update`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({
                    name: document.getElementById('edit-name').value,
                    email: document.getElementById('edit-email').value,
                    telephone: document.getElementById('edit-phone').value,
                    ville: document.getElementById('edit-city').value,
                    biographie: document.getElementById('edit-bio').value
                })
            });
            const data = await res.json();
            if (res.ok) {
                ALOGOTO.success('Profil mis à jour avec succès.');
                loadProfile();
            } else {
                ALOGOTO.error(data.message || 'Erreur lors de la mise à jour.');
            }
        } catch (e) {
            ALOGOTO.error('Erreur réseau.');
        }
    });

    document.getElementById('btn-change-pwd').addEventListener('click', async function() {
        const current = document.getElementById('pwd-current').value;
        const pwd = document.getElementById('pwd-new').value;
        const confirm = document.getElementById('pwd-confirm').value;
        if (!current || !pwd) { ALOGOTO.warning('Veuillez remplir tous les champs.'); return; }
        if (pwd !== confirm) { ALOGOTO.warning('Les mots de passe ne correspondent pas.'); return; }
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        try {
            const res = await fetch(`${API_BASE}/profile/password`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ current_password: current, password: pwd, password_confirmation: confirm })
            });
            const data = await res.json();
            if (res.ok) {
                ALOGOTO.success('Mot de passe modifié avec succès.');
                document.getElementById('pwd-current').value = '';
                document.getElementById('pwd-new').value = '';
                document.getElementById('pwd-confirm').value = '';
            } else {
                ALOGOTO.error(data.message || 'Erreur lors du changement.');
            }
        } catch (e) {
            ALOGOTO.error('Erreur réseau.');
        }
    });

    document.getElementById('avatar-upload').addEventListener('change', function() {
        var file = this.files[0];
        if (!file) return;
        var fb = document.getElementById('avatar-feedback');
        var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        var formData = new FormData();
        formData.append('avatar', file);
        fb.className = 'small';
        fb.textContent = 'Téléchargement...';
        fb.classList.remove('d-none');
        fetch(API_BASE + '/profile/avatar', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: formData
        }).then(function(r) { return r.json(); }).then(function(data) {
            if (data.avatar_url) {
                updateAvatarAll(data.avatar_url);
                fb.className = 'small text-success';
                fb.textContent = data.message || 'Photo mise à jour';
                ALOGOTO.success('Photo de profil mise à jour.');
            } else {
                fb.className = 'small text-danger';
                fb.textContent = data.message || 'Erreur lors du téléchargement';
            }
            setTimeout(function() { fb.classList.add('d-none'); }, 3000);
        }).catch(function() {
            fb.className = 'small text-danger';
            fb.textContent = 'Erreur réseau';
            setTimeout(function() { fb.classList.add('d-none'); }, 3000);
        });
    });
});
</script>
