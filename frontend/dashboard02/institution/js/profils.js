/**
 * Profil - Institution
 */

const API_BASE = '/api/v1/institution';

document.addEventListener('DOMContentLoaded', function() {
    fetchProfile();
    fetchHistory();

    document.getElementById('btn-edit-profile').addEventListener('click', toggleEdit);
    document.getElementById('btn-cancel-edit').addEventListener('click', toggleEdit);
    document.getElementById('form-profile').addEventListener('submit', handleUpdate);
    document.getElementById('logo-upload').addEventListener('change', handleLogoUpload);
    document.getElementById('user-avatar-upload').addEventListener('change', handleAvatarUpload);
});

async function fetchProfile() {
    try {
        const response = await fetch(`${API_BASE}/profile`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        const inst = data.institution;

        // Fill inputs
        document.getElementById('input-inst-name').value = inst?.nom ?? '';
        document.getElementById('input-user-name').value = data.user.name;
        document.getElementById('input-email').value = data.user.email;
        document.getElementById('input-phone').value = inst?.telephone ?? '';
        document.getElementById('input-address').value = inst?.adresse ?? '';
        document.getElementById('input-website').value = inst?.site_web ?? '';
        document.getElementById('input-description').value = inst?.description ?? '';
        
        document.getElementById('logo-img').src = inst?.logo ? `/storage/${inst.logo}` : '../../asset/images/brand/logo-dark.png';

        // Stats
        document.getElementById('stat-projets').textContent = data.stats.projets_finances;
        document.getElementById('stat-portefeuille').textContent = formatMoney(data.stats.portefeuille_actif);
        document.getElementById('stat-roi').textContent = `${data.stats.roi_moyen.toFixed(1)}%`;

    } catch (error) { console.error(error); }
}

function toggleEdit() {
    const form = document.getElementById('form-profile');
    const inputs = form.querySelectorAll('input, textarea');
    const actions = document.getElementById('edit-actions');
    const editBtn = document.getElementById('btn-edit-profile');

    const isDisabled = inputs[0].disabled;
    inputs.forEach(inp => inp.disabled = !isDisabled);
    actions.classList.toggle('d-none');
    editBtn.classList.toggle('d-none');
}

async function handleUpdate(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch(`${API_BASE}/profile/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        if (response.ok) {
            ALOGOTO.success('Profil mis à jour.');
            toggleEdit();
            fetchProfile();
            fetchHistory();
        } else {
            var errData;
            try { errData = await response.json(); } catch(e) { errData = {}; }
            var msg = errData.message || 'Erreur lors de la mise à jour.';
            if (errData.errors) {
                var details = Object.values(errData.errors).flat().join(' ');
                if (details) msg += ' ' + details;
            }
            ALOGOTO.error(msg);
        }
    } catch (e) { console.error(e); }
}

function resolveAvatarUrl(url) {
    if (!url) return '../../asset/images/profiles/1.jpg';
    if (url.startsWith('http') || url.startsWith('/')) return url;
    return '/storage/' + url;
}

function updateAvatarAll(src) {
    var resolved = resolveAvatarUrl(src);
    var headerImg = document.getElementById('header-user-avatar');
    if (headerImg) headerImg.src = resolved;
    var profileImg = document.getElementById('user-avatar-img');
    if (profileImg) profileImg.src = resolved;
}

async function handleLogoUpload(e) {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('logo', file);

    try {
        const response = await fetch(`${API_BASE}/profile/photo`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: formData
        });
        if (response.ok) {
            ALOGOTO.success('Logo mis à jour.');
            fetchProfile();
            fetchHistory();
        }
    } catch (e) { console.error(e); }
}

async function handleAvatarUpload(e) {
    const file = e.target.files[0];
    if (!file) return;
    var fb = document.getElementById('user-avatar-feedback');
    var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    var formData = new FormData();
    formData.append('avatar', file);
    fb.className = 'small';
    fb.textContent = 'Téléchargement...';
    fb.classList.remove('d-none');
    try {
        var res = await fetch('/api/v1/profile/avatar', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: formData
        });
        var data = await res.json();
        if (data.avatar_url) {
            updateAvatarAll(data.avatar_url);
            fb.className = 'small text-success';
            fb.textContent = data.message || 'Photo mise à jour';
            ALOGOTO.success('Photo de profil mise à jour.');
        } else {
            fb.className = 'small text-danger';
            fb.textContent = data.message || 'Erreur lors du téléchargement';
        }
    } catch (e) {
        fb.className = 'small text-danger';
        fb.textContent = 'Erreur réseau';
    }
    setTimeout(function() { fb.classList.add('d-none'); }, 3000);
}

async function fetchHistory() {
    try {
        const response = await fetch(`${API_BASE}/profile/history`);
        const logs = await response.json();
        const tbody = document.getElementById('history-body');
        tbody.innerHTML = '';
        logs.forEach(l => {
            tbody.innerHTML += `
                <tr>
                    <td><i class="bi ${l.icon} text-${l.color} me-2"></i> ${l.action}</td>
                    <td><small class="text-muted">${l.date}</small></td>
                </tr>
            `;
        });
    } catch (e) {}
}

function formatMoney(amount) {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', minimumFractionDigits: 0 }).format(amount);
}
