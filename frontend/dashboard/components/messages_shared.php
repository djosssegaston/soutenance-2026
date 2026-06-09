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
  padding: 0.25rem 0 0.5rem;
}

.whatsapp-shell {
  display: grid;
  grid-template-columns: minmax(300px, 34%) minmax(0, 1fr);
  min-height: clamp(640px, calc(100vh - 170px), 860px);
  height: clamp(640px, calc(100vh - 170px), 860px);
  overflow: hidden;
  position: relative;
  border-radius: 12px;
  border: 1px solid var(--border-color-2);
  background: #ffffff;
  box-shadow: 0 8px 32px rgba(6, 42, 38, 0.1);
}

.lucide-icon {
  width: 18px;
  height: 18px;
  stroke-width: 1.5;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: inherit;
}

.lucide-icon svg {
  width: 100%;
  height: 100%;
  display: block;
  stroke: currentColor;
  fill: none;
}

.whatsapp-sidebar {
  position: relative;
  display: flex;
  flex-direction: column;
  min-width: 0;
  min-height: 0;
  height: 100%;
  border-right: 1px solid var(--border-color-2);
  background: #f0f0f0;
}

.whatsapp-sidebar__top {
  display: grid;
  gap: 12px;
  padding: 16px;
  border-bottom: 1px solid var(--border-color-2);
  background: #fff;
  position: sticky;
  top: 0;
  z-index: 3;
}

.whatsapp-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.whatsapp-heading h6 {
  margin: 0 0 2px;
  font-size: 15px;
  font-weight: 600;
  color: var(--text-heading-color);
}

.whatsapp-heading p {
  font-size: 12px;
}

.whatsapp-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 999px;
  background: rgba(0, 196, 134, 0.16);
  color: var(--primary-color-1);
  font-weight: 600;
  font-size: 12px;
}

.whatsapp-search {
  display: flex;
  align-items: center;
  gap: 10px;
  min-height: 40px;
  padding: 8px 12px;
  border-radius: 20px;
  border: 1px solid var(--border-color-2);
  background: #f5f5f5;
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

.whatsapp-search input::placeholder {
  color: #999;
}

.whatsapp-conversation-list {
  flex: 1 1 auto;
  min-height: 0;
  overflow-y: auto;
  padding: 4px;
  display: grid;
  gap: 0;
  overscroll-behavior: contain;
  -webkit-overflow-scrolling: touch;
  scroll-behavior: smooth;
  scrollbar-gutter: stable;
}

.whatsapp-conversation {
  width: 100%;
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 12px;
  padding: 8px 8px;
  margin: 0 8px;
  border: 0;
  border-radius: 8px;
  background: transparent;
  text-align: left;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.whatsapp-animate-item {
  opacity: 1;
  transform: none;
  transition: opacity 0.4s ease, transform 0.4s ease;
  will-change: opacity, transform;
}

.whatsapp-shell.is-animate-enabled .whatsapp-animate-item {
  opacity: 0;
  transform: translateY(10px);
}

.whatsapp-shell.is-animate-enabled .whatsapp-animate-item.is-visible {
  opacity: 1;
  transform: translateY(0);
}

@media (prefers-reduced-motion: reduce) {
  .whatsapp-animate-item {
    transition: none;
    transform: none;
  }
}

.whatsapp-conversation:hover {
  background: #f0f0f0;
}

.whatsapp-conversation.is-active {
  background: rgba(0, 196, 134, 0.15);
}

.whatsapp-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: var(--primary-color-3);
  color: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
  flex-shrink: 0;
  box-shadow: 0 6px 14px rgba(252, 160, 40, 0.2);
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
  margin-bottom: 4px;
}

.whatsapp-name {
  margin: 0;
  font-size: 13px;
  font-weight: 500;
  color: var(--text-heading-color);
}

.whatsapp-time {
  color: var(--p-color);
  font-size: 12px;
  white-space: nowrap;
  flex-shrink: 0;
}

.whatsapp-snippet {
  margin: 0;
  color: #999;
  font-size: 12px;
  line-height: 1.3;
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
  font-size: 11px;
  font-weight: 700;
  flex-shrink: 0;
  margin-left: auto;
}

.whatsapp-thread {
  position: relative;
  min-width: 0;
  display: flex;
  flex-direction: column;
  min-height: 0;
  height: 100%;
  background-color: #ffffff;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cdefs%3E%3Cstyle%3E.icon { fill: %23FCA028; opacity: 0.08; }%3C/style%3E%3C/defs%3E%3Cg%3E%3Ctext x='10' y='30' class='icon' font-size='24'%3E💬%3C/text%3E%3Ctext x='80' y='60' class='icon' font-size='20'%3E☎️%3C/text%3E%3Ctext x='140' y='40' class='icon' font-size='18'%3E📎%3C/text%3E%3Ctext x='30' y='100' class='icon' font-size='22'%3E📄%3C/text%3E%3Ctext x='120' y='110' class='icon' font-size='20'%3E🔊%3C/text%3E%3Ctext x='60' y='150' class='icon' font-size='24'%3E✔️%3C/text%3E%3Ctext x='160' y='170' class='icon' font-size='20'%3E⏰%3C/text%3E%3Ctext x='15' y='180' class='icon' font-size='18'%3E📍%3C/text%3E%3C/g%3E%3C/svg%3E");
  background-size: 200px 200px;
  background-position: 0 0;
  background-repeat: repeat;
  background-attachment: fixed;
}

.whatsapp-thread__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 12px 16px;
  border-bottom: 1px solid rgba(0, 196, 134, 0.2);
  background: rgba(255, 255, 255, 0.9);
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
  margin: 0 0 3px;
  font-size: 13px;
  font-weight: 500;
  color: var(--text-heading-color);
}

.whatsapp-back {
  width: 36px;
  height: 36px;
  border: 0;
  border-radius: 50%;
  display: none;
  align-items: center;
  justify-content: center;
  background: transparent;
  color: var(--primary-color-1);
  flex-shrink: 0;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.whatsapp-back:hover {
  background: #f0f0f0;
}

.whatsapp-status {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  color: #999;
  font-size: 12px;
  font-weight: 400;
  max-width: 100%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.whatsapp-status::before {
  content: '';
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: currentColor;
  opacity: 1;
  flex-shrink: 0;
}

.whatsapp-status.is-offline {
  color: #999;
}

.whatsapp-status.is-offline::before {
  background: #999;
}

.whatsapp-status.is-away {
  color: var(--primary-color-3);
}

.whatsapp-status.is-away::before {
  background: var(--primary-color-3);
}

.whatsapp-icon-btn {
  width: 36px;
  height: 36px;
  aspect-ratio: 1 / 1;
  border: 0;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  background: transparent;
  color: var(--primary-color-1);
  cursor: pointer;
  transition: background-color 0.2s ease;
  flex-shrink: 0;
}

.whatsapp-icon-btn:hover {
  background: #f0f0f0;
}

.whatsapp-thread__messages {
  flex: 1 1 auto;
  min-height: 0;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 16px;
  background: transparent;
  overscroll-behavior: contain;
  -webkit-overflow-scrolling: touch;
  scroll-behavior: smooth;
  scrollbar-gutter: stable;
}

.whatsapp-day {
  align-self: center;
  padding: 6px 12px;
  border-radius: 12px;
  background: #f0f0f0;
  color: #666;
  font-size: 12px;
  font-weight: 500;
  margin: 8px 0;
}

.whatsapp-message {
  display: flex;
  flex-direction: column;
  gap: 4px;
  align-self: flex-start;
  max-width: min(70%, 600px);
}

.whatsapp-message--outgoing {
  align-self: flex-end;
}

.whatsapp-bubble {
  padding: 8px 12px;
  border-radius: 12px 12px 12px 0;
  background: #e8f5f0;
  color: var(--text-heading-color);
  word-break: break-word;
  font-size: 14px;
  line-height: 1.4;
}

.whatsapp-message--outgoing .whatsapp-bubble {
  border-radius: 12px 12px 0 12px;
  background: var(--primary-color-1);
  color: #fff;
}

.whatsapp-bubble p {
  margin: 0;
}

.whatsapp-bubble--file {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 10px;
  align-items: center;
  padding: 10px 12px;
  background: #e8f5f0;
}

.whatsapp-message--outgoing .whatsapp-bubble--file {
  background: rgba(0, 196, 134, 0.2);
}

.whatsapp-bubble--file.is-pdf {
  background: rgba(243, 81, 32, 0.1);
}

.whatsapp-message--outgoing .whatsapp-bubble--file.is-pdf {
  background: rgba(243, 81, 32, 0.15);
}

.whatsapp-file__meta {
  margin: 0;
  color: var(--p-color);
  font-size: 12px;
}

.whatsapp-voice {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 10px;
  align-items: center;
}

.whatsapp-wave {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  min-width: 100px;
}

.whatsapp-wave span {
  width: 2px;
  border-radius: 1px;
  background: currentColor;
  opacity: 0.7;
}

.whatsapp-wave span:nth-child(1) { height: 8px; }
.whatsapp-wave span:nth-child(2) { height: 12px; }
.whatsapp-wave span:nth-child(3) { height: 10px; }
.whatsapp-wave span:nth-child(4) { height: 14px; }
.whatsapp-wave span:nth-child(5) { height: 9px; }
.whatsapp-wave span:nth-child(6) { height: 11px; }

.whatsapp-message__meta {
  display: inline-flex;
  align-items: center;
  justify-content: flex-end;
  gap: 4px;
  color: #999;
  font-size: 12px;
  padding: 0 4px;
}

.whatsapp-message--outgoing .whatsapp-message__meta {
  color: rgba(255, 255, 255, 0.8);
}

.whatsapp-checks {
  display: inline-flex;
  align-items: center;
  color: #999;
  font-size: 14px;
}

.whatsapp-message--outgoing .whatsapp-checks {
  color: rgba(255, 255, 255, 0.8);
}

.whatsapp-checks.is-read {
  color: #00C486;
}

.whatsapp-message--outgoing .whatsapp-checks.is-read {
  color: rgba(255, 255, 255, 0.95);
}

.whatsapp-typing {
  align-self: flex-start;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 12px 12px 12px 0;
  background: #e8f5f0;
  color: var(--text-heading-color);
  font-size: 13px;
}

.whatsapp-typing__dots {
  display: inline-flex;
  gap: 4px;
}

.whatsapp-typing__dots span {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--primary-color-1);
  opacity: 0.5;
  animation: whatsappTyping 1.2s infinite ease-in-out;
}

.whatsapp-typing__dots span:nth-child(2) { animation-delay: 0.15s; }
.whatsapp-typing__dots span:nth-child(3) { animation-delay: 0.3s; }

.whatsapp-compose {
  padding: 12px 16px;
  border-top: 1px solid rgba(0, 196, 134, 0.2);
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
}

.whatsapp-compose__bar {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  border-radius: 20px;
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
  resize: none;
  max-height: 100px;
}

.whatsapp-compose__input::placeholder {
  color: #ccc;
}

.whatsapp-send {
  width: 36px;
  height: 36px;
  min-width: 36px;
  aspect-ratio: 1 / 1;
  border-radius: 50%;
  padding: 0 !important;
  border: none;
  background: var(--primary-color-1) !important;
  color: #fff !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer;
  transition: transform 0.2s ease, filter 0.2s ease;
  flex-shrink: 0;
}

.whatsapp-send:hover {
  filter: brightness(1.1);
  transform: scale(1.05);
}

@keyframes whatsappTyping {
  0%, 60%, 100% {
    transform: translateY(0);
    opacity: 0.6;
  }
  30% {
    transform: translateY(-8px);
    opacity: 1;
  }
}

@media (max-width: 1199.98px) {
  .whatsapp-shell {
    grid-template-columns: minmax(260px, 35%) minmax(0, 1fr);
    min-height: calc(100vh - 160px);
    height: calc(100vh - 160px);
  }

  .whatsapp-thread__header {
    padding: 10px 14px;
  }

  .whatsapp-thread__messages {
    padding: 14px;
  }

  .whatsapp-compose {
    padding: 10px 14px;
  }
}

@media (max-width: 991.98px) {
  .whatsapp-shell {
    grid-template-columns: minmax(240px, 40%) minmax(0, 1fr);
  }

  .whatsapp-conversation {
    padding: 6px 8px;
  }

  .whatsapp-avatar {
    width: 40px;
    height: 40px;
    font-size: 11px;
  }
}

@media (max-width: 767.98px) {
  .whatsapp-shell {
    grid-template-columns: 1fr;
    min-height: calc(100vh - 110px);
    height: calc(100vh - 110px);
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

  .whatsapp-thread__header {
    padding: 10px 12px;
    gap: 6px;
  }

  .whatsapp-thread__identity {
    flex: 1;
  }

  .whatsapp-avatar {
    width: 36px;
    height: 36px;
    font-size: 10px;
  }

  .whatsapp-thread__name {
    font-size: 12px;
  }

  .whatsapp-status {
    font-size: 11px;
  }

  .whatsapp-thread__messages {
    gap: 6px;
    padding: 12px;
  }

  .whatsapp-message {
    max-width: 85%;
  }

  .whatsapp-bubble {
    padding: 7px 10px;
    font-size: 13px;
  }

  .whatsapp-compose {
    padding: 10px 12px;
  }

  .whatsapp-compose__bar {
    padding: 6px 8px;
    gap: 6px;
  }

  .whatsapp-send {
    width: 32px;
    height: 32px;
    min-width: 32px;
  }

  .whatsapp-icon-btn {
    width: 30px;
    height: 30px;
  }

  .whatsapp-icon-btn .lucide-icon,
  .whatsapp-send .lucide-icon {
    width: 16px;
    height: 16px;
  }

  .whatsapp-sidebar__top {
    padding: 12px;
    gap: 10px;
  }

  .whatsapp-heading h6 {
    font-size: 14px;
  }

  .whatsapp-search {
    min-height: 36px;
    padding: 6px 10px;
  }
}

@media (max-width: 480px) {
  .whatsapp-heading h6 {
    font-size: 12px;
  }

  .whatsapp-heading p {
    font-size: 11px;
  }

  .whatsapp-message {
    max-width: 90%;
  }

  .whatsapp-thread__header {
    gap: 4px;
    padding: 8px 10px;
  }

  .whatsapp-compose__bar {
    padding: 6px;
    gap: 5px;
  }
}

/* Overrides: style WhatsApp-like split + orange icon pattern */
.whatsapp-sidebar,
.whatsapp-conversation-list {
  background: #f4f6f7;
}

.whatsapp-sidebar,
.whatsapp-thread {
  overflow: hidden;
}

.whatsapp-thread {
  background-color: #fff9f2;
  background-image:
    linear-gradient(180deg, rgba(252, 160, 40, 0.035), rgba(255, 255, 255, 0)),
    url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140' viewBox='0 0 140 140'%3E%3Cg fill='none' stroke='%23FCA028' stroke-opacity='0.14' stroke-width='2'%3E%3Crect x='10' y='12' width='26' height='18' rx='6'/%3E%3Cpath d='M18 30l8 6'/%3E%3Ccircle cx='70' cy='20' r='7'/%3E%3Crect x='90' y='12' width='24' height='16' rx='5'/%3E%3Crect x='32' y='54' width='28' height='18' rx='6'/%3E%3Cpath d='M40 72l8 6'/%3E%3Ccircle cx='106' cy='64' r='6'/%3E%3Crect x='12' y='94' width='24' height='16' rx='5'/%3E%3Crect x='72' y='92' width='28' height='18' rx='6'/%3E%3Cpath d='M80 110l8 6'/%3E%3Cpath d='M108 36h14m-7-7v14'/%3E%3Cpath d='M20 84h12m-6-6v12'/%3E%3Cpath d='M54 102h10m-5-5v10'/%3E%3C/g%3E%3Cg fill='%23FCA028' fill-opacity='0.1'%3E%3Ccircle cx='22' cy='44' r='2'/%3E%3Ccircle cx='120' cy='46' r='2'/%3E%3Ccircle cx='58' cy='128' r='2'/%3E%3Ccircle cx='120' cy='114' r='2'/%3E%3Ccircle cx='94' cy='88' r='2'/%3E%3C/g%3E%3C/svg%3E");
  background-size: 140px 140px;
  background-repeat: repeat;
  background-attachment: fixed;
  isolation: isolate;
}

.whatsapp-thread::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140' viewBox='0 0 160 160'%3E%3Cg fill='%23FCA028' fill-opacity='0.28'%3E%3Crect x='12' y='14' width='28' height='20' rx='6'/%3E%3Cpolygon points='22,34 30,34 22,42'/%3E%3Ccircle cx='76' cy='26' r='8'/%3E%3Crect x='102' y='18' width='22' height='14' rx='4'/%3E%3Crect x='46' y='70' width='30' height='18' rx='5'/%3E%3Cpolygon points='54,88 60,88 54,94'/%3E%3Ccircle cx='124' cy='82' r='7'/%3E%3Crect x='16' y='110' width='22' height='14' rx='4'/%3E%3Ccircle cx='78' cy='122' r='6'/%3E%3Crect x='112' y='114' width='28' height='18' rx='6'/%3E%3Cpolygon points='120,132 128,132 120,140'/%3E%3C/g%3E%3C/svg%3E");
  background-size: 140px 140px;
  background-position: 0 0;
  background-repeat: repeat;
  opacity: 0.1;
  z-index: 0;
  pointer-events: none;
}

.whatsapp-thread__header,
.whatsapp-thread__messages,
.whatsapp-compose {
  position: relative;
  z-index: 1;
}

/* Thread palette: replace green with brand orange */
.whatsapp-thread .whatsapp-thread__header {
  border-bottom: 1px solid rgba(252, 160, 40, 0.25);
}

.whatsapp-thread .whatsapp-compose {
  border-top: 1px solid rgba(252, 160, 40, 0.25);
}

.whatsapp-thread .whatsapp-icon-btn {
  color: var(--primary-color-3);
}

.whatsapp-thread .whatsapp-send {
  background: var(--primary-color-3) !important;
  color: #fff !important;
}

.whatsapp-thread .whatsapp-message--outgoing .whatsapp-bubble {
  background: var(--primary-color-3);
  color: #fff;
}

.whatsapp-thread .whatsapp-message--outgoing .whatsapp-bubble--file {
  background: rgba(252, 160, 40, 0.2);
  color: #111;
}

.whatsapp-thread .whatsapp-typing__dots span {
  background: var(--primary-color-3);
}

.whatsapp-thread .whatsapp-checks.is-read {
  color: var(--primary-color-3);
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

  function refreshIcons(scope) {
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
      window.lucide.createIcons(scope || shell.querySelectorAll('[data-lucide]'));
    }
  }

  function setupScrollAnimations() {
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion) {
      return;
    }

    var listRoot = shell.querySelector('.whatsapp-conversation-list');
    var threadRoot = shell.querySelector('.whatsapp-thread__messages');
    shell.classList.add('is-animate-enabled');
    var observer = window.IntersectionObserver
      ? new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add('is-visible');
              observer.unobserve(entry.target);
            }
          });
        }, { root: listRoot || null, threshold: 0.1 })
      : null;

    function observeNodes(nodes, root) {
      if (!nodes || !nodes.length) {
        return;
      }
      if (!window.IntersectionObserver) {
        nodes.forEach(function (node) { node.classList.add('is-visible'); });
        return;
      }
      if (observer && root === listRoot) {
        nodes.forEach(function (node) { observer.observe(node); });
        return;
      }
      var localObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            localObserver.unobserve(entry.target);
          }
        });
      }, { root: root || null, threshold: 0.1 });
      nodes.forEach(function (node) { localObserver.observe(node); });
    }

    conversationButtons.forEach(function (button, index) {
      button.style.transitionDelay = (index % 12) * 30 + 'ms';
    });
    observeNodes(conversationButtons, listRoot);

    if (threadRoot) {
      var threadItems = Array.prototype.slice.call(threadRoot.querySelectorAll('.whatsapp-animate-item'));
      threadItems.forEach(function (item, index) {
        item.style.transitionDelay = (index % 12) * 25 + 'ms';
      });
      observeNodes(threadItems, threadRoot);
    }
  }

  (function loadLucide() {
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
      refreshIcons();
      return;
    }
    if (document.querySelector('[data-lucide-loader]')) {
      return;
    }
    var script = document.createElement('script');
    script.src = 'https://unpkg.com/lucide@latest';
    script.async = true;
    script.setAttribute('data-lucide-loader', '1');
    script.onload = function () {
      refreshIcons();
    };
    document.head.appendChild(script);
  })();

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

  function icon(name) {
    return '<span class="lucide-icon" data-lucide="' + name + '" aria-hidden="true"></span>';
  }

  function renderBubble(message) {
    var bubble = '';
    var meta = '<div class="whatsapp-message__meta"><span>' + escapeHtml(message.time) + '</span>';

    var type = message.type || 'text';

    if (type === 'attachment' || type === 'file' || type === 'pdf') {
      var fileIcon = type === 'pdf' ? 'file-text' : 'paperclip';
      bubble =
        '<div class="whatsapp-bubble whatsapp-bubble--file ' + (type === 'pdf' ? 'is-pdf' : '') + '">' +
          '<span class="lucide-icon" data-lucide="' + fileIcon + '" aria-hidden="true"></span>' +
          '<div>' +
            '<strong class="d-block">' + escapeHtml(message.title || 'Fichier') + '</strong>' +
            '<p class="whatsapp-file__meta mb-0">' + escapeHtml(message.meta || '') + '</p>' +
          '</div>' +
        '</div>';
    } else if (type === 'voice' || type === 'audio') {
      bubble =
        '<div class="whatsapp-bubble">' +
          '<div class="whatsapp-voice">' +
            '<button type="button" class="whatsapp-icon-btn" aria-label="Lecture audio">' + icon('play') + '</button>' +
            '<div class="whatsapp-wave"><span></span><span></span><span></span><span></span><span></span><span></span></div>' +
            '<strong>' + escapeHtml(message.duration || '0:15') + '</strong>' +
          '</div>' +
        '</div>';
    } else {
      bubble = '<div class="whatsapp-bubble"><p class="mb-0">' + escapeHtml(message.text) + '</p></div>';
    }

    if (message.outgoing) {
      meta += '<span class="whatsapp-checks' + (message.status === 'Lu' ? ' is-read' : '') + '">' + icon(message.status === 'Lu' ? 'check-check' : 'check') + '</span>';
    }

    meta += '</div>';

    return '<article class="whatsapp-message whatsapp-animate-item' + (message.outgoing ? ' whatsapp-message--outgoing' : '') + '">' + bubble + meta + '</article>';
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
    refreshIcons(threadBody.querySelectorAll('[data-lucide]'));

    setupScrollAnimations();
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
  refreshIcons(shell.querySelectorAll('[data-lucide]'));
  setupScrollAnimations();
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
                <div class="whatsapp-heading">
                  <div>
                    <h6 class="mb-1"><?php echo $escapeValue($options['sidebar_title']); ?></h6>
                    <p class="mb-0 text-muted small"><?php echo $escapeValue($options['sidebar_subtitle']); ?></p>
                  </div>
                  <span class="whatsapp-pill">
                    <span class="lucide-icon" data-lucide="bell"></span>
                    <?php echo $escapeValue($options['sidebar_badge']); ?>
                  </span>
                </div>

                <label class="whatsapp-search" for="messageConversationSearch">
                  <span class="lucide-icon" data-lucide="search"></span>
                  <input id="messageConversationSearch" type="search" placeholder="Rechercher une discussion" aria-label="Rechercher une discussion" data-conversation-search />
                </label>
              </div>

              <div class="whatsapp-conversation-list">
<?php foreach ($conversationCards as $conversation): ?>
                <button type="button" class="whatsapp-conversation whatsapp-animate-item<?php echo !empty($conversation['active']) ? ' is-active' : ''; ?>" data-conversation-id="<?php echo $escapeValue($conversation['id']); ?>" aria-label="Sélectionner <?php echo $escapeValue($conversation['name']); ?>">
                  <span class="whatsapp-avatar" title="<?php echo $escapeValue($conversation['name']); ?>"><?php echo $escapeValue($conversation['avatar']); ?></span>
                  <span class="whatsapp-conversation__body">
                    <span class="whatsapp-conversation__top">
                      <span class="whatsapp-name"><?php echo $escapeValue($conversation['name']); ?></span>
                      <span class="whatsapp-time"><?php echo $escapeValue($conversation['time']); ?></span>
                    </span>
                    <p class="whatsapp-snippet"><?php echo $escapeValue($conversation['snippet']); ?></p>
                  </span>
<?php if (($conversation['unread'] ?? 0) > 0): ?>
                  <span class="whatsapp-unread" aria-label="<?php echo $escapeValue((string) $conversation['unread']); ?> messages non lus"><?php echo $escapeValue((string) $conversation['unread']); ?></span>
<?php endif; ?>
                </button>
<?php endforeach; ?>
              </div>
            </aside>

            <section class="whatsapp-thread">
              <header class="whatsapp-thread__header">
                <button type="button" class="whatsapp-back" aria-label="Retour aux conversations" data-thread-back>
                  <span class="lucide-icon" data-lucide="arrow-left"></span>
                </button>
                <div class="whatsapp-thread__identity">
                  <span class="whatsapp-avatar" data-thread-avatar><?php echo $escapeValue($activeConversation['avatar']); ?></span>
                  <div class="whatsapp-thread__copy">
                    <h6 class="whatsapp-thread__name" data-thread-name><?php echo $escapeValue($activeConversation['name']); ?></h6>
                    <p class="whatsapp-status mb-0" data-thread-status><?php echo $escapeValue($activeConversation['status']); ?></p>
                  </div>
                </div>
              </header>

              <div class="whatsapp-thread__messages" data-thread-body></div>

              <footer class="whatsapp-compose">
                <div class="whatsapp-compose__bar">
                  <button type="button" class="whatsapp-icon-btn" aria-label="Pièce jointe" title="Ajouter une pièce jointe">
                    <span class="lucide-icon" data-lucide="paperclip"></span>
                  </button>
                  <input type="text" class="whatsapp-compose__input" placeholder="<?php echo $escapeValue($options['compose_placeholder']); ?>" aria-label="<?php echo $escapeValue($options['compose_placeholder']); ?>" />
                  <button type="button" class="whatsapp-icon-btn" aria-label="Message vocal" title="Enregistrer un message vocal">
                    <span class="lucide-icon" data-lucide="mic"></span>
                  </button>
                  <button type="button" class="whatsapp-send" aria-label="Envoyer le message" title="Envoyer">
                    <span class="lucide-icon" data-lucide="send"></span>
                  </button>
                </div>
              </footer>
            </section>
          </section>
        </div>
<?php
  }
}
