/**
 * Gestion de la sidebar responsive pour ALOGOTO
 */

class SidebarManager {
    constructor() {
        this.sidebar = document.getElementById('dashboardSidebar');
        this.overlay = document.querySelector('.sidebar-overlay');
        this.toggleBtn = document.querySelector('.dashboard-menu-toggle');
        this.closeBtn = document.querySelector('.dashboard-sidebar-close');
        
        this.init();
    }

    init() {
        if (!this.sidebar) return;

        // Bouton toggle (mobile)
        if (this.toggleBtn) {
            this.toggleBtn.addEventListener('click', () => this.open());
        }

        // Bouton fermer
        if (this.closeBtn) {
            this.closeBtn.addEventListener('click', () => this.close());
        }

        // Overlay clic
        if (this.overlay) {
            this.overlay.addEventListener('click', () => this.close());
        }

        // Fermer après clic sur un lien (mobile)
        const navLinks = this.sidebar.querySelectorAll('.dashboard-nav__link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    this.close();
                }
            });
        });

        // Fermer si redimensionnement vers desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                this.close();
            }
        });

        console.log('[SidebarManager] Initialisé');
    }

    open() {
        if (this.sidebar) this.sidebar.classList.add('active');
        if (this.overlay) this.overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    close() {
        if (this.sidebar) this.sidebar.classList.remove('active');
        if (this.overlay) this.overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    setActiveLink(pageName) {
        const navLinks = this.sidebar.querySelectorAll('.dashboard-nav__link');
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.dataset.page === pageName) {
                link.classList.add('active');
            }
        });
    }
}

// Initialisation automatique
document.addEventListener('DOMContentLoaded', () => {
    window.sidebarManager = new SidebarManager();
});
