/**
 * ALOGOTO API Helper
 * Standardized interface for all AJAX/API calls
 * 
 * Usage:
 *   const data = await api.get('/projects');
 *   const result = await api.post('/projects', { titre: 'Mon projet' });
 */

const ALOGOTO_API = (function() {
    'use strict';

    // Configuration
    const CONFIG = {
        BASE_URL: window.API_BASE_URL || '/api/v1',
        CSRF_TOKEN: document.querySelector('meta[name="csrf-token"]')?.content || '',
        SESSION_TIMEOUT: 30 * 60 * 1000, // 30 minutes
        RETRY_ATTEMPTS: 3,
        RETRY_DELAY: 1000 // 1 second
    };

    // State
    let lastActivity = Date.now();
    let requestQueue = [];
    let isRefreshing = false;

    /**
     * Get authentication token (from meta tag or session)
     */
    function getAuthToken() {
        return document.querySelector('meta[name="auth-token"]')?.content || '';
    }

    /**
     * Get CSRF token
     */
    function getCsrfToken() {
        return CONFIG.CSRF_TOKEN || 
               document.querySelector('meta[name="csrf-token"]')?.content || 
               '';
    }

    /**
     * Build request headers
     */
    function buildHeaders(isFormData = false) {
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        };

        const csrfToken = getCsrfToken();
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }

        const authToken = getAuthToken();
        if (authToken) {
            headers['Authorization'] = `Bearer ${authToken}`;
        }

        if (!isFormData) {
            headers['Content-Type'] = 'application/json';
        }

        return headers;
    }

    /**
     * Handle API errors
     */
    function handleError(error, endpoint, attempt = 1) {
        console.error(`[API Error] ${endpoint}:`, error);

        // Retry on network errors
        if (attempt < CONFIG.RETRY_ATTEMPTS && (error.name === 'TypeError' || error.status >= 500)) {
            console.log(`[API] Retrying ${endpoint} (attempt ${attempt + 1}/${CONFIG.RETRY_ATTEMPTS})`);
            return new Promise(resolve => {
                setTimeout(() => resolve(null), CONFIG.RETRY_DELAY * attempt);
            });
        }

        // Handle specific status codes
        if (error.status === 401) {
            // Session expired, redirect to login
            window.location.href = '/login?session_expired=1';
            return;
        }

        if (error.status === 403) {
            alert('Accès non autorisé. Vérifiez vos permissions.');
            return;
        }

        if (error.status === 422) {
            // Validation errors
            return error.data?.errors || {};
        }

        throw error;
    }

    /**
     * Make API request
     */
    async function request(method, endpoint, data = null, isFormData = false) {
        const url = `${CONFIG.BASE_URL}${endpoint}`;
        const headers = buildHeaders(isFormData);

        const options = {
            method: method.toUpperCase(),
            headers: headers,
            credentials: 'same-origin'
        };

        if (data && ['POST', 'PUT', 'PATCH'].includes(method.toUpperCase())) {
            if (isFormData) {
                options.body = data;
            } else {
                options.body = JSON.stringify(data);
            }
        }

        try {
            const response = await fetch(url, options);
            
            // Update activity timestamp
            lastActivity = Date.now();

            const contentType = response.headers.get('content-type');
            const isJson = contentType && contentType.includes('application/json');
            
            const responseData = isJson ? await response.json() : await response.text();

            if (!response.ok) {
                const error = new Error(responseData.message || `HTTP ${response.status}`);
                error.status = response.status;
                error.data = responseData;
                throw error;
            }

            return responseData;
        } catch (error) {
            return handleError(error, endpoint);
        }
    }

    /**
     * Check session activity
     */
    function checkSessionActivity() {
        const now = Date.now();
        if (now - lastActivity > CONFIG.SESSION_TIMEOUT) {
            console.warn('[API] Session may have expired due to inactivity');
            // Don't auto-redirect, let next request handle it
        }
    }

    /**
     * Public API methods
     */
    return {
        /**
         * GET request
         */
        async get(endpoint, params = null) {
            checkSessionActivity();
            if (params) {
                const queryString = new URLSearchParams(params).toString();
                endpoint += `?${queryString}`;
            }
            return request('GET', endpoint);
        },

        /**
         * POST request
         */
        async post(endpoint, data = {}, isFormData = false) {
            checkSessionActivity();
            return request('POST', endpoint, data, isFormData);
        },

        /**
         * PUT request
         */
        async put(endpoint, data = {}) {
            checkSessionActivity();
            return request('PUT', endpoint, data);
        },

        /**
         * PATCH request
         */
        async patch(endpoint, data = {}) {
            checkSessionActivity();
            return request('PATCH', endpoint, data);
        },

        /**
         * DELETE request
         */
        async delete(endpoint) {
            checkSessionActivity();
            return request('DELETE', endpoint);
        },

        /**
         * Upload file(s)
         */
        async upload(endpoint, formData) {
            if (!(formData instanceof FormData)) {
                throw new Error('Data must be a FormData instance for file uploads');
            }
            return request('POST', endpoint, formData, true);
        },

        /**
         * Get current user info
         */
        async getCurrentUser() {
            return this.get('/auth/user');
        },

        /**
         * Logout
         */
        async logout() {
            try {
                await this.post('/auth/logout');
            } finally {
                window.location.href = '/login';
            }
        },

        /**
         * Show loading indicator
         */
        showLoading(elementId = 'loading-spinner') {
            const el = document.getElementById(elementId);
            if (el) el.style.display = 'block';
        },

        /**
         * Hide loading indicator
         */
        hideLoading(elementId = 'loading-spinner') {
            const el = document.getElementById(elementId);
            if (el) el.style.display = 'none';
        },

        /**
         * Escape HTML special chars
         */
        _stripHtml(str) {
            return String(str || '').replace(/<[^>]*>/g, '');
        },

        /**
         * Display success notification
         */
        showSuccess(message, duration = 3000) {
            const notification = document.createElement('div');
            notification.className = 'alert alert-success alert-dismissible fade show';
            notification.innerHTML = `
                <i class="bi bi-check-circle"></i> ${this._stripHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.zIndex = '9999';
            notification.style.minWidth = '300px';
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, duration);
        },

        /**
         * Display error notification
         */
        showError(message, duration = 5000) {
            const notification = document.createElement('div');
            notification.className = 'alert alert-danger alert-dismissible fade show';
            notification.innerHTML = `
                <i class="bi bi-exclamation-triangle"></i> ${this._stripHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.zIndex = '9999';
            notification.style.minWidth = '300px';
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, duration);
        },

        /**
         * Configuration
         */
        config: CONFIG
    };
})();

// Make globally available
window.api = ALOGOTO_API;

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('[ALOGOTO API] Initialized');
    console.log('[ALOGOTO API] Base URL:', ALOGOTO_API.config.BASE_URL);
});
