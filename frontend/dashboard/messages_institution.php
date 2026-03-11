<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
require_once __DIR__ . '/components/messages_shared.php';

$institutionData = require __DIR__ . '/components/institution_data.php';
$conversations = $institutionData['conversations'];

$presenceMap = [];
$threadMap = [];

if (isset($conversations[0])) {
  $presenceMap[$conversations[0]['name']] = 'En ligne';
  $threadMap[$conversations[0]['name']] = [
    [
      'type' => 'text',
      'outgoing' => false,
      'time' => '10:02',
      'text' => 'Bonjour, le bon de commande signe est maintenant visible dans votre espace de revue.',
      'status' => '',
    ],
    [
      'type' => 'attachment',
      'outgoing' => true,
      'time' => '10:11',
      'title' => 'Checklist_decaissement.pdf',
      'meta' => 'PDF - 1.1 Mo',
      'status' => 'Lu',
    ],
    [
      'type' => 'text',
      'outgoing' => false,
      'time' => '10:19',
      'text' => 'Tres bien, nous attendons votre confirmation pour lancer le prochain decaissement.',
      'status' => '',
    ],
  ];
}

if (isset($conversations[1])) {
  $presenceMap[$conversations[1]['name']] = 'Hors ligne';
  $threadMap[$conversations[1]['name']] = [
    [
      'type' => 'text',
      'outgoing' => false,
      'time' => 'Hier',
      'text' => 'Merci de confirmer la note de risque PRJ-021 avant la cloture du tableau de bord.',
      'status' => '',
    ],
    [
      'type' => 'text',
      'outgoing' => true,
      'time' => 'Hier',
      'text' => 'La note est relue. Je vous envoie la validation finale apres le comite interne.',
      'status' => 'Envoye',
    ],
  ];
}

if (isset($conversations[2])) {
  $presenceMap[$conversations[2]['name']] = 'En reunion';
  $threadMap[$conversations[2]['name']] = [
    [
      'type' => 'text',
      'outgoing' => false,
      'time' => '08:34',
      'text' => 'Le contrat fournisseur a bien ete televerse. Reste-t-il une piece a joindre ?',
      'status' => '',
    ],
    [
      'type' => 'voice',
      'outgoing' => true,
      'time' => '08:39',
      'duration' => '0:22',
      'status' => 'Lu',
    ],
  ];
}

$messagesView = dashboard_messages_prepare($conversations, $presenceMap, $threadMap);

$page_title = 'Messages';
$page_subtitle = 'Vue institution compacte, inspiree de WhatsApp pour les echanges avec les porteurs.';
$page_active_nav = 'messages';
$page_document_title = 'Messages institution - ALOGOTO';
$page_inline_styles = dashboard_messages_styles();
$page_inline_scripts = dashboard_messages_scripts($messagesView['payload']);

include __DIR__ . '/components/institution_page_start.php';

dashboard_render_messages_shell($messagesView['cards'], $messagesView['default_id'], 'dashboard_escape', [
  'sidebar_title' => 'Conversations',
  'sidebar_subtitle' => 'Messagerie institution ALOGOTO',
  'sidebar_badge' => $messagesView['unread_total'] . ' non lus',
  'compose_placeholder' => 'Ecrire un message',
]);

include __DIR__ . '/components/institution_page_end.php';
