<?php require_once __DIR__ . '/header.php'; ?>
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="page-header">
            <h1 class="page-title">Échéancier & Plan de Remboursement</h1>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Institution</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Échéances</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="mt-2">
                                <h6 class="">Projets</h6>
                                <h2 class="mb-0 number-font" id="stat-total-projects">0</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.10" data-thickness="2" data-color="#09ad95">
                                    <div class="chart-circle-value text-success"><i class="fe fe-briefcase"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="mt-2">
                                <h6 class="">Échéances à Venir</h6>
                                <h2 class="mb-0 number-font" id="stat-upcoming">0</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.25" data-thickness="2" data-color="#05c3fb">
                                    <div class="chart-circle-value text-primary"><i class="fe fe-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="mt-2">
                                <h6 class="">En Retard</h6>
                                <h2 class="mb-0 number-font" id="stat-overdue">0</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.40" data-thickness="2" data-color="#ff5b5b">
                                    <div class="chart-circle-value text-danger"><i class="fe fe-alert-triangle"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="mt-2">
                                <h6 class="">Total Échéances</h6>
                                <h2 class="mb-0 number-font" id="stat-total-repayments">0</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.30" data-thickness="2" data-color="#f7b731">
                                    <div class="chart-circle-value text-warning"><i class="fe fe-list"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Projets avec Échéances</h3>
                        <div class="card-options">
                            <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Rechercher...">
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="projects-loader" class="text-center py-4" style="display:none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0" id="projects-table">
                                <thead>
                                    <tr>
                                        <th>Projet</th>
                                        <th>Porteur</th>
                                        <th class="text-center">Échéances</th>
                                        <th>Total Dû</th>
                                        <th>Payé</th>
                                        <th>Restant</th>
                                        <th class="text-center">Retard</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="projects-body">
                                </tbody>
                            </table>
                        </div>
                        <div id="projects-pagination" class="mt-3 d-flex justify-content-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL ÉCHÉANCES PROJET -->
<div class="modal fade" id="echeancesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="echeancesModalLabel">Échéances du Projet</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-loader" class="text-center py-5" style="display:none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
                <div id="modal-content">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold" id="modal-project-title"></h6>
                            <small class="text-muted" id="modal-porteur-name"></small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Institution</small>
                            <p class="fw-bold mb-0" id="modal-institution-name"></p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Montant Total</th>
                                    <th>Montant Payé</th>
                                    <th>Reste</th>
                                    <th>Date Échéance</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody id="modal-echeances-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
<script>
const API_BASE = '/api/v1';

function formatMoney(amount) {
    return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 0 }).format(amount || 0) + ' FCFA';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

async function loadProjects(page = 1) {
    const loader = document.getElementById('projects-loader');
    const tbody = document.getElementById('projects-body');
    const search = document.getElementById('search-input').value;
    loader.style.display = 'block';
    try {
        const params = new URLSearchParams({ page, per_page: 15 });
        if (search) params.set('search', search);
        const resp = await fetch(`${API_BASE}/institution/remboursements/projects?${params}`, {
            headers: { 'Accept': 'application/json' }
        });
        if (!resp.ok) throw new Error('Erreur réseau');
        const json = await resp.json();
        if (!json.success) throw new Error('Erreur API');
        const data = json.data || [];
        const stats = json.stats || {};
        document.getElementById('stat-total-projects').textContent = stats.total_projects || 0;
        document.getElementById('stat-upcoming').textContent = (stats.total_echeances - stats.total_paid) || 0;
        document.getElementById('stat-overdue').textContent = stats.total_overdue || 0;
        document.getElementById('stat-total-repayments').textContent = stats.total_echeances || 0;
        renderProjects(data);
    } catch (err) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-danger text-center">Erreur de chargement.</td></tr>';
    } finally {
        loader.style.display = 'none';
    }
}

function renderProjects(items) {
    const tbody = document.getElementById('projects-body');
    tbody.innerHTML = '';
    if (!items.length) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Aucun projet avec échéances.</td></tr>';
        return;
    }
    items.forEach(p => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="fw-semibold">${escapeHtml(p.titre)}</td>
            <td>${escapeHtml(p.porteur_name)}</td>
            <td class="text-center"><span class="badge bg-info">${p.echeances_count}</span></td>
            <td class="fw-bold">${formatMoney(p.total_due)}</td>
            <td class="text-success fw-medium">${formatMoney(p.total_paid)}</td>
            <td class="text-warning fw-medium">${formatMoney(p.total_remaining)}</td>
            <td class="text-center">${p.overdue_count > 0 ? '<span class="badge bg-danger">' + p.overdue_count + '</span>' : '<span class="badge bg-light text-muted">0</span>'}</td>
            <td><button class="btn btn-sm btn-outline-primary consulter-btn" data-id="${p.id}" data-titre="${escapeHtml(p.titre)}" data-porteur="${escapeHtml(p.porteur_name)}"><i class="fe fe-eye me-1"></i>Consulter</button></td>`;
        tbody.appendChild(tr);
    });
    document.querySelectorAll('.consulter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            openEcheancesModal(this.dataset.id, this.dataset.titre, this.dataset.porteur);
        });
    });
}

async function openEcheancesModal(projectId, titre, porteur) {
    document.getElementById('modal-project-title').textContent = titre;
    document.getElementById('modal-porteur-name').textContent = 'Porteur : ' + porteur;
    document.getElementById('modal-loader').style.display = 'block';
    document.getElementById('modal-content').style.display = 'none';
    const modal = new bootstrap.Modal(document.getElementById('echeancesModal'));
    modal.show();

    try {
        const resp = await fetch(`${API_BASE}/institution/remboursements/project/${projectId}`, {
            headers: { 'Accept': 'application/json' }
        });
        if (!resp.ok) throw new Error('Erreur réseau');
        const json = await resp.json();
        if (!json.success) throw new Error('Erreur API');
        const items = json.data || [];
        document.getElementById('modal-institution-name').textContent = items.length > 0 ? (items[0].institution_nom || '') : '';
        renderModalEcheances(items);
    } catch (err) {
        document.getElementById('modal-echeances-body').innerHTML =
            '<tr><td colspan="6" class="text-center text-danger">Erreur de chargement.</td></tr>';
    } finally {
        document.getElementById('modal-loader').style.display = 'none';
        document.getElementById('modal-content').style.display = 'block';
    }
}

function renderModalEcheances(items) {
    const tbody = document.getElementById('modal-echeances-body');
    tbody.innerHTML = '';
    if (!items.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Aucune échéance pour ce projet.</td></tr>';
        return;
    }
    const statusBadge = { pending: 'secondary', upcoming: 'info', paid: 'success', overdue: 'danger', partial: 'warning' };
    const statusLabel = { pending: 'À venir', upcoming: 'Prochaine', paid: 'Payée', overdue: 'En retard', partial: 'Partielle' };
    items.forEach((item, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${i + 1}</td>
            <td class="fw-bold">${formatMoney(item.montant_total)}</td>
            <td class="text-success">${formatMoney(item.montant_paye)}</td>
            <td class="text-warning">${formatMoney(item.montant_restant)}</td>
            <td>${item.date_echeance ? new Date(item.date_echeance).toLocaleDateString('fr-FR') : '-'}</td>
            <td><span class="badge bg-${statusBadge[item.statut] || 'light'}">${statusLabel[item.statut] || item.statut}</span></td>`;
        tbody.appendChild(tr);
    });
}
document.addEventListener('DOMContentLoaded', function () {
    loadProjects(1);
    document.getElementById('search-input').addEventListener('keyup', function (e) {
        if (e.key === 'Enter') loadProjects(1);
    });
});
</script>
