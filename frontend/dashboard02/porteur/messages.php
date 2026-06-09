<?php
require_once __DIR__ . '/header.php';
?>
<link href="../shared/css/messaging.css" rel="stylesheet">

            <!--{ app content start }-->
            <div class="main-content app-content mt-0">
                <div class="side-app">
                    <!--{ container start }-->
                    <div class="main-container container-fluid">
                        <!--{ PAGE HEADER START }-->
                        <div class="page-header">
                            <h1 class="page-title">MES MESSAGES</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">ACCUEIL</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Messages</li>
                                </ol>
                            </div>
                        </div>
                        <!--{ PAGE HEADER END }-->

                        <div class="row messaging-wrapper">
                            <!-- LEFT SIDEBAR - CONVERSATIONS LIST -->
                            <div class="col-xxl-3 col-xl-4 col-md-5 box-col-5 msg-col msg-col-sidebar">
                                <div class="left-sidebar-wrapper card">
                                    <div class="left-sidebar-chat">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0"><i class="fe fe-search text-muted"></i></span>
                                            <input type="text" class="form-control border-start-0" placeholder="Rechercher un porteur ou projet..." id="searchConversation" onkeyup="filterConversations()">
                                        </div>
                                    </div>
                                    <div class="advance-options"> 
                                        <ul class="nav panel-tabs" id="chat-options-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active f-w-600" id="chats-tab" data-bs-toggle="tab" href="#chats" role="tab" aria-controls="chats" aria-selected="true">Discussions</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="chat-options-tabContent"> 
                                            <div class="tab-pane fade show active" id="chats" role="tabpanel" aria-labelledby="chats-tab">
                                                <div class="common-space">
                                                    <p>Discussions actives</p>
                                                </div>
                                                <div id="loadingConversations" class="text-center py-5">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Chargement...</span>
                                                    </div>
                                                </div>
                                                <div id="emptyConversations" class="text-center py-5" style="display:none;">
                                                    <i class="fe fe-message-square fs-50 text-muted"></i>
                                                    <p class="mt-3 text-muted">Aucune discussion</p>
                                                    <small class="text-muted">Les discussions apparaissent quand une institution valide votre projet</small>
                                                </div>
                                                <ul class="chats-user list-group list-group-flush" id="conversationsList">
                                                    <!-- Conversations loaded dynamically -->
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT SIDE - CHAT AREA -->
                            <div class="col-xxl-9 col-xl-8 col-md-7 msg-col msg-col-chat">
                                <div class="card chat-box-wrapper mb-0">
                                    <!-- Chat Header -->
                                    <div class="card-header border-bottom d-flex align-items-center justify-content-between py-2">
                                        <div class="d-flex align-items-center" id="chatHeader">
                                            <button type="button" class="msg-back-btn" onclick="showSidebarMobile()" title="Retour à la liste" aria-label="Retour à la liste">
                                                <i class="fe fe-arrow-left"></i>
                                            </button>
                                            <div class="header-avatar">
                                                <span class="header-initials" id="chatInitials">?</span>
                                                <img src="../../asset/images/profiles/1.jpg" alt="" id="chatAvatarImg" style="display:none;">
                                            </div>
                                            <div class="header-info">
                                                <div class="header-name" id="chatProjectName">Sélectionnez une discussion</div>
                                                <span class="header-sub" id="chatInstitutionName"></span>
                                                <span class="header-sub" id="chatStatusText" style="display:none;"><span class="header-status-dot online"></span><span class="header-status-text">En ligne</span></span>
                                            </div>
                                        </div>
                                        <div class="ms-auto">
                                            <div class="dropdown">
                                                <a href="javascript:void(0)" class="nav-link icon" data-bs-toggle="dropdown"><i class="fe fe-more-vertical"></i></a>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="if(currentConversationId){loadMessages();}"><i class="fe fe-refresh-cw me-2"></i> Actualiser</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Chat Messages Area -->
                                    <div class="chat-body chat-body-custom" id="chatMessages">
                                        <!-- Empty state -->
                                        <div id="chatEmptyState">
                                            <i class="fe fe-message-square"></i>
                                            <h4>Bienvenue dans votre messagerie</h4>
                                            <p>Sélectionnez une discussion pour voir les messages</p>
                                        </div>
                                        <!-- Loading -->
                                        <div id="loadingMessages" class="text-center py-5" style="display:none;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Chargement...</span>
                                            </div>
                                        </div>
                                        <!-- Messages loaded here dynamically -->
                                    </div>

                                    <!-- Chat Input Area -->
                                    <div class="card-footer chat-footer border-top" id="chatInputArea" style="display:none;">
                                        <form id="messageForm" enctype="multipart/form-data">
                                            <input type="hidden" id="conversationId" value="">
                                            <div class="input-group">
                                                <button type="button" class="btn-attach" onclick="document.getElementById('fileInput').click()" title="Joindre un fichier" aria-label="Joindre un fichier">
                                                    <i class="fe fe-plus"></i>
                                                </button>
                                                <input type="file" id="fileInput" style="display:none;" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.mp3,.wav,.ogg" onchange="handleFileSelect(this)">
                                                <textarea class="form-control" id="messageInput" placeholder="Écrivez votre message ici..." rows="1" onkeydown="handleKeyPress(event)"></textarea>
                                                    <button type="button" class="btn-record" id="btnRecord" title="Enregistrer un message audio" aria-label="Enregistrer un message audio" onclick="toggleAudioRecording(this)">
                                                        <i class="fe fe-mic"></i>
                                                    </button>
                                                <button type="button" class="btn-send" onclick="sendMessage()" title="Envoyer" aria-label="Envoyer">
                                                    <i class="fe fe-send"></i>
                                                </button>
                                            </div>
                                            <div id="filePreview" class="mt-2" style="display:none;">
                                                <small class="text-muted">Fichier sélectionné: <span id="fileName"></span> <a href="javascript:void(0)" onclick="clearFile()" class="text-danger">×</a></small>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--{ container end }-->
                </div>
            </div>


<script>
// CSRF Token
const csrfToken = '<?php echo csrf_token(); ?>';
const currentUserId = <?php echo auth()->id() ?: 0; ?>;

// State
let conversations = [];
let currentConversationId = null;
let messagesPollingInterval = null;

// Load conversations on page load
document.addEventListener('DOMContentLoaded', function() {
    loadConversations();

    // Auto-expand textarea
    const msgInput = document.getElementById('messageInput');
    if (msgInput) {
        msgInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
    }
});

// Load conversations from API
function loadConversations() {
    const loading = document.getElementById('loadingConversations');
    const empty = document.getElementById('emptyConversations');
    const list = document.getElementById('conversationsList');

    loading.style.display = 'block';
    empty.style.display = 'none';
    list.innerHTML = '';

        fetch('/dashboard/porteur/conversations', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';

            if (data.conversations) {
                conversations = data.conversations;
                renderConversations();

                if (conversations.length > 0) {
                    selectConversation(conversations[0].id);
                } else {
                    empty.style.display = 'block';
                }
            } else {
                empty.style.display = 'block';
            }
        })
    .catch(err => {
        loading.style.display = 'none';
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur de chargement des conversations.');
    });
}

// Render conversations list
function renderConversations() {
    const list = document.getElementById('conversationsList');
    list.innerHTML = '';

    conversations.forEach(conv => {
        const isActive = currentConversationId === conv.id ? 'active' : '';
        const unreadCount = conv.unread_count || 0;
        const unreadBadge = unreadCount > 0
            ? '<span class="conv-badge">' + unreadCount + '</span>'
            : '';
        const initials = (conv.institution && conv.institution.nom ? conv.institution.nom.substring(0, 2) : 'IN').toUpperCase();
        const name = conv.institution ? conv.institution.nom : 'Institution';
        const avatar = conv.institution?.logo || null;
        const preview = conv.last_message ? conv.last_message.content : 'Aucun message';
        const time = conv.last_message ? conv.last_message.created_at : '';

        const li = document.createElement('li');
        li.className = 'conversation-item ' + isActive;
        li.onclick = function() { selectConversation(conv.id); };
        li.innerHTML = `
            <div class="conv-avatar-wrap">
                <div class="conv-avatar">
                    ${avatar ? '<img src="' + resolveAvatarUrl(avatar) + '" alt="">' : '<span class="conv-initials">' + escapeHtml(initials) + '</span>'}
                </div>
            </div>
            <div class="conv-info">
                <div class="conv-top">
                    <span class="conv-name">${escapeHtml(name)}</span>
                    <span class="conv-time">${escapeHtml(time)}</span>
                </div>
                <div class="conv-bottom">
                    <span class="conv-preview">${escapeHtml(preview)}</span>
                    ${unreadBadge}
                </div>
            </div>
        `;
        list.appendChild(li);
    });
}

// Mobile toggle
function showChatMobile() {
    document.querySelector('.msg-col-sidebar')?.classList.add('hide');
    document.querySelector('.msg-col-chat')?.classList.add('show');
}
function showSidebarMobile() {
    document.querySelector('.msg-col-sidebar')?.classList.remove('hide');
    document.querySelector('.msg-col-chat')?.classList.remove('show');
}

// Select a conversation
function selectConversation(conversationId) {
    showChatMobile();
    currentConversationId = conversationId;
    document.getElementById('chatEmptyState').style.display = 'none';
    document.getElementById('chatInputArea').style.display = 'block';

    // Update active conversation in list
    document.querySelectorAll('.conversation-item').forEach(function(item) { item.classList.remove('active'); });
    document.querySelectorAll('.conversation-item').forEach(function(item, idx) {
        if (conversations[idx] && conversations[idx].id === conversationId) {
            item.classList.add('active');
        }
    });

    // Find conversation details
    const conv = conversations.find(function(c) { return c.id === conversationId; });
    if (conv) {
        const initialsEl = document.getElementById('chatInitials');
        const imgEl = document.getElementById('chatAvatarImg');
        const logo = conv.institution?.logo || null;
        initialsEl.textContent = conv.institution && conv.institution.nom ? conv.institution.nom.substring(0, 2).toUpperCase() : 'IN';
        if (imgEl) {
            if (logo) {
                imgEl.src = (typeof resolveAvatarUrl === 'function') ? resolveAvatarUrl(logo) : logo;
                imgEl.style.display = 'block';
                initialsEl.style.display = 'none';
            } else {
                imgEl.style.display = 'none';
                initialsEl.style.display = 'block';
            }
        }
        document.getElementById('chatProjectName').textContent = conv.project ? conv.project.titre : 'Projet';
        document.getElementById('chatInstitutionName').textContent = conv.institution ? conv.institution.nom : '';
        document.getElementById('conversationId').value = conversationId;
    }

    loadMessages();

    // Start polling for new messages
    if (messagesPollingInterval) {
        clearInterval(messagesPollingInterval);
    }
    messagesPollingInterval = setInterval(loadMessages, 5000);
}

// Load messages for current conversation
function loadMessages() {
    if (!currentConversationId) return;

    const chatArea = document.getElementById('chatMessages');

    // Skip if chat area is hidden (mobile sidebar visible)
    if (!chatArea || chatArea.offsetParent === null) return;

    const loading = document.getElementById('loadingMessages');

    if (chatArea.children.length === 0 || !chatArea.querySelector('.whatsapp-message')) {
        if (loading) loading.style.display = 'block';
    }

        fetch('/dashboard/porteur/conversations/' + currentConversationId + '/messages', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
    .then(response => response.json())
    .then(data => {
        loading.style.display = 'none';

        if (data.messages) {
            const chatArea = document.getElementById('chatMessages');
            const wasAtBottom = chatArea.scrollHeight - chatArea.scrollTop - chatArea.clientHeight < 80;
            renderMessages(data.messages);
            if (wasAtBottom) scrollToBottom();
        }
    })
    .catch(err => {
        loading.style.display = 'none';
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur de chargement des messages.');
    });
}

// Render messages
function renderMessages(messages) {
    const chatArea = document.getElementById('chatMessages');

    // Clear old messages and date separators
    chatArea.querySelectorAll('.whatsapp-message, .whatsapp-day').forEach(function(el) { el.remove(); });

    let lastDate = null;

    messages.forEach(function(msg) {
        const isCurrentUser = msg.sender_id == currentUserId;
        const msgClass = isCurrentUser ? 'whatsapp-message whatsapp-message--outgoing' : 'whatsapp-message';

        // Date separator
        if (msg.created_at) {
            const msgDate = new Date(msg.created_at);
            const dateStr = msgDate.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
            const todayStr = new Date().toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
            if (dateStr !== lastDate) {
                const sep = document.createElement('div');
                sep.className = 'whatsapp-day';
                sep.innerHTML = (dateStr === todayStr ? "Aujourd'hui" : dateStr);
                chatArea.appendChild(sep);
                lastDate = dateStr;
            }
        }

        const div = document.createElement('div');
        div.className = msgClass;
        div.setAttribute('data-msg-id', msg.id);

        let fileHtml = '';
        if (msg.type !== 'texte' && msg.file_path) {
            const fileUrl = '/storage/' + msg.file_path;
            if (msg.type === 'image') {
                fileHtml = '<img src="' + fileUrl + '" class="msg-img-preview" onclick="window.open(\'' + fileUrl + '\',\'_blank\')" alt="Image">';
            } else if (msg.type === 'audio') {
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
                    '<audio preload="metadata" style="display:none" src="' + fileUrl + '"></audio>' +
                '</div>';
            } else {
                fileHtml = '<div class="msg-file" onclick="window.open(\'' + fileUrl + '\',\'_blank\')"><i class="fe fe-paperclip file-icon"></i><div class="file-info"><div class="file-name">' + escapeHtml(msg.file_path.split('/').pop()) + '</div></div></div>';
            }
        }

        const senderName = !isCurrentUser && msg.sender_name
            ? '<div class="whatsapp-sender-name">' + escapeHtml(msg.sender_name) + '</div>'
            : '';

        const actionBtns = isCurrentUser && !msg.is_deleted
            ? '<span class="whatsapp-actions"><a href="javascript:void(0)" onclick="editMessage(' + msg.id + ')" title="Modifier"><i class="fe fe-edit-2"></i></a><a href="javascript:void(0)" onclick="deleteMessage(' + msg.id + ')" title="Supprimer"><i class="fe fe-trash-2"></i></a></span>'
            : '';

        const timeStr = msg.created_at ? new Date(msg.created_at).toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'}) : '';
        const readIcon = isCurrentUser && !msg.is_deleted
            ? (msg.is_read ? '<span class="whatsapp-checks is-read"><i class="fe fe-check-circle"></i></span>' : '<span class="whatsapp-checks"><i class="fe fe-check"></i></span>')
            : '';

        const editedBadge = msg.is_edited ? '<span class="whatsapp-edited">modifié</span>' : '';

        div.innerHTML = `
            <div class="whatsapp-bubble">
                ${senderName}
                ${fileHtml}
                ${msg.is_deleted ? '<div class="whatsapp-deleted">Ce message a été supprimé</div>' : '<div class="msg-text">' + escapeHtml(msg.message || '') + '</div>'}
                <div class="whatsapp-message__meta">
                    <span class="whatsapp-time">${timeStr}${editedBadge}${actionBtns}</span>
                    ${readIcon}
                </div>
            </div>
        `;
        chatArea.appendChild(div);
    });
    restoreAudioVisualState();
}

// Send message
function sendMessage() {
    const conversationId = document.getElementById('conversationId').value;
    const messageInput = document.getElementById('messageInput');
    const fileInput = document.getElementById('fileInput');

    if (!conversationId) return;

    const formData = new FormData();
    formData.append('conversation_id', conversationId);

    const hasFile = fileInput.files.length > 0;
    const hasAudio = !!recordedAudioFile;
    const hasText = messageInput.value.trim().length > 0;

    if (!hasFile && !hasAudio && !hasText) return;

    if (hasFile) {
        formData.append('file', fileInput.files[0]);
        formData.append('type', getFileType(fileInput.files[0].name));
    } else if (hasAudio) {
        formData.append('file', recordedAudioFile);
        formData.append('type', 'audio');
        recordedAudioFile = null;
    }
    if (hasText) {
        formData.append('message', messageInput.value.trim());
        if (!hasFile && !hasAudio) formData.append('type', 'texte');
    }

    fetch('/api/v1/messages', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        messageInput.value = '';
        clearFile();
        loadMessages();
    })
    .catch(err => {
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur lors de l\'envoi du message.');
    });
}

// Handle Enter key press (textarea: Shift+Enter for newline, Enter to send)
function handleKeyPress(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage();
    }
}

// Handle file selection
function handleFileSelect(input) {
    if (input.files.length > 0) {
        document.getElementById('fileName').textContent = input.files[0].name;
        document.getElementById('filePreview').style.display = 'block';
    }
}

function clearFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').style.display = 'none';
    recordedAudioFile = null;
}

function getFileType(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) return 'image';
    if (['mp3', 'wav', 'ogg', 'm4a', 'webm'].includes(ext)) return 'audio';
    return 'document';
}

// Edit / Delete message
let editingMessageId = null;

function editMessage(messageId) {
    const row = document.querySelector('[data-msg-id="' + messageId + '"]');
    if (!row) return;
    const bubble = row.querySelector('.whatsapp-bubble');
    if (!bubble) return;
    const textEl = bubble.querySelector('.msg-text');
    if (!textEl) return;
    const currentText = textEl.textContent.trim();
    editingMessageId = messageId;
    textEl.innerHTML = `
        <div class="edit-wrap d-flex gap-2 mb-1">
            <input type="text" class="form-control form-control-sm" id="edit-message-input" value="${escapeHtml(currentText)}" style="font-size:13px;">
            <button class="btn btn-sm btn-primary py-0" onclick="saveEdit(${messageId})"><i class="fe fe-check"></i></button>
            <button class="btn btn-sm btn-light py-0" onclick="cancelEdit(${messageId})"><i class="fe fe-x"></i></button>
        </div>
        <small class="text-muted">Modification...</small>
    `;
    setTimeout(function() { var el = document.getElementById('edit-message-input'); if (el) el.focus(); }, 50);
}

function cancelEdit(messageId) {
    editingMessageId = null;
    loadMessages();
}

async function saveEdit(messageId) {
    const input = document.getElementById('edit-message-input');
    if (!input || !input.value.trim()) return;
    try {
        const response = await fetch('/api/v1/messages/' + messageId, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: input.value.trim() })
        });
        const data = await response.json();
        if (data.success) {
            editingMessageId = null;
            loadMessages();
        } else {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.error(data.error || 'Erreur lors de la modification');
        }
    } catch (error) {
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur lors de la modification');
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
        const response = await fetch('/api/v1/messages/' + messageId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (data.success) {
            loadMessages();
        } else {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.error(data.error || 'Erreur lors de la suppression');
        }
    } catch (error) {
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur lors de la suppression');
    }
}

// Filter conversations
function filterConversations() {
    const search = document.getElementById('searchConversation').value.toLowerCase();
    document.querySelectorAll('.conversation-item').forEach(function(item) {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(search) ? '' : 'none';
    });
}

// Scroll to bottom
function scrollToBottom() {
    const chatArea = document.getElementById('chatMessages');
    chatArea.scrollTop = chatArea.scrollHeight;
}

// Utility
function escapeHtml(text) {
    if (text == null) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/* ── Polling des conversations (30s) ── */
if (typeof conversationsPollingInterval !== 'undefined') clearInterval(conversationsPollingInterval);
var conversationsPollingInterval = setInterval(loadConversations, 30000);

/* ── ── Audio Recording ── ── */
let recordedAudioFile = null;
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
            recordedAudioFile = file;
            document.getElementById('fileName').textContent = '🎤 ' + file.name;
            document.getElementById('filePreview').style.display = 'block';
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
</script>

<?php 
require_once __DIR__ . '/footer.php'; 
?>
