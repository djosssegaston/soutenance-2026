<?php require_once __DIR__ . '/header.php'; ?>
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title text-uppercase fw-bold">Gestion des Secteurs</h1>
                    <p class="text-muted mb-0">Ajouter, modifier, activer ou désactiver les secteurs d'activité.</p>
                </div>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
                        <li class="breadcrumb-item active">Secteurs</li>
                    </ol>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <h5 class="card-title mb-0">Liste des secteurs</h5>
                            <button class="btn btn-primary btn-sm" id="btn-add-secteur">
                                <i class="fe fe-plus me-1"></i>Nouveau secteur
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table text-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Nom</th>
                                            <th>Description</th>
                                            <th>Statut</th>
                                            <th>Ordre</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="secteurs-tbody">
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fe fe-loader fa-spin fs-3 d-block mb-2"></i>
                                                Chargement...
                                            </td>
                                        </tr>
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

<!-- MODAL SECTEUR -->
<div class="modal fade" id="secteurModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="secteurForm">
                <input type="hidden" name="id" id="secteur-id">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="secteurModalTitle">Nouveau secteur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nom" id="secteur-nom" required maxlength="255">
                        <div class="invalid-feedback" id="secteur-nom-error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="secteur-description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icône (classe CSS)</label>
                        <input type="text" class="form-control" name="icone" id="secteur-icone" placeholder="Ex: fe fe-briefcase">
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Ordre d'affichage</label>
                            <input type="number" class="form-control" name="order_column" id="secteur-order" min="0" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Statut</label>
                            <select class="form-select" name="is_active" id="secteur-active">
                                <option value="1">Actif</option>
                                <option value="0">Inactif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="secteur-submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const BASE_URL = '/api/v1/admin/secteurs';
    let editingId = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const headers = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken };
    const postHeaders = Object.assign({}, headers, { 'Content-Type': 'application/json' });

    function escHtml(str) {
        return str ? String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') : '';
    }

    async function apiFetch(url, opts) {
        const res = await fetch(url, opts);
        if (!res.ok) {
            const body = await res.json().catch(function(){ return {}; });
            const err = new Error(body.message || 'API error');
            err.response = { data: body, status: res.status };
            throw err;
        }
        return res.json();
    }

    async function loadSecteurs() {
        try {
            const data = await apiFetch(BASE_URL, { headers: headers });
            const secteurs = data.secteurs || [];
            const tbody = document.getElementById('secteurs-tbody');
            if (secteurs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted"><i class="fe fe-folder fs-3 d-block mb-2"></i>Aucun secteur trouvé.</td></tr>';
                return;
            }
            tbody.innerHTML = secteurs.map(function(s, i) {
                const active = s.is_active || s.is_active === null;
                return '<tr>' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td class="fw-medium">' + escHtml(s.nom) + '</td>' +
                    '<td class="text-muted">' + escHtml(s.description || '-') + '</td>' +
                    '<td><span class="badge bg-' + (active ? 'success' : 'secondary') + '-transparent text-' + (active ? 'success' : 'secondary') + ' rounded-pill px-3">' + (active ? 'Actif' : 'Inactif') + '</span></td>' +
                    '<td>' + (s.order_column ?? 0) + '</td>' +
                    '<td class="text-end">' +
                        '<button class="btn btn-sm btn-icon btn-info me-1 btn-edit-secteur" data-id="' + s.id + '" title="Modifier"><i class="fe fe-edit"></i></button>' +
                        '<button class="btn btn-sm btn-icon btn-' + (active ? 'warning' : 'success') + ' me-1 btn-toggle-secteur" data-id="' + s.id + '" data-active="' + (active ? 1 : 0) + '" title="' + (active ? 'Désactiver' : 'Activer') + '"><i class="fe fe-' + (active ? 'pause-circle' : 'play-circle') + '"></i></button>' +
                        '<button class="btn btn-sm btn-icon btn-danger btn-delete-secteur" data-id="' + s.id + '" title="Supprimer"><i class="fe fe-trash-2"></i></button>' +
                    '</td>' +
                    '</tr>';
            }).join('');
        } catch (e) {
            document.getElementById('secteurs-tbody').innerHTML = '<tr><td colspan="6" class="text-center py-5 text-danger">Erreur de chargement.</td></tr>';
            if (typeof ModalHelper !== 'undefined') {
                ModalHelper.error('Erreur', 'Impossible de charger les secteurs.');
            }
        }
    }

    function resetForm() {
        editingId = null;
        document.getElementById('secteur-id').value = '';
        document.getElementById('secteur-nom').value = '';
        document.getElementById('secteur-description').value = '';
        document.getElementById('secteur-icone').value = '';
        document.getElementById('secteur-order').value = '0';
        document.getElementById('secteur-active').value = '1';
        document.getElementById('secteurModalTitle').textContent = 'Nouveau secteur';
        document.getElementById('secteur-nom').classList.remove('is-invalid');
        document.getElementById('secteur-nom-error').textContent = '';
    }

    function openEdit(id) {
        apiFetch(BASE_URL + '/' + id, { headers: headers }).then(function(data) {
            const s = data.secteur;
            editingId = s.id;
            document.getElementById('secteur-id').value = s.id;
            document.getElementById('secteur-nom').value = s.nom;
            document.getElementById('secteur-description').value = s.description || '';
            document.getElementById('secteur-icone').value = s.icone || '';
            document.getElementById('secteur-order').value = s.order_column ?? 0;
            document.getElementById('secteur-active').value = s.is_active ? '1' : '0';
            document.getElementById('secteurModalTitle').textContent = 'Modifier le secteur';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('secteurModal')).show();
        }).catch(function() {
            if (typeof ModalHelper !== 'undefined') {
                ModalHelper.error('Erreur', 'Impossible de charger les données du secteur.');
            }
        });
    }

    document.getElementById('btn-add-secteur').addEventListener('click', function() {
        resetForm();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('secteurModal')).show();
    });

    document.getElementById('secteurForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const nom = document.getElementById('secteur-nom').value.trim();
        if (!nom) {
            document.getElementById('secteur-nom').classList.add('is-invalid');
            document.getElementById('secteur-nom-error').textContent = 'Le nom est requis.';
            return;
        }
        const payload = {
            nom: nom,
            description: document.getElementById('secteur-description').value.trim(),
            icone: document.getElementById('secteur-icone').value.trim(),
            order_column: parseInt(document.getElementById('secteur-order').value) || 0,
            is_active: document.getElementById('secteur-active').value === '1',
        };
        try {
            if (editingId) {
                await apiFetch(BASE_URL + '/' + editingId, { method: 'PUT', headers: postHeaders, body: JSON.stringify(payload) });
                if (typeof ModalHelper !== 'undefined') ModalHelper.success('Secteur mis à jour', 'Le secteur a été modifié avec succès.');
            } else {
                await apiFetch(BASE_URL, { method: 'POST', headers: postHeaders, body: JSON.stringify(payload) });
                if (typeof ModalHelper !== 'undefined') ModalHelper.success('Secteur créé', 'Le secteur a été ajouté avec succès.');
            }
            bootstrap.Modal.getInstance(document.getElementById('secteurModal')).hide();
            loadSecteurs();
        } catch (e) {
            const msg = e.response?.data?.message || e.message || 'Erreur lors de l\'enregistrement.';
            if (msg.includes('duplicate') || msg.includes('unique') || msg.includes('existe')) {
                document.getElementById('secteur-nom').classList.add('is-invalid');
                document.getElementById('secteur-nom-error').textContent = 'Ce nom existe déjà.';
            } else {
                ModalHelper.error('Erreur', msg);
            }
        }
    });

    document.getElementById('secteurs-tbody').addEventListener('click', function(e) {
        const btn = e.target.closest('button');
        if (!btn) return;
        const id = btn.getAttribute('data-id');
        if (btn.classList.contains('btn-edit-secteur')) {
            openEdit(id);
        } else if (btn.classList.contains('btn-toggle-secteur')) {
            const isActive = btn.getAttribute('data-active') === '1';
            ModalHelper.confirm(
                '<i class="bi bi-' + (isActive ? 'pause-circle' : 'play-circle') + ' me-2 text-' + (isActive ? 'warning' : 'success') + '"></i> ' + (isActive ? 'Désactiver' : 'Activer'),
                (isActive ? 'Désactiver' : 'Activer') + ' le secteur ?',
                'Oui, ' + (isActive ? 'désactiver' : 'activer'),
                'Annuler',
                isActive ? 'btn-warning' : 'btn-success'
            ).then(function(confirmed) {
                if (!confirmed) return;
                apiFetch(BASE_URL + '/' + id, { method: 'PUT', headers: postHeaders, body: JSON.stringify({ is_active: !isActive }) }).then(function() {
                    loadSecteurs();
                }).catch(function() {
                    if (typeof ModalHelper !== 'undefined') ModalHelper.error('Erreur', 'Impossible de modifier le statut.');
                });
            });
        } else if (btn.classList.contains('btn-delete-secteur')) {
            ModalHelper.confirm(
                '<i class="bi bi-trash me-2 text-danger"></i> Supprimer',
                'Supprimer définitivement ce secteur ? Cette action est irréversible.',
                'Oui, supprimer',
                'Annuler',
                'btn-danger'
            ).then(function(confirmed) {
                if (!confirmed) return;
                apiFetch(BASE_URL + '/' + id, { method: 'DELETE', headers: headers }).then(function() {
                    loadSecteurs();
                }).catch(function() {
                    if (typeof ModalHelper !== 'undefined') ModalHelper.error('Erreur', 'Impossible de supprimer le secteur.');
                });
            });
        }
    });

    loadSecteurs();
});
</script>
<?php require_once __DIR__ . '/footer.php'; ?>
