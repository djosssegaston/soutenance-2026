/**
 * Remboursements Admin JS
 */
document.addEventListener('DOMContentLoaded', function() {
    const API_BASE = '/api/v1';
    loadRemboursements();

    function debounce(fn, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    const autoFilter = debounce(() => loadRemboursements(1), 300);

    document.getElementById('apply-filters').addEventListener('click', () => loadRemboursements(1));
    document.getElementById('search-input').addEventListener('input', autoFilter);
    document.getElementById('filter-status').addEventListener('change', () => loadRemboursements(1));
    document.getElementById('search-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); loadRemboursements(1); }
    });

    async function loadRemboursements(page = 1) {
        const tbody = document.getElementById('remb-tbody');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>';
        try {
            const params = new URLSearchParams({ page, search: document.getElementById('search-input').value, status: document.getElementById('filter-status').value });
            const res = await fetch(`${API_BASE}/admin/remboursements?${params}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            if (!data.success) throw new Error('Erreur API');
            document.getElementById('remb-count').textContent = `${data.total || 0} remboursements`;
            if (data.stats) {
                document.getElementById('kpi-total-repaid').textContent = new Intl.NumberFormat('fr-FR').format(data.stats.total_repaid || 0) + ' FCFA';
                document.getElementById('kpi-recovery-rate').textContent = (data.stats.recovery_rate || 0) + '%';
                document.getElementById('kpi-in-progress').textContent = data.stats.in_progress || 0;
                document.getElementById('kpi-defaults').textContent = data.stats.defaults || 0;
            }
            renderTable(data.data || []);
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Erreur de chargement.</td></tr>';
        }
    }

    function renderTable(items) {
        const tbody = document.getElementById('remb-tbody');
        tbody.innerHTML = '';
        if (items.length === 0) { tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5">Aucun remboursement.</td></tr>'; return; }
        const fmt = v => new Intl.NumberFormat('fr-FR').format(v || 0);
        const badges = {'paye':'bg-success-transparent text-success','en_attente':'bg-warning-transparent text-warning','en_retard':'bg-danger-transparent text-danger','defaulted':'bg-dark text-white'};
        items.forEach(r => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="fw-semibold">RMB-${String(r.id).padStart(5,'0')}</td>
                <td>${r.project_title || 'N/A'}</td>
                <td>${r.porteur_name || 'N/A'}</td>
                <td class="fw-bold">${fmt(r.montant_total)} FCFA</td>
                <td class="text-success">${fmt(r.montant_paye)} FCFA</td>
                <td class="text-danger">${fmt(r.montant_restant)} FCFA</td>
                <td><span class="badge ${badges[r.statut] || 'bg-light'}">${r.statut || 'N/A'}</span></td>
                <td><button class="btn btn-sm btn-primary"><i class="fe fe-eye"></i></button></td>`;
            tbody.appendChild(tr);
        });
    }
});
