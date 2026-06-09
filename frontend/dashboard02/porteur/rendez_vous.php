<?php
require_once __DIR__ . '/header.php';
?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <!-- PAGE HEADER -->
            <div class="page-header">
                <h1 class="page-title"><strong>RENDEZ-VOUS</strong></h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">ACCEUIL</a></li>
                        <li class="breadcrumb-item active" aria-current="page">RENDEZ-VOUS</li>
                    </ol>
                </div>
            </div>

            <!-- STATS CARDS (même design que mes_projets.php) -->
            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">PLANIFIÉS</span>
                                    <h2 class="mb-0 mt-1" id="statPlanifies">0</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-primary-transparent">
                                        <i class="bx bx-calendar-check fs-20 text-primary"></i>
                                    </span>
                                </div>
                                <div class="progress progress-sm mt-3 w-100">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
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
                                    <span class="text-muted text-uppercase fs-12 fw-bold">ACCEPTÉS</span>
                                    <h2 class="mb-0 mt-1" id="statAcceptes">0</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-success-transparent">
                                        <i class="bx bx-check-circle fs-20 text-success"></i>
                                    </span>
                                </div>
                                <div class="progress progress-sm mt-3 w-100">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
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
                                    <span class="text-muted text-uppercase fs-12 fw-bold">TERMINÉS</span>
                                    <h2 class="mb-0 mt-1" id="statTermines">0</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-secondary-transparent">
                                        <i class="bx bx-check-double fs-20 text-secondary"></i>
                                    </span>
                                </div>
                                <div class="progress progress-sm mt-3 w-100">
                                    <div class="progress-bar bg-secondary" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
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
                                    <span class="text-muted text-uppercase fs-12 fw-bold">REJETÉS</span>
                                    <h2 class="mb-0 mt-1" id="statRejetes">0</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-danger-transparent">
                                        <i class="bx bx-x-circle fs-20 text-danger"></i>
                                    </span>
                                </div>
                                <div class="progress progress-sm mt-3 w-100">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLEAU RENDEZ-VOUS -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h3 class="card-title mb-0">
                                <i class="bx bx-calendar-event me-2 text-primary"></i>Liste des rendez-vous
                            </h3>
                            <div class="d-flex gap-2 flex-wrap">
                                <select id="filterStatut" class="form-select form-select-sm w-auto">
                                    <option value="">Tous les statuts</option>
                                    <option value="planifie">Planifié</option>
                                    <option value="accepte">Accepté</option>
                                    <option value="rejete">Rejeté</option>
                                    <option value="termine">Terminé</option>
                                    <option value="annule">Annulé</option>
                                </select>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#rdvModal" onclick="openCreateModal()">
                                    <i class="bx bx-plus me-1"></i> Nouveau
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- VERSION DESKTOP -->
                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-bordered table-hover align-middle" id="rdvTableDesktop">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="bx bx-calendar me-1"></i> Date & Heure</th>
                                            <th><i class="bx bx-detail me-1"></i> Objet</th>
                                            <th><i class="bx bx-building me-1"></i> Institution</th>
                                            <th><i class="bx bx-folder me-1"></i> Projet</th>
                                            <th><i class="bx bx-map me-1"></i> Lieu</th>
                                            <th><i class="bx bx-badge me-1"></i> Statut</th>
                                            <th class="text-center"><i class="bx bx-cog me-1"></i> Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rdvTableBody">
                                        <tr><td colspan="7" class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div>
                                        </td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- VERSION MOBILE (CARDS) -->
                            <div class="d-md-none" id="rdvCardsBody">
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div>
                                </div>
                            </div>

                            <!-- PAGINATION -->
                            <nav id="rdvPagination" class="d-flex justify-content-center mt-3"></nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CREATION / EDITION -->
<div class="modal fade" id="rdvModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rdvModalTitle">
                    <i class="bx bx-calendar-plus me-2"></i>Nouveau rendez-vous
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label"><i class="bx bx-detail me-1"></i> Objet *</label>
                    <input type="text" class="form-control" id="rdvObjet" maxlength="255" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bx bx-calendar me-1"></i> Date & Heure *</label>
                    <input type="datetime-local" class="form-control" id="rdvDate" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bx bx-building me-1"></i> Institution</label>
                    <select class="form-select" id="rdvInstitution">
                        <option value="">-- Sélectionner --</option>
                    </select>
                    <small class="text-muted">Seules les institutions qui vous ont déjà contacté apparaissent</small>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bx bx-folder me-1"></i> Projet lié</label>
                    <select class="form-select" id="rdvProject">
                        <option value="">-- Aucun --</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bx bx-map me-1"></i> Lieu</label>
                    <input type="text" class="form-control" id="rdvLieu" maxlength="255" placeholder="Ex: Agence Cotonou, Visio...">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="bx bx-note me-1"></i> Description</label>
                    <textarea class="form-control" id="rdvDescription" rows="3" placeholder="Objet et ordre du jour..."></textarea>
                </div>
                <div id="rdvFormError" class="text-danger small mb-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-arrow-back me-1"></i> Annuler
                </button>
                <button type="button" class="btn btn-primary" id="rdvSaveBtn" onclick="saveRdv()">
                    <i class="bx bx-save me-1"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETAILS -->
<div class="modal fade" id="rdvDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-info-circle me-2"></i> Détails du rendez-vous</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="rdvDetailsBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-check me-1"></i> Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.rdv-statut-badge { font-size: 11px; padding: 4px 12px; border-radius: 999px; font-weight: 600; display: inline-block; }
.rdv-statut-planifie { background: #e8f1ff; color: #1a73e8; }
.rdv-statut-accepte { background: #e8f8f0; color: #1a8c5a; }
.rdv-statut-rejete { background: #fde8e8; color: #c0392b; }
.rdv-statut-termine { background: #f0e8fd; color: #6c2eb7; }
.rdv-statut-annule { background: #f5f5f5; color: #888; }
.rdv-card { border: 1px solid #e0e0e0; border-radius: 12px; padding: 16px; margin-bottom: 12px; background: #fff; transition: 0.2s; }
.rdv-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.rdv-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.rdv-card-body { font-size: 13px; color: #555; }
.rdv-card-body > div { margin-bottom: 6px; }
.rdv-card-actions { display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap; }
</style>

<script>
(function() {
    const apiBase = '/api/v1';
    let currentPage = 1;
    let currentStatut = '';
    let editingId = null;

    function apiHeaders(extra = {}) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value
            || '';
        return {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrf,
            ...extra
        };
    }

    function loadRdv(page = 1) {
        currentPage = page;
        const params = new URLSearchParams({ page: page });
        if (currentStatut) params.append('statut', currentStatut);

        fetch(apiBase + '/rendez-vous?' + params.toString(), { headers: apiHeaders() })
        .then(r => r.json())
        .then(data => {
            renderTable(data.data || []);
            renderCards(data.data || []);
            renderPagination(data);
            updateStats(data.data || []);
        })
        .catch(() => {
            document.getElementById('rdvTableBody').innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4"><i class="bx bx-error-circle fs-20 me-2"></i>Erreur de chargement</td></tr>';
        });
    }

    function updateStats(items) {
        const counts = { planifie: 0, accepte: 0, termine: 0, rejete: 0 };
        items.forEach(r => { if (counts[r.statut] !== undefined) counts[r.statut]++; });
        document.getElementById('statPlanifies').textContent = counts.planifie;
        document.getElementById('statAcceptes').textContent = counts.accepte;
        document.getElementById('statTermines').textContent = counts.termine;
        document.getElementById('statRejetes').textContent = counts.rejete;
    }

    function renderTable(items) {
        if (items.length === 0) {
            document.getElementById('rdvTableBody').innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted"><i class="bx bx-calendar-x fs-30 d-block mb-2"></i>Aucun rendez-vous trouvé</td></tr>';
            return;
        }
        document.getElementById('rdvTableBody').innerHTML = items.map(r => `
            <tr>
                <td><i class="bx bx-calendar me-1 text-primary"></i> ${formatDateTime(r.date_heure)}</td>
                <td><strong>${escHtml(r.objet)}</strong></td>
                <td>${r.institution ? '<i class="bx bx-building me-1"></i> ' + escHtml(r.institution.nom) : '<span class="text-muted">--</span>'}</td>
                <td>${r.project ? '<i class="bx bx-folder me-1"></i> ' + escHtml(r.project.titre) : '<span class="text-muted">--</span>'}</td>
                <td>${r.lieu ? '<i class="bx bx-map me-1"></i> ' + escHtml(r.lieu) : '<span class="text-muted">--</span>'}</td>
                <td><span class="rdv-statut-badge rdv-statut-${r.statut}"><i class="bx ${statutIcon(r.statut)} me-1"></i>${statutLabel(r.statut)}</span></td>
                <td class="text-center">
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bx bx-cog"></i> Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="event.stopPropagation();viewRdv(${r.id});return false;"><i class="bx bx-show me-2"></i> Voir</a></li>
                            ${r.statut === 'planifie' ? `
                                <li><a class="dropdown-item text-success" href="#" onclick="event.stopPropagation();acceptRdv(${r.id});return false;"><i class="bx bx-check me-2"></i> Accepter</a></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="event.stopPropagation();rejectRdv(${r.id});return false;"><i class="bx bx-x me-2"></i> Rejeter</a></li>
                            ` : ''}
                            ${!r.from_institution ? `
                            <li><a class="dropdown-item" href="#" onclick="event.stopPropagation();editRdv(${r.id});return false;"><i class="bx bx-pencil me-2"></i> Modifier</a></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="event.stopPropagation();deleteRdv(${r.id});return false;"><i class="bx bx-trash me-2"></i> Supprimer</a></li>
                            ` : ''}
                        </ul>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function renderCards(items) {
        const container = document.getElementById('rdvCardsBody');
        if (items.length === 0) {
            container.innerHTML = '<div class="text-center py-5 text-muted"><i class="bx bx-calendar-x fs-40 d-block mb-2"></i>Aucun rendez-vous trouvé</div>';
            return;
        }
        container.innerHTML = items.map(r => `
            <div class="rdv-card">
                <div class="rdv-card-header">
                    <div>
                        <strong>${escHtml(r.objet)}</strong>
                    </div>
                    <span class="rdv-statut-badge rdv-statut-${r.statut}"><i class="bx ${statutIcon(r.statut)} me-1"></i>${statutLabel(r.statut)}</span>
                </div>
                <div class="rdv-card-body">
                    <div><i class="bx bx-calendar me-1"></i> ${formatDateTime(r.date_heure)}</div>
                    ${r.institution ? `<div><i class="bx bx-building me-1"></i> ${escHtml(r.institution.nom)}</div>` : ''}
                    ${r.lieu ? `<div><i class="bx bx-map me-1"></i> ${escHtml(r.lieu)}</div>` : ''}
                </div>
                <div class="rdv-card-actions">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewRdv(${r.id})"><i class="bx bx-show me-1"></i>Voir</button>
                    ${r.statut === 'planifie' ? `
                        <button class="btn btn-sm btn-success" onclick="acceptRdv(${r.id})"><i class="bx bx-check me-1"></i>Accepter</button>
                        <button class="btn btn-sm btn-outline-danger" onclick="rejectRdv(${r.id})"><i class="bx bx-x me-1"></i>Rejeter</button>
                    ` : ''}
                    ${!r.from_institution ? `
                    <button class="btn btn-sm btn-outline-secondary" onclick="editRdv(${r.id})"><i class="bx bx-pencil me-1"></i></button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteRdv(${r.id})"><i class="bx bx-trash me-1"></i></button>
                    ` : ''}
                </div>
            </div>
        `).join('');
    }

    function renderPagination(data) {
        if (!data.last_page || data.last_page <= 1) { document.getElementById('rdvPagination').innerHTML = ''; return; }
        let html = '<ul class="pagination pagination-sm">';
        if (data.prev_page_url) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="loadRdv(${data.current_page-1});return false;">Précédent</a></li>`;
        }
        for (let i = 1; i <= data.last_page; i++) {
            html += `<li class="page-item ${i === data.current_page ? 'active' : ''}"><a class="page-link" href="#" onclick="loadRdv(${i});return false;">${i}</a></li>`;
        }
        if (data.next_page_url) {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="loadRdv(${data.current_page+1});return false;">Suivant</a></li>`;
        }
        html += '</ul>';
        document.getElementById('rdvPagination').innerHTML = html;
    }

    function formatDateTime(dt) {
        if (!dt) return '--';
        const d = new Date(dt);
        return d.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }
    function statutLabel(s) {
        return { planifie: 'Planifié', accepte: 'Accepté', rejete: 'Rejeté', termine: 'Terminé', annule: 'Annulé' }[s] || s;
    }
    function statutIcon(s) {
        return { planifie: 'bx-calendar', accepte: 'bx-check-circle', rejete: 'bx-x-circle', termine: 'bx-check-double', annule: 'bx-block' }[s] || 'bx-calendar';
    }
    function escHtml(str) { return str ? String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') : ''; }

    window.viewRdv = function(id) {
        fetch(apiBase + '/rendez-vous/' + id, { headers: apiHeaders() })
        .then(r => r.json())
        .then(rdv => {
            document.getElementById('rdvDetailsBody').innerHTML = `
                <div class="mb-2"><i class="bx bx-detail me-2 text-primary"></i><strong>Objet :</strong> ${escHtml(rdv.objet)}</div>
                <div class="mb-2"><i class="bx bx-calendar me-2 text-primary"></i><strong>Date & Heure :</strong> ${formatDateTime(rdv.date_heure)}</div>
                <div class="mb-2"><i class="bx bx-badge me-2"></i><strong>Statut :</strong> <span class="rdv-statut-badge rdv-statut-${rdv.statut}">${statutLabel(rdv.statut)}</span></div>
                ${rdv.institution ? `<div class="mb-2"><i class="bx bx-building me-2"></i><strong>Institution :</strong> ${escHtml(rdv.institution.nom)}</div>` : ''}
                ${rdv.project ? `<div class="mb-2"><i class="bx bx-folder me-2"></i><strong>Projet :</strong> ${escHtml(rdv.project.titre)}</div>` : ''}
                ${rdv.lieu ? `<div class="mb-2"><i class="bx bx-map me-2"></i><strong>Lieu :</strong> ${escHtml(rdv.lieu)}</div>` : ''}
                ${rdv.description ? `<div class="mb-2"><i class="bx bx-note me-2"></i><strong>Description :</strong> ${escHtml(rdv.description)}</div>` : ''}
                ${rdv.notes_porteur ? `<div class="mb-2"><i class="bx bx-user me-2"></i><strong>Mes notes :</strong> ${escHtml(rdv.notes_porteur)}</div>` : ''}
            `;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('rdvDetailsModal')).show();
        });
    };

    window.acceptRdv = async function(id) {
        var confirmed = await ModalHelper.confirm('<i class="bi bi-check-circle me-2 text-success"></i> Accepter', 'Accepter ce rendez-vous ?', 'Oui, accepter', 'Annuler', 'btn-success');
        if (!confirmed) return;
        fetch(apiBase + '/rendez-vous/' + id + '/accept', {
            method: 'POST',
            headers: apiHeaders({ 'Content-Type': 'application/json' })
        }).then(() => loadRdv(currentPage));
    };

    window.rejectRdv = async function(id) {
        var confirmed = await ModalHelper.confirm('<i class="bi bi-x-circle me-2 text-danger"></i> Rejeter', 'Rejeter ce rendez-vous ?', 'Oui, rejeter', 'Annuler', 'btn-danger');
        if (!confirmed) return;
        fetch(apiBase + '/rendez-vous/' + id + '/reject', {
            method: 'POST',
            headers: apiHeaders({ 'Content-Type': 'application/json' })
        }).then(() => loadRdv(currentPage));
    };

    window.editRdv = function(id) {
        editingId = id;
        document.getElementById('rdvModalTitle').innerHTML = '<i class="bx bx-pencil me-2"></i> Modifier le rendez-vous';
        fetch(apiBase + '/rendez-vous/' + id, { headers: apiHeaders() })
        .then(r => r.json())
        .then(rdv => {
            if (rdv.from_institution) {
                ModalHelper.alert('<i class="bi bi-info-circle me-2 text-info"></i> Information', 'Ce rendez-vous a été créé par l\'institution et ne peut pas être modifié.');
                editingId = null;
                return;
            }
            document.getElementById('rdvObjet').value = rdv.objet || '';
            document.getElementById('rdvDate').value = rdv.date_heure ? rdv.date_heure.slice(0, 16) : '';
            document.getElementById('rdvLieu').value = rdv.lieu || '';
            document.getElementById('rdvDescription').value = rdv.description || '';
            document.getElementById('rdvInstitution').value = rdv.institution_id || '';
            document.getElementById('rdvProject').value = rdv.project_id || '';
            document.getElementById('rdvFormError').textContent = '';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('rdvModal')).show();
        });
    };

    window.deleteRdv = async function(id) {
        var confirmed = await ModalHelper.confirm('<i class="bi bi-trash me-2 text-danger"></i> Supprimer', 'Supprimer ce rendez-vous ?', 'Oui, supprimer', 'Annuler', 'btn-danger');
        if (!confirmed) return;
        fetch(apiBase + '/rendez-vous/' + id, {
            method: 'DELETE',
            headers: apiHeaders()
        })
        .then(r => {
            if (r.status === 403) return r.json().then(d => { ModalHelper.error('Erreur', d.message); return null; });
            return r;
        })
        .then(result => { if (result) loadRdv(currentPage); });
    };

    window.openCreateModal = function() {
        editingId = null;
        document.getElementById('rdvModalTitle').innerHTML = '<i class="bx bx-calendar-plus me-2"></i> Nouveau rendez-vous';
        document.getElementById('rdvObjet').value = '';
        document.getElementById('rdvDate').value = '';
        document.getElementById('rdvLieu').value = '';
        document.getElementById('rdvDescription').value = '';
        document.getElementById('rdvInstitution').value = '';
        document.getElementById('rdvProject').value = '';
        document.getElementById('rdvFormError').textContent = '';
        loadInstitutions();
        loadProjects();
    };

    window.saveRdv = function() {
        const payload = {
            objet: document.getElementById('rdvObjet').value.trim(),
            date_heure: document.getElementById('rdvDate').value,
            institution_id: document.getElementById('rdvInstitution').value || null,
            project_id: document.getElementById('rdvProject').value || null,
            lieu: document.getElementById('rdvLieu').value.trim() || null,
            description: document.getElementById('rdvDescription').value.trim() || null,
        };
        if (!payload.objet || !payload.date_heure) {
            document.getElementById('rdvFormError').innerHTML = '<i class="bx bx-error-circle me-1"></i> Objet et date sont requis.';
            return;
        }
        const url = editingId ? apiBase + '/rendez-vous/' + editingId : apiBase + '/rendez-vous';
        const method = editingId ? 'PUT' : 'POST';
        fetch(url, {
            method: method,
            headers: apiHeaders({ 'Content-Type': 'application/json' }),
            body: JSON.stringify(payload)
        })
        .then(r => r.json().then(d => ({ status: r.status, data: d })))
        .then(({ status, data }) => {
            if (status >= 400) {
                document.getElementById('rdvFormError').innerHTML = '<i class="bx bx-error-circle me-1"></i> ' + (data.message || 'Erreur lors de l\'enregistrement');
                return;
            }
            bootstrap.Modal.getInstance(document.getElementById('rdvModal')).hide();
            loadRdv(currentPage);
        });
    };

    function loadInstitutions() {
        const sel = document.getElementById('rdvInstitution');
        fetch(apiBase + '/porteur/institutions', { headers: apiHeaders() })
        .then(r => r.json())
        .then(instData => {
            const insts = Array.isArray(instData) ? instData : (instData.data || []);
            sel.innerHTML = '<option value="">-- Sélectionner --</option>';
            if (insts.length === 0) {
                sel.innerHTML = '<option value="">Aucune institution disponible</option>';
                sel.disabled = true;
                return;
            }
            sel.disabled = false;
            insts.forEach(i => {
                sel.innerHTML += `<option value="${i.id}">${escHtml(i.nom)}</option>`;
            });
        })
        .catch(() => {
            sel.innerHTML = '<option value="">Erreur chargement</option>';
        });
    }

    function loadProjects() {
        fetch(apiBase + '/porteur/projects-list', { headers: apiHeaders() })
        .then(r => r.json())
        .then(data => {
            const sel = document.getElementById('rdvProject');
            sel.innerHTML = '<option value="">-- Aucun --</option>';
            (data.data || []).forEach(p => sel.innerHTML += `<option value="${p.id}">${escHtml(p.titre)}</option>`);
        });
    }

    document.getElementById('filterStatut').addEventListener('change', function() {
        currentStatut = this.value;
        loadRdv(1);
    });

    loadRdv(1);
})();
</script>

<?php
require_once __DIR__ . '/footer.php';
?>

  