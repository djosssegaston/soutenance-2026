/**
 * Dashboard Main - Admin Global Supervision Cockpit
 */

document.addEventListener('DOMContentLoaded', function() {
    fetchDashboardKpis();
    fetchChartData();
    fetchAlerts();
    fetchActivity();
    fetchRecentProjects();
    fetchSystemStatus();
});

const API_BASE = '/api/v1';
const JSON_HEADERS = {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
};

function getStatusLabel(status) {
    var labels = {
        'draft': 'Brouillon',
        'submitted': 'Soumis',
        'under_admin_review': 'En révision admin',
        'admin_rejected': 'Rejeté par admin',
        'admin_validated': 'Validé par admin',
        'available_for_imf': 'Disponible pour IMF',
        'under_institution_review': 'En analyse institution',
        'interview_scheduled': 'Entretien planifié',
        'interview_confirmed': 'Entretien confirmé',
        'documents_requested': 'Documents demandés',
        'institution_rejected': 'Rejeté par institution',
        'institution_accepted': 'Accepté par institution',
        'funded': 'Financé',
        'active': 'En cours',
        'suspended': 'Suspendu',
        'completed': 'Terminé',
        'cancelled': 'Annulé',
    };
    return labels[status] || status;
}

function getStatusBadge(status) {
    var map = {
        'draft': 'secondary',
        'submitted': 'info',
        'under_admin_review': 'warning',
        'admin_rejected': 'danger',
        'admin_validated': 'success',
        'available_for_imf': 'info',
        'under_institution_review': 'warning',
        'interview_scheduled': 'info',
        'interview_confirmed': 'success',
        'documents_requested': 'warning',
        'institution_rejected': 'danger',
        'institution_accepted': 'success',
        'funded': 'success',
        'active': 'primary',
        'suspended': 'danger',
        'completed': 'dark',
        'cancelled': 'dark',
    };
    return map[status] || 'secondary';
}

function showError(message) {
    const container = document.getElementById('alerts-container');
    if (container) {
        container.innerHTML = `<div class="alert alert-danger border-0 shadow-sm mb-2">
            <div class="d-flex">
                <i class="fe fe-alert-triangle me-2 fs-20"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">Erreur</h6>
                    <p class="mb-0 fs-12">${message}</p>
                </div>
            </div>
        </div>`;
    }
}

async function fetchDashboardKpis() {
    try {
        const response = await fetch(`${API_BASE}/admin/dashboard`, { headers: JSON_HEADERS });
        if (!response.ok) throw new Error('Erreur serveur (' + response.status + ')');
        const result = await response.json();
        if (result.success) {
            updateKPIs(result.kpis);
        } else {
            showError('Impossible de charger les indicateurs.');
        }
    } catch (error) {
        console.error('Error fetching KPIs:', error);
        showError('Impossible de charger les indicateurs : ' + error.message);
    }
}

async function fetchChartData() {
    try {
        const response = await fetch(`${API_BASE}/admin/dashboard/charts`, { headers: JSON_HEADERS });
        if (!response.ok) throw new Error('Erreur serveur (' + response.status + ')');
        const result = await response.json();
        if (result.success) {
            renderCharts(result.charts);
        }
    } catch (error) {
        console.error('Error fetching charts:', error);
    }
}

async function fetchAlerts() {
    try {
        const response = await fetch(`${API_BASE}/admin/dashboard/alerts`, { headers: JSON_HEADERS });
        if (!response.ok) throw new Error('Erreur serveur (' + response.status + ')');
        const result = await response.json();
        if (result.success && document.getElementById('alerts-container')) {
            renderAlerts(result.alerts);
        }
    } catch (error) {
        console.error('Error fetching alerts:', error);
        showError('Impossible de charger les alertes : ' + error.message);
    }
}

async function fetchActivity() {
    try {
        const response = await fetch(`${API_BASE}/admin/dashboard/activity`, { headers: JSON_HEADERS });
        if (!response.ok) throw new Error('Erreur serveur (' + response.status + ')');
        const result = await response.json();
        if (result.success) {
            renderActivity(result.activities);
        }
    } catch (error) {
        console.error('Error fetching activity:', error);
    }
}

async function fetchRecentProjects() {
    try {
        const response = await fetch(`${API_BASE}/admin/dashboard/recent-projects`, { headers: JSON_HEADERS });
        if (!response.ok) throw new Error('Erreur serveur (' + response.status + ')');
        const result = await response.json();
        if (result.success) {
            renderRecentProjects(result.projects);
        }
    } catch (error) {
        console.error('Error fetching projects:', error);
    }
}

async function fetchSystemStatus() {
    try {
        const response = await fetch(`${API_BASE}/admin/dashboard/system`, { headers: JSON_HEADERS });
        if (!response.ok) throw new Error('Erreur serveur (' + response.status + ')');
        const result = await response.json();
        if (result.success) {
            renderSystemStatus(result.system);
        }
    } catch (error) {
        console.error('Error fetching system status:', error);
    }
}

function updateKPIs(kpis) {
    const formatFCFA = (val) => new Intl.NumberFormat('fr-FR').format(val) + ' FCFA';

    // Projects
    document.getElementById('kpi-total-projects').textContent = kpis.projects.total;
    document.getElementById('kpi-active-projects').textContent = kpis.projects.active;
    document.getElementById('kpi-validated-projects').textContent = kpis.projects.validated;
    document.getElementById('kpi-suspended-projects').textContent = kpis.projects.suspended;

    // Finances
    document.getElementById('kpi-total-funded').textContent = formatFCFA(kpis.finances.total_funded);
    document.getElementById('kpi-total-repaid').textContent = formatFCFA(kpis.finances.total_repaid);
    document.getElementById('kpi-over-funded').textContent = formatFCFA(kpis.finances.over_funded);
    document.getElementById('kpi-late-repayments').textContent = formatFCFA(kpis.finances.late_repayments);

    // Users
    document.getElementById('kpi-total-users').textContent = kpis.users.total;
    document.getElementById('kpi-total-institutions').textContent = kpis.users.institutions;
    document.getElementById('kpi-total-porteurs').textContent = kpis.users.porteurs;
    document.getElementById('kpi-total-suspended-users').textContent = kpis.users.suspended;

    // Risks
    document.getElementById('kpi-active-disputes').textContent = kpis.risks.active_disputes;
    document.getElementById('kpi-critical-projects').textContent = kpis.risks.critical_projects;
    document.getElementById('kpi-repayment-defaults').textContent = kpis.risks.repayment_defaults;
}

function renderCharts(charts) {
    const isMobile = window.innerWidth < 768;
    const chartHeight = isMobile ? 250 : 350;
    const donutHeight = isMobile ? 250 : 300;

    // 1. Evolution Chart (Line/Area)
    const evolutionOptions = {
        series: charts.evolution.datasets,
        chart: { height: chartHeight, type: 'area', toolbar: { show: false } },
        xaxis: { categories: charts.evolution.labels },
        stroke: { curve: 'smooth', width: 2 },
        colors: ['#6259ca', '#09ad95', '#fca028', '#f35120'],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } }
    };
    new ApexCharts(document.querySelector("#chart-evolution"), evolutionOptions).render();

    // 2. Sectors Chart (Donut)
    const sectorOptions = {
        series: charts.sectors.map(s => s.value),
        labels: charts.sectors.map(s => s.label),
        chart: { type: 'donut', height: donutHeight },
        colors: ['#6259ca', '#fca028', '#05c3fb', '#09ad95', '#f5334f', '#f7b731'],
        legend: { position: 'bottom', fontSize: isMobile ? '10px' : '12px' }
    };
    new ApexCharts(document.querySelector("#chart-sectors"), sectorOptions).render();

    // 3. Status Chart (Bar)
    const statusOptions = {
        series: [{ name: 'Projets', data: charts.status.map(s => s.value) }],
        chart: { type: 'bar', height: donutHeight, toolbar: { show: false } },
        plotOptions: { bar: { borderRadius: 4, horizontal: true } },
        xaxis: { categories: charts.status.map(s => s.label) },
        colors: ['#6259ca']
    };
    new ApexCharts(document.querySelector("#chart-status"), statusOptions).render();

    // 4. Risks Chart (Pie)
    const riskOptions = {
        series: charts.risks.map(r => r.value),
        labels: charts.risks.map(r => r.label),
        chart: { type: 'pie', height: donutHeight },
        colors: ['#09ad95', '#fca028', '#f35120', '#310000'],
        legend: { position: 'bottom', fontSize: isMobile ? '10px' : '12px' }
    };
    new ApexCharts(document.querySelector("#chart-risks"), riskOptions).render();
}

function renderAlerts(alerts) {
    const container = document.getElementById('alerts-container');
    if (!container) return;
    if (alerts.length === 0) {
        container.innerHTML = '<div class="alert alert-success">Aucune alerte critique détectée.</div>';
        return;
    }

    container.innerHTML = alerts.map(a => `
        <div class="alert alert-${a.type} border-0 shadow-sm mb-2">
            <div class="d-flex">
                <i class="fe fe-alert-triangle me-2 fs-20"></i>
                <div>
                    <h6 class="alert-heading fw-bold mb-1">${a.title}</h6>
                    <p class="mb-0 fs-12">${a.message}</p>
                </div>
                <a href="projets.php?search=${a.project_id}" class="btn btn-sm btn-${a.type} ms-auto align-self-center">Audit</a>
            </div>
        </div>
    `).join('');
}

function renderActivity(activities) {
    const timeline = document.getElementById('activity-timeline');
    timeline.innerHTML = activities.map(a => `
        <li class="task-list-item p-2 mb-2">
            <i class="task-icon badge-soft-${a.variant} ${a.icon}"></i>
            <h6 class="fw-semibold mb-1 fs-13">${a.title}<span class="text-muted fs-10 ms-2 float-end">${a.time}</span></h6>
            <p class="text-muted fs-11 mb-0">${a.message}</p>
        </li>
    `).join('');
}

function renderRecentProjects(projects) {
    const tbody = document.getElementById('recent-projects-tbody');
    tbody.innerHTML = projects.map(p => `
        <tr>
            <td>
                <h6 class="mb-0 fw-semibold fs-13">${p.titre}</h6>
                <small class="text-muted fs-11">REF: ${p.id}</small>
            </td>
            <td><span class="fs-13">${p.owner || p.owner_name || ''}</span></td>
            <td><span class="fw-bold">${new Intl.NumberFormat('fr-FR').format(p.montant || p.montant_demande || 0)} FCFA</span></td>
            <td><span class="badge bg-${(p.risk === 'critique' || p.risk === 'critique') ? 'danger' : 'warning'}-transparent text-${(p.risk === 'critique' || p.risk === 'critique') ? 'danger' : 'warning'}">${(p.risk || 'modere').toUpperCase()}</span></td>
            <td><span class="badge bg-${p.statut_color || 'primary'}-transparent text-${p.statut_color || 'primary'}">${p.statut_label || p.statut || ''}</span></td>
            <td>
                <div class="btn-list">
                    <button class="btn btn-sm btn-icon btn-primary-light rounded-circle btn-view-project" title="Voir" data-id="${p.id}"><i class="fe fe-eye"></i></button>
                    ${p.is_financed || p.statut === 'draft' ? '' : '<button class="btn btn-sm btn-icon btn-success-light rounded-circle btn-validate-project" title="Valider" data-id="' + p.id + '"><i class="fe fe-check"></i></button>'}
                </div>
            </td>
        </tr>
    `).join('');
    // Réattacher les événements après rendu
    attachProjectButtons();
}

function attachProjectButtons() {
    // Les clics sont gérés par délégation sur le tbody
}

// Bouton Valider dans la modale détail
document.addEventListener('click', function(e) {
    const validBtn = e.target.closest('#md-validate-btn');
    if (validBtn && validBtn.dataset.id) {
        e.preventDefault();
        bootstrap.Modal.getInstance(document.getElementById('projectDetailModal'))?.hide();
        validateProject(validBtn.dataset.id);
    }
});

// Délégation pour les boutons projets
document.getElementById('recent-projects-tbody').addEventListener('click', function(e) {
    const viewBtn = e.target.closest('.btn-view-project');
    if (viewBtn) {
        e.preventDefault();
        openProjectDetail(viewBtn.dataset.id);
        return;
    }
    const validBtn = e.target.closest('.btn-validate-project');
    if (validBtn) {
        e.preventDefault();
        validateProject(validBtn.dataset.id);
        return;
    }
});

async function openProjectDetail(id) {
    try {
        const resp = await fetch(`${API_BASE}/admin/projects/${id}`, { headers: JSON_HEADERS });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const json = await resp.json();
        if (!json.success) throw new Error('Erreur API');
        const p = json.data;

        document.getElementById('modal-detail-title').textContent = p.titre || 'Détail Projet';
        document.getElementById('md-ref').textContent = '#' + p.id;
        document.getElementById('md-description').innerHTML = p.description || 'Aucune description';
        document.getElementById('md-owner').textContent = p.owner?.name || '';
        document.getElementById('md-email').textContent = p.owner?.email || '';
        document.getElementById('md-secteur').textContent = p.secteur || '';
        document.getElementById('md-montant').textContent = new Intl.NumberFormat('fr-FR').format(p.montant_demande || 0) + ' FCFA';
        var aFundingDecaisse = p.financements && p.financements.some(function(f) { return ['disbursed', 'active'].includes(f.statut); });
        var aFundingPropose = p.financements && p.financements.some(function(f) { return ['proposed', 'awaiting_borrower_plan', 'awaiting_imf_validation'].includes(f.statut); });
        var isFinanced = (parseFloat(p.montant_finance) > 0) || aFundingDecaisse;
        var label = isFinanced ? 'Projet d\u00e9j\u00e0 financ\u00e9' : (aFundingPropose ? 'Financement propos\u00e9' : getStatusLabel(p.statut_label || p.statut || ''));
        var color = isFinanced ? 'success' : (aFundingPropose ? 'info' : getStatusBadge(p.statut_color || p.statut || 'secondary'));
        var statutEl = document.getElementById('md-statut');
        statutEl.textContent = label;
        statutEl.className = 'badge fs-13 px-3 py-2 bg-' + color;
        document.getElementById('md-created').textContent = p.created_at ? new Date(p.created_at).toLocaleDateString('fr-FR') : '';
        document.getElementById('md-updated').textContent = p.updated_at ? new Date(p.updated_at).toLocaleDateString('fr-FR') : '';

        const validateBtn = document.getElementById('md-validate-btn');
        validateBtn.dataset.id = id;
        validateBtn.style.display = isFinanced || p.statut === 'draft' ? 'none' : (p.statut === 'submitted' ? '' : 'none');

        bootstrap.Modal.getOrCreateInstance(document.getElementById('projectDetailModal')).show();
    } catch (error) {
        var msg = 'Erreur chargement projet' + (error.message ? ' : ' + error.message : '');
        if (ALOGOTO && ALOGOTO.error) ALOGOTO.error(msg); else alert(msg);
    }
}

async function validateProject(id) {
    if (!confirm('Valider ce projet ?')) return;
    try {
        const resp = await fetch(`${API_BASE}/admin/projects/${id}/validate`, {
            method: 'POST',
            headers: { ...JSON_HEADERS, 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
        });
        const json = await resp.json();
        if (json.success) {
            if (ALOGOTO && ALOGOTO.success) ALOGOTO.success('Projet validé avec succès');
            fetchRecentProjects();
            fetchDashboardKpis();
        } else {
            throw new Error(json.message || 'Erreur validation');
        }
    } catch (error) {
        if (ALOGOTO && ALOGOTO.error) ALOGOTO.error(error.message); else alert(error.message);
    }
}

function renderSystemStatus(system) {
    const container = document.getElementById('system-status-container');
    container.innerHTML = `
        <div class="list-group list-group-flush">
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>Serveur</span>
                <span class="badge bg-success">${system.server.status}</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>Base de données</span>
                <span class="badge bg-success">${system.database.status}</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>Dernière sauvegarde</span>
                <span class="text-muted fs-11">${system.backups.last_backup}</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>Taux d'erreur API</span>
                <span class="text-info">${system.api.error_rate}</span>
            </div>
        </div>
    `;
}
