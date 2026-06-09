/**
 * Header Dynamic - Porteur Dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
    fetchHeaderData();
    setInterval(fetchHeaderData, 30000);
});

const API_HEADERS = {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
};

async function fetchHeaderData() {
    try {
        const userResponse = await fetch('/api/v1/auth/me', { headers: API_HEADERS });
        if (userResponse.ok) {
            const user = await userResponse.json();
            window.currentUserId = user.id;

            const nameEl = document.getElementById('header-user-name');
            const avatarEl = document.getElementById('header-user-avatar');
            const roleEl = document.getElementById('header-user-role');

            if (nameEl) nameEl.textContent = user.name;
            if (roleEl) roleEl.textContent = 'Porteur de Projet';
            if (avatarEl && user.avatar_url) avatarEl.src = resolveAvatarUrl(user.avatar_url);
        }

        const msgResponse = await fetch('/api/v1/conversations', { headers: API_HEADERS });
        if (msgResponse.ok) {
            const data = await msgResponse.json();
            const conversations = data.conversations || data.data || data || [];
            const unreadMessages = conversations.reduce((acc, conv) => acc + (conv.unread_count || 0), 0);

            const msgPulse = document.getElementById('header-messages-pulse');
            if (msgPulse) {
                if (unreadMessages > 0) {
                    msgPulse.textContent = unreadMessages > 99 ? '99+' : unreadMessages;
                    msgPulse.style.display = '';
                } else {
                    msgPulse.textContent = '0';
                    msgPulse.style.display = 'none';
                }
            }

            const msgTitle = document.querySelector('.message .drop-heading h6');
            if (msgTitle) {
                msgTitle.textContent = unreadMessages > 0 ? `Vous avez ${unreadMessages} nouveau(x) message(s)` : 'Aucun nouveau message';
            }

            renderHeaderMessages(conversations);
        }

        const notifResponse = await fetch('/api/v1/notifications', { headers: API_HEADERS });
        if (notifResponse.ok) {
            const notifications = await notifResponse.json();
            const notifList = notifications.data || notifications || [];
            const unreadNotifs = notifList.filter(n => !n.is_read).length;

            const notifPulse = document.getElementById('header-notifications-pulse');
            if (notifPulse) {
                if (unreadNotifs > 0) {
                    notifPulse.textContent = unreadNotifs > 99 ? '99+' : unreadNotifs;
                    notifPulse.style.display = '';
                } else {
                    notifPulse.textContent = '0';
                    notifPulse.style.display = 'none';
                }
            }

            renderHeaderNotifications(notifList);
        }

    } catch (error) {
        console.error('Error fetching header data:', error);
    }
}

function renderHeaderNotifications(notifications) {
    const list = document.getElementById('header-notifications-list');
    if (!list) return;

    if (!notifications || notifications.length === 0) {
        list.innerHTML = '<div class="dropdown-item text-center p-3 text-muted">Aucune notification</div>';
        return;
    }

    list.innerHTML = notifications.slice(0, 5).map(notif => `
        <div class="d-flex align-items-start dropdown-item">
            <div class="flex-grow-1">
                <div class="d-flex align-items-start justify-content-between mb-1">
                    <h5 class="mb-0 fs-13 fw-semibold">${notif.title || notif.type || 'Notification'}</h5>
                    <small class="text-muted">${notif.created_at_human || ''}</small>
                </div>
                <p class="mb-0 text-muted fs-12">${notif.content || notif.message || ''}</p>
            </div>
        </div>
    `).join('');
}

function renderHeaderMessages(conversations) {
    const list = document.getElementById('header-messages-list');
    if (!list) return;

    if (!conversations || conversations.length === 0) {
        list.innerHTML = '<div class="dropdown-item text-center p-3 text-muted">Aucun message</div>';
        return;
    }

    list.innerHTML = conversations.slice(0, 5).map(conv => {
        const otherName = conv.institution_name || conv.other_user_name || 'Utilisateur';
        const otherAvatar = conv.institution_avatar || conv.other_user_avatar || '';
        return `
        <a class="dropdown-item d-flex" href="messages.php?id=${conv.id}">
            <span class="avatar avatar-md me-3 align-self-center cover-image rounded-circle bg-primary-transparent">
                ${otherAvatar ? `<img src="${resolveAvatarUrl(otherAvatar)}" class="rounded-circle">` : `<span class="text-primary fw-bold">${otherName.substring(0, 2).toUpperCase()}</span>`}
            </span>
            <div class="wd-90p">
                <div class="d-flex">
                    <h5 class="mb-1">${otherName}</h5>
                    <small class="text-muted ms-auto">${conv.last_message_at || ''}</small>
                </div>
                <span class="fs-12 text-muted text-truncate d-block" style="max-width: 200px;">${conv.last_message || 'Nouvelle conversation'}</span>
            </div>
        </a>`;
    }).join('');
}
