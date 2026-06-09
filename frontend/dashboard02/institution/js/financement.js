/**
 * Gestion des Financements - Institution Dashboard
 */

const API_BASE = '/api/v1';
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    // Initial Load
    fetchStats();
    fetchFundings();
    loadProjectsForProposal();

    // Auto-select project from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const preselectedProjectId = urlParams.get('project_id');

    // Event Listeners
    document.getElementById('search-btn').addEventListener('click', () => {
        currentPage = 1;
        fetchFundings();
    });

    document.getElementById('form-propose-funding').addEventListener('submit', handleProposeFunding);

    // Select2 pour le modal
    if ($.fn.select2) {
        $('.select2-modal').select2({
            dropdownParent: $('#modal-propose-funding')
        });
    }

    // Prevent modal backdrop stacking
    document.querySelectorAll('.modal').forEach(m => {
        m.addEventListener('hidden.bs.modal', () => {
            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
        });
    });

    // Auto-open modal if project_id is in URL
    if (preselectedProjectId) {
        const checkSelect = setInterval(() => {
            const select = document.getElementById('select-project');
            if (select.options.length > 1) {
                clearInterval(checkSelect);
                select.value = preselectedProjectId;
                $(select).trigger('change');
                const modalEl = document.getElementById('modal-propose-funding');
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        }, 100);
    }
});

/**
 * Récupère les stats
 */
async function fetchStats() {
    try {
        const response = await fetch(`${API_BASE}/institution/financements/statistiques`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) return;
        const data = await response.json();

        document.getElementById('stat-total-funded').textContent = formatMoney(data.total_finance);
        document.getElementById('stat-active-count').textContent = data.actifs;
        document.getElementById('stat-pending-count').textContent = data.en_attente;
        const roi = parseFloat(data.roi_estime) || 0;
        document.getElementById('stat-avg-roi').textContent = `${roi.toFixed(1)}%`;
    } catch (error) {
        console.error('Error fetching stats:', error);
    }
}

/**
 * Charge les projets analysés pour la proposition
 */
async function loadProjectsForProposal() {
    try {
        const response = await fetch(`${API_BASE}/institution/analyses?statut=approuve&financeable=1`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        const select = document.getElementById('select-project');
        
        select.innerHTML = '<option value="">Choisir un projet à financer...</option>';
        data.data.forEach(analysis => {
            const option = new Option(`${analysis.project.titre} (${formatMoney(analysis.project.montant_demande)})`, analysis.project.id);
            select.add(option);
        });
    } catch (error) {
        console.error('Error loading projects:', error);
    }
}

/**
 * Récupère la liste des financements
 */
async function fetchFundings(page = 1) {
    currentPage = page;
    const loader = document.getElementById('fundings-loader');
    const container = document.getElementById('fundings-list');
    
    if (loader) loader.style.display = 'block';
    if (container) container.innerHTML = '';

    const statut = document.getElementById('filter-status').value;

    try {
        const response = await fetch(`${API_BASE}/institution/financements?page=${page}&statut=${statut}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();

        renderFundings(data.data);
        renderPagination(data);
    } catch (error) {
        console.error('Error fetching fundings:', error);
        container.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erreur de chargement.</td></tr>';
    } finally {
        if (loader) loader.style.display = 'none';
    }
}

/**
 * Affiche les lignes
 */
function renderFundings(fundings) {
    const container = document.getElementById('fundings-list');
    container.innerHTML = '';

    if (fundings.length === 0) {
        container.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">Aucun dossier de financement trouvé.</td></tr>';
        return;
    }

    fundings.forEach(item => {
        const statusBadge = getStatusBadge(item.statut);
        const progress = item.project.montant_demande > 0 ? (item.project.montant_finance / item.project.montant_demande) * 100 : 0;
        
        const row = `
            <tr>
                <td>
                    <div class="fw-semibold">${item.project.titre}</div>
                    <div class="small text-muted">${item.project.owner.name}</div>
                </td>
                <td><span class="fw-bold">${formatMoney(item.montant_propose)}</span></td>
                <td>
                    <div class="small">${item.taux_interet}% / ${item.duree} mois</div>
                </td>
                <td>
                    <div class="progress progress-xs mb-1">
                        <div class="progress-bar bg-success" style="width: ${progress}%"></div>
                    </div>
                    <small class="text-muted">${Math.round(progress)}% financé</small>
                </td>
                <td>${statusBadge}</td>
                <td>
                    ${getActionButton(item)}
                </td>
            </tr>
        `;
        container.innerHTML += row;
    });
}

/**
 * Proposition de financement
 */
async function handleProposeFunding(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch(`${API_BASE}/institution/financements`, {
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
            ALOGOTO.success('Proposition envoyée avec succès.');
            bootstrap.Modal.getInstance(document.getElementById('modal-propose-funding')).hide();
            e.target.reset();
            fetchFundings();
            fetchStats();
        } else {
            const err = await response.json();
            ALOGOTO.error(err.message || 'Erreur lors de l\'envoi.');
        }
    } catch (error) {
        console.error('Proposal error:', error);
    }
}

/**
 * Détails du dossier
 */
async function viewFundingDetails(id) {
    try {
        const response = await fetch(`${API_BASE}/institution/financements/${id}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        const item = data.funding || data;


        // Compute echeances installment table HTML
        const echeancesList = item.echeances || [];
        const echeancesTableHtml = echeancesList.length > 0 ? `
        <h6 class="fw-bold mt-4">Échéancier & Plan d'Amortissement</h6>
        <div class="table-responsive">
            <table class="table table-sm table-bordered table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>N°</th>
                        <th>Date d'échéance</th>
                        <th class="text-end">Capital</th>
                        <th class="text-end">Intérêts</th>
                        <th class="text-end">Total dû</th>
                        <th class="text-end">Payé</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    ${echeancesList.map((e, i) => `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${new Date(e.date_echeance).toLocaleDateString('fr-FR')}</td>
                            <td class="text-end">${formatMoney(e.montant_capital)}</td>
                            <td class="text-end">${formatMoney(e.montant_interets)}</td>
                            <td class="text-end fw-bold">${formatMoney(e.montant_total)}</td>
                            <td class="text-end">${formatMoney(e.montant_paye || 0)}</td>
                            <td>${getEcheanceStatusBadge(e.statut)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        ` : '';
        const container = document.getElementById('modal-details-body');
        container.innerHTML = `
            <div class="row">
                <div class="col-md-7">
                    <h6 class="fw-bold mb-3">Récapitulatif de l'Offre</h6>
                    <table class="table table-sm">
                        <tr><td>Projet:</td><td class="fw-bold">${item.project.titre}</td></tr>
                        <tr><td>Montant Proposé:</td><td class="fw-bold text-primary">${formatMoney(item.montant_propose)}</td></tr>
                        <tr><td>Taux d'intérêt:</td><td>${item.taux_interet}%</td></tr>
                        <tr><td>Durée:</td><td>${item.duree} mois</td></tr>
                        <tr><td>Statut Actuel:</td><td>${getStatusBadge(item.statut)}</td></tr>
                    </table>
                    
                    <h6 class="fw-bold mt-4">Conditions & Commentaires</h6>
                    <p class="p-3 bg-light rounded">${item.commentaires || 'Aucun commentaire.'}</p>

                    ${item.montant_mensuel ? `
                    <h6 class="fw-bold mt-4">Plan de Remboursement du Porteur</h6>
                    <div class="p-3 bg-light rounded">
                        <div class="row">
                            <div class="col-6"><small class="text-muted">Montant mensuel</small><div class="fw-bold fs-5 text-success">${formatMoney(item.montant_mensuel)}</div></div>
                            <div class="col-6"><small class="text-muted">Jour de remboursement</small><div class="fw-bold fs-5 text-primary">Le ${item.jour_remboursement}e du mois</div></div>
                        </div>
                        ${item.commentaire_plan ? `<hr class="my-2"><small class="text-muted">Commentaire du porteur</small><p class="mb-0">${item.commentaire_plan}</p>` : ''}
                    </div>
                    ` : ''}

                    ${echeancesTableHtml}


                    <h6 class="fw-bold mt-4">Documents du Projet (Porteur)</h6>
                    ${renderProjectDocs(item.project.documents)}

                    <h6 class="fw-bold mt-4">Historique du Dossier</h6>
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
                <div class="col-md-5 border-start text-center">
                    <h6 class="fw-bold mb-3">Progression du Décaissement</h6>
                    <div class="chart-circle chart-circle-md mt-4" data-value="${item.montant_decaisse / item.montant_valide || 0}" data-thickness="8" data-color="#09ad95">
                        <div class="chart-circle-value">
                            <h4 class="mb-0">${Math.round((item.montant_decaisse / item.montant_valide || 0) * 100)}%</h4>
                        </div>
                    </div>
                    <div class="mt-4">
                        <small class="text-muted d-block">Montant Validé</small>
                        <h5 class="fw-bold">${formatMoney(item.montant_valide)}</h5>
                        <small class="text-muted d-block mt-2">Montant Décaissé</small>
                        <h5 class="fw-bold text-success">${formatMoney(item.montant_decaisse)}</h5>
                    </div>
                </div>
            </div>
        `;

        // Boutons d'action selon le statut
        const approveBtn = document.getElementById('btn-approve-plan');
        const rejectBtn = document.getElementById('btn-reject-plan');
        const revisionBtn = document.getElementById('btn-request-revision');
        const disburseBtn = document.getElementById('btn-disburse');

        [approveBtn, rejectBtn, revisionBtn, disburseBtn].forEach(b => b.classList.add('d-none'));

        if (item.statut === 'awaiting_imf_validation') {
            approveBtn.classList.remove('d-none');
            rejectBtn.classList.remove('d-none');
            revisionBtn.classList.remove('d-none');
            approveBtn.onclick = () => handleApprovePlan(id);
            rejectBtn.onclick = () => handleRejectPlan(id);
            revisionBtn.onclick = () => handleRequestRevision(id);
        } else if (item.statut === 'approved') {
            disburseBtn.classList.remove('d-none');
            disburseBtn.onclick = () => handleDisbursement(id);
        }

        // Action documents — liens directs (pas de modal)
        const docsBtn = document.getElementById('btn-docs');
        if (docsBtn) {
            docsBtn.style.display = (item.documents && item.documents.length > 0) ? 'inline-block' : 'none';
            docsBtn.onclick = () => {
                const docs = item.documents || [];
                if (docs.length === 0) {
                    ALOGOTO.info('Aucun document associé à ce financement.');
                    return;
                }
                const docList = docs.map(d => `
                    <div class="d-flex align-items-center mb-2 p-2 border rounded">
                        <i class="fe fe-file-text me-2 fs-16 text-info"></i>
                        <span class="flex-fill">${d.type_document}</span>
                        <a href="/documents/secure/financement/${d.id}/view" class="btn btn-sm btn-outline-primary">
                            <i class="fe fe-eye"></i> Voir
                        </a>
                    </div>
                `).join('');
                Swal.fire({
                    title: 'Documents du financement',
                    html: `<div style="max-height: 400px; overflow-y: auto;">${docList}</div>`,
                    icon: 'info',
                    confirmButtonText: 'Fermer'
                });
            };
        }

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-funding-details')).show();
    } catch (error) {
        console.error('Error fetching details:', error);
    }
}

async function handleApprovePlan(id) {
    const modalEl = document.getElementById('modal-funding-details');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    const { isConfirmed } = await ALOGOTO.confirm(
        'Approuver le plan',
        'Voulez-vous approuver le plan de remboursement du porteur et procéder au décaissement ?',
        'Oui, approuver',
        'Annuler'
    );
    if (!isConfirmed) return;

    try {
        const response = await fetch(`${API_BASE}/institution/financements/${id}/approuver-plan`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.ok) {
            ALOGOTO.success('Plan approuvé, décaissement effectué.');
            fetchFundings();
            fetchStats();
        } else {
            const result = await response.json().catch(() => ({}));
            ALOGOTO.error(result.message || 'Erreur lors de l\'approbation.');
        }
    } catch (error) {
        ALOGOTO.error('Erreur de connexion.');
    }
}

async function handleRejectPlan(id) {
    const modalEl = document.getElementById('modal-funding-details');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    const { value: motif } = await Swal.fire({
        title: 'Rejeter le plan',
        text: 'Veuillez indiquer le motif du rejet',
        input: 'textarea',
        inputPlaceholder: 'Motif du rejet...',
        showCancelButton: true,
        confirmButtonText: 'Rejeter',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#dc3545',
        inputValidator: (value) => { if (!value) return 'Veuillez saisir un motif'; }
    });
    if (!motif) return;

    try {
        const response = await fetch(`${API_BASE}/institution/financements/${id}/rejeter-plan`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ motif })
        });

        if (response.ok) {
            ALOGOTO.success('Plan rejeté.');
            fetchFundings();
            fetchStats();
        } else {
            const result = await response.json().catch(() => ({}));
            ALOGOTO.error(result.message || 'Erreur lors du rejet.');
        }
    } catch (error) {
        ALOGOTO.error('Erreur de connexion.');
    }
}

async function handleRequestRevision(id) {
    const modalEl = document.getElementById('modal-funding-details');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    const { value: commentaire } = await Swal.fire({
        title: 'Demander une révision',
        text: 'Expliquez au porteur ce qui doit être modifié dans son plan',
        input: 'textarea',
        inputPlaceholder: 'Commentaires pour le porteur...',
        showCancelButton: true,
        confirmButtonText: 'Envoyer',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#f7b731',
        inputValidator: (value) => { if (!value) return 'Veuillez saisir un commentaire'; }
    });
    if (!commentaire) return;

    try {
        const response = await fetch(`${API_BASE}/institution/financements/${id}/demander-revision`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ commentaire })
        });

        if (response.ok) {
            ALOGOTO.success('Révision demandée au porteur.');
            fetchFundings();
            fetchStats();
        } else {
            const result = await response.json().catch(() => ({}));
            ALOGOTO.error(result.message || 'Erreur lors de la demande.');
        }
    } catch (error) {
        ALOGOTO.error('Erreur de connexion.');
    }
}

async function handleDisbursement(id) {
    const modalEl = document.getElementById('modal-funding-details');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    const { isConfirmed } = await ALOGOTO.confirm(
        'Confirmer le décaissement',
        'Voulez-vous valider le décaissement des fonds vers le porteur ?',
        'Oui, décaisser',
        'Annuler'
    );
    if (!isConfirmed) return;

    try {
        const response = await fetch(`${API_BASE}/institution/financements/${id}/decaisser`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.ok) {
            ALOGOTO.success('Décaissement validé avec succès.');
            fetchFundings();
            fetchStats();
        } else {
            const result = await response.json().catch(() => ({}));
            ALOGOTO.error(result.message || 'Erreur lors du décaissement.');
        }
    } catch (error) {
        ALOGOTO.error('Erreur de connexion.');
    }
}

function renderPagination(data) {
    const container = document.getElementById('pagination-container');
    if (!container || !data.links) return;

    let html = '<ul class="pagination pagination-rounded">';
    data.links.forEach(link => {
        if (link.url) {
            const pageNum = link.url.split('page=')[1];
            html += `<li class="page-item ${link.active ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" onclick="fetchFundings(${pageNum})">${link.label}</a></li>`;
        }
    });
    html += '</ul>';
    container.innerHTML = html;
}

function getActionButton(item) {
    if (item.statut === 'awaiting_imf_validation') {
        return `<button class="btn btn-sm btn-primary" onclick="event.stopPropagation();viewFundingDetails(${item.id})">
            <i class="fe fe-eye"></i> Dossier
        </button>`;
    }
    if (item.statut === 'approved') {
        return `<button class="btn btn-sm btn-success" onclick="event.stopPropagation();handleDisbursement(${item.id})">
            <i class="fe fe-send"></i> Décaisser
        </button>`;
    }
    if (['disbursed', 'active', 'completed'].includes(item.statut)) {
        return `<button class="btn btn-sm btn-primary" onclick="event.stopPropagation();viewFundingDetails(${item.id})">
            <i class="fe fe-eye"></i> Voir
        </button>`;
    }
    return `<button class="btn btn-sm btn-primary" onclick="event.stopPropagation();viewFundingDetails(${item.id})">
        <i class="fe fe-eye"></i> Dossier
    </button>`;
}

// Helpers
function formatMoney(amount) {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', minimumFractionDigits: 0 }).format(amount);
}

function renderProjectDocs(docs) {
    if (!docs || docs.length === 0) {
        return '<p class="text-muted small">Aucun document fourni par le porteur.</p>';
    }
    return '<div class="list-group list-group-flush mb-0">' +
        docs.map(d => `
            <div class="list-group-item px-0 d-flex align-items-center border-0 border-bottom">
                <i class="fe fe-file-text me-2 fs-16 text-info"></i>
                <span class="flex-fill fw-medium">${d.type}</span>
                <span class="badge bg-${d.statut_validation === 'valide' ? 'success' : d.statut_validation === 'rejete' ? 'danger' : 'warning'} ms-2">${d.statut_validation}</span>
            </div>
        `).join('') +
    '</div>';
}

function getStatusBadge(statut) {
    const map = {
        'pending': '<span class="badge bg-secondary">En attente</span>',
        'proposed': '<span class="badge bg-primary">Proposition faite</span>',
        'awaiting_borrower_plan': '<span class="badge bg-warning">Attente plan porteur</span>',
        'awaiting_imf_validation': '<span class="badge bg-warning">Attente validation IMF</span>',
        'approved': '<span class="badge bg-success">Approuvé</span>',
        'rejected': '<span class="badge bg-danger">Rejeté</span>',
        'disbursed': '<span class="badge bg-primary">Décaissé</span>',
        'decaisse': '<span class="badge bg-primary">Décaissé</span>',
        'active': '<span class="badge bg-success">Actif</span>',
        'completed': '<span class="badge bg-dark">Terminé</span>',
        'defaulted': '<span class="badge bg-danger">En défaut</span>'
    };
    return map[statut] || `<span class="badge bg-secondary">${statut}</span>`;
}

function getEcheanceStatusBadge(statut) {
    const map = {
        'pending': '<span class="badge bg-warning">En attente</span>',
        'upcoming': '<span class="badge bg-info">À venir</span>',
        'paid': '<span class="badge bg-success">Payée</span>',
        'overdue': '<span class="badge bg-danger">En retard</span>',
        'partial': '<span class="badge bg-secondary">Partielle</span>'
    };
    return map[statut] || `<span class="badge bg-secondary">${statut}</span>`;
}
