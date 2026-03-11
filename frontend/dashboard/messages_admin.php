<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
require_once __DIR__ . '/components/messages_shared.php';

$adminData = require __DIR__ . '/components/admin_data.php';
$conversations = $adminData['conversations'];

$presenceMap = [];
$threadMap = [];

if (isset($conversations[0])) {
  $presenceMap[$conversations[0]['name']] = 'En ligne';
  $threadMap[$conversations[0]['name']] = [
    [
      'type' => 'text',
      'outgoing' => false,
      'time' => '11:02',
      'text' => 'Pouvez-vous confirmer si le projet PRJ-004 peut passer en validation finale aujourd hui ?',
      'status' => '',
    ],
    [
      'type' => 'text',
      'outgoing' => true,
      'time' => '11:05',
      'text' => 'Oui, la revue documentaire est terminee. Le dossier peut etre presente au comite cet apres-midi.',
      'status' => 'Lu',
    ],
    [
      'type' => 'attachment',
      'outgoing' => false,
      'time' => '11:11',
      'title' => 'Note_interne_PRJ004.pdf',
      'meta' => 'PDF - 840 Ko',
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
      'time' => '10:42',
      'text' => 'Merci pour le retour. J ai bien recu la notification de validation.',
      'status' => '',
    ],
    [
      'type' => 'voice',
      'outgoing' => true,
      'time' => '10:48',
      'duration' => '0:16',
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
      'time' => 'Hier',
      'text' => 'Le rapport KYC consolide est pret. Souhaitez-vous un export PDF ou Excel ?',
      'status' => '',
    ],
    [
      'type' => 'text',
      'outgoing' => true,
      'time' => 'Hier',
      'text' => 'Envoyez le PDF pour la revue comite, puis l export Excel au support conformite.',
      'status' => 'Lu',
    ],
  ];
}

$messagesView = dashboard_messages_prepare($conversations, $presenceMap, $threadMap);

$page_title = 'Messages';
$page_subtitle = 'Supervisez les echanges institution, porteur et support dans une interface compacte.';
$page_active_nav = 'messages';
$page_document_title = 'Messages admin - ALOGOTO';
$page_inline_styles = dashboard_messages_styles();
$page_inline_scripts = dashboard_messages_scripts($messagesView['payload']);

include __DIR__ . '/components/admin_page_start.php';

dashboard_render_messages_shell($messagesView['cards'], $messagesView['default_id'], 'dashboard_escape', [
  'sidebar_title' => 'Centre de conversation',
  'sidebar_subtitle' => 'Messagerie admin ALOGOTO',
  'sidebar_badge' => $messagesView['unread_total'] . ' non lus',
  'compose_placeholder' => 'Ecrire un message',
]);

include __DIR__ . '/components/admin_page_end.php';
