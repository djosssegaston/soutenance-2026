/**
 * Supervision des Financements - Admin JS
 */
document.addEventListener('DOMContentLoaded', function() {
    const API_BASE = '/api/v1';

    loadStats();
    loadFinancements();

    // Auto-filtering
    function debounce(fn, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    const autoFilter = debounce(() => loadFinancements(1), 300);

    document.getElementById('apply-filters').addEventListener('click', () => loadFinancements(1));
    document.getElementById('search-input').addEventListener('input', autoFilter);
    document.getElementById('filter-type').addEventListener('change', () => loadFinancements(1));
    document.getElementById('filter-status').addEventListener('change', () => loadFinancements(1));
    document.getElementById('filter-date-from').addEventListener('change', () => loadFinancements(1));
    document.getElementById('search-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); loadFinancements(1); }
    });

    // Delegation pour bouton Voir (détail)
    document.getElementById('finance-tbody').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-view-finance');
        if (btn) openDetailModal(btn.dataset.id);
    });
    // Delegation pour bouton Reçu
    document.getElementById('finance-tbody').addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-receipt-finance');
        if (btn) openRecuModal(btn.dataset.id);
    });

    async function openDetailModal(id) {
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('detailModal'));
        document.getElementById('detail-loading').style.display = '';
        document.getElementById('detail-content').style.display = 'none';
        document.getElementById('modalDetailTitle').textContent = 'Chargement...';
        modal.show();
        try {
            const resp = await fetch(`${API_BASE}/admin/financements/${id}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!resp.ok) throw new Error('HTTP ' + resp.status);
            const json = await resp.json();
            if (!json.success) throw new Error('Erreur API');
            const d = json.data;
            document.getElementById('modalDetailTitle').textContent = 'Détail - ' + d.reference;
            document.getElementById('dd-project').textContent = d.project_title;
            document.getElementById('dd-description').innerHTML = d.project_description || 'Aucune description';
            document.getElementById('dd-porteur').textContent = d.porteur_name;
            document.getElementById('dd-porteur-email').textContent = d.porteur_email;
            document.getElementById('dd-statut').textContent = d.statut_label;
            document.getElementById('dd-statut').className = 'badge fs-13 px-3 py-2 bg-' + (d.statut === 'disbursed' || d.statut === 'active' ? 'success' : d.statut === 'completed' ? 'primary' : d.statut === 'rejected' || d.statut === 'defaulted' ? 'danger' : 'warning');
            document.getElementById('dd-reference').textContent = d.reference;
            document.getElementById('dd-montant-demande').textContent = formatMoney(d.montant_demande) + ' FCFA';
            document.getElementById('dd-montant-valide').textContent = formatMoney(d.montant_valide) + ' FCFA';
            document.getElementById('dd-montant-mensuel').textContent = formatMoney(d.montant_mensuel) + ' FCFA';
            document.getElementById('dd-taux').textContent = d.taux_interet + ' %';
            document.getElementById('dd-duree').textContent = d.duree + ' mois';
            document.getElementById('dd-progression').textContent = d.progression + ' %';
            document.getElementById('dd-reste').textContent = formatMoney(d.reste_du) + ' FCFA';
            document.getElementById('dd-date-financement').textContent = d.date_financement || '—';
            document.getElementById('dd-date-validation').textContent = d.date_validation || '—';
            document.getElementById('dd-date-decaissement').textContent = d.date_decaissement || '—';
            const condRow = document.getElementById('dd-conditions-row');
            const condEl = document.getElementById('dd-conditions');
            if (d.conditions) { condRow.style.display = ''; condEl.textContent = d.conditions; }
            else { condRow.style.display = 'none'; }
            const commRow = document.getElementById('dd-commentaires-row');
            const commEl = document.getElementById('dd-commentaires');
            if (d.commentaires) { commRow.style.display = ''; commEl.textContent = d.commentaires; }
            else { commRow.style.display = 'none'; }
            document.getElementById('detail-loading').style.display = 'none';
            document.getElementById('detail-content').style.display = '';
        } catch (error) {
            document.getElementById('detail-loading').innerHTML = '<p class="text-danger">Erreur de chargement du détail.</p>';
        }
    }

    async function openRecuModal(id) {
        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('recuModal'));
        document.getElementById('recu-loading').style.display = '';
        document.getElementById('recu-content').style.display = 'none';
        modal.show();
        try {
            const resp = await fetch(`${API_BASE}/admin/financements/${id}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!resp.ok) throw new Error('HTTP ' + resp.status);
            const json = await resp.json();
            if (!json.success) throw new Error('Erreur API');
            const d = json.data;
            document.getElementById('recu-reference').textContent = d.reference;
            document.getElementById('recu-projet').textContent = d.project_title;
            document.getElementById('recu-institution').textContent = d.institution_name;
            document.getElementById('recu-porteur').textContent = d.porteur_name;
            document.getElementById('recu-montant').textContent = formatMoney(d.montant_valide) + ' FCFA';
            document.getElementById('recu-taux').textContent = d.taux_interet + ' %';
            document.getElementById('recu-duree').textContent = d.duree + ' mois';
            document.getElementById('recu-statut').innerHTML = '<span class="badge bg-success-transparent text-success">' + d.statut_label + '</span>';
            document.getElementById('recu-date').textContent = d.created_at;
            document.getElementById('recu-loading').style.display = 'none';
            document.getElementById('recu-content').style.display = '';
        } catch (error) {
            document.getElementById('recu-loading').style.display = 'none';
            document.getElementById('recu-content').innerHTML = '<p class="text-danger text-center">Erreur de chargement du reçu.</p>';
        }
    }

    async function loadStats() {
        try {
            const response = await fetch(`${API_BASE}/admin/financements/statistics`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const data = await response.json();
            if (!data.success) throw new Error('Erreur API');
            document.getElementById('kpi-total-volume').textContent = formatMoney(data.total_volume) + ' FCFA';
            document.getElementById('kpi-active-funding').textContent = data.active_count || 0;
            document.getElementById('kpi-pending-disbursement').textContent = data.pending_count || 0;
            document.getElementById('kpi-payment-incidents').textContent = data.incidents_count || 0;
            initCharts(data);
        } catch (error) {
            console.error('Erreur stats financement:', error);
        }
    }

    function initCharts(data) {
        const flowData = data.monthly_flow || Array(12).fill(0);
        new ApexCharts(document.querySelector("#finance-flow-chart"), {
            series: [{ name: 'Décaissements', data: flowData }],
            chart: { height: 350, type: 'area', toolbar: { show: false } },
            xaxis: { categories: ['Jan','Fev','Mar','Avr','Mai','Jun','Jul','Aou','Sep','Oct','Nov','Dec'] },
            colors: ['#28a745'], stroke: { curve: 'smooth' }
        }).render();

        const typeData = data.type_distribution || { investment: 0, loan: 0, grant: 0 };
        new ApexCharts(document.querySelector("#finance-type-chart"), {
            series: [typeData.investment || 0, typeData.loan || 0, typeData.grant || 0],
            chart: { type: 'donut', height: 350 },
            labels: ['Investissement', 'Prêt', 'Subvention'],
            colors: ['#6259ca', '#ffc107', '#28a745'],
            legend: { position: 'bottom' }
        }).render();
    }

    async function loadFinancements(page = 1) {
        const tbody = document.getElementById('finance-tbody');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>';
        const params = new URLSearchParams({
            page, search: document.getElementById('search-input').value,
            type: document.getElementById('filter-type').value,
            status: document.getElementById('filter-status').value,
            date_from: document.getElementById('filter-date-from').value
        });
        try {
            const response = await fetch(`${API_BASE}/admin/financements?${params}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const data = await response.json();
            if (!data.success) throw new Error('Erreur API');
            document.getElementById('finance-count').textContent = `${data.total} opérations`;
            renderTable(data.data);
            renderPagination(data);
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Erreur de chargement.</td></tr>';
        }
    }

    function renderTable(items) {
        const tbody = document.getElementById('finance-tbody');
        tbody.innerHTML = '';
        if (!items || items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5">Aucun financement trouvé.</td></tr>';
            return;
        }
        items.forEach(f => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><span class="fw-semibold">FIN-${String(f.id).padStart(5,'0')}</span></td>
                <td><h6 class="mb-0 fw-semibold">${f.project_title || ''}</h6></td>
                <td>${f.institution_name || ''}</td>
                <td class="fw-bold text-success">${formatMoney(f.montant)} FCFA</td>
                <td><span class="badge ${getTypeBadge(f.type)}">${f.type || ''}</span></td>
                <td><span class="badge ${getStatusBadge(f.statut)}">${f.statut || ''}</span></td>
                <td class="fs-12">${formatDate(f.created_at)}</td>
                <td>
                    <div class="btn-list">
                        <button class="btn btn-sm btn-primary btn-view-finance" title="Voir" data-id="${f.id}"><i class="fe fe-eye"></i></button>
                        <button class="btn btn-sm btn-info btn-receipt-finance" title="Reçu" data-id="${f.id}"><i class="fe fe-file-text"></i></button>
                    </div>
                </td>`;
            tbody.appendChild(tr);
        });
    }

    function formatMoney(a) { return new Intl.NumberFormat('fr-FR').format(a || 0); }
    function formatDate(d) { if (!d) return ''; return new Date(d).toLocaleDateString('fr-FR', {day:'2-digit',month:'2-digit',year:'numeric'}); }
    function getTypeBadge(t) { return {'investment':'bg-primary-transparent text-primary','loan':'bg-warning-transparent text-warning','grant':'bg-success-transparent text-success'}[t] || 'bg-light text-dark'; }
    function getStatusBadge(s) { return {'pending':'bg-warning-transparent text-warning','disbursed':'bg-success-transparent text-success','completed':'bg-primary-transparent text-primary','failed':'bg-danger-transparent text-danger'}[s] || 'bg-light text-dark'; }

    function renderPagination(data) {
        const container = document.getElementById('pagination-container');
        container.innerHTML = '';
        if (!data.last_page || data.last_page <= 1) return;
        const nav = document.createElement('ul'); nav.className = 'pagination';
        data.links.forEach(link => {
            const li = document.createElement('li');
            li.className = `page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}`;
            const a = document.createElement('a'); a.className = 'page-link'; a.innerHTML = link.label; a.href = '#';
            if (link.url) { const p = new URL(link.url).searchParams.get('page'); a.onclick = (e) => { e.preventDefault(); loadFinancements(p); }; }
            li.appendChild(a); nav.appendChild(li);
        });
        container.appendChild(nav);
    }
});
