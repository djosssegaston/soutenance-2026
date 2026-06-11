/**
 * Gestion des Utilisateurs - Admin JS
 */

document.addEventListener('DOMContentLoaded', function() {
    const API_BASE = '/api/v1';
    let currentUser = null;

    // --- INITIALISATION ---
    loadStats();
    loadUsers();

    // --- RECHERCHE ET FILTRES (auto-filtering) ---
    function debounce(fn, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    const autoFilter = debounce(() => loadUsers(1), 300);

    document.getElementById('apply-filters').addEventListener('click', () => loadUsers(1));
    document.getElementById('search-input').addEventListener('input', autoFilter);
    document.getElementById('filter-role').addEventListener('change', () => loadUsers(1));
    document.getElementById('filter-status').addEventListener('change', () => loadUsers(1));
    document.getElementById('search-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); loadUsers(1); }
    });

    // --- CHARGEMENT DES STATISTIQUES ---
    async function loadStats() {
        try {
            const response = await fetch(`${API_BASE}/admin/users/statistics`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const data = await response.json();
            if (!data.success) throw new Error('Erreur API');

            document.getElementById('kpi-total-users').textContent = data.total || 0;
            document.getElementById('kpi-porteurs').textContent = data.porteurs || 0;
            document.getElementById('kpi-institutions').textContent = data.institutions || 0;
            document.getElementById('kpi-suspended').textContent = data.suspended || 0;

            initCharts(data);
        } catch (error) {
            console.error('Erreur stats utilisateurs:', error);
            // Fallback: afficher des données par défaut
            document.getElementById('kpi-total-users').textContent = '—';
            document.getElementById('kpi-porteurs').textContent = '—';
            document.getElementById('kpi-institutions').textContent = '—';
            document.getElementById('kpi-suspended').textContent = '—';
        }
    }

    function initCharts(data) {
        // Registrations Chart
        const regData = data.registrations_monthly || [0,0,0,0,0,0,0,0,0,0,0,0];
        const registrationsOptions = {
            series: [{
                name: 'Inscriptions',
                data: regData
            }],
            chart: { height: 350, type: 'bar', toolbar: { show: false } },
            xaxis: { categories: ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec'] },
            colors: ['#6259ca'],
            plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } }
        };
        new ApexCharts(document.querySelector("#registrations-chart"), registrationsOptions).render();

        // Roles Chart
        const rolesData = data.roles_distribution || { porteurs: 0, institutions: 0, admins: 0 };
        const rolesOptions = {
            series: [rolesData.porteurs || 0, rolesData.institutions || 0, rolesData.admins || 0],
            chart: { type: 'donut', height: 350 },
            labels: ['Porteurs', 'Institutions', 'Admins'],
            colors: ['#6259ca', '#ffc107', '#17a2b8'],
            legend: { position: 'bottom' }
        };
        new ApexCharts(document.querySelector("#roles-chart"), rolesOptions).render();
    }

    // --- CHARGEMENT DES UTILISATEURS ---
    async function loadUsers(page = 1) {
        const tbody = document.getElementById('users-tbody');
        // Skeleton loader animation for a more premium feel
        tbody.innerHTML = Array(5).fill(0).map(() => `
            <tr>
                <td><div class="loading-skeleton" style="height: 40px; width: 100%;"></div></td>
                <td><div class="loading-skeleton" style="height: 20px; width: 120px;"></div></td>
                <td><div class="loading-skeleton" style="height: 20px; width: 80px;"></div></td>
                <td><div class="loading-skeleton" style="height: 20px; width: 80px;"></div></td>
                <td><div class="loading-skeleton" style="height: 20px; width: 100px;"></div></td>
                <td><div class="loading-skeleton" style="height: 20px; width: 100px;"></div></td>
                <td><div class="loading-skeleton" style="height: 20px; width: 80px;"></div></td>
            </tr>
        `).join('');

        const params = new URLSearchParams({
            page: page,
            search: document.getElementById('search-input').value,
            role: document.getElementById('filter-role').value,
            status: document.getElementById('filter-status').value
        });

        try {
            const response = await fetch(`${API_BASE}/admin/users?${params}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const data = await response.json();
            if (!data.success) throw new Error('Erreur API');

            document.getElementById('users-count').textContent = `${data.total} utilisateurs`;
            renderTable(data.data);
            renderPagination(data);
        } catch (error) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Erreur de chargement des utilisateurs.</td></tr>';
        }
    }

    function renderTable(users) {
        const tbody = document.getElementById('users-tbody');
        tbody.innerHTML = '';

        if (!users || users.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5">Aucun utilisateur trouvé.</td></tr>';
            return;
        }

        users.forEach(u => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-md brround me-3" style="background-image: url('${resolveAvatarUrl(u.avatar)}')"></span>
                        <div>
                            <h6 class="mb-0 fw-semibold">${u.name || ''}</h6>
                            <span class="fs-12 text-muted">ID: ${u.id}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="fw-semibold">${u.email || ''}</div>
                    <div class="fs-12 text-muted">${u.phone || '—'}</div>
                </td>
                <td><span class="badge ${getRoleBadge(u.role)}">${formatRole(u.role)}</span></td>
                <td><span class="badge ${getStatusBadge(u.status)}">${formatStatus(u.status)}</span></td>
                <td>
                    <div class="fs-12">${formatDate(u.created_at)}</div>
                </td>
                <td>
                    <div class="fs-12 text-muted">${u.last_login ? formatDate(u.last_login) : 'Jamais'}</div>
                </td>
                <td>
                    <div class="btn-list">
                        <button class="btn btn-sm btn-primary btn-view-user" data-id="${u.id}" title="Voir"><i class="fe fe-eye"></i></button>
                        <button class="btn btn-sm btn-warning btn-suspend-user" data-id="${u.id}" title="Suspendre"><i class="fe fe-user-x"></i></button>
                        <button class="btn btn-sm btn-danger btn-delete-user" data-id="${u.id}" title="Supprimer"><i class="fe fe-trash-2"></i></button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Event listeners
        document.querySelectorAll('.btn-view-user').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                openUserModal(btn.dataset.id);
            });
        });
        document.querySelectorAll('.btn-suspend-user').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                quickAction(btn.dataset.id, 'suspend');
            });
        });
        document.querySelectorAll('.btn-delete-user').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                quickAction(btn.dataset.id, 'delete');
            });
        });
    }

    // --- MODALE UTILISATEUR ---
    async function openUserModal(userId) {
        try {
            const response = await fetch(`${API_BASE}/admin/users/${userId}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const u = await response.json();
            if (!u.success) throw new Error('Erreur API');
            currentUser = u;

            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-user-admin'));

            document.getElementById('user-modal-title').textContent = `Profil: ${u.name}`;
            document.getElementById('user-modal-content').innerHTML = `
                <div class="text-center mb-4">
                    <span class="avatar avatar-xxl brround" style="background-image: url('${resolveAvatarUrl(u.avatar)}')"></span>
                    <h4 class="mt-3 mb-0">${u.name}</h4>
                    <span class="badge ${getRoleBadge(u.role)} mt-1">${formatRole(u.role)}</span>
                    <span class="badge ${getStatusBadge(u.status)} mt-1 ms-1">${formatStatus(u.status)}</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <p><strong>Email:</strong> ${u.email}</p>
                        <p><strong>Téléphone:</strong> ${u.phone || '—'}</p>
                        <p><strong>Ville:</strong> ${u.city || '—'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Inscrit le:</strong> ${formatDate(u.created_at)}</p>
                        <p><strong>Dernière connexion:</strong> ${u.last_login ? formatDate(u.last_login) : 'Jamais'}</p>
                        <p><strong>KYC Vérifié:</strong> <span class="badge ${u.kyc_verified ? 'bg-success' : 'bg-warning'}">${u.kyc_verified ? 'Oui' : 'Non'}</span></p>
                    </div>
                </div>
            `;

            // Projets associés
            const projectsList = document.getElementById('user-projects-list');
            if (u.projects && u.projects.length > 0) {
                projectsList.innerHTML = '<div class="list-group list-group-flush">';
                u.projects.forEach(p => {
                    projectsList.innerHTML += `
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">${p.titre}</h6>
                                <small class="text-muted">${formatMoney(p.montant_demande)} FCFA</small>
                            </div>
                            <span class="badge ${getProjectStatusBadge(p.statut)}">${p.statut}</span>
                        </div>
                    `;
                });
                projectsList.innerHTML += '</div>';
            } else {
                projectsList.innerHTML = '<p class="text-muted text-center">Aucun projet associé.</p>';
            }

            modal.show();
        } catch (error) {
            console.error('Erreur détails utilisateur:', error);
            ALOGOTO.error('Impossible de charger les détails de l\'utilisateur.');
        }
    }

    // --- ACTIONS ---
    document.getElementById('btn-activate-user').addEventListener('click', () => performAction('activate'));
    document.getElementById('btn-suspend-user').addEventListener('click', () => performAction('suspend'));
    document.getElementById('btn-delete-user').addEventListener('click', () => performAction('delete'));
    document.getElementById('btn-reset-password').addEventListener('click', () => performAction('reset-password'));

    async function performAction(action) {
        if (!currentUser) return;

        const modalEl = document.getElementById('modal-user-admin');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        const comment = document.getElementById('user-admin-comment').value;
        if (action !== 'reset-password' && !comment) {
            ALOGOTO.warning('Veuillez ajouter un motif pour justifier votre action.');
            return;
        }

        const confirmTexts = {
            'activate': ['Activer le compte', 'Voulez-vous activer ce compte utilisateur ?', 'Oui, activer'],
            'suspend': ['Suspendre le compte', 'Voulez-vous suspendre ce compte utilisateur ?', 'Oui, suspendre'],
            'delete': ['Supprimer le compte', 'Voulez-vous SUPPRIMER définitivement ce compte ?', 'Oui, supprimer'],
            'reset-password': ['Réinitialiser le mot de passe', 'Voulez-vous réinitialiser le mot de passe de cet utilisateur ?', 'Oui, réinitialiser']
        };
        const [title, text, confirmBtn] = confirmTexts[action] || [`Action: ${action}`, 'Confirmer cette action ?', 'Confirmer'];
        const confirmed = await ALOGOTO.confirm(title, text, confirmBtn);
        if (!confirmed.isConfirmed) return;

        try {
            const response = await fetch(`${API_BASE}/admin/users/${currentUser.id}/${action}`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({ motif: comment })
            });
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const result = await response.json();
            if (!result.success) throw new Error('Erreur API');

            if (result.success) {
                ALOGOTO.success(result.message || 'Action effectuée avec succès.');
                loadUsers();
                loadStats();
            } else {
                ALOGOTO.error(result.message || 'Action échouée.');
            }
        } catch (error) {
            ALOGOTO.error('Une erreur est survenue lors de l\'action.');
        }
    }

    async function quickAction(userId, action) {
        const confirmTexts = {
            'suspend': ['Suspendre le compte', 'Voulez-vous suspendre cet utilisateur ?', 'Oui, suspendre'],
            'delete': ['Supprimer le compte', 'Voulez-vous SUPPRIMER définitivement cet utilisateur ?', 'Oui, supprimer']
        };
        const [title, text, confirmBtn] = confirmTexts[action] || ['Confirmer', 'Confirmer cette action ?', 'Confirmer'];
        const confirmed = await ALOGOTO.confirm(title, text, confirmBtn);
        if (!confirmed.isConfirmed) return;

        try {
            const response = await fetch(`${API_BASE}/admin/users/${userId}/${action}`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({ motif: 'Action rapide admin' })
            });
            if (!response.ok) throw new Error('HTTP ' + response.status);
            const result = await response.json();
            if (!result.success) throw new Error('Erreur API');
            if (result.success) {
                ALOGOTO.success(result.message || 'Action effectuée.');
                loadUsers();
                loadStats();
            } else {
                ALOGOTO.error(result.message || 'Échec.');
            }
        } catch (error) {
            ALOGOTO.error('Erreur réseau.');
        }
    }

    // --- HELPERS ---
    function formatMoney(amount) {
        return new Intl.NumberFormat('fr-FR').format(amount || 0);
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        const d = new Date(dateString);
        return d.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    function formatRole(role) {
        const map = { 'porteur': 'Porteur', 'institution': 'Institution', 'admin': 'Administrateur' };
        return map[role] || role || '';
    }

    function formatStatus(status) {
        const map = { 'active': 'Actif', 'suspended': 'Suspendu', 'pending': 'En attente' };
        return map[status] || status || '';
    }

    function getRoleBadge(role) {
        const map = {
            'porteur': 'bg-primary-transparent text-primary',
            'institution': 'bg-warning-transparent text-warning',
            'admin': 'bg-danger-transparent text-danger'
        };
        return map[role] || 'bg-light text-dark';
    }

    function getStatusBadge(status) {
        const map = {
            'active': 'bg-success-transparent text-success',
            'suspended': 'bg-danger-transparent text-danger',
            'pending': 'bg-warning-transparent text-warning'
        };
        return map[status] || 'bg-light text-dark';
    }

    function getProjectStatusBadge(status) {
        const map = {
            'draft': 'bg-secondary',
            'submitted': 'bg-info',
            'admin_validated': 'bg-success',
            'active': 'bg-primary',
            'suspended': 'bg-danger'
        };
        return map[status] || 'bg-light text-dark';
    }

    function renderPagination(data) {
        const container = document.getElementById('pagination-container');
        container.innerHTML = '';

        if (!data.last_page || data.last_page <= 1) return;

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
                    loadUsers(page);
                };
            }

            li.appendChild(a);
            nav.appendChild(li);
        });

        container.appendChild(nav);
    }
});
