<?php

if (!function_exists('dashboard_messages_prepare')) {
  function dashboard_messages_prepare(array $conversations, array $presenceMap, array $threadMap): array
  {
    $conversationCards = [];
    $defaultConversationId = '';
    $unreadTotal = 0;

    foreach ($conversations as $index => $conversation) {
      $name = isset($conversation['name']) ? (string) $conversation['name'] : 'Conversation';
      $conversationId = 'thread-' . ($index + 1);
      $conversationStatus = isset($conversation['status']) ? (string) $conversation['status'] : (isset($presenceMap[$name]) ? (string) $presenceMap[$name] : 'En ligne');
      $unreadCount = isset($conversation['unread']) ? (int) $conversation['unread'] : 0;

      $conversationCards[] = [
        'id' => $conversationId,
        'name' => $name,
        'role' => isset($conversation['role']) ? (string) $conversation['role'] : '',
        'avatar' => isset($conversation['avatar']) ? (string) $conversation['avatar'] : strtoupper(substr($name, 0, 2)),
        'snippet' => isset($conversation['snippet']) ? (string) $conversation['snippet'] : '',
        'time' => isset($conversation['time']) ? (string) $conversation['time'] : '',
        'unread' => $unreadCount,
        'status' => $conversationStatus,
        'messages' => isset($threadMap[$name]) && is_array($threadMap[$name]) ? $threadMap[$name] : [],
        'active' => !empty($conversation['active']),
      ];

      $unreadTotal += $unreadCount;

      if ($defaultConversationId === '' || !empty($conversation['active'])) {
        $defaultConversationId = $conversationId;
      }
    }

    $conversationPayload = json_encode($conversationCards, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    if ($conversationPayload === false) {
      $conversationPayload = '[]';
    }

    return [
      'cards' => $conversationCards,
      'default_id' => $defaultConversationId,
      'payload' => $conversationPayload,
      'unread_total' => $unreadTotal,
    ];
  }

  function dashboard_messages_styles(): string
  {
    return <<<'CSS'
    .whatsapp-page {
      padding-top: 0.25rem;
      padding-bottom: 0.5rem;
    }

    .whatsapp-shell {
      display: grid;
      grid-template-columns: minmax(280px, 30%) minmax(0, 1fr);
      min-height: clamp(620px, calc(100vh - 180px), 760px);
      padding: 0;
      overflow: hidden;
      border-radius: 24px;
    }

    .whatsapp-sidebar {
      display: flex;
      flex-direction: column;
      min-width: 0;
      border-right: 1px solid var(--border-color-2);
      background: #fbfbfb;
    }

    .whatsapp-sidebar__top {
      display: grid;
      gap: 10px;
      padding: 14px 14px 10px;
      border-bottom: 1px solid var(--border-color-2);
      background: #fff;
    }

    .whatsapp-search {
      display: flex;
      align-items: center;
      gap: 8px;
      min-height: 40px;
      padding: 7px 10px;
      border-radius: 12px;
      border: 1px solid var(--border-color-2);
      background: #f8fafa;
    }

    .whatsapp-search i {
      color: var(--p-color);
      font-size: 13px;
    }

    .whatsapp-search input {
      flex: 1 1 auto;
      min-width: 0;
      border: 0;
      outline: none;
      box-shadow: none;
      background: transparent;
      color: var(--text-heading-color);
      padding: 0;
      font-size: 13px;
    }

    .whatsapp-conversation-list {
      flex: 1 1 auto;
      min-height: 0;
      overflow-y: auto;
      padding: 6px;
      display: grid;
      gap: 2px;
    }

    .whatsapp-conversation {
      width: 100%;
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 8px 10px;
      border: 0;
      border-radius: 14px;
      background: transparent;
      text-align: left;
      cursor: pointer;
      transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .whatsapp-conversation:hover {
      background: rgba(252, 160, 40, 0.08);
    }

    .whatsapp-conversation.is-active {
      background: rgba(0, 196, 134, 0.12);
    }

    .whatsapp-avatar {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: rgba(252, 160, 40, 0.16);
      color: var(--primary-color-3);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      font-weight: 700;
      flex-shrink: 0;
    }

    .whatsapp-conversation__body {
      flex: 1 1 auto;
      min-width: 0;
    }

    .whatsapp-conversation__top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      margin-bottom: 2px;
    }

    .whatsapp-name {
      margin: 0;
      font-size: 13px;
      font-weight: 600;
      color: var(--text-heading-color);
    }

    .whatsapp-time {
      color: var(--p-color);
      font-size: 10px;
      white-space: nowrap;
    }

    .whatsapp-snippet {
      margin: 0;
      color: var(--p-color);
      font-size: 11px;
      line-height: 1.35;
      display: -webkit-box;
      -webkit-line-clamp: 1;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .whatsapp-unread {
      min-width: 20px;
      height: 20px;
      padding: 0 6px;
      border-radius: 999px;
      background: var(--primary-color-1);
      color: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
      font-weight: 700;
      flex-shrink: 0;
    }

    .whatsapp-thread {
      min-width: 0;
      display: flex;
      flex-direction: column;
      background: linear-gradient(180deg, #f8faf9 0%, #f3f7f5 100%);
    }

    .whatsapp-thread__header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      padding: 14px 18px;
      border-bottom: 1px solid var(--border-color-2);
      background: rgba(255, 255, 255, 0.94);
      backdrop-filter: blur(8px);
    }

    .whatsapp-thread__identity {
      min-width: 0;
      display: flex;
      align-items: center;
      gap: 12px;
      flex: 1 1 auto;
    }

    .whatsapp-thread__copy {
      flex: 1 1 auto;
      min-width: 0;
      overflow: hidden;
    }

    .whatsapp-thread__name {
      display: block;
      min-width: 0;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .whatsapp-back {
      width: 38px;
      height: 38px;
      border: 0;
      border-radius: 50%;
      display: none;
      align-items: center;
      justify-content: center;
      background: rgba(0, 72, 220, 0.08);
      color: var(--primary-color-2);
      flex-shrink: 0;
    }

    .whatsapp-status {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: var(--primary-color-1);
      font-size: 12px;
      font-weight: 500;
      max-width: 100%;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .whatsapp-status::before {
      content: '';
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: currentColor;
      opacity: 0.75;
    }

    .whatsapp-status.is-offline {
      color: var(--p-color);
    }

    .whatsapp-status.is-away {
      color: var(--primary-color-3);
    }

    .whatsapp-icon-btn {
      width: 38px;
      height: 38px;
      border: 0;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(0, 72, 220, 0.08);
      color: var(--primary-color-2);
      transition: transform 0.2s ease, background-color 0.2s ease;
    }

    .whatsapp-icon-btn:hover {
      transform: translateY(-1px);
      background: rgba(0, 72, 220, 0.14);
    }

    .whatsapp-thread__messages {
      flex: 1 1 auto;
      min-height: 0;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 12px;
      padding: 18px;
    }

    .whatsapp-day {
      align-self: center;
      padding: 6px 12px;
      border-radius: 999px;
      background: rgba(0, 72, 220, 0.08);
      color: var(--primary-color-2);
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.02em;
    }

    .whatsapp-message {
      display: flex;
      flex-direction: column;
      gap: 6px;
      align-self: flex-start;
      max-width: min(76%, 560px);
    }

    .whatsapp-message--outgoing {
      align-self: flex-end;
    }

    .whatsapp-bubble {
      padding: 12px 14px;
      border-radius: 18px 18px 18px 6px;
      background: #fff;
      color: var(--text-heading-color);
      box-shadow: 0 8px 20px rgba(6, 42, 38, 0.06);
    }

    .whatsapp-message--outgoing .whatsapp-bubble {
      border-radius: 18px 18px 6px 18px;
      background: rgba(0, 196, 134, 0.14);
    }

    .whatsapp-message__meta {
      display: inline-flex;
      align-items: center;
      justify-content: flex-end;
      gap: 6px;
      color: var(--p-color);
      font-size: 10px;
      padding: 0 4px;
    }

    .whatsapp-checks {
      display: inline-flex;
      color: var(--p-color);
    }

    .whatsapp-checks.is-read {
      color: var(--primary-color-2);
    }

    .whatsapp-attachment,
    .whatsapp-voice {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .whatsapp-attachment__icon,
    .whatsapp-voice__icon {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(0, 72, 220, 0.12);
      color: var(--primary-color-2);
      flex-shrink: 0;
    }

    .whatsapp-voice__wave {
      display: inline-flex;
      align-items: center;
      gap: 3px;
      min-width: 80px;
    }

    .whatsapp-voice__wave span {
      width: 3px;
      border-radius: 999px;
      background: currentColor;
      opacity: 0.55;
    }

    .whatsapp-voice__wave span:nth-child(1) { height: 10px; }
    .whatsapp-voice__wave span:nth-child(2) { height: 16px; }
    .whatsapp-voice__wave span:nth-child(3) { height: 12px; }
    .whatsapp-voice__wave span:nth-child(4) { height: 18px; }
    .whatsapp-voice__wave span:nth-child(5) { height: 11px; }
    .whatsapp-voice__wave span:nth-child(6) { height: 15px; }

    .whatsapp-compose {
      padding: 14px 18px 18px;
      border-top: 1px solid var(--border-color-2);
      background: rgba(255, 255, 255, 0.94);
    }

    .whatsapp-compose__bar {
      display: flex;
      align-items: center;
      gap: 4px;
      padding: 8px 10px;
      border-radius: 18px;
      border: 1px solid var(--border-color-2);
      background: #fff;
      min-width: 0;
    }

    .whatsapp-compose__input {
      flex: 1 1 100%;
      min-width: 0;
      width: 100%;
      border: 0;
      outline: none;
      box-shadow: none;
      background: transparent;
      font-size: 14px;
      color: var(--text-heading-color);
    }

    .whatsapp-send {
      width: 42px;
      height: 42px;
      min-width: 42px;
      border-radius: 50%;
      padding: 0 !important;
      justify-content: center !important;
    }

    .whatsapp-typing {
      align-self: flex-start;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 10px 14px;
      border-radius: 999px;
      background: #fff;
      border: 1px solid var(--border-color-2);
      color: var(--p-color);
      font-size: 12px;
      box-shadow: 0 4px 12px rgba(6, 42, 38, 0.04);
    }

    .whatsapp-typing__dots {
      display: inline-flex;
      gap: 4px;
    }

    .whatsapp-typing__dots span {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: currentColor;
      opacity: 0.35;
      animation: whatsappTyping 1.2s infinite ease-in-out;
    }

    .whatsapp-typing__dots span:nth-child(2) {
      animation-delay: 0.15s;
    }

    .whatsapp-typing__dots span:nth-child(3) {
      animation-delay: 0.3s;
    }

    @keyframes whatsappTyping {
      0%, 80%, 100% {
        transform: scale(0.85);
        opacity: 0.3;
      }

      40% {
        transform: scale(1);
        opacity: 0.85;
      }
    }

    @media (max-width: 991.98px) {
      .whatsapp-shell {
        min-height: calc(100vh - 160px);
      }
    }

    @media (max-width: 767.98px) {
      .whatsapp-shell {
        grid-template-columns: 1fr;
        min-height: calc(100vh - 132px);
      }

      .whatsapp-sidebar,
      .whatsapp-thread {
        border-right: 0;
      }

      .whatsapp-thread {
        display: none;
      }

      .whatsapp-shell.is-thread-open .whatsapp-sidebar {
        display: none;
      }

      .whatsapp-shell.is-thread-open .whatsapp-thread {
        display: flex;
      }

      .whatsapp-back {
        display: inline-flex;
      }

      .whatsapp-thread__header,
      .whatsapp-thread__messages,
      .whatsapp-compose {
        padding-left: 10px;
        padding-right: 10px;
      }

      .whatsapp-thread__header {
        display: grid;
        grid-template-columns: 10% minmax(0, 70%) 10% 10%;
        align-items: center;
        flex-wrap: nowrap;
        gap: 0;
        padding-top: 8px;
        padding-bottom: 8px;
      }

      .whatsapp-thread__identity {
        grid-column: 2;
        gap: 5px;
        flex: 1 1 auto;
        width: 100%;
      }

      .whatsapp-thread__copy {
        min-width: 0;
      }

      .whatsapp-thread__identity .whatsapp-avatar {
        width: 32px;
        height: 32px;
        font-size: 9px;
      }

      .whatsapp-thread__name {
        font-size: 12px;
        line-height: 1.15;
        margin-bottom: 1px !important;
      }

      .whatsapp-status {
        font-size: 9px;
        gap: 3px;
      }

      .whatsapp-status::before {
        width: 5px;
        height: 5px;
      }

      .whatsapp-back,
      .whatsapp-thread__header .whatsapp-icon-btn {
        width: 100%;
        height: 26px;
        min-width: 0;
        font-size: 11px;
        border-radius: 8px;
        background: transparent;
        padding: 0;
      }

      .whatsapp-back {
        grid-column: 1;
      }

      .whatsapp-thread__header .whatsapp-icon-btn[aria-label="Appel vocal"] {
        grid-column: 3;
      }

      .whatsapp-thread__header .whatsapp-icon-btn[aria-label="Appel video"] {
        grid-column: 4;
      }

      .whatsapp-message {
        max-width: 88%;
      }

      .whatsapp-compose {
        padding-top: 6px;
        padding-bottom: 8px;
      }

      .whatsapp-compose__bar {
        display: grid;
        grid-template-columns: 10% 10% minmax(0, 60%) 10% 10%;
        align-items: center;
        gap: 0;
        padding: 4px 5px;
        border-radius: 14px;
      }

      .whatsapp-compose__bar .whatsapp-icon-btn {
        width: 100%;
        height: 26px;
        min-width: 0;
        font-size: 11px;
        border-radius: 8px;
        background: transparent;
        padding: 0;
      }

      .whatsapp-compose__input {
        font-size: 12px;
        padding: 0 2px;
        flex: 1 1 100%;
        width: 100%;
      }

      .whatsapp-send {
        width: 100%;
        height: 26px;
        min-width: 0;
        font-size: 11px;
        padding: 0 !important;
        border-radius: 8px;
      }
    }
CSS;
  }

  function dashboard_messages_scripts(string $conversationPayload): string
  {
    $scripts = <<<'JS'
(function () {
  var conversationData = __CONVERSATION_DATA__;
  var shell = document.querySelector('[data-whatsapp-shell]');
  if (!shell || !conversationData.length) {
    return;
  }

  var conversationLookup = {};
  conversationData.forEach(function (conversation) {
    conversationLookup[conversation.id] = conversation;
  });

  var conversationButtons = Array.prototype.slice.call(shell.querySelectorAll('[data-conversation-id]'));
  var searchInput = shell.querySelector('[data-conversation-search]');
  var backButton = shell.querySelector('[data-thread-back]');
  var headerAvatar = shell.querySelector('[data-thread-avatar]');
  var headerName = shell.querySelector('[data-thread-name]');
  var headerStatus = shell.querySelector('[data-thread-status]');
  var threadBody = shell.querySelector('[data-thread-body]');
  var currentConversationId = shell.getAttribute('data-default-conversation') || conversationData[0].id;

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function normalize(value) {
    var text = String(value || '').toLowerCase();
    if (typeof text.normalize === 'function') {
      text = text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }
    return text;
  }

  function statusClass(status) {
    var value = normalize(status);
    if (value.indexOf('hors') !== -1) {
      return 'whatsapp-status is-offline';
    }
    if (value.indexOf('reunion') !== -1) {
      return 'whatsapp-status is-away';
    }
    return 'whatsapp-status';
  }

  function renderBubble(message) {
    var bubble = '';
    var meta = '<div class="whatsapp-message__meta"><span>' + escapeHtml(message.time) + '</span>';

    if (message.type === 'attachment') {
      bubble = '' +
        '<div class="whatsapp-bubble">' +
          '<div class="whatsapp-attachment">' +
            '<span class="whatsapp-attachment__icon"><i class="bi bi-file-earmark-text"></i></span>' +
            '<div>' +
              '<strong>' + escapeHtml(message.title) + '</strong>' +
              '<p class="mb-0 text-muted small">' + escapeHtml(message.meta) + '</p>' +
            '</div>' +
          '</div>' +
        '</div>';
    } else if (message.type === 'voice') {
      bubble = '' +
        '<div class="whatsapp-bubble">' +
          '<div class="whatsapp-voice">' +
            '<span class="whatsapp-voice__icon"><i class="bi bi-play-fill"></i></span>' +
            '<div class="whatsapp-voice__wave"><span></span><span></span><span></span><span></span><span></span><span></span></div>' +
            '<strong>' + escapeHtml(message.duration) + '</strong>' +
          '</div>' +
        '</div>';
    } else {
      bubble = '<div class="whatsapp-bubble"><p class="mb-0">' + escapeHtml(message.text) + '</p></div>';
    }

    if (message.outgoing) {
      meta += '<span class="whatsapp-checks' + (message.status === 'Lu' ? ' is-read' : '') + '"><i class="bi ' + (message.status === 'Lu' ? 'bi-check2-all' : 'bi-check2') + '"></i></span>';
    }

    meta += '</div>';

    return '<article class="whatsapp-message' + (message.outgoing ? ' whatsapp-message--outgoing' : '') + '">' + bubble + meta + '</article>';
  }

  function renderThread(conversationId) {
    var conversation = conversationLookup[conversationId] || conversationData[0];
    if (!conversation) {
      return;
    }

    currentConversationId = conversation.id;
    headerAvatar.textContent = conversation.avatar;
    headerName.textContent = conversation.name;
    headerStatus.textContent = conversation.status;
    headerStatus.className = statusClass(conversation.status);

    conversationButtons.forEach(function (button) {
      button.classList.toggle('is-active', button.getAttribute('data-conversation-id') === conversation.id);
    });

    var html = '<div class="whatsapp-day">Aujourd hui</div>';
    html += conversation.messages.map(renderBubble).join('');
    html += '' +
      '<div class="whatsapp-typing">' +
        '<span>' + escapeHtml(conversation.name) + ' est en train d ecrire</span>' +
        '<span class="whatsapp-typing__dots"><span></span><span></span><span></span></span>' +
      '</div>';
    threadBody.innerHTML = html;
    threadBody.scrollTop = threadBody.scrollHeight;
  }

  conversationButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      renderThread(button.getAttribute('data-conversation-id'));
      if (window.innerWidth < 768) {
        shell.classList.add('is-thread-open');
      }
    });
  });

  if (searchInput) {
    searchInput.addEventListener('input', function () {
      var query = normalize(searchInput.value);
      conversationButtons.forEach(function (button) {
        var haystack = normalize(button.textContent);
        button.style.display = !query || haystack.indexOf(query) !== -1 ? '' : 'none';
      });
    });
  }

  if (backButton) {
    backButton.addEventListener('click', function () {
      shell.classList.remove('is-thread-open');
    });
  }

  window.addEventListener('resize', function () {
    if (window.innerWidth >= 768) {
      shell.classList.remove('is-thread-open');
    }
  });

  renderThread(currentConversationId);
})();
JS;

    return str_replace('__CONVERSATION_DATA__', $conversationPayload !== '' ? $conversationPayload : '[]', $scripts);
  }

  function dashboard_render_messages_shell(array $conversationCards, string $defaultConversationId, string $escapeFunction, array $options = []): void
  {
    $options = array_merge([
      'sidebar_title' => 'Conversations',
      'sidebar_subtitle' => 'Messagerie interne ALOGOTO',
      'sidebar_badge' => 'Actif',
      'compose_placeholder' => 'Tapez un message',
    ], $options);

    $escapeValue = static function ($value) use ($escapeFunction): string {
      if (is_callable($escapeFunction)) {
        return (string) call_user_func($escapeFunction, (string) $value);
      }

      return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    };

    $activeConversation = null;
    foreach ($conversationCards as $conversation) {
      if (!empty($conversation['active'])) {
        $activeConversation = $conversation;
        break;
      }
    }

    if ($activeConversation === null && isset($conversationCards[0])) {
      $activeConversation = $conversationCards[0];
    }

    if ($activeConversation === null) {
      $activeConversation = [
        'avatar' => 'AL',
        'name' => 'ALOGOTO',
        'status' => 'En ligne',
      ];
    }
?>
        <div class="whatsapp-page">
          <section class="dashboard-card whatsapp-shell" data-whatsapp-shell data-default-conversation="<?php echo $escapeValue($defaultConversationId); ?>">
            <aside class="whatsapp-sidebar">
              <div class="whatsapp-sidebar__top">
                <div class="d-flex align-items-center justify-content-between gap-3">
                  <div>
                    <h6 class="mb-1"><?php echo $escapeValue($options['sidebar_title']); ?></h6>
                    <p class="mb-0 text-muted small"><?php echo $escapeValue($options['sidebar_subtitle']); ?></p>
                  </div>
                  <span class="status-badge status-submitted submitted"><?php echo $escapeValue($options['sidebar_badge']); ?></span>
                </div>

                <label class="whatsapp-search" for="messageConversationSearch">
                  <i class="bi bi-search"></i>
                  <input id="messageConversationSearch" type="search" placeholder="Rechercher une discussion" aria-label="Rechercher une discussion" data-conversation-search />
                </label>
              </div>

              <div class="whatsapp-conversation-list">
<?php foreach ($conversationCards as $conversation): ?>
                <button type="button" class="whatsapp-conversation<?php echo !empty($conversation['active']) ? ' is-active' : ''; ?>" data-conversation-id="<?php echo $escapeValue($conversation['id']); ?>">
                  <span class="whatsapp-avatar"><?php echo $escapeValue($conversation['avatar']); ?></span>
                  <span class="whatsapp-conversation__body">
                    <span class="whatsapp-conversation__top">
                      <span class="whatsapp-name"><?php echo $escapeValue($conversation['name']); ?></span>
                      <span class="whatsapp-time"><?php echo $escapeValue($conversation['time']); ?></span>
                    </span>
                    <span class="whatsapp-snippet"><?php echo $escapeValue($conversation['snippet']); ?></span>
                  </span>
<?php if (($conversation['unread'] ?? 0) > 0): ?>
                  <span class="whatsapp-unread"><?php echo $escapeValue((string) $conversation['unread']); ?></span>
<?php endif; ?>
                </button>
<?php endforeach; ?>
              </div>
            </aside>

            <section class="whatsapp-thread">
              <header class="whatsapp-thread__header">
                <button type="button" class="whatsapp-back" aria-label="Retour aux conversations" data-thread-back>
                  <i class="bi bi-arrow-left"></i>
                </button>
                <div class="whatsapp-thread__identity">
                  <span class="whatsapp-avatar" data-thread-avatar><?php echo $escapeValue($activeConversation['avatar']); ?></span>
                  <div class="whatsapp-thread__copy">
                    <h6 class="whatsapp-thread__name mb-1" data-thread-name><?php echo $escapeValue($activeConversation['name']); ?></h6>
                    <p class="whatsapp-status mb-0" data-thread-status><?php echo $escapeValue($activeConversation['status']); ?></p>
                  </div>
                </div>
                <button type="button" class="whatsapp-icon-btn" aria-label="Appel vocal">
                  <i class="bi bi-telephone"></i>
                </button>
                <button type="button" class="whatsapp-icon-btn" aria-label="Appel video">
                  <i class="bi bi-camera-video"></i>
                </button>
              </header>

              <div class="whatsapp-thread__messages" data-thread-body></div>

              <footer class="whatsapp-compose">
                <div class="whatsapp-compose__bar">
                  <button type="button" class="whatsapp-icon-btn" aria-label="Emoji">
                    <i class="bi bi-emoji-smile"></i>
                  </button>
                  <button type="button" class="whatsapp-icon-btn" aria-label="Piece jointe">
                    <i class="bi bi-paperclip"></i>
                  </button>
                  <input type="text" class="whatsapp-compose__input" placeholder="<?php echo $escapeValue($options['compose_placeholder']); ?>" aria-label="<?php echo $escapeValue($options['compose_placeholder']); ?>" />
                  <button type="button" class="whatsapp-icon-btn" aria-label="Message vocal">
                    <i class="bi bi-mic"></i>
                  </button>
                  <button type="button" class="dashboard-btn-primary btn-dashboard primary whatsapp-send" aria-label="Envoyer">
                    <i class="bi bi-send-fill"></i>
                  </button>
                </div>
              </footer>
            </section>
          </section>
        </div>
<?php
  }
}
