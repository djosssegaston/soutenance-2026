<?php
require_once __DIR__ . '/components/porteur_helpers.php';
require_once __DIR__ . '/components/messages_shared.php';

$porteurData = require __DIR__ . '/components/porteur_data.php';
$conversations = $porteurData['conversations'];

// Extend conversations for scroll testing in porteur view.
$extraConversations = [
  ['name' => 'Comite financement', 'role' => 'Institution', 'snippet' => 'Veuillez confirmer la prochaine etape.', 'time' => '11:22', 'unread' => 0, 'active' => false, 'avatar' => 'CF', 'status' => 'En ligne'],
  ['name' => 'Support ALOGOTO', 'role' => 'Support', 'snippet' => 'Votre ticket a ete mis a jour.', 'time' => '11:08', 'unread' => 1, 'active' => false, 'avatar' => 'SA', 'status' => 'En ligne'],
  ['name' => 'BOAD', 'role' => 'Institution', 'snippet' => 'Merci pour les documents complets.', 'time' => '10:57', 'unread' => 0, 'active' => false, 'avatar' => 'BD', 'status' => 'Hors ligne'],
  ['name' => 'Ecobank CI', 'role' => 'Institution', 'snippet' => 'Decision attendue demain matin.', 'time' => '10:43', 'unread' => 2, 'active' => false, 'avatar' => 'EC', 'status' => 'En reunion'],
  ['name' => 'Cellule KYC', 'role' => 'Equipe interne', 'snippet' => 'Verification terminee, retour OK.', 'time' => '10:28', 'unread' => 0, 'active' => false, 'avatar' => 'KY', 'status' => 'En ligne'],
  ['name' => 'Equipe risques', 'role' => 'Equipe interne', 'snippet' => 'Nouvelle demande d ajustement.', 'time' => '10:12', 'unread' => 1, 'active' => false, 'avatar' => 'ER', 'status' => 'En ligne'],
  ['name' => 'Direction IT', 'role' => 'Equipe interne', 'snippet' => 'Maintenance prevue cette semaine.', 'time' => '09:56', 'unread' => 0, 'active' => false, 'avatar' => 'IT', 'status' => 'Hors ligne'],
  ['name' => 'Finance', 'role' => 'Equipe interne', 'snippet' => 'Suivi tresorerie a valider.', 'time' => '09:41', 'unread' => 0, 'active' => false, 'avatar' => 'FI', 'status' => 'En ligne'],
  ['name' => 'Comite credit', 'role' => 'Direction', 'snippet' => 'Merci de partager la synthese.', 'time' => '09:22', 'unread' => 3, 'active' => false, 'avatar' => 'CC', 'status' => 'En ligne'],
  ['name' => 'Support juridique', 'role' => 'Equipe interne', 'snippet' => 'Besoin de la version signee.', 'time' => 'Hier', 'unread' => 0, 'active' => false, 'avatar' => 'SJ', 'status' => 'Hors ligne'],
  ['name' => 'Aicha Coulibaly', 'role' => 'Porteur', 'snippet' => 'Peux-tu valider le budget final ?', 'time' => 'Hier', 'unread' => 0, 'active' => false, 'avatar' => 'AC', 'status' => 'En ligne'],
  ['name' => 'Kossi Agbeko', 'role' => 'Porteur', 'snippet' => 'Justificatifs ajoutes.', 'time' => 'Hier', 'unread' => 0, 'active' => false, 'avatar' => 'KA', 'status' => 'En ligne'],
];

$conversations = array_merge($conversations, $extraConversations);

$presenceMap = [
  'Banque Atlantique' => 'En ligne',
  'Administrateur ALOGOTO' => 'Hors ligne',
  'BOAD' => 'En reunion',
  'Ecobank CI' => 'En ligne',
];

$threadMap = [
  'Banque Atlantique' => [
    [
      'type' => 'text',
      'outgoing' => false,
      'time' => '09:12',
      'text' => 'Bonjour, pouvez-vous partager la derniere facture du fournisseur principal ?',
      'status' => '',
    ],
    [
      'type' => 'pdf',
      'outgoing' => true,
      'time' => '09:16',
      'title' => 'Facture_fournisseur.pdf',
      'meta' => 'PDF - 2.4 Mo',
      'status' => 'Lu',
    ],
    [
      'type' => 'text',
      'outgoing' => false,
      'time' => '09:21',
      'text' => 'Parfait, nous verifions aussi le bon de commande signe pour valider le prochain decaissement.',
      'status' => '',
    ],
    [
      'type' => 'voice',
      'outgoing' => true,
      'time' => '09:24',
      'duration' => '0:18',
      'status' => 'Lu',
    ],
    [
      'type' => 'file',
      'outgoing' => false,
      'time' => '09:30',
      'title' => 'Bon_commande_signe.docx',
      'meta' => 'Word - 380 Ko',
      'status' => '',
    ],
  ],
  'Administrateur ALOGOTO' => [
    [
      'type' => 'text',
      'outgoing' => false,
      'time' => 'Hier',
      'text' => 'Votre document legal a ete valide. Il ne reste que la confirmation du plan de remboursement.',
      'status' => '',
    ],
    [
      'type' => 'text',
      'outgoing' => true,
      'time' => 'Hier',
      'text' => 'Merci, je mets a jour le dossier complet avant 18h.',
      'status' => 'Envoye',
    ],
  ],
  'BOAD' => [
    [
      'type' => 'text',
      'outgoing' => false,
      'time' => '08:42',
      'text' => 'Merci de confirmer votre calendrier de remboursement avant le comite de lundi.',
      'status' => '',
    ],
    [
      'type' => 'text',
      'outgoing' => true,
      'time' => '08:47',
      'text' => 'Le calendrier a ete ajuste et sera envoye dans le tableau de suivi aujourd hui.',
      'status' => 'Lu',
    ],
    [
      'type' => 'audio',
      'outgoing' => false,
      'time' => '08:55',
      'duration' => '01:12',
      'status' => '',
    ],
  ],
  'Ecobank CI' => [
    [
      'type' => 'text',
      'outgoing' => false,
      'time' => '06 Mar',
      'text' => 'Votre demande passe en comite vendredi. Nous revenons vers vous avec la decision finale.',
      'status' => '',
    ],
    [
      'type' => 'attachment',
      'outgoing' => true,
      'time' => '06 Mar',
      'title' => 'Projection_tresorerie.xlsx',
      'meta' => 'Excel - 540 Ko',
      'status' => 'Envoye',
    ],
  ],
];

if (isset($threadMap['Banque Atlantique'])) {
  for ($i = 1; $i <= 14; $i++) {
    $threadMap['Banque Atlantique'][] = [
      'type' => 'text',
      'outgoing' => $i % 2 === 0,
      'time' => '09:' . str_pad((30 + $i) % 60, 2, '0', STR_PAD_LEFT),
      'text' => $i % 2 === 0
        ? 'Mise a jour recue, nous revenons vers vous rapidement.'
        : 'Merci, nous restons disponibles si besoin.',
      'status' => $i % 2 === 0 ? 'Lu' : '',
    ];
  }
}

$messagesView = dashboard_messages_prepare($conversations, $presenceMap, $threadMap);

$page_title = 'Messages';
$page_subtitle = 'Messagerie interne inspiree de WhatsApp Web, dans la charte ALOGOTO.';
$page_active_nav = 'messages';
$page_document_title = 'Messages - ALOGOTO';

$customMessagesStyles = <<<'CSS'
.whatsapp-page {
  padding: 0.25rem 0 0.5rem;
}

.whatsapp-shell {
  display: grid;
  grid-template-columns: minmax(300px, 34%) minmax(0, 1fr);
  min-height: clamp(640px, calc(100vh - 170px), 860px);
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
}

.whatsapp-sidebar {
  position: relative;
  display: flex;
  flex-direction: column;
  min-width: 0;
  border-right: 1px solid var(--border-color-2);
  background: #f4f6f7;
}

.whatsapp-sidebar__top {
  display: grid;
  gap: 12px;
  padding: 16px;
  border-bottom: 1px solid var(--border-color-2);
  background: var(--white);
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
  background: #f4f6f7;
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

.whatsapp-conversation:hover {
  background: rgba(0, 196, 134, 0.08);
}

.whatsapp-conversation.is-active {
  background: rgba(0, 196, 134, 0.2);
}

.whatsapp-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary-color-1), var(--primary-color-3));
  color: #fff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
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
  background-color: #ffffff;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cdefs%3E%3Cstyle%3E.icon { fill: %23FCA028; opacity: 0.08; }%3C/style%3E%3C/defs%3E%3Cg%3E%3Ctext x='10' y='30' class='icon' font-size='24'%3E💬%3C/text%3E%3Ctext x='80' y='60' class='icon' font-size='20'%3E☎️%3C/text%3E%3Ctext x='140' y='40' class='icon' font-size='18'%3E📎%3C/text%3E%3Ctext x='30' y='100' class='icon' font-size='22'%3E📄%3C/text%3E%3Ctext x='120' y='110' class='icon' font-size='20'%3E🔊%3C/text%3E%3Ctext x='60' y='150' class='icon' font-size='24'%3E✔️%3C/text%3E%3Ctext x='160' y='170' class='icon' font-size='20'%3E⏰%3C/text%3E%3Ctext x='15' y='180' class='icon' font-size='18'%3E📍%3C/text%3E%3C/g%3E%3C/svg%3E");
  background-size: 200px 200px;
  background-position: 0 0;
  background-repeat: repeat;
  background-attachment: fixed;
  background-image: none;
  isolation: isolate;
}

.whatsapp-thread::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160' viewBox='0 0 160 160'%3E%3Cg fill='%23FCA028' fill-opacity='0.12'%3E%3Crect x='12' y='14' width='28' height='20' rx='6'/%3E%3Cpolygon points='22,34 30,34 22,42'/%3E%3Ccircle cx='76' cy='26' r='8'/%3E%3Crect x='102' y='18' width='22' height='14' rx='4'/%3E%3Crect x='46' y='70' width='30' height='18' rx='5'/%3E%3Cpolygon points='54,88 60,88 54,94'/%3E%3Ccircle cx='124' cy='82' r='7'/%3E%3Crect x='16' y='110' width='22' height='14' rx='4'/%3E%3Ccircle cx='78' cy='122' r='6'/%3E%3Crect x='112' y='114' width='28' height='18' rx='6'/%3E%3Cpolygon points='120,132 128,132 120,140'/%3E%3C/g%3E%3C/svg%3E");
  background-size: 160px 160px;
  background-position: 0 0;
  background-repeat: repeat;
  opacity: 1;
  z-index: 0;
  pointer-events: none;
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
  position: relative;
  z-index: 1;
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
  border: 0;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
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
  position: relative;
  z-index: 1;
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
  background: var(--color-2);
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
  position: relative;
  z-index: 1;
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
    padding: 6px 10px;
  }

  .whatsapp-send {
    width: 32px;
    height: 32px;
    min-width: 32px;
  }

  .whatsapp-icon-btn {
    width: 32px;
    height: 32px;
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
}
CSS;

$customMessagesStyles = dashboard_messages_styles();
$customMessagesStyles .= <<<'CSS'

/* UI overrides: hide call + sticker icons, keep attach + mic */
.whatsapp-thread__header .whatsapp-icon-btn[aria-label*="Appel"],
.whatsapp-thread__header .whatsapp-icon-btn[title*="Appel"],
.whatsapp-compose__bar .whatsapp-icon-btn[aria-label*="emoji"],
.whatsapp-compose__bar .whatsapp-icon-btn[title*="emoji"],
.whatsapp-compose__bar .whatsapp-icon-btn[aria-label*="sticker"],
.whatsapp-compose__bar .whatsapp-icon-btn[title*="sticker"] {
  display: none !important;
}
CSS;
$page_inline_styles = $customMessagesStyles;
// DEBUG: CSS length check
error_log('DEBUG messages.php: customMessagesStyles length = ' . strlen($customMessagesStyles) . ' chars');
error_log('DEBUG messages.php: page_inline_styles length = ' . strlen($page_inline_styles) . ' chars');
$page_inline_scripts = dashboard_messages_scripts($messagesView['payload']);

include __DIR__ . '/components/porteur_page_start.php';

dashboard_render_messages_shell($messagesView['cards'], $messagesView['default_id'], 'porteur_escape', [
  'sidebar_title' => 'Centre de conversation',
  'sidebar_subtitle' => 'Synchronise avec vos partenaires',
  'sidebar_badge' => $messagesView['unread_total'] . ' non lus',
  'compose_placeholder' => 'Tapez un message',
]);

include __DIR__ . '/components/porteur_page_end.php';
