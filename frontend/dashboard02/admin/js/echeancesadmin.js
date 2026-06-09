document.addEventListener('DOMContentLoaded', function () {
    const API_BASE = '/api/v1';
    let currentProjectId = null;

    loadProjects();

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
    document.getElementById('search-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); loadProjects(1); }
    });

    async function loadProjects(page = 1) {
        const tbody = document.getElementById('projects-tbody');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>';
        try {
            const params = new URLSearchParams({ page, search: document.getElementById('search-input').value });
            const res = await fetch(`${API_BASE}/admin/echeances/projects?${params}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const json = await res.json();
            if (!json.success) throw new Error('Erreur API');
            const data = json.data || [];
            document.getElementById('projects-count').textContent = `${(json.stats?.total_projects || 0)} projets`;
            document.getElementById('kpi-total-projects').textContent = json.stats?.total_projects || 0;
            document.getElementById('kpi-total-paid').textContent = json.stats?.total_paid || 0;
            document.getElementById('kpi-total-overdue').textContent = json.stats?.total_overdue || 0;
            document.getElementById('kpi-total-repayments').textContent = json.stats?.total_echeances || 0;
            renderProjects(data);
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Erreur de chargement.</td></tr>';
        }
    }

    function renderProjects(items) {
        const tbody = document.getElementById('projects-tbody');
        tbody.innerHTML = '';
        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5">Aucun projet avec échéances.</td></tr>';
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
                const id = this.dataset.id;
                const titre = this.dataset.titre;
                const porteur = this.dataset.porteur;
                openEcheancesModal(id, titre, porteur);
            });
        });
    }

    async function openEcheancesModal(projectId, titre, porteur) {
        currentProjectId = projectId;
        document.getElementById('modal-project-title').textContent = titre;
        document.getElementById('modal-porteur-name').textContent = 'Porteur : ' + porteur;
        document.getElementById('modal-loader').style.display = 'block';
        document.getElementById('modal-content').style.display = 'none';
        const modal = new bootstrap.Modal(document.getElementById('echeancesModal'));
        modal.show();

        try {
            const res = await fetch(`${API_BASE}/admin/echeances/project/${projectId}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const json = await res.json();
            if (!json.success) throw new Error('Erreur API');
            const items = json.data || [];
            document.getElementById('modal-institution-name').textContent = items.length > 0 ? (items[0].institution_nom || 'N/A') : 'N/A';
            renderModalEcheances(items);
        } catch (e) {
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

    function formatMoney(amount) {
        return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 0 }).format(amount || 0) + ' FCFA';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
