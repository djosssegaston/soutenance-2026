/**
 * Supervision Globale des Projets - Admin JS
 */

document.addEventListener('DOMContentLoaded', function() {
    const API_BASE = '/api/v1';
    let currentProject = null;

    // --- INITIALISATION ---
    loadStats();
    loadProjects();
    loadAlerts();

    // --- RECHERCHE ET FILTRES (auto-filtering) ---
    function debounce(fn, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    const autoFilter = debounce(() => loadProjects(1), 300);

    document.getElementById('apply-filters').addEventListener('click', () => loadProjects(1));
    document.getElementById('search-input').addEventListener('input', autoFilter);
    document.getElementById('filter-status').addEventListener('change', () => loadProjects(1));
    document.getElementById('filter-sector').addEventListener('change', () => loadProjects(1));
    document.getElementById('filter-risk').addEventListener('change', () => loadProjects(1));
    document.getElementById('search-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); loadProjects(1); }
    });

    // --- CHARGEMENT DES STATISTIQUES ET GRAPHIQUES ---
    async function loadStats() {
        try {
            const response = await fetch(`${API_BASE}/admin/projects/statistics`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const result = await response.json();
            if (!result.success) throw new Error('Erreur API');

            const data = result.data;
            document.getElementById('kpi-total-projects').textContent = data.projects?.total || 0;
            document.getElementById('kpi-pending-validation').textContent = data.validation?.pending || 0;
            document.getElementById('kpi-high-risk').textContent = data.finance?.high_risk_count || 0;
            document.getElementById('kpi-total-funded').textContent = (data.finance?.total_funded ? formatMoney(data.finance.total_funded) : '0') + ' FCFA';

            if (data.charts) initCharts(data.charts);
        } catch (error) {
            console.error('Erreur stats:', error);
        }
    }

    function initCharts(chartData) {
        // Evolution Chart
        const evolutionOptions = {
            series: [{
                name: 'Projets',
                data: chartData.evolution.map(item => item.count)
            }],
            chart: { height: 350, type: 'area', toolbar: { show: false } },
            xaxis: { categories: ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec'] },
            colors: ['#6259ca'],
            stroke: { curve: 'smooth' }
        };
        new ApexCharts(document.querySelector("#evolution-chart"), evolutionOptions).render();

        // Sectors Chart
        const sectorsOptions = {
            series: chartData.secteurs.map(item => item.count),
            chart: { type: 'donut', height: 300 },
            labels: chartData.secteurs.map(item => item.secteur),
            legend: { position: 'bottom' }
        };
        new ApexCharts(document.querySelector("#sectors-chart"), sectorsOptions).render();

        // Status Chart
        const statusOptions = {
            series: chartData.statuts.map(item => item.count),
            chart: { type: 'pie', height: 300 },
            labels: chartData.statuts.map(item => getStatusLabel(item.statut)),
            legend: { position: 'bottom' }
        };
        new ApexCharts(document.querySelector("#status-chart"), statusOptions).render();
    }

    // --- CHARGEMENT DES PROJETS ---
    async function loadProjects(page = 1) {
        const tbody = document.getElementById('projects-tbody');
        // Skeleton loader animation for a more premium feel
        tbody.innerHTML = Array(5).fill(0).map(() => `
            <tr>
                <td><div class="loading-skeleton" style="height: 20px; width: 150px;"></div></td>
                <td><div class="loading-skeleton" style="height: 20px; width: 120px;"></div></td>
                <td><div class="loading-skeleton" style="height: 20px; width: 100px;"></div></td>
                <td><div class="loading-skeleton" style="height: 20px; width: 80px;"></div></td>
                <td><div class="loading-skeleton" style="height: 20px; width: 60px;"></div></td>
                <td><div class="loading-skeleton" style="height: 20px; width: 100px;"></div></td>
                <td><div class="loading-skeleton" style="height: 20px; width: 60px;"></div></td>
            </tr>
        `).join('');

        const params = new URLSearchParams({
            page: page,
            search: document.getElementById('search-input').value,
            statut: document.getElementById('filter-status').value,
            secteur: document.getElementById('filter-sector').value
        });

        try {
            const response = await fetch(`${API_BASE}/admin/projects?${params}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const result = await response.json();
            if (!result.success) throw new Error('Erreur API');

            document.getElementById('projects-count').textContent = `${result.total || 0} projets`;
            renderTable(result.data || []);
            renderPagination(result);
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Erreur de chargement.</td></tr>';
        }
    }

    function renderTable(projects) {
        const tbody = document.getElementById('projects-tbody');
        tbody.innerHTML = '';

        if (projects.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5">Aucun projet trouvé.</td></tr>';
            return;
        }

        projects.forEach(p => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="d-flex">
                        <div class="ms-2 mt-0">
                            <h6 class="mb-0 fw-semibold">${p.titre}</h6>
                            <span class="fs-12 text-muted">REF: ${p.id.toString().padStart(5, '0')}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="fw-semibold">${p.owner ? p.owner.name : 'N/A'}</div>
                    <div class="fs-12 text-muted">${p.localisation || 'Secteur non défini'}</div>
                </td>
                <td>
                    <div class="fw-bold text-primary">${formatMoney(p.montant_finance)} / ${formatMoney(p.montant_demande)}</div>
                    <div class="progress progress-xs mt-1">
                        <div class="progress-bar bg-primary" style="width: ${(p.montant_finance / p.montant_demande * 100) || 0}%"></div>
                    </div>
                </td>
                <td>${renderStatusCell(p)}</td>
                <td><span class="badge ${getRiskBadge(p.risk.level)}">${p.risk.level}</span></td>
                <td>
                    <div class="fs-12">Créé: ${formatDate(p.created_at)}</div>
                    <div class="fs-12 text-muted">Maj: ${formatDate(p.updated_at)}</div>
                </td>
                <td>
                    <div class="btn-list">
                        <button class="btn btn-sm btn-primary btn-view" data-id="${p.id}"><i class="fe fe-eye"></i></button>
                        <button class="btn btn-sm btn-info btn-audit" data-id="${p.id}"><i class="fe fe-file-text"></i></button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Event listeners
        document.querySelectorAll('.btn-view, .btn-audit').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                openAdminModal(btn.dataset.id);
            });
        });
    }

    // --- MODALE ADMIN ---
    async function openAdminModal(projectId) {
        try {
            const response = await fetch(`${API_BASE}/admin/projects/${projectId}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const result = await response.json();
            if (!result.success || !result.data) throw new Error('Erreur API');
            const p = result.data;
            currentProject = p;

            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-project-admin'));
            
            // Titre et infos
            document.getElementById('admin-modal-title').textContent = `Audit Projet: ${p.titre}`;
            document.getElementById('admin-modal-content').innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <p><strong>Porteur:</strong> ${p.owner.name} (${p.owner.email})</p>
                        <p><strong>Secteur:</strong> ${p.secteur}</p>
                        <p><strong>Ville:</strong> ${p.localisation}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Montant Demandé:</strong> ${formatMoney(p.montant_demande)} FCFA</p>
                        <p><strong>Montant Financé:</strong> ${formatMoney(p.montant_finance)} FCFA</p>
                        <p><strong>Score de Risque:</strong> <span class="badge ${getRiskBadge(p.risk ? p.risk.risk_level : 'faible')}">${p.risk ? p.risk.score : 0}% (${p.risk ? p.risk.risk_level : 'faible'})</span></p>
                    </div>
                    <div class="col-12 mt-3">
                        <h6>Description</h6>
                        <p class="text-muted">${p.description}</p>
                    </div>
                </div>
            `;

            // Timeline
            const timeline = document.getElementById('admin-timeline');
            timeline.innerHTML = '';
            p.audits.forEach(a => {
                timeline.innerHTML += `
                    <div class="timeline-badge success"><i class="fe fe-check"></i></div>
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h6 class="timeline-title">${a.action.toUpperCase()} par ${a.admin_name}</h6>
                        </div>
                        <div class="timeline-body">
                            <p>${a.details}</p>
                            <small class="text-muted"><i class="fe fe-clock me-1"></i>${formatDate(a.created_at)}</small>
                        </div>
                    </div>
                `;
            });

            // Docs
            const docsList = document.getElementById('admin-docs-list');
            docsList.innerHTML = '';
            if (p.documents && p.documents.length > 0) {
                p.documents.forEach(d => {
                    docsList.innerHTML += [
                        '<li class="list-group-item d-flex justify-content-between align-items-center">',
                            d.type || 'Document',
                            '<a href="/documents/secure/project/' + d.id + '/view" class="btn btn-sm btn-outline-primary" title="Voir"><i class="fe fe-eye"></i></a>',
                        '</li>'
                    ].join('');
                });
            } else {
                docsList.innerHTML = '<li class="list-group-item text-center">Aucun document</li>';
            }

            modal.show();
        } catch (error) {
            console.error('Erreur détails:', error);
            ALOGOTO.error('Impossible de charger les détails du projet.');
        }
    }

    // --- ACTIONS ACTIONS ---
    document.getElementById('btn-validate').addEventListener('click', () => performAction('validate'));
    document.getElementById('btn-reject').addEventListener('click', () => performAction('reject'));
    document.getElementById('btn-suspend').addEventListener('click', () => performAction('suspend'));

    async function performAction(action) {
        if (!currentProject) return;

        const modalEl = document.getElementById('modal-project-admin');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        const comment = document.getElementById('admin-comment').value;
        if (!comment) {
            ALOGOTO.warning('Veuillez ajouter un commentaire pour justifier votre action.');
            return;
        }

        const actionLabels = { validate: 'Valider', reject: 'Rejeter', suspend: 'Suspendre' };
        const confirmed = await ALOGOTO.confirm(
            `Confirmer l'action`,
            `Voulez-vous vraiment ${actionLabels[action] || action} ce projet ?`,
            `Oui, ${actionLabels[action] || action}`,
            'Annuler'
        );
        if (!confirmed.isConfirmed) return;

        try {
            const response = await fetch(`${API_BASE}/admin/projects/${currentProject.id}/${action}`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({ commentaire: comment })
            });
            const result = await response.json();

            if (result.success) {
                ALOGOTO.success(result.message);
                loadProjects();
                loadStats();
            } else {
                ALOGOTO.error(result.message || 'Action échouée.');
            }
        } catch (error) {
            ALOGOTO.error('Une erreur est survenue lors de l\'action.');
        }
    }

    // --- ALERTES ---
    async function loadAlerts() {
        try {
            const response = await fetch(`${API_BASE}/admin/projects/alerts`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const result = await response.json();
            if (!result.success) throw new Error('Erreur API');

            const alerts = result.alerts || [];
            if (alerts.length > 0) {
                document.getElementById('critical-alerts-section').classList.remove('d-none');
                const container = document.getElementById('alerts-container');
                container.innerHTML = '';
                alerts.forEach(a => {
                    container.innerHTML += `
                        <div class="alert-item d-flex justify-content-between align-items-center mb-2">
                            <span><strong>${a.title}:</strong> ${a.message}</span>
                            <button class="btn btn-sm btn-outline-danger" onclick="window.location.href='projets.php?search=${a.project_id}'">Voir</button>
                        </div>
                    `;
                });
            }
        } catch (error) {
            console.error('Erreur alertes:', error);
        }
    }

    // --- HELPERS ---
    function renderStatusCell(p) {
        const financedStatuses = ['funded', 'active', 'completed'];
        const hasFunding = (p.financements && p.financements.length > 0) || (p.montant_finance && p.montant_finance > 0);
        const isFinanced = financedStatuses.includes(p.statut) || hasFunding;
        const label = isFinanced ? 'Projet déjà financé' : getStatusLabel(p.statut);
        const badge = isFinanced ? 'bg-success' : getStatusBadge(p.statut);
        return `<span class="badge ${badge}">${label}</span>`;
    }

    function formatMoney(amount) {
        return new Intl.NumberFormat('fr-FR').format(amount);
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const d = new Date(dateString);
        return d.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    function getStatusLabel(status) {
        const labels = {
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
        const map = {
            'draft': 'bg-secondary',
            'submitted': 'bg-info',
            'under_admin_review': 'bg-warning',
            'admin_rejected': 'bg-danger',
            'admin_validated': 'bg-success',
            'available_for_imf': 'bg-info',
            'under_institution_review': 'bg-warning',
            'interview_scheduled': 'bg-info',
            'interview_confirmed': 'bg-success',
            'documents_requested': 'bg-warning',
            'institution_rejected': 'bg-danger',
            'institution_accepted': 'bg-success',
            'funded': 'bg-success',
            'active': 'bg-primary',
            'suspended': 'bg-danger',
            'completed': 'bg-dark',
            'cancelled': 'bg-dark',
        };
        return map[status] || 'bg-light text-dark';
    }

    function getRiskBadge(level) {
        const map = {
            'faible': 'bg-success-transparent text-success',
            'moyen': 'bg-warning-transparent text-warning',
            'élevé': 'bg-danger-transparent text-danger',
            'critique': 'bg-danger text-white'
        };
        return map[level] || 'bg-light';
    }

    function renderPagination(data) {
        const container = document.getElementById('pagination-container');
        container.innerHTML = '';

        if (data.last_page <= 1) return;

        const nav = document.createElement('ul');
        nav.className = 'pagination';

        data.links.forEach(link => {
            const li = document.createElement('li');
            li.className = `page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}`;
            
            const a = document.createElement('a');
            a.className = 'page-link';
            a.innerHTML = link.label;
            a.href = '#';
            if (link.url) {
                const page = new URL(link.url).searchParams.get('page');
                a.onclick = (e) => {
                    e.preventDefault();
                    loadProjects(page);
                };
            }

            li.appendChild(a);
            nav.appendChild(li);
        });

        container.appendChild(nav);
    }

});
