<?php require_once __DIR__ . '/header.php'; ?>
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title text-uppercase fw-bold">Gestion des Documents d'Inscription</h1>
                    <p class="text-muted mb-0">Configurer les documents requis lors de la création de compte (porteur / institution).</p>
                </div>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
                        <li class="breadcrumb-item active">Documents inscription</li>
                    </ol>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <h5 class="card-title mb-0">Liste des documents</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="btn-group btn-group-sm" role="group" id="acteur-tabs">
                                    <input type="radio" class="btn-check" name="acteur-tab" id="tab-porteur" value="porteur" checked>
                                    <label class="btn btn-outline-primary" for="tab-porteur">Porteur</label>
                                    <input type="radio" class="btn-check" name="acteur-tab" id="tab-porteur-entreprise" value="porteur_entreprise">
                                    <label class="btn btn-outline-primary" for="tab-porteur-entreprise">Porteur Entreprise</label>
                                    <input type="radio" class="btn-check" name="acteur-tab" id="tab-institution" value="institution">
                                    <label class="btn btn-outline-primary" for="tab-institution">Institution</label>
                                </div>
                                <button class="btn btn-primary btn-sm" id="btn-add-rule">
                                    <i class="fe fe-plus me-1"></i>Nouveau document
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table text-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Document</th>
                                            <th>Type</th>
                                            <th>Extensions</th>
                                            <th>Taille max</th>
                                            <th>Statut</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rules-tbody">
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
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

<!-- MODAL DOCUMENT RULE -->
<div class="modal fade" id="ruleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="ruleForm">
                <input type="hidden" name="id" id="rule-id">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="ruleModalTitle">Nouveau document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom du document <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="label" id="rule-label" required maxlength="255" placeholder="Ex: Pièce d'identité">
                        <div class="invalid-feedback" id="rule-label-error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Identifiant technique (slug) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="slug" id="rule-slug" required maxlength="255" placeholder="Ex: piece_identite">
                        <small class="text-muted">Utilisé en interne. Lettres minuscules, chiffres, underscores.</small>
                        <div class="invalid-feedback" id="rule-slug-error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description courte</label>
                        <textarea class="form-control" name="description" id="rule-description" rows="2" placeholder="Ex: Copie du registre de commerce"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="obligatoire" id="rule-obligatoire">
                                <option value="1">Obligatoire</option>
                                <option value="0">Facultatif</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Acteur</label>
                            <select class="form-select" name="acteur" id="rule-acteur">
                                <option value="porteur">Porteur</option>
                                <option value="porteur_entreprise">Porteur Entreprise</option>
                                <option value="institution">Institution</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Extensions autorisées</label>
                            <select class="form-select" name="types_mime" id="rule-types" multiple size="5">
                                <option value="pdf">PDF</option>
                                <option value="jpg">JPG</option>
                                <option value="jpeg">JPEG</option>
                                <option value="png">PNG</option>
                                <option value="gif">GIF</option>
                                <option value="webp">WebP</option>
                                <option value="doc">DOC (Word)</option>
                                <option value="docx">DOCX (Word)</option>
                                <option value="xls">XLS (Excel)</option>
                                <option value="xlsx">XLSX (Excel)</option>
                                <option value="csv">CSV</option>
                            </select>
                            <small class="text-muted">Maintenez Ctrl (Mac: ⌘) pour sélectionner plusieurs extensions.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Taille maximale (Mo)</label>
                            <input type="number" class="form-control" name="max_size" id="rule-max-size" min="1" max="102400" value="5" placeholder="Ex: 5">
                            <small class="text-muted">Maximum 100 Mo.</small>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label">Statut</label>
                            <select class="form-select" name="is_active" id="rule-active">
                                <option value="1">Actif</option>
                                <option value="0">Inactif</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Ordre d'affichage</label>
                            <input type="number" class="form-control" name="order_column" id="rule-order" min="0" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="rule-submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const BASE_URL = '/api/v1/admin/document-rules';
    let editingId = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const headers = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken };
    const postHeaders = Object.assign({}, headers, { 'Content-Type': 'application/json' });

    function escHtml(str) {
        return str ? String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') : '';
    }

    function slugify(text) {
        return text.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '') || 'document';
    }

    function getCurrentActeur() {
        return document.querySelector('input[name="acteur-tab"]:checked')?.value || 'porteur';
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

    async function loadRules() {
        const acteur = getCurrentActeur();
        try {
            const data = await apiFetch(BASE_URL + '?context=registration&acteur=' + acteur, { headers: headers });
            let rules = data.rules || [];
            rules = rules.filter(function(r) { return r.acteur === acteur; });
            const tbody = document.getElementById('rules-tbody');
            if (rules.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted"><i class="fe fe-file-text fs-3 d-block mb-2"></i>Aucun document trouvé pour ce type de compte.</td></tr>';
                return;
            }
            tbody.innerHTML = rules.map(function(r, i) {
                const active = r.is_active || r.is_active === null;
                const actorLabel = r.acteur === 'institution' ? 'Institution' : (r.acteur === 'porteur_entreprise' ? 'Porteur Entreprise' : 'Porteur');
                return '<tr>' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td><div class="fw-medium">' + escHtml(r.label) + '</div><small class="text-muted">' + escHtml(r.slug) + '</small></td>' +
                    '<td><span class="badge bg-' + (r.obligatoire ? 'danger' : 'info') + '-transparent text-' + (r.obligatoire ? 'danger' : 'info') + ' rounded-pill px-3">' + (r.obligatoire ? 'Obligatoire' : 'Facultatif') + '</span></td>' +
                    '<td><small>' + escHtml(r.types_mime || 'Toutes') + '</small></td>' +
                    '<td>' + (r.max_size ? r.max_size + ' Mo' : '-') + '</td>' +
                    '<td><span class="badge bg-' + (active ? 'success' : 'secondary') + '-transparent text-' + (active ? 'success' : 'secondary') + ' rounded-pill px-3">' + (active ? 'Actif' : 'Inactif') + '</span></td>' +
                    '<td class="text-end">' +
                        '<button class="btn btn-sm btn-icon btn-info me-1 btn-edit-rule" data-id="' + r.id + '" title="Modifier"><i class="fe fe-edit"></i></button>' +
                        '<button class="btn btn-sm btn-icon btn-' + (active ? 'warning' : 'success') + ' me-1 btn-toggle-rule" data-id="' + r.id + '" data-active="' + (active ? 1 : 0) + '" title="' + (active ? 'Désactiver' : 'Activer') + '"><i class="fe fe-' + (active ? 'pause-circle' : 'play-circle') + '"></i></button>' +
                        '<button class="btn btn-sm btn-icon btn-danger btn-delete-rule" data-id="' + r.id + '" title="Supprimer"><i class="fe fe-trash-2"></i></button>' +
                    '</td>' +
                    '</tr>';
            }).join('');
        } catch (e) {
            document.getElementById('rules-tbody').innerHTML = '<tr><td colspan="7" class="text-center py-5 text-danger">Erreur de chargement.</td></tr>';
        }
    }

    function setTypesSelect(values) {
        const sel = document.getElementById('rule-types');
        Array.from(sel.options).forEach(function(opt) { opt.selected = false; });
        if (!values) return;
        values.split(',').forEach(function(v) {
            v = v.trim();
            Array.from(sel.options).some(function(opt) { if (opt.value === v) { opt.selected = true; return true; } });
        });
    }

    function getTypesSelect() {
        const sel = document.getElementById('rule-types');
        return Array.from(sel.selectedOptions).map(function(o) { return o.value; }).join(',');
    }

    function resetForm() {
        editingId = null;
        document.getElementById('rule-id').value = '';
        document.getElementById('rule-label').value = '';
        document.getElementById('rule-slug').value = '';
        document.getElementById('rule-slug').readOnly = false;
        document.getElementById('rule-description').value = '';
        document.getElementById('rule-obligatoire').value = '1';
        document.getElementById('rule-acteur').value = getCurrentActeur();
        setTypesSelect('');
        document.getElementById('rule-max-size').value = '5';
        document.getElementById('rule-active').value = '1';
        document.getElementById('rule-order').value = '0';
        document.getElementById('ruleModalTitle').textContent = 'Nouveau document';
        document.getElementById('rule-label').classList.remove('is-invalid');
        document.getElementById('rule-label-error').textContent = '';
        document.getElementById('rule-slug').classList.remove('is-invalid');
        document.getElementById('rule-slug-error').textContent = '';
    }

    function openEdit(id) {
        apiFetch(BASE_URL + '/' + id, { headers: headers }).then(function(data) {
            const r = data.rule;
            editingId = r.id;
            document.getElementById('rule-id').value = r.id;
            document.getElementById('rule-label').value = r.label;
            document.getElementById('rule-slug').value = r.slug;
            document.getElementById('rule-slug').readOnly = true;
            document.getElementById('rule-description').value = r.description || '';
            document.getElementById('rule-obligatoire').value = r.obligatoire ? '1' : '0';
            document.getElementById('rule-acteur').value = r.acteur || getCurrentActeur();
            setTypesSelect(r.types_mime);
            document.getElementById('rule-max-size').value = r.max_size || '5';
            document.getElementById('rule-active').value = r.is_active ? '1' : '0';
            document.getElementById('rule-order').value = r.order_column ?? 0;
            document.getElementById('ruleModalTitle').textContent = 'Modifier le document';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('ruleModal')).show();
        }).catch(function() {
            if (typeof ModalHelper !== 'undefined') ModalHelper.error('Erreur', 'Impossible de charger les données du document.');
        });
    }

    document.getElementById('rule-label').addEventListener('input', function() {
        if (!editingId) {
            document.getElementById('rule-slug').value = slugify(this.value);
        }
    });

    document.getElementById('btn-add-rule').addEventListener('click', function() {
        resetForm();
        bootstrap.Modal.getOrCreateInstance(document.getElementById('ruleModal')).show();
    });

    document.getElementById('ruleForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const label = document.getElementById('rule-label').value.trim();
        const slug = document.getElementById('rule-slug').value.trim();
        if (!label) {
            document.getElementById('rule-label').classList.add('is-invalid');
            document.getElementById('rule-label-error').textContent = 'Le nom est requis.';
            return;
        }
        if (!slug) {
            document.getElementById('rule-slug').classList.add('is-invalid');
            document.getElementById('rule-slug-error').textContent = 'Le slug est requis.';
            return;
        }
        const acteur = document.getElementById('rule-acteur').value;
        const payload = {
            context: 'registration',
            label: label,
            slug: slug,
            description: document.getElementById('rule-description').value.trim(),
            secteur_id: null,
            obligatoire: document.getElementById('rule-obligatoire').value === '1',
            acteur: acteur,
            types_mime: getTypesSelect() || null,
            max_size: parseInt(document.getElementById('rule-max-size').value) || null,
            is_active: document.getElementById('rule-active').value === '1',
            order_column: parseInt(document.getElementById('rule-order').value) || 0,
        };
        try {
            if (editingId) {
                await apiFetch(BASE_URL + '/' + editingId, { method: 'PUT', headers: postHeaders, body: JSON.stringify(payload) });
                if (typeof ModalHelper !== 'undefined') ModalHelper.success('Document mis à jour', 'Le document a été modifié avec succès.');
            } else {
                await apiFetch(BASE_URL, { method: 'POST', headers: postHeaders, body: JSON.stringify(payload) });
                if (typeof ModalHelper !== 'undefined') ModalHelper.success('Document créé', 'Le document a été ajouté avec succès.');
            }
            bootstrap.Modal.getInstance(document.getElementById('ruleModal')).hide();
            loadRules();
        } catch (e) {
            const resp = e.response?.data;
            const errors = resp?.errors || {};
            const msg = resp?.message || e.message || 'Erreur lors de l\'enregistrement.';
            const fieldMap = {
                slug: { el: 'rule-slug', err: 'rule-slug-error' },
                label: { el: 'rule-label', err: 'rule-label-error' },
            };
            const unmatched = [];
            let hasFieldError = false;
            for (const [field, messages] of Object.entries(errors)) {
                const map = fieldMap[field];
                if (map) {
                    document.getElementById(map.el).classList.add('is-invalid');
                    document.getElementById(map.err).textContent = messages[0] || '';
                    hasFieldError = true;
                } else {
                    unmatched.push(field + ' : ' + (messages[0] || ''));
                }
            }
            if (!hasFieldError && unmatched.length === 0) {
                if (typeof ModalHelper !== 'undefined') ModalHelper.error('Erreur', msg);
            } else if (unmatched.length > 0) {
                if (typeof ModalHelper !== 'undefined') ModalHelper.error('Erreur de validation', unmatched.join('<br>'));
            }
        }
    });

    document.getElementById('rules-tbody').addEventListener('click', function(e) {
        const btn = e.target.closest('button');
        if (!btn) return;
        const id = btn.getAttribute('data-id');
        if (btn.classList.contains('btn-edit-rule')) {
            openEdit(id);
        } else if (btn.classList.contains('btn-toggle-rule')) {
            const isActive = btn.getAttribute('data-active') === '1';
            ModalHelper.confirm(
                '<i class="bi bi-' + (isActive ? 'pause-circle' : 'play-circle') + ' me-2 text-' + (isActive ? 'warning' : 'success') + '"></i> ' + (isActive ? 'Désactiver' : 'Activer'),
                (isActive ? 'Désactiver' : 'Activer') + ' le document "' + escHtml(btn.closest('tr').querySelector('.fw-medium')?.textContent || '') + '" ?',
                'Oui, ' + (isActive ? 'désactiver' : 'activer'),
                'Annuler',
                isActive ? 'btn-warning' : 'btn-success'
            ).then(function(confirmed) {
                if (!confirmed) return;
                apiFetch(BASE_URL + '/' + id, { method: 'PUT', headers: postHeaders, body: JSON.stringify({ is_active: !isActive }) }).then(function() {
                    loadRules();
                }).catch(function() {
                    if (typeof ModalHelper !== 'undefined') ModalHelper.error('Erreur', 'Impossible de modifier le statut.');
                });
            });
        } else if (btn.classList.contains('btn-delete-rule')) {
            ModalHelper.confirm(
                '<i class="bi bi-trash me-2 text-danger"></i> Supprimer',
                'Supprimer définitivement ce document ? Cette action est irréversible.',
                'Oui, supprimer',
                'Annuler',
                'btn-danger'
            ).then(function(confirmed) {
                if (!confirmed) return;
                apiFetch(BASE_URL + '/' + id, { method: 'DELETE', headers: headers }).then(function() {
                    loadRules();
                }).catch(function() {
                    if (typeof ModalHelper !== 'undefined') ModalHelper.error('Erreur', 'Impossible de supprimer le document.');
                });
            });
        }
    });

    document.querySelectorAll('input[name="acteur-tab"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.checked) {
                loadRules();
            }
        });
    });

    loadRules();
});
</script>
<?php require_once __DIR__ . '/footer.php'; ?>
