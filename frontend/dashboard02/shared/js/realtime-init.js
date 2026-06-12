/**
 * Initialisation temps réel ALOGOTO
 * Architecture: Laravel Reverb (WebSocket) + fallback polling AJAX
 *
 * - Si Reverb est disponible (window.REVERB_AVAILABLE === true):
 *     → charge Pusher + Echo via CDN → initialise Echo → écoute les events
 * - Si Reverb indisponible:
 *     → polling AJAX toutes les 10s → ZERO erreur console
 * - Toujours: showRealtimeAlert() + updateNotificationBadge() disponibles
 */

/* ------------------------------------------------------------------ */
/*  Fonctions globales (toasts + badge)                                */
/* ------------------------------------------------------------------ */

function stripHtml(str) {
    return String(str || '').replace(/<[^>]*>/g, '');
}

function showRealtimeAlert(type, title, message) {
    try {
        var container = document.getElementById('realtime-toast-container');
        if (!container) return;

        var colors = {
            success: 'border-success',
            warning: 'border-warning',
            danger: 'border-danger',
            info: 'border-info',
            primary: 'border-primary',
        };

        var icons = {
            success: 'fe fe-check-circle',
            warning: 'fe fe-alert-triangle',
            danger: 'fe fe-x-circle',
            info: 'fe fe-bell',
            primary: 'fe fe-briefcase',
        };

        var id = 'toast-' + Date.now();
        var html =
            '<div id="' + id + '" class="toast show border-0 ' + (colors[type] || 'border-info') + ' border-start border-4 shadow-sm mb-2" role="alert">' +
                '<div class="toast-header bg-white">' +
                    '<i class="' + (icons[type] || 'fe fe-bell') + ' me-2 text-' + type + '"></i>' +
                    '<strong class="me-auto fs-12">' + stripHtml(title) + '</strong>' +
                    '<button type="button" class="btn-close" data-bs-dismiss="toast"></button>' +
                '</div>' +
                '<div class="toast-body fs-11 py-2">' + stripHtml(message) + '</div>' +
            '</div>';

        container.insertAdjacentHTML('afterbegin', html);

        setTimeout(function () {
            var el = document.getElementById(id);
            if (el) {
                el.classList.remove('show');
                setTimeout(function () { try { el.remove(); } catch (e) {} }, 300);
            }
        }, 8000);
    } catch (e) {}
}

function updateNotificationBadge() {
    try {
        document.querySelectorAll('.notification-badge').forEach(function (badge) {
            var current = parseInt(badge.textContent, 10) || 0;
            badge.textContent = current + 1;
            badge.classList.remove('d-none');
        });
    } catch (e) {}
}

/* ------------------------------------------------------------------ */
/*  Initialisation (IIFE)                                             */
/* ------------------------------------------------------------------ */

(function () {
    'use strict';

    var POLL_INTERVAL = 10000; /* 10 secondes */

    var userId = document.body && document.body.dataset.userId;
    if (!userId) return;

    var seenIds   = new Set();
    var lastUnread  = -1;
    var pollTimer   = null;
    var echoReady   = false;

    /* ---- Helpers API ---- */

    function apiUrl(path) {
        return '/api/v1' + path;
    }

    function apiFetch(path) {
        return fetch(apiUrl(path), {
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        }).then(function (r) {
            if (!r.ok) throw null;
            return r.json();
        });
    }

    function safeShow(type, title, msg) {
        try { showRealtimeAlert(type, title, msg); } catch (e) {}
    }

    function safeBadge() {
        try { updateNotificationBadge(); } catch (e) {}
    }

    /* ---- Polling (fallback principal) ---- */

    function loadSeenIds() {
        return apiFetch('/notifications?type=unread').then(function (notifs) {
            if (Array.isArray(notifs)) {
                notifs.forEach(function (n) { seenIds.add(n.id); });
            }
        });
    }

    function fetchNew() {
        apiFetch('/notifications?type=unread').then(function (notifs) {
            if (!Array.isArray(notifs)) return;
            notifs.forEach(function (n) {
                if (!seenIds.has(n.id)) {
                    seenIds.add(n.id);
                    var t = n.type === 'critical' ? 'danger'
                          : n.type === 'warning'  ? 'warning'
                          : 'info';
                    safeShow(t, n.title || n.type, n.message || n.content);
                    safeBadge();
                }
            });
        });
    }

    function pollTick() {
        apiFetch('/notifications/unread-count').then(function (data) {
            var count = data.unread_count;
            if (lastUnread === -1) {
                lastUnread = count;
                return loadSeenIds();
            }
            if (count > lastUnread) {
                lastUnread = count;
                return fetchNew();
            }
            lastUnread = count;
        });
    }

    function startPolling() {
        stopPolling();
        pollTick();
        pollTimer = setInterval(pollTick, POLL_INTERVAL);
    }

    function stopPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    }

    /* ---- Echo / WebSocket ---- */

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            var s = document.createElement('script');
            s.src = src;
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }

    function initEcho() {
        try {
            if (window.Pusher && window.Pusher.log) {
                window.Pusher.log = function () {};
            }
        } catch (e) {}

        var key = 'zdaq7c5thvlwtw5f6mgk';
        try {
            var meta = document.querySelector('meta[name="reverb-key"]');
            if (meta) key = meta.getAttribute('content');
        } catch (e) {}

        var localhost = window.location.hostname === '127.0.0.1' || window.location.hostname === 'localhost';

        try {
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: key,
                wsHost: window.location.hostname,
                wsPort: 8080,
                wssPort: 443,
                forceTLS: false,
                encrypted: false,
                enabledTransports: localhost ? ['ws'] : ['ws', 'wss'],
            });

            var ch = window.Echo.private('notifications.' + userId);

            ch.listen('.NewNotification', function (e) {
                safeShow('info', e.title, e.message);
                safeBadge();
            });
            ch.listen('.RepaymentRecorded', function (e) {
                safeShow(e.statut === 'paye' ? 'success' : 'warning', e.title, e.message);
                safeBadge();
            });
            ch.listen('.ProjectSubmitted', function (e) {
                safeShow('primary', e.title, e.message);
                safeBadge();
            });
            ch.listen('.FundingReceived', function (e) {
                safeShow('success', e.title, e.message);
                safeBadge();
            });

            echoReady = true;
            return true;
        } catch (e) {
            return false;
        }
    }

    /* ---- Bootstrap ---- */

    function init() {
        if (window.REVERB_AVAILABLE) {
            /* Reverb est disponible → charger Pusher + Echo puis initialiser */
            Promise.all([
                loadScript('https://js.pusher.com/8.2.0/pusher.min.js'),
                loadScript('https://unpkg.com/laravel-echo@1.16.0/dist/echo.iife.js'),
            ]).then(function () {
                if (!initEcho()) {
                    startPolling();
                } else {
                    /* Echo créé, mais la connexion peut encore échouer → fallback après 5s */
                    setTimeout(function () {
                        if (echoReady) return;
                        try {
                            if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
                                if (window.Echo.connector.pusher.connection.state === 'connected') {
                                    return;
                                }
                            }
                        } catch (e) {}
                        startPolling();
                    }, 5000);
                }
            }).catch(function () {
                /* CDN injoignable → polling */
                startPolling();
            });
        } else {
            /* Reverb pas disponible → polling direct (zéro WebSocket) */
            startPolling();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
