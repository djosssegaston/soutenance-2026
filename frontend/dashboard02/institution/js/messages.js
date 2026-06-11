/**
 * Institution Messaging Logic v3.0 — Premium UI
 */

let conversations = [];
let currentConversationId = null;
let pollInterval = null;
let selectedFiles = [];
let editingMessageId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadConversations();
    setInterval(loadConversations, 30000);

    // Auto-expand textarea
    const msgInput = document.getElementById('messageInput');
    if (msgInput) {
        msgInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
    }

    const params = new URLSearchParams(window.location.search);
    const projectId = params.get('project_id');
    const conversationId = params.get('id');

    if (projectId) {
        startConversation(projectId);
    } else if (conversationId) {
        selectConversation(parseInt(conversationId));
    }
});

async function loadConversations() {
    const list = document.getElementById('conversationsList');
    if (!list || list.offsetParent === null) return;

    const loading = document.getElementById('loadingConversations');
    const empty = document.getElementById('emptyConversations');

    try {
        const response = await fetch('/api/v1/institution/messages');
        const data = await response.json();

        loading.style.display = 'none';

        if (data.status === 'success' && data.conversations?.length > 0) {
            conversations = data.conversations;
            renderConversations();
            empty.style.display = 'none';
        } else {
            empty.style.display = 'block';
            list.innerHTML = '';
        }
    } catch (error) {
        loading.style.display = 'none';
    }
}

function renderConversations() {
    const list = document.getElementById('conversationsList');
    const searchVal = document.getElementById('searchConv')?.value?.toLowerCase() || '';

    list.innerHTML = '';

    conversations.forEach(conv => {
        if (searchVal) {
            const name = (conv.porteur_name || '').toLowerCase();
            const project = (conv.project_title || '').toLowerCase();
            if (!name.includes(searchVal) && !project.includes(searchVal)) return;
        }

        const isActive = currentConversationId === conv.id ? 'active' : '';
        const unreadCount = conv.unread_count || 0;
        const unreadBadge = unreadCount > 0
            ? '<span class="conv-badge">' + unreadCount + '</span>'
            : '';
        const initials = (conv.porteur_name || '??').substring(0, 2).toUpperCase();

        const li = document.createElement('li');
        li.className = 'conversation-item ' + isActive;
        li.onclick = function() { selectConversation(conv.id); };
        li.innerHTML = `
            <div class="conv-avatar-wrap">
                <div class="conv-avatar">
                    ${conv.porteur_avatar ? '<img src="' + resolveAvatarUrl(conv.porteur_avatar) + '" alt="">' : '<span class="conv-initials">' + escapeHtml(initials) + '</span>'}
                </div>
            </div>
            <div class="conv-info">
                <div class="conv-top">
                    <span class="conv-name">${escapeHtml(conv.porteur_name || '')}</span>
                    <span class="conv-time">${escapeHtml(conv.last_message_at || '')}</span>
                </div>
                <div class="conv-bottom">
                    <span class="conv-preview">${escapeHtml(conv.last_message || 'Nouvelle discussion')}</span>
                    ${unreadBadge}
                </div>
                <span class="conv-role">Projet: ${escapeHtml(conv.project_title || '')}</span>
            </div>
        `;
        list.appendChild(li);
    });
}

function filterConversations() {
    renderConversations();
}

async function selectConversation(id) {
    currentConversationId = id;

    document.querySelectorAll('.conversation-item').forEach(item => item.classList.remove('active'));
    renderConversations();

    document.getElementById('chatEmptyState').style.display = 'none';
    document.getElementById('noChatHeader').style.display = 'none';
    document.getElementById('activeChatHeader').style.display = 'flex';
    document.getElementById('chatInputArea').style.display = 'block';

    const activeItem = conversations.find(c => c.id === id);
    if (activeItem) {
        document.getElementById('headerPorteurName').textContent = activeItem.porteur_name || 'Porteur';
        document.getElementById('headerProjectTitle').textContent = activeItem.project_title || 'Projet';
        
        // Handle avatar
        const initialsEl = document.getElementById('headerInitials');
        const imgEl = document.getElementById('headerPorteurImg');
        if (initialsEl) {
            initialsEl.textContent = (activeItem.porteur_name || '??').substring(0, 2).toUpperCase();
        }
        if (imgEl) {
            if (activeItem.porteur_avatar) {
                imgEl.src = resolveAvatarUrl(activeItem.porteur_avatar);
                imgEl.style.display = 'block';
                if (initialsEl) initialsEl.style.display = 'none';
            } else {
                imgEl.style.display = 'none';
                if (initialsEl) initialsEl.style.display = 'block';
            }
        }
    }

    loadMessages();

    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(loadMessages, 5000);
}

async function loadMessages() {
    if (!currentConversationId) return;

    const chatArea = document.getElementById('chatMessages');

    if (!chatArea || chatArea.offsetParent === null) return;

    const loading = document.getElementById('loadingMessages');

    try {
        const wasAtBottom = chatArea.scrollHeight - chatArea.scrollTop - chatArea.clientHeight < 80;
        const prevScroll = chatArea.scrollTop;
        const prevHeight = chatArea.scrollHeight;

        const response = await fetch('/api/v1/institution/messages/' + currentConversationId);
        const data = await response.json();

        if (data.status === 'success') {
            renderMessages(data.messages || []);
            if (loading) loading.style.display = 'none';

            if (wasAtBottom) {
                scrollToBottom();
            } else {
                chatArea.scrollTop = chatArea.scrollHeight - prevHeight + prevScroll;
            }
        }
    } catch (error) {
        /* silent */
    }
}

function renderMessages(messages) {
    const chatArea = document.getElementById('chatMessages');

    chatArea.querySelectorAll('.whatsapp-message, .whatsapp-day').forEach(function(el) { el.remove(); });

    if (!messages || messages.length === 0) {
        return;
    }

    let lastDate = null;

    messages.forEach(msg => {
        const isMe = msg.sender_role === 'institution';
        const msgClass = isMe ? 'whatsapp-message whatsapp-message--outgoing' : 'whatsapp-message';

        const msgDate = msg.created_at ? new Date(msg.created_at) : new Date();
        const dateStr = msgDate.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });

        if (dateStr !== lastDate) {
            chatArea.innerHTML += '<div class="whatsapp-day">' + (dateStr === new Date().toLocaleDateString('fr-FR', { day:'numeric', month:'long', year:'numeric' }) ? "Aujourd'hui" : dateStr) + '</div>';
            lastDate = dateStr;
        }

        let fileHtml = '';
        if (msg.file_path) {
            const isImage = msg.type === 'image';
            const isAudio = msg.type === 'audio';
            if (isImage) {
                fileHtml = '<img src="/storage/' + msg.file_path + '" class="msg-img-preview" onclick="window.open(\'/storage/' + msg.file_path + '\',\'_blank\')">';
            } else if (isAudio) {
                fileHtml = '<div class="wa-audio">' +
                    '<button class="wa-audio-play" onclick="toggleAudioPlayer(this)" aria-label="Lecture/Pause">' +
                        '<svg viewBox="0 0 24 24" width="18" height="18"><polygon points="5,3 19,12 5,21" fill="currentColor"/></svg>' +
                    '</button>' +
                    '<div class="wa-audio-body">' +
                        '<div class="wa-audio-row">' +
                            '<div class="wa-audio-wave">' +
                                '<span></span><span></span><span></span><span></span><span></span>' +
                                '<span></span><span></span><span></span><span></span><span></span>' +
                                '<span></span><span></span><span></span><span></span><span></span>' +
                            '</div>' +
                            '<span class="wa-audio-duration">0:00</span>' +
                        '</div>' +
                        '<div class="wa-audio-row wa-audio-current">0:00</div>' +
                        '<div class="wa-audio-progress" style="width:0%"></div>' +
                    '</div>' +
                    '<audio preload="metadata" style="display:none" src="/storage/' + msg.file_path + '"></audio>' +
                '</div>';
            } else {
                fileHtml = '<div class="msg-file" onclick="window.open(\'/storage/' + msg.file_path + '\',\'_blank\')"><i class="fe fe-paperclip file-icon"></i><div class="file-info"><div class="file-name">' + escapeHtml(msg.file_path.split('/').pop()) + '</div></div></div>';
            }
        }

        const timeStr = msgDate.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        const isDeleted = msg.is_deleted || false;
        const isEdited = msg.is_edited || false;
        const isRead = msg.is_read || false;

        const checkIcon = isMe && !isDeleted
            ? '<span class="whatsapp-checks' + (isRead ? ' is-read' : '') + '"><i class="fe fe-' + (isRead ? 'check-circle' : 'check') + '"></i></span>'
            : '';

        const actionBtns = isMe && !isDeleted
            ? '<span class="whatsapp-actions"><a href="javascript:void(0)" onclick="editMessage(' + msg.id + ')" title="Modifier"><i class="fe fe-edit-2"></i></a><a href="javascript:void(0)" onclick="deleteMessage(' + msg.id + ')" title="Supprimer"><i class="fe fe-trash-2"></i></a></span>'
            : '';

        const senderNameHtml = !isMe && msg.sender_name
            ? '<div class="whatsapp-sender-name">' + escapeHtml(msg.sender_name) + '</div>'
            : '';

        const editedBadge = isEdited ? '<span class="whatsapp-edited">modifié</span>' : '';

        chatArea.innerHTML += `
            <div class="${msgClass}" data-msg-id="${msg.id}">
                <div class="whatsapp-bubble">
                    ${senderNameHtml}
                    ${fileHtml}
                    ${isDeleted ? '<div class="whatsapp-deleted">Ce message a été supprimé</div>' : '<div class="msg-text">' + escapeHtml(msg.message || '') + '</div>'}
                    <div class="whatsapp-message__meta">
                        <span class="whatsapp-time">${timeStr}${editedBadge}${actionBtns}</span>
                        ${checkIcon}
                    </div>
                </div>
            </div>`;
    });

    scrollToBottom();
    restoreAudioVisualState();
}

async function sendMessage() {
    const input = document.getElementById('messageInput');
    const msg = input?.value?.trim() || '';
    const fileInput = document.getElementById('fileInput');

    if (!msg && selectedFiles.length === 0) return;

    if (!currentConversationId) {
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Aucune conversation sélectionnée');
        return;
    }

    const formData = new FormData();
    formData.append('conversation_id', currentConversationId);
    formData.append('message', msg);

    if (selectedFiles.length > 0) {
        selectedFiles.forEach(f => formData.append('files[]', f));
        formData.append('file', selectedFiles[0]);
        formData.append('type', getFileType(selectedFiles[0].name));
    } else {
        formData.append('type', 'texte');
    }

    try {
        const response = await fetch('/api/v1/institution/messages/send', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await response.json();

        if (!response.ok) {
            if (data.errors) {
                const msgs = Object.values(data.errors).flat().join('. ');
                if (typeof ALOGOTO !== 'undefined') ALOGOTO.error(msgs || 'Erreur de validation');
            } else {
                if (typeof ALOGOTO !== 'undefined') ALOGOTO.error(data.error || 'Erreur d\'envoi');
            }
            return;
        }

        if (data.status === 'success') {
            if (input) input.value = '';
            clearFiles();
            await loadMessages();
            loadConversations();
        }
    } catch (error) {
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur réseau');
    }
}

function handleFileSelect(input) {
    selectedFiles = Array.from(input.files);
    const badge = document.getElementById('fileBadge');
    const count = document.getElementById('fileCount');
    const preview = document.getElementById('filePreviews');

    if (selectedFiles.length > 0) {
        if (badge) badge.style.display = 'block';
        if (count) count.textContent = selectedFiles.length;
        if (preview) {
            preview.innerHTML = '';
            selectedFiles.forEach(file => {
                preview.insertAdjacentHTML('beforeend', '<div class="badge bg-light text-dark border p-2"><i class="fe fe-file me-1"></i> ' + escapeHtml(file.name) + '</div>');
            });
        }
    }
}

function clearFiles() {
    selectedFiles = [];
    const fi = document.getElementById('fileInput');
    if (fi) fi.value = '';
    const badge = document.getElementById('fileBadge');
    if (badge) badge.style.display = 'none';
    const preview = document.getElementById('filePreviews');
    if (preview) preview.innerHTML = '';
}

function checkSubmit(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
}

function scrollToBottom() {
    const chatArea = document.getElementById('chatMessages');
    requestAnimationFrame(function() { chatArea.scrollTop = chatArea.scrollHeight; });
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getFileType(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) return 'image';
    if (['mp3', 'wav', 'ogg', 'webm', 'm4a'].includes(ext)) return 'audio';
    return 'document';
}

async function startConversation(projectId) {
    try {
        const response = await fetch('/api/v1/institution/messages/start', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ project_id: projectId })
        });

        const data = await response.json();

        if (data.status === 'success') {
            const url = new URL(window.location);
            url.searchParams.delete('project_id');
            window.history.replaceState({}, '', url);
            await loadConversations();
            selectConversation(data.conversation.id);
        } else {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Impossible d\'ouvrir la discussion');
        }
    } catch (error) {
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur réseau');
    }
}

function viewProject() {
    const conv = conversations.find(c => c.id === currentConversationId);
    if (conv) {
        window.location.href = 'projets_disponibles.php?id=' + conv.project_id;
    }
}

// Edit / Delete
function editMessage(messageId) {
    const row = document.querySelector('[data-msg-id="' + messageId + '"]');
    if (!row) return;
    const bubble = row.querySelector('.whatsapp-bubble');
    if (!bubble) return;
    const textEl = bubble.querySelector('.msg-text');
    if (!textEl) return;

    const currentText = textEl.textContent;
    editingMessageId = messageId;
    textEl.innerHTML = `
        <div class="edit-wrap">
            <textarea class="form-control form-control-sm mb-1" id="edit-textarea" rows="2" style="min-width:200px;font-size:13px">${escapeHtml(currentText)}</textarea>
            <div class="d-flex gap-1">
                <button class="btn btn-sm btn-success py-0" onclick="saveEdit(${messageId})"><i class="fe fe-check"></i></button>
                <button class="btn btn-sm btn-light py-0" onclick="cancelEdit(${messageId})"><i class="fe fe-x"></i></button>
            </div>
        </div>`;
    setTimeout(function() { var el = document.getElementById('edit-textarea'); if (el) el.focus(); }, 50);
}

function cancelEdit() {
    editingMessageId = null;
    loadMessages();
}

async function saveEdit(messageId) {
    const textarea = document.getElementById('edit-textarea');
    if (!textarea || !textarea.value.trim()) return;
    try {
        const response = await fetch('/api/v1/institution/messages/' + messageId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: textarea.value.trim() })
        });
        const data = await response.json();
        if (data.status === 'success') {
            editingMessageId = null;
            loadMessages();
        } else {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.error(data.error || 'Erreur lors de la modification');
        }
    } catch (error) {
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur réseau');
    }
}

async function deleteMessage(messageId) {
    var confirmed;
    if (typeof ALOGOTO !== 'undefined') {
        confirmed = await ALOGOTO.confirm('Supprimer ce message ?', 'Cette action est irréversible.', 'Oui, supprimer', 'Annuler');
        if (!confirmed || !confirmed.isConfirmed) return;
    } else {
        var ok = await ModalHelper.confirm('<i class="bi bi-trash me-2 text-danger"></i> Supprimer', 'Supprimer ce message ? Cette action est irréversible.', 'Oui, supprimer', 'Annuler', 'btn-danger');
        if (!ok) return;
    }
    try {
        const response = await fetch('/api/v1/institution/messages/' + messageId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (data.status === 'success') {
            loadMessages();
        } else {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.error(data.error || 'Erreur lors de la suppression');
        }
    } catch (error) {
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur réseau');
    }
}

/* ── ── Audio Recording ── ── */
let mediaRecorder = null;
let audioChunks = [];
let recTimer = null;
let recSeconds = 0;

async function toggleAudioRecording(btn) {
    if (mediaRecorder && mediaRecorder.state === 'recording') {
        stopAudioRecording(btn);
    } else {
        await startAudioRecording(btn);
    }
}

async function startAudioRecording(btn) {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm;codecs=opus' });
        audioChunks = [];
        recSeconds = 0;

        mediaRecorder.ondataavailable = e => audioChunks.push(e.data);

        mediaRecorder.onstop = () => {
            const blob = new Blob(audioChunks, { type: 'audio/webm' });
            const file = new File([blob], `audio_${Date.now()}.webm`, { type: 'audio/webm' });
            stream.getTracks().forEach(t => t.stop());
            selectedFiles.push(file);
            const badge = document.getElementById('fileBadge');
            const count = document.getElementById('fileCount');
            const preview = document.getElementById('filePreviews');
            if (badge) badge.style.display = 'block';
            if (count) count.textContent = selectedFiles.length;
            if (preview) {
                preview.insertAdjacentHTML('beforeend', '<div class="badge bg-light text-dark border p-2"><i class="fe fe-mic me-1"></i> ' + escapeHtml(file.name) + '</div>');
            }
            btn.innerHTML = '<i class="fe fe-mic"></i>';
        };

        mediaRecorder.start();
        btn.classList.add('is-recording');
        btn.innerHTML = '<i class="fe fe-stop-circle"></i>';
        recTimer = setInterval(() => {
            recSeconds++;
            const m = Math.floor(recSeconds / 60);
            const s = recSeconds % 60;
            const timeStr = `${m}:${s.toString().padStart(2, '0')}`;
            let timeEl = btn.querySelector('.rec-time');
            if (!timeEl) {
                timeEl = document.createElement('span');
                timeEl.className = 'rec-time';
                btn.appendChild(timeEl);
            }
            timeEl.textContent = timeStr;
        }, 1000);
    } catch (err) {
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Impossible d\'accéder au microphone.');
    }
}

function stopAudioRecording(btn) {
    if (mediaRecorder && mediaRecorder.state === 'recording') {
        mediaRecorder.stop();
        clearInterval(recTimer);
        btn.classList.remove('is-recording');
        btn.querySelector('.rec-time')?.remove();
    }
}

/* ── ── WhatsApp-style Audio Player (global, survives re-render) ── ── */
let currentAudioMsgId = null;
let currentAudioPlayer = null;

function ensureGlobalAudio() {
    let audio = document.getElementById('waGlobalAudio');
    if (!audio) {
        audio = document.createElement('audio');
        audio.id = 'waGlobalAudio';
        audio.preload = 'metadata';
        audio.style.display = 'none';
        document.body.appendChild(audio);
        audio.ontimeupdate = function() {
            syncAudioVisualState(this);
        };
        audio.onloadedmetadata = function() {
            syncAudioVisualState(this);
        };
        audio.onended = function() {
            resetAudioVisualState();
            currentAudioMsgId = null;
            currentAudioPlayer = null;
        };
        audio.onerror = function() {
            currentAudioMsgId = null;
            currentAudioPlayer = null;
        };
    }
    return audio;
}

function syncAudioVisualState(audio) {
    if (!currentAudioMsgId) return;
    const container = document.querySelector(`[data-msg-id="${currentAudioMsgId}"] .wa-audio`);
    if (!container) return;
    const el = container.querySelector('.wa-audio-current');
    const p = container.querySelector('.wa-audio-progress');
    const d = container.querySelector('.wa-audio-duration');
    if (el) el.textContent = formatAudioTime(audio.currentTime);
    if (p) p.style.width = (audio.duration ? (audio.currentTime / audio.duration * 100) : 0) + '%';
    if (d && audio.duration && d.textContent === '0:00') d.textContent = formatAudioTime(audio.duration);
}

function resetAudioVisualState() {
    if (!currentAudioMsgId) return;
    const container = document.querySelector(`[data-msg-id="${currentAudioMsgId}"] .wa-audio`);
    if (container) {
        container.classList.remove('is-playing');
        const svg = container.querySelector('.wa-audio-play svg');
        if (svg) svg.innerHTML = '<polygon points="5,3 19,12 5,21" fill="currentColor"/>';
        const el = container.querySelector('.wa-audio-current');
        const p = container.querySelector('.wa-audio-progress');
        if (el) el.textContent = '0:00';
        if (p) p.style.width = '0%';
    }
    currentAudioMsgId = null;
    currentAudioPlayer = null;
}

function restoreAudioVisualState() {
    if (!currentAudioMsgId || !currentAudioPlayer || currentAudioPlayer.paused) return;
    const container = document.querySelector(`[data-msg-id="${currentAudioMsgId}"] .wa-audio`);
    if (!container) return;
    const audio = currentAudioPlayer;
    container.classList.add('is-playing');
    const svg = container.querySelector('.wa-audio-play svg');
    if (svg) svg.innerHTML = '<rect x="6" y="4" width="4" height="16" rx="1" fill="currentColor"/><rect x="14" y="4" width="4" height="16" rx="1" fill="currentColor"/>';
    syncAudioVisualState(audio);
}

function toggleAudioPlayer(btn) {
    const container = btn.closest('.wa-audio');
    const msgEl = container.closest('[data-msg-id]');
    const msgId = msgEl ? msgEl.dataset.msgId : null;
    const hiddenAudio = container.querySelector('audio');
    const src = hiddenAudio ? hiddenAudio.getAttribute('src') : '';
    const svg = btn.querySelector('svg');
    const globalAudio = ensureGlobalAudio();

    if (currentAudioMsgId === msgId && !globalAudio.paused) {
        globalAudio.pause();
        container.classList.remove('is-playing');
        svg.innerHTML = '<polygon points="5,3 19,12 5,21" fill="currentColor"/>';
        currentAudioPlayer = null;
    } else {
        if (currentAudioPlayer && !currentAudioPlayer.paused) {
            const prevContainer = document.querySelector(`[data-msg-id="${currentAudioMsgId}"] .wa-audio`);
            if (prevContainer) {
                prevContainer.classList.remove('is-playing');
                const prevSvg = prevContainer.querySelector('.wa-audio-play svg');
                if (prevSvg) prevSvg.innerHTML = '<polygon points="5,3 19,12 5,21" fill="currentColor"/>';
            }
            globalAudio.pause();
        }

        globalAudio.src = src;
        globalAudio.play();
        currentAudioMsgId = msgId;
        currentAudioPlayer = globalAudio;
        container.classList.add('is-playing');
        svg.innerHTML = '<rect x="6" y="4" width="4" height="16" rx="1" fill="currentColor"/><rect x="14" y="4" width="4" height="16" rx="1" fill="currentColor"/>';

        if (globalAudio.readyState >= 1 && globalAudio.duration) {
            const d = container.querySelector('.wa-audio-duration');
            if (d) d.textContent = formatAudioTime(globalAudio.duration);
        }
    }
}

function formatAudioTime(seconds) {
    if (!seconds || isNaN(seconds)) return '0:00';
    const m = Math.floor(seconds / 60);
    const s = Math.floor(seconds % 60);
    return m + ':' + (s < 10 ? '0' : '') + s;
}
