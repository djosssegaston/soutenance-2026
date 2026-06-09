/**
 * Système global de communication API pour ALOGOTO
 * Gère tous les appels AJAX vers le backend Laravel
 */

// Récupération du token Sanctum stocké dans localStorage
const API_TOKEN = localStorage.getItem('alogoto_api_token');

// URL de base de l'API Laravel - Détectée automatiquement selon l'environnement
const API_BASE_URL = window.location.origin + '/api/v1';

/**
 * Fonction principale pour les appels API
 * @param {string} endpoint - Le endpoint API (ex: 'dashboard/admin/stats')
 * @param {object} options - Options supplémentaires (method, body, etc.)
 * @returns {Promise} - La réponse JSON de l'API
 */
async function apiFetch(endpoint, options = {}) {
    // Construction de l'URL complète
    const url = `${API_BASE_URL}/${endpoint.replace(/^\//, '')}`;
    
    // Configuration par défaut de la requête
    const config = {
        method: options.method || 'GET',
        headers: {
            'Authorization': `Bearer ${API_TOKEN}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        ...options,
    };

    // Si body fourni et method GET/HEAD, on le retire (interdit par HTTP)
    if ((config.method === 'GET' || config.method === 'HEAD') && config.body) {
        delete config.body;
    }

    try {
        // Exécution de la requête
        const response = await fetch(url, config);

        // Gestion des erreurs HTTP
        if (!response.ok) {
            // Token expiré ou invalide
            if (response.status === 401) {
                console.error('Token expiré ou invalide. Redirection vers login...');
                localStorage.removeItem('alogoto_api_token');
                window.location.href = '/login';
                throw new Error('Session expirée');
            }
            
            // Accès non autorisé
            if (response.status === 403) {
                throw new Error('Accès non autorisé à cette ressource');
            }

            // Erreur serveur
            if (response.status >= 500) {
                throw new Error('Erreur serveur. Veuillez réessayer plus tard.');
            }

            // Autre erreur
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `Erreur HTTP: ${response.status}`);
        }

        // Retourne la réponse JSON
        return await response.json();
    } catch (error) {
        // Gestion des erreurs réseau
        console.error('Erreur API:', error);
        
        // Affiche une alerte utilisateur si le DOM est prêt
        if (document.readyState !== 'loading') {
            showApiError(error.message);
        }
        
        throw error;
    }
}

/**
 * Affiche une notification d'erreur API
 * @param {string} message - Le message d'erreur à afficher
 */
function showApiError(message) {
    // Vérifie si un conteneur d'alerte existe déjà
    let alertContainer = document.getElementById('api-alert-container');
    
    if (!alertContainer) {
        // Crée le conteneur
        alertContainer = document.createElement('div');
        alertContainer.id = 'api-alert-container';
        alertContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        `;
        document.body.appendChild(alertContainer);
    }

    // Crée l'alerte
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger alert-dismissible fade show';
    alert.style.cssText = `
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease-out;
    `;
    alert.innerHTML = `
        <strong><i class="bi bi-exclamation-triangle"></i> Erreur:</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Ajoute au conteneur
    alertContainer.appendChild(alert);

    // Supprime automatiquement après 5 secondes
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

/**
 * Affiche une notification de succès
 * @param {string} message - Le message de succès à afficher
 */
function showApiSuccess(message) {
    let alertContainer = document.getElementById('api-alert-container');
    
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.id = 'api-alert-container';
        alertContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        `;
        document.body.appendChild(alertContainer);
    }

    const alert = document.createElement('div');
    alert.className = 'alert alert-success alert-dismissible fade show';
    alert.style.cssText = `
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease-out;
    `;
    alert.innerHTML = `
        <strong><i class="bi bi-check-circle"></i> Succès:</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    alertContainer.appendChild(alert);

    setTimeout(() => {
        alert.remove();
    }, 3000);
}

/**
 * Formate un montant en devise FCFA
 * @param {number} amount - Le montant à formater
 * @returns {string} - Le montant formaté (ex: "2.4Mrd FCFA")
 */
function formatMoney(amount) {
    if (!amount && amount !== 0) return '0 FCFA';
    
    // Conversion en nombre si string
    amount = parseFloat(amount);
    
    if (amount >= 1000000000) {
        // Milliards
        return (amount / 1000000000).toFixed(1).replace('.0', '') + 'Mrd FCFA';
    } else if (amount >= 1000000) {
        // Millions
        return (amount / 1000000).toFixed(1).replace('.0', '') + 'M FCFA';
    } else if (amount >= 1000) {
        // Milliers
        return (amount / 1000).toFixed(1).replace('.0', '') + 'K FCFA';
    } else {
        // Unités
        return amount.toFixed(0) + ' FCFA';
    }
}

/**
 * Formate un nombre avec séparateur de milliers
 * @param {number} num - Le nombre à formater
 * @returns {string} - Le nombre formaté (ex: "1 234")
 */
function formatNumber(num) {
    if (!num && num !== 0) return '0';
    return parseFloat(num).toLocaleString('fr-FR');
}

/**
 * Calcule une tendance entre deux valeurs
 * @param {number} current - Valeur actuelle
 * @param {number} previous - Valeur précédente
 * @returns {object} - { label, class, icon }
 */
function calculateTrend(current, previous) {
    if (previous === 0) {
        return {
            label: current > 0 ? '+100%' : '0%',
            class: current > 0 ? 'is-up' : 'is-neutral',
            icon: current > 0 ? 'bi-arrow-up-right' : 'bi-dash'
        };
    }
    
    const trend = ((current - previous) / previous) * 100;
    const trendRound = Math.abs(trend).toFixed(1);
    
    if (trend > 0) {
        return {
            label: `+${trendRound}%`,
            class: 'is-up',
            icon: 'bi-arrow-up-right'
        };
    } else if (trend < 0) {
        return {
            label: `-${trendRound}%`,
            class: 'is-down',
            icon: 'bi-arrow-down-right'
        };
    } else {
        return {
            label: '0%',
            class: 'is-neutral',
            icon: 'bi-dash'
        };
    }
}

// Ajout de l'animation CSS pour les alertes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;
document.head.appendChild(style);
