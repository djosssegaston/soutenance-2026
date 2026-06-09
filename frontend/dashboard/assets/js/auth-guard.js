/**
 * Système de garde d'authentification pour ALOGOTO
 * Vérifie les tokens et les rôles à chaque chargement de page
 */

class AuthGuard {
    constructor() {
        this.tokenKey = 'alogoto_api_token';
        this.roleKey = 'alogoto_user_role';
        this.userIdKey = 'alogoto_user_id';
    }

    /**
     * Vérifie si l'utilisateur est authentifié
     */
    isAuthenticated() {
        return !!localStorage.getItem(this.tokenKey);
    }

    /**
     * Récupère le token
     */
    getToken() {
        return localStorage.getItem(this.tokenKey);
    }

    /**
     * Récupère le rôle
     */
    getRole() {
        return localStorage.getItem(this.roleKey);
    }

    /**
     * Récupère l'ID utilisateur
     */
    getUserId() {
        return localStorage.getItem(this.userIdKey);
    }

    /**
     * Vérifie le rôle requis
     */
    hasRole(requiredRole) {
        return this.getRole() === requiredRole;
    }

    /**
     * Protège une page - Redirige si non authentifié ou rôle incorrect
     * @param {string} requiredRole - Le rôle requis ('admin', 'porteur', 'institution')
     */
    requireAuth(requiredRole) {
        // Vérifier si authentifié
        if (!this.isAuthenticated()) {
            console.warn('[AuthGuard] Non authentifié, redirection vers login');
            window.location.href = '/login';
            return false;
        }

        // Vérifier le rôle
        if (requiredRole && !this.hasRole(requiredRole)) {
            console.error(`[AuthGuard] Accès refusé. Rôle requis: ${requiredRole}, rôle actuel: ${this.getRole()}`);
            this.redirectToProperDashboard();
            return false;
        }

        // Authentiﬁcation et rôle valides
        console.log(`[AuthGuard] Accès autorisé - Rôle: ${this.getRole()}`);
        return true;
    }

    /**
     * Redirige vers le dashboard correspondant au rôle
     */
    redirectToProperDashboard() {
        const role = this.getRole();
        const routes = {
            'admin': '/dashboard/admin',
            'porteur': '/dashboard/porteur',
            'institution': '/dashboard/institution'
        };

        const route = routes[role];
        if (route) {
            console.log(`[AuthGuard] Redirection vers ${route}`);
            window.location.href = route;
        } else {
            // Rôle inconnu, déconnexion
            this.logout();
        }
    }

    /**
     * Stocke les informations d'authentification après login
     */
    storeAuth(token, role, userId) {
        if (!token || !role) {
            console.error('[AuthGuard] Token ou rôle manquant');
            return false;
        }

        localStorage.setItem(this.tokenKey, token);
        localStorage.setItem(this.roleKey, role);
        localStorage.setItem(this.userIdKey, userId || '');

        console.log(`[AuthGuard] Authentification stockée - Rôle: ${role}`);
        return true;
    }

    /**
     * Déconnexion propre
     */
    async logout() {
        try {
            // Appel API pour invalider le token côté serveur
            const token = this.getToken();
            if (token) {
                await fetch('/api/v1/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                    }
                });
            }
        } catch (error) {
            console.error('[AuthGuard] Erreur lors de la déconnexion:', error);
        } finally {
            // Nettoyage local
            localStorage.removeItem(this.tokenKey);
            localStorage.removeItem(this.roleKey);
            localStorage.removeItem(this.userIdKey);
            
            console.log('[AuthGuard] Déconnexion réussie');
            window.location.href = '/login';
        }
    }

    /**
     * Retourne les headers pour les requêtes API
     */
    getAuthHeaders() {
        const token = this.getToken();
        
        if (!token) {
            console.error('[AuthGuard] Aucun token disponible');
            return {};
        }

        return {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        };
    }

    /**
     * Vérifie la validité du token côté serveur
     */
    async validateToken() {
        try {
            const response = await fetch('/api/v1/auth/me', {
                headers: this.getAuthHeaders()
            });

            if (!response.ok) {
                if (response.status === 401) {
                    console.warn('[AuthGuard] Token expiré ou invalide');
                    this.logout();
                    return false;
                }
            }

            const user = await response.json();
            
            // Vérifier que le rôle correspond
            if (user.role && user.role !== this.getRole()) {
                console.warn('[AuthGuard] Rôle mismatch, mise à jour');
                this.storeAuth(this.getToken(), user.role, user.id);
            }

            return true;
        } catch (error) {
            console.error('[AuthGuard] Erreur validation token:', error);
            return false;
        }
    }
}

// Instance globale
const authGuard = new AuthGuard();

// Auto-vérification au chargement
document.addEventListener('DOMContentLoaded', () => {
    // Vérifier si on est sur une page protégée
    const protectedPages = ['/dashboard/admin', '/dashboard/porteur', '/dashboard/institution'];
    const currentPath = window.location.pathname;
    
    const isProtectedPage = protectedPages.some(page => currentPath.startsWith(page));
    
    if (isProtectedPage) {
        // Déterminer le rôle requis
        let requiredRole = null;
        if (currentPath.includes('/admin')) requiredRole = 'admin';
        else if (currentPath.includes('/porteur')) requiredRole = 'porteur';
        else if (currentPath.includes('/institution')) requiredRole = 'institution';
        
        // Vérifier l'accès
        authGuard.requireAuth(requiredRole);
        
        // Valider le token côté serveur
        authGuard.validateToken();
    }
});
