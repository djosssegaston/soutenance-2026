<?php require_once __DIR__ . '/header.php'; ?>
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title text-uppercase fw-bold">Centre de Notifications</h1>
                    <p class="text-muted mb-0">Alertes système, notifications utilisateurs et événements plateforme.</p>
                </div>
                <div><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Admin</a></li><li class="breadcrumb-item active">Notifications</li></ol></div>
            </div>

            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Non Lues</p>
                                    <h3 class="mb-0 number-font fw-bold text-danger" id="kpi-unread">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-danger-transparent text-danger">
                                        <i class="fe fe-bell"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-danger fw-bold"><i class="fa fa-bell"></i> en attente</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Aujourd'hui</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-today">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-primary-transparent text-primary">
                                        <i class="fe fe-inbox"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-primary fw-bold"><i class="fa fa-calendar-o"></i> reçues</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Critiques</p>
                                    <h3 class="mb-0 number-font fw-bold text-warning" id="kpi-critical">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-warning-transparent text-warning">
                                        <i class="fe fe-alert-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-warning fw-bold"><i class="fa fa-exclamation"></i> urgentes</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Total Ce Mois</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-month">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-success-transparent text-success">
                                        <i class="fe fe-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-success fw-bold"><i class="fa fa-check"></i> toutes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Toutes les Notifications</h3>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-2" id="btn-mark-all">Tout marquer comme lu</button>
                                <span class="badge bg-primary" id="notif-count">0</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-sm-6 col-md-4"><select id="filter-type" class="form-control"><option value="">Tous types</option><option value="system">Système</option><option value="project">Projet</option><option value="payment">Paiement</option><option value="security">Sécurité</option></select></div>
                                <div class="col-12 col-sm-6 col-md-4"><select id="filter-read" class="form-control"><option value="">Toutes</option><option value="unread">Non lues</option><option value="read">Lues</option></select></div>
                                <div class="col-12 col-md-4"><button id="apply-filters" class="btn btn-primary w-100">Filtrer</button></div>
                            </div>
                            <div class="list-group list-group-flush" id="notif-list" style="max-height: 600px; overflow-y: auto;">
                                <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
                            </div>
                            <div id="pagination-container" class="mt-4 d-flex justify-content-center"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="viewNotifModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détail de la notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewNotifBody">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
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
document.addEventListener('DOMContentLoaded', function() {
    const API_BASE = '/api/v1';
    const JSON_HEADERS = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' };
    loadNotifications();

    function debounce(fn, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    document.getElementById('apply-filters').addEventListener('click', () => loadNotifications());
    document.getElementById('filter-type').addEventListener('change', () => loadNotifications());
    document.getElementById('filter-read').addEventListener('change', () => loadNotifications());
    document.getElementById('btn-mark-all').addEventListener('click', markAllRead);

    // Delegation des actions sur les notifications
    document.getElementById('notif-list').addEventListener('click', function(e) {
        const btn = e.target.closest('button');
        if (!btn) return;
        const item = btn.closest('.list-group-item');
        if (!item) return;
        const id = item.dataset.id;
        if (btn.classList.contains('btn-view-notif')) viewNotif(item);
        else if (btn.classList.contains('btn-mark-read')) markAsRead(id);
        else if (btn.classList.contains('btn-archive')) archiveNotif(id);
        else if (btn.classList.contains('btn-delete-notif')) deleteNotif(id);
    });

    let viewNotifModal = null;

    async function loadNotifications() {
        const list = document.getElementById('notif-list');
        list.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
        try {
            const params = new URLSearchParams({ type: document.getElementById('filter-type').value, read: document.getElementById('filter-read').value });
            const res = await fetch(`${API_BASE}/admin/notifications?${params}`, { headers: JSON_HEADERS });
            const data = await res.json();
            document.getElementById('notif-count').textContent = `${data.total || 0} notifications`;
            if (data.stats) {
                document.getElementById('kpi-unread').textContent = data.stats.unread || 0;
                document.getElementById('kpi-today').textContent = data.stats.today || 0;
                document.getElementById('kpi-critical').textContent = data.stats.critical || 0;
                document.getElementById('kpi-month').textContent = data.stats.month || 0;
            }
            list.innerHTML = '';
            const icons = {system:'fe-settings',project:'fe-briefcase',payment:'fe-credit-card',security:'fe-shield'};
            const colors = {system:'bg-info-transparent text-info',project:'bg-primary-transparent text-primary',payment:'bg-success-transparent text-success',security:'bg-danger-transparent text-danger'};
            (data.data || []).forEach(n => {
                const typeLabels = {system:'Système',project:'Projet',payment:'Paiement',security:'Sécurité'};
                list.innerHTML += `
                <div class="list-group-item ${!n.read ? 'bg-light border-start border-primary border-3' : ''}"
                     data-id="${n.id}"
                     data-subject="${escapeHtml(n.subject || n.title)}"
                     data-message="${escapeHtml(n.message)}"
                     data-type="${escapeHtml(n.type)}"
                     data-date="${n.created_at ? formatDate(n.created_at) : ''}"
                     data-read="${n.read}">
                    <div class="d-flex align-items-start gap-3">
                        <span class="avatar avatar-md ${colors[n.type] || 'bg-light'} rounded-circle flex-shrink-0"><i class="fe ${icons[n.type] || 'fe-bell'}"></i></span>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div class="min-w-0">
                                    <h6 class="mb-1 ${!n.read ? 'fw-bold' : ''}">${escapeHtml(n.subject || n.title)}</h6>
                                    <p class="text-muted mb-0 fs-13 text-break">${escapeHtml(n.message)}</p>
                                </div>
                                <div class="d-flex align-items-center gap-1 flex-shrink-0 notif-actions">
                                    <small class="text-muted me-1 d-none d-sm-inline text-nowrap">${n.created_at ? formatDate(n.created_at) : ''}</small>
                                    <button class="btn btn-sm btn-outline-info btn-view-notif" title="Voir"><i class="fe fe-eye"></i></button>
                                    ${!n.read ? '<button class="btn btn-sm btn-outline-primary btn-mark-read" title="Marquer comme lu"><i class="fe fe-check"></i></button>' : ''}
                                    <button class="btn btn-sm btn-outline-secondary btn-archive" title="Archiver"><i class="fe fe-archive"></i></button>
                                    <button class="btn btn-sm btn-outline-danger btn-delete-notif" title="Supprimer"><i class="fe fe-trash-2"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            });
            if ((data.data || []).length === 0) list.innerHTML = '<p class="text-center text-muted py-5">Aucune notification.</p>';
        } catch (e) { list.innerHTML = '<p class="text-center text-danger py-4">Erreur de chargement.</p>'; }
    }

    function viewNotif(item) {
        if (!viewNotifModal) viewNotifModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('viewNotifModal'));
        const subject = item.dataset.subject || 'Notification';
        const message = item.dataset.message || '';
        const type = item.dataset.type || 'info';
        const date = item.dataset.date || '';
        const isRead = item.dataset.read === 'true';

        const typeLabels = {system: 'Système', project: 'Projet', payment: 'Paiement', security: 'Sécurité'};
        const typeColors = {system: 'info', project: 'primary', payment: 'success', security: 'danger'};
        const typeIcons = {system: 'fe-settings', project: 'fe-briefcase', payment: 'fe-credit-card', security: 'fe-shield'};
        const color = typeColors[type] || 'secondary';
        const label = typeLabels[type] || type;
        const icon = typeIcons[type] || 'fe-bell';
        const statusLabel = isRead ? 'Lue' : 'Non lue';
        const statusColor = isRead ? 'success' : 'warning';

        document.getElementById('viewNotifBody').innerHTML =
            '<div class="mb-3">' +
                '<span class="badge bg-' + color + '-transparent text-' + color + ' p-2 px-3 mb-2 d-inline-block">' +
                    '<i class="fe ' + icon + ' me-1"></i> ' + label +
                '</span>' +
                '<span class="badge bg-' + statusColor + '-transparent text-' + statusColor + ' p-2 px-3 mb-2 d-inline-block ms-2">' + statusLabel + '</span>' +
            '</div>' +
            '<h5 class="fw-bold mb-2">' + escapeHtml(subject) + '</h5>' +
            '<p class="text-muted mb-3">' + escapeHtml(message) + '</p>' +
            '<small class="text-muted">Reçue le : ' + date + '</small>';

        viewNotifModal.show();

        if (!isRead) markAsRead(item.dataset.id);
    }

    async function markAsRead(id) {
        try {
            const res = await fetch(`${API_BASE}/admin/notifications/${id}/read`, {
                method: 'POST', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
            });
            const data = await res.json();
            if (data.success) loadNotifications();
        } catch(e) {}
    }

    async function archiveNotif(id) {
        try {
            const res = await fetch(`${API_BASE}/admin/notifications/${id}/archive`, {
                method: 'POST', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
            });
            const data = await res.json();
            if (data.success) loadNotifications();
        } catch(e) {}
    }

    async function deleteNotif(id) {
        if (typeof ALOGOTO !== 'undefined' && ALOGOTO.confirm) {
            const result = await ALOGOTO.confirm('Confirmer', 'Supprimer cette notification ?', 'Oui, supprimer', 'Annuler');
            if (!result.isConfirmed) return;
        } else if (!window.confirm('Supprimer cette notification ?')) {
            return;
        }
        try {
            const res = await fetch(`${API_BASE}/admin/notifications/${id}`, {
                method: 'DELETE', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
            });
            const data = await res.json();
            if (data.success) loadNotifications();
        } catch(e) {}
    }

    async function markAllRead() {
        try {
            await fetch(`${API_BASE}/admin/notifications/mark-all-read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });
            loadNotifications();
        } catch(e) {}
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        return new Date(dateStr).toLocaleString('fr-FR');
    }
});
</script>
