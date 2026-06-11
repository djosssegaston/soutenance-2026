/**
 * Entretiens Planifiés - Institution Dashboard
 */

const API_BASE = '/api/v1';
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    // Initial Load
    fetchStats();
    fetchInterviews();
    loadProjectsForSelect();

    // Event Listeners
    document.getElementById('search-btn').addEventListener('click', () => {
        currentPage = 1;
        fetchInterviews();
    });

    document.getElementById('form-create-interview').addEventListener('submit', handleCreateInterview);
    
    // Select2 pour le modal
    if ($.fn.select2) {
        $('.select2-modal').select2({
            dropdownParent: $('#modal-create-interview')
        });
    }
});

/**
 * Récupère les stats
 */
async function fetchStats() {
    try {
        const response = await fetch(`${API_BASE}/institution/entretiens/stats`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) return;
        const data = await response.json();

        document.getElementById('stat-today').textContent = data.aujourdhui;
        document.getElementById('stat-confirmed').textContent = data.confirmes;
        document.getElementById('stat-pending').textContent = data.en_attente;
        document.getElementById('stat-cancelled').textContent = data.annules;
    } catch (error) {
        console.error('Error fetching stats:', error);
    }
}

/**
 * Charge la liste des projets pour le select de création
 */
async function loadProjectsForSelect() {
    try {
        const response = await fetch(`${API_BASE}/institution/projets?per_page=50`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        const select = document.getElementById('select-project');
        
        select.innerHTML = '<option value="">Choisir un projet...</option>';
        (data.data || []).forEach(project => {
            const option = new Option(project.titre, project.id);
            select.add(option);
        });
    } catch (error) {
        console.error('Error loading projects:', error);
    }
}

/**
 * Récupère la liste des entretiens
 */
async function fetchInterviews(page = 1) {
    currentPage = page;
    const loader = document.getElementById('interviews-loader');
    const container = document.getElementById('interviews-list');
    
    if (loader) loader.style.display = 'block';
    if (container) container.innerHTML = '';

    const statut = document.getElementById('filter-status').value;
    const date = document.getElementById('filter-date').value;

    try {
        const response = await fetch(`${API_BASE}/institution/entretiens?page=${page}&statut=${statut}&date=${date}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();

        renderInterviews(data.data);
        renderPagination(data);
    } catch (error) {
        console.error('Error fetching interviews:', error);
        container.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erreur de chargement.</td></tr>';
    } finally {
        if (loader) loader.style.display = 'none';
    }
}

/**
 * Affiche les lignes d'entretiens
 */
function renderInterviews(interviews) {
    const container = document.getElementById('interviews-list');
    container.innerHTML = '';

    if (interviews.length === 0) {
        container.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Aucun entretien programmé.</td></tr>';
        return;
    }

    interviews.forEach(item => {
        const statusBadge = getStatusBadge(item.statut);
        const row = `
            <tr>
                <td>
                    <div class="fw-bold">${new Date(item.date_entretien).toLocaleDateString()}</div>
                    <div class="small text-muted">${item.heure_entretien.substring(0, 5)}</div>
                </td>
                <td>
                    <div class="fw-semibold">${item.project.titre}</div>
                    <div class="small text-muted">${item.porteur.name}</div>
                </td>
                <td>
                    <span class="badge bg-light text-dark">${item.type_entretien.toUpperCase()}</span>
                    <div class="small text-truncate" style="max-width: 150px;">${item.lieu || ''}</div>
                </td>
                <td>
                    <div class="small">${item.analyste ? item.analyste.name : ''}</div>
                </td>
                <td>${statusBadge}</td>
                <td>
                    <div class="btn-list">
                        <button class="btn btn-sm btn-primary" onclick="event.stopPropagation();viewInterviewDetails(${item.id})">
                            <i class="fe fe-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-icon btn-info" onclick="event.stopPropagation();openDiscussion(${item.project_id})">
                            <i class="fe fe-message-circle"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        container.innerHTML += row;
    });
}

/**
 * Pagination
 */
function renderPagination(data) {
    const container = document.getElementById('pagination-container');
    if (!container || !data.links) return;

    let html = '<ul class="pagination pagination-rounded">';
    data.links.forEach(link => {
        if (link.url) {
            const pageNum = link.url.split('page=')[1];
            html += `<li class="page-item ${link.active ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" onclick="fetchInterviews(${pageNum})">${link.label}</a></li>`;
        }
    });
    html += '</ul>';
    container.innerHTML = html;
}

/**
 * Création d'un entretien
 */
async function handleCreateInterview(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch(`${API_BASE}/institution/entretiens`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            ALOGOTO.success('Entretien programmé avec succès.');
            bootstrap.Modal.getInstance(document.getElementById('modal-create-interview')).hide();
            e.target.reset();
            fetchInterviews();
            fetchStats();
        } else {
            const err = await response.json();
            ALOGOTO.error(err.message || 'Erreur lors de la création.');
        }
    } catch (error) {
        console.error('Error creating interview:', error);
    }
}

/**
 * Détails et actions sur un entretien
 */
async function viewInterviewDetails(id) {
    try {
        const response = await fetch(`${API_BASE}/institution/entretiens/${id}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const item = await response.json();

        const container = document.getElementById('modal-details-body');
        container.innerHTML = `
            <div class="row">
                <div class="col-md-7">
                    <h6 class="fw-bold mb-3">Informations Générales</h6>
                    <table class="table table-sm">
                        <tr><td class="fw-bold">Objet:</td><td>${item.titre}</td></tr>
                        <tr><td class="fw-bold">Projet:</td><td>${item.project.titre}</td></tr>
                        <tr><td class="fw-bold">Porteur:</td><td>${item.porteur.name}</td></tr>
                        <tr><td class="fw-bold">Date & Heure:</td><td>${new Date(item.date_entretien).toLocaleDateString()} à ${item.heure_entretien.substring(0, 5)}</td></tr>
                        <tr><td class="fw-bold">Lieu / Lien:</td><td>${item.lieu || ''}</td></tr>
                    </table>
                    
                    <h6 class="fw-bold mt-4">Description / Consignes</h6>
                    <p class="text-muted p-2 bg-light rounded">${item.description || 'Aucune consigne.'}</p>

                    <h6 class="fw-bold mt-4">Compte Rendu & Décision</h6>
                    <div id="report-section">
                        ${item.statut === 'termine' ? `
                            <div class="alert alert-success">
                                <strong>Décision: ${item.decision_preliminaire}</strong><br>
                                ${item.compte_rendu}
                            </div>
                        ` : `
                            <div class="form-group">
                                <label class="form-label">Rédaction du Compte Rendu</label>
                                <textarea class="form-control" id="report-text" rows="4" placeholder="Points clés de l'entretien..."></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Décision Préliminaire</label>
                                <select class="form-control" id="report-decision">
                                    <option value="favorable">Avis Favorable</option>
                                    <option value="reserve">Avis Réservé</option>
                                    <option value="defavorable">Avis Défavorable</option>
                                </select>
                            </div>
                            <button class="btn btn-primary" onclick="submitReport(${item.id})">Enregistrer le Compte Rendu</button>
                        `}
                    </div>
                </div>
                <div class="col-md-5 border-start">
                    <h6 class="fw-bold mb-3">Historique des Actions</h6>
                    <div class="timeline-v2">
                        ${item.histories.map(h => `
                            <div class="mb-3 ps-3 border-start">
                                <div class="small fw-bold">${h.action.toUpperCase()}</div>
                                <div class="text-muted fs-11">${new Date(h.created_at).toLocaleString()}</div>
                                <div class="small">${h.details}</div>
                                <div class="text-muted fs-10">Par: ${h.auteur}</div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;

        // Actions
        document.getElementById('btn-confirm').onclick = () => handleAction(id, 'confirm');
        document.getElementById('btn-cancel').onclick = async () => {
            const modalEl = document.getElementById('modal-interview-details');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            const { value: reason } = await Swal.fire({
                title: "Raison de l'annulation",
                input: 'text',
                inputPlaceholder: 'Motif...',
                showCancelButton: true,
                cancelButtonText: 'Annuler',
                confirmButtonText: 'Confirmer',
                reverseButtons: true,
            });
            if (reason) handleAction(id, 'cancel', reason);
        };
        document.getElementById('btn-convocation').onclick = () => {
            window.location.href = '/dashboard/institution/interviews/' + id + '/convocation';
        };

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-interview-details')).show();
    } catch (error) {
        console.error('Error fetching details:', error);
    }
}

async function handleAction(id, action, comment = '') {
    const modalEl = document.getElementById('modal-interview-details');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    try {
        const response = await fetch(`${API_BASE}/institution/entretiens/${id}/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ commentaire: comment })
        });
        if (response.ok) {
            ALOGOTO.success('Action effectuée.');
            fetchInterviews();
        }
    } catch (error) {
        console.error('Action error:', error);
    }
}

async function submitReport(id) {
    const text = document.getElementById('report-text').value;
    const decision = document.getElementById('report-decision').value;
    if (!text) return ALOGOTO.warning('Le compte rendu est obligatoire.');

    try {
        const response = await fetch(`${API_BASE}/institution/entretiens/${id}/report`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ compte_rendu: text, decision_preliminaire: decision })
        });
        if (response.ok) {
            ALOGOTO.success('Compte rendu enregistré.');
            bootstrap.Modal.getInstance(document.getElementById('modal-interview-details')).hide();
            fetchInterviews();
        }
    } catch (error) {
        console.error('Report error:', error);
    }
}

function openDiscussion(projectId) {
    window.location.href = `messages.php?project_id=${projectId}`;
}

// Helpers
function getStatusBadge(statut) {
    const map = {
        'programme': '<span class="badge bg-primary">Programmé</span>',
        'confirme': '<span class="badge bg-success">Confirmé</span>',
        'termine': '<span class="badge bg-info">Terminé</span>',
        'annule': '<span class="badge bg-danger">Annulé</span>',
        'reporte': '<span class="badge bg-warning">Reporté</span>'
    };
    return map[statut] || `<span class="badge bg-secondary">${statut}</span>`;
}

// polyfill pour Object.fromEntries si besoin
if (!Object.fromEntries) {
    Object.fromEntries = function(entries) {
        const obj = {};
        for (const [key, value] of entries) {
            obj[key] = value;
        }
        return obj;
    };
}
