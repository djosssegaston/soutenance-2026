/**
 * Notifications - Institution
 */

const API_BASE = '/api/v1/institution';
let currentTab = 'all';

document.addEventListener('DOMContentLoaded', function() {
    fetchNotifications();

    var markBtn = document.getElementById('markAllReadBtn');
    if (markBtn) markBtn.addEventListener('click', markAllRead);
});

function switchTab(tab) {
    currentTab = tab;
    document.querySelectorAll('#notificationTabs .nav-link').forEach(link => {
        link.classList.toggle('active', link.textContent.toLowerCase().includes(tab === 'all' ? 'non archivées' : tab));
    });
    fetchNotifications(1);
}

async function fetchNotifications(page = 1) {
    const loader = document.getElementById('loader');
    const tbody = document.getElementById('notificationsBody');
    
    if (loader) loader.style.display = 'block';
    if (!tbody) return;
    tbody.innerHTML = '';

    try {
        const response = await fetch(`${API_BASE}/notifications?tab=${currentTab}&page=${page}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();

        updateStats(data.stats);
        renderNotifications(data.notifications.data);
        renderPagination(data.notifications);
    } catch (error) {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erreur de chargement.</td></tr>';
    } finally {
        if (loader) loader.style.display = 'none';
    }
}

function updateStats(stats) {
    document.getElementById('stat-unread').textContent = stats.unread;
    document.getElementById('stat-read').textContent = stats.read;
    document.getElementById('stat-archived').textContent = stats.archived;
    document.getElementById('stat-total').textContent = stats.total;
}

function renderNotifications(notifs) {
    const tbody = document.getElementById('notificationsBody');
    tbody.innerHTML = '';

    if (notifs.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">Aucune notification.</td></tr>';
        return;
    }

    notifs.forEach(n => {
        const typeIcon = getTypeIcon(n.type);
        const row = `
            <tr class="${n.is_read ? '' : 'fw-bold'}">
                <td>
                    <span class="avatar avatar-md rounded-circle bg-light d-flex align-items-center justify-content-center">
                        <i class="${typeIcon} fs-18"></i>
                    </span>
                </td>
                <td>${n.title || 'Sans titre'}</td>
                <td><span class="text-muted">${n.content.substring(0, 50)}...</span></td>
                <td>${getStatusBadge(n)}</td>
                <td><small>${new Date(n.created_at).toLocaleString()}</small></td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-info" onclick="event.stopPropagation();viewNotif(${n.id})"><i class="fe fe-eye"></i></button>
                        ${!n.archived_at ? `<button class="btn btn-sm btn-outline-warning" onclick="event.stopPropagation();archiveNotif(${n.id})"><i class="fe fe-folder"></i></button>` : ''}
                        <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation();deleteNotif(${n.id})"><i class="fe fe-trash-2"></i></button>
                    </div>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
}

async function viewNotif(id) {
    try {
        const response = await fetch(`${API_BASE}/notifications/${id}/read`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        });
        
        const resp = await fetch(`${API_BASE}/notifications?tab=${currentTab}`);
        const data = await resp.json();
        const n = data.notifications.data.find(x => x.id === id);

        const modalBody = document.getElementById('viewModalBody');
        modalBody.innerHTML = `
            <h5 class="fw-bold">${n.title}</h5>
            <p class="text-muted">${n.content}</p>
            <div class="small text-muted mt-3">Reçue le ${new Date(n.created_at).toLocaleString()}</div>
        `;
        
        bootstrap.Modal.getOrCreateInstance(document.getElementById('viewModal')).show();
        fetchNotifications();
    } catch (error) { console.error(error); }
}

async function archiveNotif(id) {
    const { isConfirmed } = await ALOGOTO.confirm('Archiver', 'Archiver cette notification ?', 'Oui, archiver', 'Annuler');
    if (!isConfirmed) return;
    try {
        await fetch(`${API_BASE}/notifications/${id}/archive`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        ALOGOTO.success('Notification archivée.');
        fetchNotifications();
    } catch (e) {}
}

async function deleteNotif(id) {
    const { isConfirmed } = await ALOGOTO.confirm('Supprimer', 'Supprimer cette notification ?', 'Oui, supprimer', 'Annuler');
    if (!isConfirmed) return;
    try {
        await fetch(`${API_BASE}/notifications/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        ALOGOTO.success('Notification supprimée.');
        fetchNotifications();
    } catch (e) {}
}

async function markAllRead() {
    try {
        await fetch(`${API_BASE}/notifications/read-all`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        fetchNotifications();
    } catch (e) {}
}

function renderPagination(data) {
    const container = document.getElementById('pagination-container');
    if (!data.links) return;
    let html = '<ul class="pagination">';
    data.links.forEach(link => {
        if (link.url) {
            const page = link.url.split('page=')[1];
            html += `<li class="page-item ${link.active ? 'active' : ''}"><a class="page-link" href="javascript:void(0)" onclick="fetchNotifications(${page})">${link.label}</a></li>`;
        }
    });
    html += '</ul>';
    container.innerHTML = html;
}

function getTypeIcon(type) {
    const map = { 'info': 'bi bi-info-circle text-info', 'success': 'bi bi-check-circle text-success', 'warning': 'bi bi-exclamation-triangle text-warning', 'danger': 'bi bi-x-octagon text-danger' };
    return map[type] || 'bi bi-bell text-primary';
}

function getStatusBadge(n) {
    if (n.archived_at) return '<span class="badge bg-info-transparent text-info">Archivée</span>';
    if (!n.is_read) return '<span class="badge bg-warning-transparent text-warning">Non lue</span>';
    return '<span class="badge bg-success-transparent text-success">Lue</span>';
}
