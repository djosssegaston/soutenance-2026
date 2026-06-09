/**
 * Gestion des Remboursements - Institution Dashboard
 */

const API_BASE = '/api/v1';
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    // Initial Load
    fetchStats();
    fetchRepayments();

    // Event Listeners
    document.getElementById('search-btn').addEventListener('click', () => {
        currentPage = 1;
        fetchRepayments();
    });

    document.getElementById('reset-filters').addEventListener('click', () => {
        document.getElementById('filter-status').value = '';
        document.getElementById('filter-risk').value = '';
        $('.select2').val(null).trigger('change');
        currentPage = 1;
        fetchRepayments();
    });
});

/**
 * Récupère les stats
 */
async function fetchStats() {
    try {
        const response = await fetch(`${API_BASE}/institution/remboursements/statistiques`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) return;
        const data = await response.json();

        document.getElementById('stat-total-paid').textContent = formatMoney(data.total_rembourse);
        document.getElementById('stat-total-remaining').textContent = formatMoney(data.total_restant);
        document.getElementById('stat-recovery-rate').textContent = `${data.taux_remboursement}%`;
        document.getElementById('stat-total-late').textContent = data.retards;
    } catch (error) {
        console.error('Error fetching stats:', error);
    }
}

/**
 * Récupère la liste des remboursements
 */
async function fetchRepayments(page = 1) {
    currentPage = page;
    const loader = document.getElementById('repayments-loader');
    const container = document.getElementById('repayments-list');
    
    if (loader) loader.style.display = 'block';
    if (container) container.innerHTML = '';

    const statut = document.getElementById('filter-status').value;
    const risque = document.getElementById('filter-risk').value;

    try {
        const response = await fetch(`${API_BASE}/institution/remboursements?page=${page}&statut=${statut}&risque=${risque}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();

        renderRepayments(data.data);
        renderPagination(data);
    } catch (error) {
        console.error('Error fetching repayments:', error);
        container.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Erreur de chargement.</td></tr>';
    } finally {
        if (loader) loader.style.display = 'none';
    }
}

/**
 * Affiche les lignes de remboursements
 */
function renderRepayments(repayments) {
    const container = document.getElementById('repayments-list');
    container.innerHTML = '';

    if (repayments.length === 0) {
        container.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">Aucun remboursement trouvé.</td></tr>';
        return;
    }

    repayments.forEach(item => {
        const riskBadge = getRiskBadge(item.niveau_risque);
        const statusBadge = getStatusBadge(item.statut);
        
        const row = `
            <tr>
                <td>
                    <div class="fw-semibold">${item.project.titre}</div>
                    <div class="small text-muted">${item.project.owner.name}</div>
                </td>
                <td><span class="fw-bold">${formatMoney(item.montant_total)}</span></td>
                <td><span class="text-danger">${formatMoney(item.montant_restant)}</span></td>
                <td>${new Date(item.date_echeance).toLocaleDateString()}</td>
                <td>${riskBadge}</td>
                <td>${statusBadge}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="event.stopPropagation();viewRepaymentDetails(${item.id})">
                        <i class="fe fe-eye"></i> Suivi
                    </button>
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
            html += `<li class="page-item ${link.active ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" onclick="fetchRepayments(${pageNum})">${link.label}</a></li>`;
        }
    });
    html += '</ul>';
    container.innerHTML = html;
}

/**
 * Détails du remboursement
 */
async function viewRepaymentDetails(id) {
    try {
        const response = await fetch(`${API_BASE}/institution/remboursements/${id}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const item = await response.json();

        const container = document.getElementById('modal-body');
        container.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold mb-3">Résumé Échéance</h6>
                    <table class="table table-sm">
                        <tr><td>Projet:</td><td class="fw-bold">${item.project.titre}</td></tr>
                        <tr><td>Montant Dû:</td><td class="fw-bold">${formatMoney(item.montant_total)}</td></tr>
                        <tr><td>Déjà Remboursé:</td><td class="text-success">${formatMoney(item.montant_rembourse)}</td></tr>
                        <tr><td>Reste à Payer:</td><td class="text-danger fw-bold">${formatMoney(item.montant_restant)}</td></tr>
                        <tr><td>Échéance:</td><td>${new Date(item.date_echeance).toLocaleDateString()}</td></tr>
                    </table>
                </div>
                <div class="col-md-6 border-start">
                    <h6 class="fw-bold mb-3">Analyse du Risque</h6>
                    <div class="p-3 rounded bg-light mb-3 text-center">
                        <div class="h4 mb-1">${getRiskBadge(item.niveau_risque)}</div>
                        <small class="text-muted">Basé sur les délais de paiement</small>
                    </div>
                    ${item.penalites > 0 ? `
                        <div class="alert alert-warning py-2">
                            <i class="fe fe-alert-triangle me-2"></i> Pénalités estimées: <strong>${formatMoney(item.penalites)}</strong>
                        </div>
                    ` : ''}
                </div>
            </div>
            
            <h6 class="fw-bold mt-4">Historique des Événements</h6>
            <ul class="list-group list-group-flush border rounded">
                ${item.events && item.events.length > 0 ? item.events.map(e => `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${e.type_evenement.toUpperCase()}</strong> - ${e.details}
                            <div class="fs-11 text-muted">Par: ${e.auteur}</div>
                        </div>
                        <span class="small text-muted">${new Date(e.created_at).toLocaleString()}</span>
                    </li>
                `).join('') : '<li class="list-group-item text-muted">Aucun événement enregistré.</li>'}
            </ul>
        `;

        // Actions
        document.getElementById('btn-validate-repayment').onclick = () => validateRepayment(id);
        document.getElementById('btn-open-dispute').onclick = async () => {
            const modalEl = document.getElementById('modal-repayment-details');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            const { value: motif } = await Swal.fire({
                title: 'Motif du litige',
                input: 'text',
                inputPlaceholder: 'Ex: Retard > 30j sans justificatif',
                showCancelButton: true,
                cancelButtonText: 'Annuler',
                confirmButtonText: 'Ouvrir le litige',
                reverseButtons: true,
            });
            if (motif) openDispute(id, motif);
        };

        bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-repayment-details')).show();
    } catch (error) {
        console.error('Error fetching details:', error);
    }
}

async function validateRepayment(id) {
    const modalEl = document.getElementById('modal-repayment-details');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    const { isConfirmed } = await ALOGOTO.confirm(
        'Valider le remboursement',
        'Confirmez-vous avoir reçu ce remboursement ?',
        'Oui, valider',
        'Annuler'
    );
    if (!isConfirmed) return;

    try {
        const response = await fetch(`${API_BASE}/institution/remboursements/${id}/validate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (response.ok) {
            ALOGOTO.success('Remboursement validé.');
            fetchRepayments(currentPage);
            fetchStats();
        } else {
            const result = await response.json().catch(() => ({}));
            ALOGOTO.error(result.message || 'Erreur lors de la validation.');
        }
    } catch (error) {
        ALOGOTO.error('Erreur de connexion.');
    }
}

async function openDispute(id, motif) {
    try {
        const response = await fetch(`${API_BASE}/institution/remboursements/${id}/litige`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ motif: motif })
        });
        if (response.ok) {
            ALOGOTO.success('Litige ouvert.');
            fetchRepayments(currentPage);
        } else {
            const result = await response.json().catch(() => ({}));
            ALOGOTO.error(result.message || 'Erreur lors de l\'ouverture du litige.');
        }
    } catch (error) {
        ALOGOTO.error('Erreur de connexion.');
    }
}

// Helpers
function formatMoney(amount) {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', minimumFractionDigits: 0 }).format(amount);
}

function getRiskBadge(risk) {
    const map = {
        'faible': '<span class="badge bg-success">Faible</span>',
        'moyen': '<span class="badge bg-warning">Moyen</span>',
        'eleve': '<span class="badge bg-danger">Élevé</span>',
        'critique': '<span class="badge bg-dark">Critique</span>'
    };
    return map[risk] || `<span class="badge bg-secondary">${risk}</span>`;
}

function getStatusBadge(statut) {
    const map = {
        'en_attente': '<span class="badge bg-primary-transparent text-primary">En attente</span>',
        'paye': '<span class="badge bg-success-transparent text-success">Payé</span>',
        'retard': '<span class="badge bg-danger-transparent text-danger">En retard</span>',
        'litige': '<span class="badge bg-dark-transparent text-dark">Litige</span>',
        'partiel': '<span class="badge bg-info-transparent text-info">Partiel</span>'
    };
    return map[statut] || `<span class="badge bg-secondary-transparent text-secondary">${statut}</span>`;
}
