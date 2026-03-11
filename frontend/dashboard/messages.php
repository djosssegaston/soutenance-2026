<?php
require_once __DIR__ . '/components/porteur_helpers.php';
require_once __DIR__ . '/components/messages_shared.php';

$porteurData = require __DIR__ . '/components/porteur_data.php';
$conversations = $porteurData['conversations'];

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

$messagesView = dashboard_messages_prepare($conversations, $presenceMap, $threadMap);

$page_title = 'Messages';
$page_subtitle = 'Messagerie interne inspiree de WhatsApp Web, dans la charte ALOGOTO.';
$page_active_nav = 'messages';
$page_document_title = 'Messages - ALOGOTO';
$page_inline_styles = dashboard_messages_styles();
$page_inline_scripts = dashboard_messages_scripts($messagesView['payload']);

include __DIR__ . '/components/porteur_page_start.php';

dashboard_render_messages_shell($messagesView['cards'], $messagesView['default_id'], 'porteur_escape', [
  'sidebar_title' => 'Centre de conversation',
  'sidebar_subtitle' => 'Synchronise avec vos partenaires',
  'sidebar_badge' => $messagesView['unread_total'] . ' non lus',
  'compose_placeholder' => 'Tapez un message',
]);

include __DIR__ . '/components/porteur_page_end.php';
