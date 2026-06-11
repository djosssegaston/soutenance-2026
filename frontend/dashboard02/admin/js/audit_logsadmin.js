/**
 * Audit Logs Admin JS
 */
document.addEventListener('DOMContentLoaded', function() {
    const API_BASE = '/api/v1';
    loadLogs();

    function debounce(fn, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    const autoFilter = debounce(() => loadLogs(1), 300);

    document.getElementById('apply-filters').addEventListener('click', () => loadLogs(1));
    document.getElementById('search-input').addEventListener('input', autoFilter);
    document.getElementById('filter-action').addEventListener('change', () => loadLogs(1));
    document.getElementById('filter-level').addEventListener('change', () => loadLogs(1));
    document.getElementById('filter-date').addEventListener('change', () => loadLogs(1));
    document.getElementById('search-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); loadLogs(1); }
    });

    async function loadLogs(page = 1) {
        const tbody = document.getElementById('logs-tbody');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>';
        try {
            const params = new URLSearchParams({
                page, search: document.getElementById('search-input').value,
                action: document.getElementById('filter-action').value,
                level: document.getElementById('filter-level').value,
                date: document.getElementById('filter-date').value
            });
            const res = await fetch(`${API_BASE}/admin/audit-logs?${params}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            if (!data.success) throw new Error('Erreur API');
            document.getElementById('logs-count').textContent = `${data.total || 0} entrées`;
            if (data.stats) {
                document.getElementById('kpi-today').textContent = data.stats.today || 0;
                document.getElementById('kpi-validations').textContent = data.stats.validations || 0;
                document.getElementById('kpi-rejections').textContent = data.stats.rejections || 0;
                document.getElementById('kpi-security').textContent = data.stats.security || 0;
            }
            renderTable(data.data || []);
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Erreur de chargement.</td></tr>';
        }
    }

    function renderTable(items) {
        const tbody = document.getElementById('logs-tbody');
        tbody.innerHTML = '';
        if (items.length === 0) { tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5">Aucun log.</td></tr>'; return; }
        const levelBadges = {'info':'bg-info-transparent text-info','warning':'bg-warning-transparent text-warning','critical':'bg-danger-transparent text-danger'};
        const actionBadges = {'validate':'bg-success-transparent text-success','reject':'bg-danger-transparent text-danger','suspend':'bg-warning-transparent text-warning','login':'bg-primary-transparent text-primary','update':'bg-info-transparent text-info'};
        items.forEach(l => {
            const tr = document.createElement('tr');
            const dt = l.created_at ? new Date(l.created_at).toLocaleString('fr-FR') : '';
            tr.innerHTML = `
                <td class="fs-12">${dt}</td>
                <td><span class="fw-semibold">${l.user_name || 'Système'}</span><br><small class="text-muted">${l.user_role || ''}</small></td>
                <td><span class="badge ${actionBadges[l.action] || 'bg-light text-dark'}">${l.action || ''}</span></td>
                <td>${l.target || ''}</td>
                <td><span class="badge ${levelBadges[l.level] || 'bg-light'}">${l.level || 'info'}</span></td>
                <td class="fs-12 text-muted">${l.ip_address || '—'}</td>
                <td><button class="btn btn-sm btn-outline-primary" title="Détails"><i class="fe fe-eye"></i></button></td>`;
            tbody.appendChild(tr);
        });
    }
});
