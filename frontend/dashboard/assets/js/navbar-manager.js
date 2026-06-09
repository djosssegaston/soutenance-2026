/**
 * Gestionnaire de la navbar pour ALOGOTO
 * Gère les notifications, l'avatar, les dropdowns et la recherche
 */

class NavbarManager {
    constructor() {
        this.notificationDot = document.querySelector('.notification-dot');
        this.notificationDropdown = document.getElementById('notificationDropdown');
        this.userDropdown = document.getElementById('userDropdown');
        this.avatarElement = document.querySelector('.dashboard-avatar');
        
        this.init();
    }

    init() {
        // Gestion des dropdowns
        this.setupDropdowns();
        
        // Chargement des notifications
        this.loadNotifications();
        
        // Configuration de l'avatar
        this.setupAvatar();
        
        // Configuration de la recherche
        this.setupSearch();
        
        // Configuration du logout
        this.setupLogout();
        
        console.log('[NavbarManager] Initialisé');
    }

    /**
     * Gestion des dropdowns (notifications et utilisateur)
     */
    setupDropdowns() {
        const dropdownButtons = document.querySelectorAll('[data-dropdown-target]');
        
        dropdownButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                const targetId = button.dataset.dropdownTarget;
                const target = document.getElementById(targetId);
                
                if (!target) return;
                
                const isVisible = target.classList.contains('show');
                
                // Fermer tous les dropdowns
                this.closeAllDropdowns();
                
                // Ouvrir le cible si était fermé
                if (!isVisible) {
                    target.classList.add('show');
                }
            });
        });

        // Fermer en cliquant ailleurs
        document.addEventListener('click', () => {
            this.closeAllDropdowns();
        });

        // Empêcher la fermeture quand on clique dans le dropdown
        [this.notificationDropdown, this.userDropdown].forEach(dropdown => {
            if (dropdown) {
                dropdown.addEventListener('click', (e) => e.stopPropagation());
            }
        });
    }

    closeAllDropdowns() {
        document.querySelectorAll('.dashboard-dropdown-panel').forEach(panel => {
            panel.classList.remove('show');
        });
    }

    /**
     * Charge les notifications depuis l'API
     */
    async loadNotifications() {
        try {
            // Charger le compteur
            const counts = await apiFetch('notifications/unread-count');
            
            if (this.notificationDot && counts.unread_count > 0) {
                this.notificationDot.style.display = 'block';
            } else if (this.notificationDot) {
                this.notificationDot.style.display = 'none';
            }

            // Charger la liste des notifications
            const notifications = await apiFetch('notifications');
            
            if (this.notificationDropdown && notifications.length > 0) {
                this.renderNotifications(notifications.slice(0, 5)); // 5 plus récentes
            }
        } catch (error) {
            console.error('[NavbarManager] Erreur chargement notifications:', error);
        }
    }

    /**
     * Affiche les notifications dans le dropdown
     */
    renderNotifications(notifications) {
        if (!this.notificationDropdown) return;

        const html = notifications.map(notification => {
            const isUnread = !notification.lu;
            const time = this.timeAgo(notification.created_at);
            
            return `
                <div class="dashboard-notification-item" data-id="${notification.id}" style="cursor: pointer; ${isUnread ? 'background: rgba(0, 196, 134, 0.05);' : ''}">
                    ${isUnread ? '<span class="dashboard-notification-unread"></span>' : ''}
                    <div class="dashboard-notification-content">
                        <strong>${this.escapeHtml(notification.titre || 'Notification')}</strong>
                        <span>${this.escapeHtml(notification.message || '')}</span>
                        <span>${time}</span>
                    </div>
                </div>
            `;
        }).join('');

        this.notificationDropdown.innerHTML = `
            <p class="dashboard-dropdown-title">Notifications</p>
            ${html}
            <div style="text-align: center; padding: 12px; border-top: 1px solid #E9E9E8;">
                <a href="/dashboard/notifications" style="color: var(--primary-color-1); text-decoration: none; font-weight: 500;">
                    Voir toutes les notifications
                </a>
            </div>
        `;

        // Ajouter les gestionnaires de clic
        this.notificationDropdown.querySelectorAll('.dashboard-notification-item').forEach(item => {
            item.addEventListener('click', async () => {
                const notificationId = item.dataset.id;
                await this.markAsRead(notificationId);
                item.style.background = '';
                const unreadBadge = item.querySelector('.dashboard-notification-unread');
                if (unreadBadge) unreadBadge.remove();
                
                // Mettre à jour le compteur
                this.loadNotifications();
            });
        });
    }

    /**
     * Marque une notification comme lue
     */
    async markAsRead(notificationId) {
        try {
            await apiFetch(`notifications/${notificationId}/read`, {
                method: 'POST'
            });
        } catch (error) {
            console.error('[NavbarManager] Erreur marquage notification:', error);
        }
    }

    /**
     * Configure l'avatar avec l'image utilisateur
     */
    setupAvatar() {
        // L'avatar utilise déjà les initiales depuis le PHP
        // On peut ajouter une image si disponible
        const user = window.dashboardUser || {};
        
        if (user.avatar_url && this.avatarElement) {
            this.avatarElement.innerHTML = `<img src="${user.avatar_url}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
        }
    }

    /**
     * Configure la recherche
     */
    setupSearch() {
        const searchForm = document.getElementById('headerSearchForm');
        const searchInput = searchForm?.querySelector('input[type="search"]');
        
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const query = e.target.value;
                
                if (query.length < 2) {
                    this.hideSearchSuggestions();
                    return;
                }

                this.performSearch(query);
            });
        }
    }

    async performSearch(query) {
        try {
            // Adapter selon votre endpoint de recherche
            const results = await apiFetch(`search?q=${encodeURIComponent(query)}`);
            this.showSearchSuggestions(results);
        } catch (error) {
            console.error('[NavbarManager] Erreur recherche:', error);
        }
    }

    showSearchSuggestions(results) {
        // Implémenter selon vos besoins
    }

    hideSearchSuggestions() {
        // Implémenter selon vos besoins
    }

    /**
     * Configure le bouton de déconnexion
     */
    setupLogout() {
        const logoutButtons = document.querySelectorAll('.js-dashboard-logout');
        
        logoutButtons.forEach(button => {
            button.addEventListener('click', async (e) => {
                e.preventDefault();
                
                if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                    await this.logout();
                }
            });
        });
    }

    /**
     * Déconnexion
     */
    async logout() {
        try {
            await apiFetch('auth/logout', { method: 'POST' });
        } catch (error) {
            console.error('[NavbarManager] Erreur déconnexion:', error);
        } finally {
            // Nettoyage local
            localStorage.removeItem('alogoto_api_token');
            localStorage.removeItem('alogoto_user_role');
            localStorage.removeItem('alogoto_user_id');
            
            window.location.href = '/login';
        }
    }

    /**
     * Calcule le temps écoulé
     */
    timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        if (seconds < 60) return 'À l\'instant';
        if (seconds < 3600) return `Il y a ${Math.floor(seconds / 60)} min`;
        if (seconds < 86400) return `Il y a ${Math.floor(seconds / 3600)} h`;
        if (seconds < 604800) return `Il y a ${Math.floor(seconds / 86400)} j`;
        
        return date.toLocaleDateString('fr-FR');
    }

    /**
     * Échappe le HTML
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }
}

// Initialisation automatique
document.addEventListener('DOMContentLoaded', () => {
    window.navbarManager = new NavbarManager();
});
