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
                <h1 class="page-title">CENTRE DE MESSAGERIE</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Tableau de bord</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Messagerie</li>
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
                                <input type="text" class="form-control border-start-0" placeholder="Rechercher un porteur ou projet..." id="searchConv" onkeyup="filterConversations()">
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
                                        <p class="mt-3 text-muted">Aucune discussion active</p>
                                        <small class="text-muted">Validez un projet pour ouvrir une discussion</small>
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
                            <div class="d-flex align-items-center" id="activeChatHeader" style="display: none;">
                                <button type="button" class="msg-back-btn" onclick="showSidebarMobile()" title="Retour à la liste" aria-label="Retour à la liste">
                                    <i class="fe fe-arrow-left"></i>
                                </button>
                                <div class="header-avatar">
                                    <span class="header-initials" id="headerInitials">?</span>
                                    <img src="../../asset/images/profiles/1.jpg" alt="" id="headerPorteurImg" style="display:none;">
                                </div>
                                <div class="header-info">
                                    <div class="header-name" id="headerPorteurName">Chargement...</div>
                                    <span class="header-sub" id="headerProjectTitle">Projet</span>
                                    <span class="header-sub" id="headerStatusText" style="display:none;"><span class="header-status-dot online"></span><span class="header-status-text">En ligne</span></span>
                                </div>
                            </div>
                            <div id="noChatHeader" class="d-flex align-items-center">
                                <h5 class="mb-0 fw-semibold text-muted">Sélectionnez une discussion</h5>
                            </div>
                            <div class="ms-auto">
                                <div class="dropdown">
                                    <a href="javascript:void(0)" class="nav-link icon" data-bs-toggle="dropdown"><i class="fe fe-more-vertical"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="loadMessages()"><i class="fe fe-refresh-cw me-2"></i> Actualiser</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="viewProject()"><i class="fe fe-eye me-2"></i> Voir le projet</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Messages Area -->
                        <div class="chat-body chat-body-custom" id="chatMessages">
                            <!-- Empty state -->
                            <div id="chatEmptyState">
                                <div class="mb-4">
                                    <i class="fe fe-message-circle"></i>
                                </div>
                                <h4>Bienvenue dans votre messagerie sécurisée</h4>
                                <p>Sélectionnez une discussion dans la liste de gauche pour commencer à échanger avec les porteurs de projets.</p>
                            </div>
                            
                            <!-- Loading -->
                            <div id="loadingMessages" class="text-center py-5" style="display:none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Chargement...</span>
                                </div>
                            </div>
                            
                            <!-- Messages loaded here -->
                        </div>

                        <!-- Chat Input Area -->
                        <div class="card-footer chat-footer border-top" id="chatInputArea" style="display:none;">
                            <form id="messageForm" enctype="multipart/form-data">
                                <div class="input-group">
                                    <button type="button" class="btn-attach" onclick="document.getElementById('fileInput').click()" title="Joindre un fichier" aria-label="Joindre un fichier">
                                        <i class="fe fe-plus"></i>
                                    </button>
                                    <input type="file" id="fileInput" style="display:none;" multiple onchange="handleFileSelect(this)">
                                    <textarea class="form-control" id="messageInput" placeholder="Écrivez votre message ici..." rows="1" onkeydown="checkSubmit(event)"></textarea>
                                    <button type="button" class="btn-record" id="btnRecord" title="Enregistrer un message audio" aria-label="Enregistrer un message audio" onclick="toggleAudioRecording(this)">
                                        <i class="fe fe-mic"></i>
                                    </button>
                                    <button type="button" class="btn-send" onclick="sendMessage()" title="Envoyer" aria-label="Envoyer">
                                        <i class="fe fe-send"></i>
                                    </button>
                                </div>
                                <div id="fileBadge" style="display:none;">
                                    <span id="fileCount">0</span> fichier(s) <i class="fe fe-x ms-1 cursor-pointer" onclick="clearFiles()"></i>
                                </div>
                                <div id="filePreviews" class="d-flex flex-wrap gap-2 mt-2"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--{ container end }-->
    </div>
</div>

<script src="js/messages.js"></script>
<script>
function showChatMobile() {
    document.querySelector('.msg-col-sidebar')?.classList.add('hide');
    document.querySelector('.msg-col-chat')?.classList.add('show');
}
function showSidebarMobile() {
    document.querySelector('.msg-col-sidebar')?.classList.remove('hide');
    document.querySelector('.msg-col-chat')?.classList.remove('show');
}
// Patch selectConversation if it exists in messages.js
document.addEventListener('DOMContentLoaded', function() {
    const origSelect = window.selectConversation;
    if (typeof origSelect === 'function') {
        window.selectConversation = function(id) {
            showChatMobile();
            return origSelect(id);
        };
    }
    // Edge case: if a conversation was already selected via URL param (?id=X)
    // before the patch ran, ensure chat is visible on mobile
    if (typeof currentConversationId !== 'undefined' && currentConversationId) {
        showChatMobile();
    }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
