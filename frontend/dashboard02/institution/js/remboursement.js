/**
 * Gestion des Remboursements - Institution Dashboard
 */

const API_BASE = '/api/v1';
let allRepayments = [];

document.addEventListener('DOMContentLoaded', function() {
    fetchStats();
    fetchRepayments();

    document.getElementById('search-btn').addEventListener('click', filterProjects);
    document.getElementById('search-input').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') filterProjects();
    });
});

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

async function fetchRepayments() {
    const loader = document.getElementById('projects-loader');
    const container = document.getElementById('projects-list');
    const empty = document.getElementById('projects-empty');

    if (loader) loader.style.display = 'block';
    if (container) container.innerHTML = '';
    if (empty) empty.style.display = 'none';

    try {
        const response = await fetch(`${API_BASE}/institution/remboursements?per_page=500`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        allRepayments = data.data || [];
        renderProjects();
    } catch (error) {
        console.error('Error fetching repayments:', error);
        if (container) container.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erreur de chargement.</td></tr>';
    } finally {
        if (loader) loader.style.display = 'none';
    }
}

function groupByProject(repayments) {
    const map = {};
    repayments.forEach(r => {
        if (!r.project) return;
        const pid = r.project.id;
        if (!map[pid]) {
            map[pid] = {
                project_id: pid,
                titre: r.project.titre,
                owner: r.project.owner,
                repayments: [],
                total_due: 0,
                total_paid: 0,
                total_remaining: 0,
                count: 0,
            };
        }
        map[pid].repayments.push(r);
        map[pid].total_due += parseFloat(r.montant_total || 0);
        map[pid].total_paid += parseFloat(r.montant_rembourse || 0);
        map[pid].total_remaining += parseFloat(r.montant_restant || 0);
        map[pid].count++;
    });
    return Object.values(map);
}

function renderProjects() {
    const container = document.getElementById('projects-list');
    const empty = document.getElementById('projects-empty');
    if (!container) return;
    container.innerHTML = '';

    const paid = allRepayments.filter(r => r.statut === 'paye');

    if (paid.length === 0) {
        if (empty) empty.style.display = 'block';
        return;
    }

    if (empty) empty.style.display = 'none';
    const projects = groupByProject(paid);

    projects.forEach(p => {
        container.innerHTML += renderProjectRow(p);
    });
}

function renderProjectRow(p) {
    return `
        <tr role="button" style="cursor:pointer" onclick="openProjetPaiements(${p.project_id})">
            <td class="ps-4">
                <div class="fw-semibold">${escHtml(p.titre)}</div>
                <div class="small text-muted">${escHtml(p.owner?.name || '')}</div>
            </td>
            <td><span class="fw-bold">${formatMoney(p.total_due)}</span></td>
            <td><span class="text-success">${formatMoney(p.total_paid)}</span></td>
            <td><span class="text-danger">${formatMoney(p.total_remaining)}</span></td>
            <td>${p.count}</td>
            <td class="pe-4">
                <button class="btn btn-sm btn-primary" onclick="event.stopPropagation();openProjetPaiements(${p.project_id})">
                    <i class="fe fe-eye"></i> Consulter
                </button>
            </td>
        </tr>
    `;
}

function filterProjects() {
    const q = document.getElementById('search-input').value.toLowerCase().trim();
    const container = document.getElementById('projects-list');
    const empty = document.getElementById('projects-empty');
    if (!container) return;
    container.innerHTML = '';

    const paid = allRepayments.filter(r => r.statut === 'paye');
    let projects = groupByProject(paid);

    if (q) {
        projects = projects.filter(p =>
            p.titre.toLowerCase().includes(q) ||
            (p.owner?.name || '').toLowerCase().includes(q)
        );
    }

    if (projects.length === 0) {
        if (empty) empty.style.display = 'block';
        return;
    }

    if (empty) empty.style.display = 'none';

    projects.forEach(p => {
        container.innerHTML += renderProjectRow(p);
    });
}

function openProjetPaiements(projectId) {
    const paid = allRepayments.filter(r => r.statut === 'paye' && r.project && r.project.id === projectId);
    const project = paid[0]?.project || allRepayments.find(r => r.project && r.project.id === projectId)?.project;

    if (!project) return;

    document.getElementById('modal-projet-title').textContent = escHtml(project.titre);

    const list = document.getElementById('projet-paiements-list');
    const empty = document.getElementById('projet-paiements-empty');
    list.innerHTML = '';

    if (paid.length === 0) {
        empty.style.display = 'block';
    } else {
        empty.style.display = 'none';
        paid.forEach(r => {
            const tr = `
                <tr>
                    <td>${formatMoney(r.montant_total)}</td>
                    <td class="text-success">${formatMoney(r.montant_rembourse)}</td>
                    <td class="text-danger">${formatMoney(r.montant_restant)}</td>
                    <td>${new Date(r.date_echeance).toLocaleDateString()}</td>
                    <td class="risk-col">${getRiskBadge(r.niveau_risque)}</td>
                    <td class="statut-col">${getStatusBadge(r.statut)}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="event.stopPropagation();viewRepaymentDetails(${r.id})">
                            <i class="fe fe-eye"></i> Suivi
                        </button>
                    </td>
                </tr>
            `;
            list.innerHTML += tr;
        });
    }

    bootstrap.Modal.getOrCreateInstance(document.getElementById('projetPaiementsModal')).show();
}

async function viewRepaymentDetails(id) {
    const projetModal = bootstrap.Modal.getInstance(document.getElementById('projetPaiementsModal'));
    if (projetModal) projetModal.hide();

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
                        <tr><td>Projet:</td><td class="fw-bold">${escHtml(item.project.titre)}</td></tr>
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
                            <strong>${escHtml(e.type).toUpperCase()}</strong> - ${escHtml(e.description)}
                            <div class="fs-11 text-muted">Par: ${escHtml(e.user_name)}</div>
                        </div>
                        <span class="small text-muted">${new Date(e.created_at).toLocaleString()}</span>
                    </li>
                `).join('') : '<li class="list-group-item text-muted">Aucun événement enregistré.</li>'}
            </ul>
        `;

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
            fetchRepayments();
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
            fetchRepayments();
        } else {
            const result = await response.json().catch(() => ({}));
            ALOGOTO.error(result.message || 'Erreur lors de l\'ouverture du litige.');
        }
    } catch (error) {
        ALOGOTO.error('Erreur de connexion.');
    }
}

function escHtml(str) {
    if (!str) return '';
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

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
