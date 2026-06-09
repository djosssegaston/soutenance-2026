/**
 * Système d'authentification pour ALOGOTO
 * Gère les tokens Sanctum et l'isolation des sessions
 */

class AuthManager {
    constructor() {
        this.tokenKey = 'alogoto_api_token';
        this.roleKey = 'alogoto_user_role';
        this.userIdKey = 'alogoto_user_id';
    }

    /**
     * Stocke les informations d'authentification
     */
    setAuth(token, role, userId) {
        if (!token) {
            console.error('[AuthManager] Token manquant');
            return false;
        }

        localStorage.setItem(this.tokenKey, token);
        localStorage.setItem(this.roleKey, role);
        localStorage.setItem(this.userIdKey, userId);

        console.log(`[AuthManager] Authentification réussie - Rôle: ${role}`);
        return true;
    }

    getToken() {
        return localStorage.getItem(this.tokenKey);
    }

    getRole() {
        return localStorage.getItem(this.roleKey);
    }

    getUserId() {
        return localStorage.getItem(this.userIdKey);
    }

    isAuthenticated() {
        return !!this.getToken();
    }

    hasRole(requiredRole) {
        const currentRole = this.getRole();
        return currentRole === requiredRole;
    }

    /**
     * Déconnexion propre
     */
    logout() {
        localStorage.removeItem(this.tokenKey);
        localStorage.removeItem(this.roleKey);
        localStorage.removeItem(this.userIdKey);
        
        console.log('[AuthManager] Déconnexion réussie');
        window.location.href = '/login';
    }

    /**
     * Vérifie l'authentification et le rôle
     */
    requireAuth(requiredRole = null) {
        if (!this.isAuthenticated()) {
            console.warn('[AuthManager] Non authentifié, redirection login');
            window.location.href = '/login';
            return false;
        }

        if (requiredRole && !this.hasRole(requiredRole)) {
            console.error(`[AuthManager] Rôle requis: ${requiredRole}, rôle actuel: ${this.getRole()}`);
            this.redirectToDashboard();
            return false;
        }

        return true;
    }

    /**
     * Redirige vers le dashboard du rôle
     */
    redirectToDashboard() {
        const role = this.getRole();
        const routes = {
            'admin': '/dashboard/admin',
            'porteur': '/dashboard/porteur',
            'institution': '/dashboard/institution'
        };

        const route = routes[role] || '/login';
        window.location.href = route;
    }

    /**
     * Headers d'authentification pour les requêtes API
     */
    getAuthHeaders() {
        const token = this.getToken();
        
        if (!token) {
            console.error('[AuthManager] Aucun token disponible');
            return {};
        }

        return {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        };
    }
}

// Instance globale
const authManager = new AuthManager();
