/**
 * Sécurité - Institution
 */

const API_BASE = '/api/v1/institution';

document.addEventListener('DOMContentLoaded', function() {
    fetchSecurityData();
    fetchPasswordHistory();

    document.getElementById('form-password').addEventListener('submit', handlePasswordChange);
});

function switchSection(section) {
    // UI toggle
    ['password', 'sessions', 'logs'].forEach(s => {
        document.getElementById(`section-${s}`).classList.toggle('d-none', s !== section);
        const tab = document.getElementById(`tab-${s}`);
        tab.classList.toggle('active', s === section);
        tab.classList.toggle('border-bottom', s === section);
        tab.classList.toggle('border-primary', s === section);
        tab.classList.toggle('fw-bold', s === section);
        tab.classList.toggle('text-muted', s !== section);
    });
}

async function fetchSecurityData() {
    try {
        const response = await fetch(`${API_BASE}/security`);
        const data = await response.json();

        // Stats
        document.getElementById('stat-sessions').textContent = data.stats.active_sessions;
        document.getElementById('stat-pwd-date').textContent = data.stats.password_updated;
        document.getElementById('stat-logs').textContent = data.stats.total_logs;

        // Sessions
        const sBody = document.getElementById('sessions-body');
        sBody.innerHTML = '';
        data.sessions.forEach(s => {
            sBody.innerHTML += `
                <tr>
                    <td><i class="bi bi-${s.device === 'Mobile' ? 'phone' : 'laptop'} me-2"></i> ${s.browser} (${s.platform})</td>
                    <td>${s.ip_address}</td>
                    <td>${s.last_activity_ago} ${s.is_current ? '<span class="badge bg-success-transparent text-success ms-2">Actuelle</span>' : ''}</td>
                    <td>
                        ${!s.is_current ? `<button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation();killSession('${s.id}')"><i class="fe fe-x"></i></button>` : ''}
                    </td>
                </tr>
            `;
        });

        // Logs
        const lBody = document.getElementById('logs-body');
        lBody.innerHTML = '';
        data.audit_logs.forEach(l => {
            lBody.innerHTML += `
                <tr>
                    <td><i class="bi ${l.icon || 'bi-info-circle'} text-${l.color || 'primary'} me-2"></i> ${l.action}</td>
                    <td>${l.ip}</td>
                    <td>${new Date(l.date).toLocaleString()}</td>
                </tr>
            `;
        });

    } catch (e) { console.error(e); }
}

async function fetchPasswordHistory() {
    try {
        const response = await fetch(`${API_BASE}/security/password-history`);
        const logs = await response.json();
        const list = document.getElementById('pwd-history');
        list.innerHTML = '';
        if (logs.length === 0) {
            list.innerHTML = '<li class="list-group-item text-muted">Aucun historique récent.</li>';
            return;
        }
        logs.forEach(l => {
            list.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-2"></i> Mis à jour</span>
                    <small class="text-muted">${l.date} (IP: ${l.ip})</small>
                </li>
            `;
        });
    } catch (e) {}
}

async function handlePasswordChange(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch(`${API_BASE}/security/password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        if (response.ok) {
            ALOGOTO.success('Mot de passe mis à jour avec succès.');
            e.target.reset();
            fetchPasswordHistory();
        } else {
            ALOGOTO.error(result.message || 'Erreur lors de la mise à jour.');
        }
    } catch (e) { console.error(e); }
}

async function killSession(id) {
    const { isConfirmed } = await ALOGOTO.confirm('Déconnexion', 'Déconnecter cet appareil ?', 'Oui, déconnecter', 'Annuler');
    if (!isConfirmed) return;
    try {
        await fetch(`${API_BASE}/security/sessions/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        fetchSecurityData();
    } catch (e) {}
}
