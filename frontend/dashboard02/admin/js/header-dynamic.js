/**
 * Dynamic Header - Admin Profile & Notifications
 */

document.addEventListener('DOMContentLoaded', function() {
    fetchUserProfile();
    fetchUnreadCounts();
    setInterval(fetchUnreadCounts, 30000);
});

async function fetchUserProfile() {
    try {
        const response = await fetch('/api/v1/auth/me', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) throw new Error('HTTP ' + response.status);
        const body = await response.json();
        const user = body.user || body;
        const nameEl = document.getElementById('header-user-name');
        const roleEl = document.getElementById('header-user-role');
        if (nameEl) nameEl.textContent = user.name;
        if (roleEl) roleEl.textContent = (user.role || '').toUpperCase();
        
        if (user.avatar) {
            document.querySelectorAll('.profile-user').forEach(img => {
                img.src = resolveAvatarUrl(user.avatar);
            });
        }
    } catch (error) {
        console.error('Error fetching user profile:', error);
    }
}

async function fetchUnreadCounts() {
    try {
        const response = await fetch('/api/v1/dashboard/counts', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) throw new Error('HTTP ' + response.status);
        const counts = await response.json();
            
            // Notifications
            const notifBadge = document.getElementById('header-notifications-pulse');
            if (notifBadge) {
                if (counts.notifications_count > 0) {
                    notifBadge.textContent = counts.notifications_count > 99 ? '99+' : counts.notifications_count;
                    notifBadge.style.display = '';
                } else {
                    notifBadge.textContent = '0';
                    notifBadge.style.display = 'none';
                }
            }

            // Messages
            const msgBadge = document.getElementById('header-messages-pulse');
            if (msgBadge) {
                if (counts.messages_count > 0) {
                    msgBadge.textContent = counts.messages_count > 99 ? '99+' : counts.messages_count;
                    msgBadge.style.display = '';
                } else {
                    msgBadge.textContent = '0';
                    msgBadge.style.display = 'none';
                }
            }
    } catch (error) {
        console.error('Error fetching unread counts:', error);
    }
}

// Logout is now handled directly in the header HTML (POST /logout with CSRF)
