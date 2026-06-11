/**
 * Dashboard Main - Institution Cockpit
 */

document.addEventListener('DOMContentLoaded', function() {
    fetchDashboardSummary();
    fetchRecentProjects();
});

function showError(message) {
    const container = document.getElementById('critical-alerts-container');
    if (container) {
        container.classList.remove('d-none');
        container.innerHTML = `<div class="alert alert-danger border-0 shadow-sm mb-2">
            <i class="fe fe-alert-triangle me-2"></i> ${message}
        </div>`;
    }
}

async function fetchDashboardSummary() {
    try {
        const response = await fetch('/api/v1/institution/dashboard');
        if (!response.ok) throw new Error('Erreur serveur (' + response.status + ')');
        const result = await response.json();
        if (result.status === 'success' && result.data) {
            updateKPIs(result.data.summary);
            updateAlerts(result.data.alerts);
            updateActivityTimeline(result.data.recent_activity);
        } else {
            showError('Impossible de charger le résumé du tableau de bord.');
        }
    } catch (error) {
        console.error('Error fetching dashboard summary:', error);
        showError('Erreur de chargement des données : ' + error.message);
    }
}

async function fetchRecentProjects() {
    try {
        const response = await fetch('/api/v1/dashboard/institution/projects');
        if (!response.ok) throw new Error('Erreur serveur (' + response.status + ')');
        const result = await response.json();
        if (result.projects) {
            renderRecentProjects(result.projects);
        } else {
            const tbody = document.getElementById('dashboard-recent-projects');
            if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="text-center p-5 text-muted">Impossible de charger les projets.</td></tr>';
        }
    } catch (error) {
        console.error('Error fetching recent projects:', error);
        const tbody = document.getElementById('dashboard-recent-projects');
        if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="text-center p-5 text-muted">Erreur de chargement : ' + error.message + '</td></tr>';
    }
}

function updateKPIs(summary) {
    const formatFCFA = (val) => new Intl.NumberFormat('fr-FR').format(val) + ' FCFA';
    
    document.getElementById('kpi-total-funded').textContent = formatFCFA(summary.portfolio.total_funded);
    document.getElementById('kpi-active-projects').textContent = `${summary.portfolio.active_count} Projets actifs`;
    
    document.getElementById('kpi-repayment-rate').textContent = `${Math.round(summary.repayments.rate)}%`;
    document.getElementById('kpi-total-repaid').textContent = `${formatFCFA(summary.repayments.total_repaid)} encaissés`;
    
    document.getElementById('kpi-at-risk').textContent = summary.risks.at_risk_count;
    document.getElementById('kpi-late-repayments').textContent = `${summary.repayments.late_count} retards actifs`;
    
    document.getElementById('kpi-roi').textContent = `${summary.portfolio.avg_roi.toFixed(1)}%`;
    document.getElementById('kpi-analyzed-projects').textContent = `${summary.pipeline.analyzed_count} analyses effectuées`;
}

function updateAlerts(alerts) {
    const container = document.getElementById('critical-alerts-container');
    const text = document.getElementById('critical-alerts-text');
    
    const totalAlerts = alerts.late_repayments.length + alerts.high_risk_projects.length;
    
    if (totalAlerts > 0) {
        container.classList.remove('d-none');
        text.innerHTML = `Attention : <strong>${alerts.late_repayments.length} retards</strong> de remboursement et <strong>${alerts.high_risk_projects.length} projets</strong> à haut risque détectés.`;
    } else {
        container.classList.add('d-none');
    }
}

function updateActivityTimeline(activity) {
    const timeline = document.getElementById('dashboard-activity-timeline');
    if (activity.length === 0) {
        timeline.innerHTML = '<li class="text-muted p-3">Aucune activité récente</li>';
        return;
    }

    timeline.innerHTML = activity.map(item => `
        <li class="task-list-item p-2 mb-2">
            <i class="task-icon badge-soft-${item.color} ${item.icon}"></i>
            <h6 class="fw-semibold mb-1 fs-13">${item.title}<span class="text-muted fs-10 ms-2 float-end">${item.date}</span></h6>
            <p class="text-muted fs-11 mb-0">${item.description}</p>
        </li>
    `).join('');
}

function renderRecentProjects(projects) {
    const tbody = document.getElementById('dashboard-recent-projects');
    if (!projects || projects.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center p-5 text-muted">Aucun projet en revue</td></tr>';
        return;
    }

    tbody.innerHTML = projects.slice(0, 5).map(project => {
        const riskColor = getRiskColor(project.niveau_risque);
        const statusBadge = getStatusBadge(project.statut);
        
        return `
            <tr>
                <td class="ps-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-light rounded-circle me-3">
                            <span class="text-primary fw-bold fs-12">${project.titre.substring(0, 2).toUpperCase()}</span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold fs-13">${project.titre}</h6>
                            <small class="text-muted fs-11">${project.secteur}</small>
                        </div>
                    </div>
                </td>
                <td><span class="fs-13 text-dark">${project.porteur ? project.porteur.name : ''}</span></td>
                <td><span class="fw-bold text-dark fs-13">${new Intl.NumberFormat('fr-FR').format(project.montant_demande)} FCFA</span></td>
                <td><span class="badge badge-soft-${riskColor} rounded-pill px-3">${project.niveau_risque || 'Faible'}</span></td>
                <td><span class="badge badge-soft-${statusBadge.color} rounded-pill px-3">${statusBadge.label}</span></td>
                <td class="pe-4">
                    <div class="btn-list">
                        <a href="projets_analyses.php?id=${project.id}" class="btn btn-sm btn-icon btn-primary-light rounded-circle" data-bs-toggle="tooltip" title="Analyser"><i class="fe fe-edit"></i></a>
                        <a href="messages.php?project_id=${project.id}" class="btn btn-sm btn-icon btn-info-light rounded-circle" data-bs-toggle="tooltip" title="Discuter"><i class="fe fe-message-square"></i></a>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function getRiskColor(risk) {
    switch (risk?.toLowerCase()) {
        case 'critique': return 'danger';
        case 'eleve': return 'warning';
        case 'moyen': return 'info';
        default: return 'success';
    }
}

function getStatusBadge(status) {
    const map = {
        'under_institution_review': { label: 'En analyse', color: 'warning' },
        'institution_accepted': { label: 'Accepter', color: 'info' },
        'funded': { label: 'Financé', color: 'success' },
        'active': { label: 'Actif', color: 'primary' }
    };
    return map[status] || { label: status, color: 'secondary' };
}
