<?php require_once __DIR__ . '/../../includes/path_helpers.php'; ?>
<?php
$page_title = isset($page_title) ? $page_title : 'Tableau de bord';
$page_subtitle = isset($page_subtitle) ? $page_subtitle : 'Kouame Assi - Porteur de projet';
$page_document_title = isset($page_document_title) ? $page_document_title : $page_title . ' - ALOGOTO';
$page_active_nav = isset($page_active_nav) ? $page_active_nav : 'dashboard';
$page_inline_styles = isset($page_inline_styles) ? trim((string) $page_inline_styles) : '';
$has_mobile_nav_messages_badge = isset($mobile_nav_messages_badge);
$has_mobile_nav_notifications_badge = isset($mobile_nav_notifications_badge);
$mobile_nav_context = isset($mobile_nav_context) ? $mobile_nav_context : 'porteur';
$mobile_nav_messages_badge = $has_mobile_nav_messages_badge ? (int) $mobile_nav_messages_badge : 0;
$mobile_nav_notifications_badge = $has_mobile_nav_notifications_badge ? (int) $mobile_nav_notifications_badge : 0;

if (!$has_mobile_nav_messages_badge && isset($porteurData['conversations']) && is_array($porteurData['conversations'])) {
  foreach ($porteurData['conversations'] as $conversation) {
    $mobile_nav_messages_badge += isset($conversation['unread']) ? (int) $conversation['unread'] : 0;
  }
}

if (!$has_mobile_nav_notifications_badge && isset($porteurData['notifications']) && is_array($porteurData['notifications'])) {
  $mobile_nav_notifications_badge = count($porteurData['notifications']);
}

$dashboard_user_name = isset($dashboard_user_name) ? $dashboard_user_name : (isset($porteurData['profile']['name']) ? $porteurData['profile']['name'] : 'Kouame Assi');
$dashboard_user_id = isset($dashboard_user_id) ? $dashboard_user_id : (isset($porteurData['profile']['user_id']) ? $porteurData['profile']['user_id'] : 'PRT-2026-001');
$dashboard_user_role = isset($dashboard_user_role) ? $dashboard_user_role : (isset($porteurData['profile']['role']) ? $porteurData['profile']['role'] : 'Porteur de projet');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo porteur_escape($page_document_title); ?></title>
  <link rel="icon" type="image/png" href="<?php echo htmlspecialchars(frontend_public_asset('img/favicon-1.png'), ENT_QUOTES, 'UTF-8'); ?>" />

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

  <link rel="stylesheet" href="<?php echo htmlspecialchars(frontend_public_asset('sass/style.css?v=20260303-1'), ENT_QUOTES, 'UTF-8'); ?>" />
  <link rel="stylesheet" href="<?php echo htmlspecialchars(dashboard_asset('css/dashboard.css'), ENT_QUOTES, 'UTF-8'); ?>" />
  <link rel="stylesheet" href="<?php echo htmlspecialchars(dashboard_asset('css/dashboard-fix.css'), ENT_QUOTES, 'UTF-8'); ?>" />
  <link rel="stylesheet" href="<?php echo htmlspecialchars(dashboard_asset('css/dashboard-enhancements.css'), ENT_QUOTES, 'UTF-8'); ?>" />
  <style>
    .porteur-page-kpi-row .stat-card {
      min-height: 146px;
      padding-top: 14px;
      padding-bottom: 14px;
    }

    .porteur-page-kpi-row .stat-card__label,
    .porteur-page-kpi-row .stat-card__value {
      margin: 0;
    }

    .porteur-page-kpi-row .stat-card__trend {
      margin-top: auto;
      display: flex;
      width: fit-content;
    }

    .porteur-page-kpi-row .stat-card__value {
      text-align: center;
    }

    .porteur-page-subtle {
      color: var(--p-color);
    }

    .porteur-table-actions,
    .porteur-action-group {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .porteur-form-card .form-label {
      font-weight: 600;
      color: var(--text-heading-color);
      margin-bottom: 8px;
    }

    .porteur-form-card .form-control,
    .porteur-form-card .form-select {
      min-height: 48px;
      border: 1px solid var(--border-color-2);
      border-radius: 14px;
      padding: 12px 14px;
      box-shadow: none;
    }

    .porteur-form-card textarea.form-control {
      min-height: 140px;
      resize: vertical;
    }

    .porteur-form-card .form-control[type="file"] {
      padding-top: 10px;
      padding-bottom: 10px;
    }

    .porteur-form-card .form-control:focus,
    .porteur-form-card .form-select:focus {
      border-color: rgba(252, 160, 40, 0.65);
      box-shadow: 0 0 0 0.2rem rgba(252, 160, 40, 0.12);
    }

    .porteur-soft-card {
      background: #f8fafa;
      border: 1px solid var(--border-color-2);
      border-radius: 12px;
      padding: 16px;
    }

    .porteur-avatar-lg {
      width: 84px;
      height: 84px;
      border-radius: 50%;
      background: rgba(0, 72, 220, 0.12);
      color: var(--primary-color-2);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 30px;
      font-weight: 700;
    }

    .porteur-meta-list {
      display: grid;
      gap: 12px;
    }

    .porteur-meta-list__item {
      display: flex;
      justify-content: space-between;
      gap: 16px;
      padding-bottom: 12px;
      border-bottom: 1px solid rgba(233, 233, 232, 0.85);
    }

    .porteur-meta-list__item:last-child {
      padding-bottom: 0;
      border-bottom: 0;
    }

    .porteur-meta-list__label {
      color: var(--p-color);
      font-size: 12px;
      margin: 0;
    }

    .porteur-meta-list__value {
      color: var(--text-heading-color);
      font-size: 13px;
      font-weight: 600;
      text-align: right;
      margin: 0;
    }

    .porteur-empty-state {
      border: 1px dashed var(--border-color-2);
      border-radius: 12px;
      padding: 18px;
      text-align: center;
      color: var(--p-color);
    }

    @media (max-width: 767.98px) {
      .porteur-page-kpi-row .stat-card {
        min-height: auto;
      }

      .porteur-meta-list__item {
        display: block;
      }

      .porteur-meta-list__value {
        text-align: left;
        margin-top: 4px;
      }
    }

<?php if ($page_inline_styles !== ''): ?>
<?php echo $page_inline_styles . PHP_EOL; ?>
<?php endif; ?>
  </style>
</head>
<body class="dashboard-body dashboard-body--porteur" data-active-nav="<?php echo porteur_escape($page_active_nav); ?>">
  <?php include __DIR__ . '/sidebar_porteur.php'; ?>
  <div class="sidebar-overlay"></div>

  <div class="dashboard-wrapper">
    <?php include __DIR__ . '/header.php'; ?>

    <main class="dashboard-content main-content">
      <div class="container-fluid px-0">
