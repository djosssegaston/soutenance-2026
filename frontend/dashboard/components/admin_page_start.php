<?php require_once __DIR__ . '/../../includes/path_helpers.php'; ?>
<?php
$page_title = isset($page_title) ? $page_title : 'Tableau de bord';
$page_subtitle = isset($page_subtitle) ? $page_subtitle : html_entity_decode('Administration g&eacute;n&eacute;rale', ENT_QUOTES, 'UTF-8');
$page_document_title = isset($page_document_title) ? $page_document_title : $page_title . ' - ALOGOTO';
$page_active_nav = isset($page_active_nav) ? $page_active_nav : 'dashboard';
$page_inline_styles = isset($page_inline_styles) ? trim((string) $page_inline_styles) : '';
$has_mobile_nav_messages_badge = isset($mobile_nav_messages_badge);
$has_mobile_nav_notifications_badge = isset($mobile_nav_notifications_badge);
$mobile_nav_context = isset($mobile_nav_context) ? $mobile_nav_context : 'admin';
$mobile_nav_messages_badge = $has_mobile_nav_messages_badge ? (int) $mobile_nav_messages_badge : 0;
$mobile_nav_notifications_badge = $has_mobile_nav_notifications_badge ? (int) $mobile_nav_notifications_badge : 0;

if (!$has_mobile_nav_messages_badge && isset($adminData['conversations']) && is_array($adminData['conversations'])) {
  foreach ($adminData['conversations'] as $conversation) {
    $mobile_nav_messages_badge += isset($conversation['unread']) ? (int) $conversation['unread'] : 0;
  }
}

if (!$has_mobile_nav_notifications_badge && isset($adminData['notifications']) && is_array($adminData['notifications'])) {
  $mobile_nav_notifications_badge = count($adminData['notifications']);
}

$dashboard_user_name = isset($dashboard_user_name) ? $dashboard_user_name : (isset($adminData['profile']['name']) ? $adminData['profile']['name'] : 'Admin principal');
$dashboard_user_id = isset($dashboard_user_id) ? $dashboard_user_id : (isset($adminData['profile']['user_id']) ? $adminData['profile']['user_id'] : 'ADM-2026-001');
$dashboard_user_role = isset($dashboard_user_role) ? $dashboard_user_role : (isset($adminData['profile']['role']) ? $adminData['profile']['role'] : 'Administrateur plateforme');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo dashboard_escape($page_document_title); ?></title>
  <link rel="icon" type="image/png" href="<?php echo htmlspecialchars(frontend_public_asset('img/favicon-1.png'), ENT_QUOTES, 'UTF-8'); ?>" />

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

  <link rel="stylesheet" href="<?php echo htmlspecialchars(frontend_public_asset('sass/style.css?v=20260303-1'), ENT_QUOTES, 'UTF-8'); ?>" />
  <link rel="stylesheet" href="<?php echo htmlspecialchars(dashboard_asset('css/dashboard.css'), ENT_QUOTES, 'UTF-8'); ?>" />
  <link rel="stylesheet" href="<?php echo htmlspecialchars(dashboard_asset('css/dashboard-fix.css'), ENT_QUOTES, 'UTF-8'); ?>" />
  <link rel="stylesheet" href="<?php echo htmlspecialchars(dashboard_asset('css/dashboard-enhancements.css'), ENT_QUOTES, 'UTF-8'); ?>" />
  <style>
    .admin-page-kpi-row .stat-card {
      min-height: 150px;
      padding-top: 14px;
      padding-bottom: 14px;
      display: grid;
      grid-template-columns: auto 1fr;
      grid-template-rows: auto 1fr auto;
      column-gap: 12px;
      row-gap: 6px;
      align-items: center;
    }

    .admin-page-kpi-row .stat-card__icon {
      grid-column: 1;
      grid-row: 1;
      margin: 0;
    }

    .admin-page-kpi-row .stat-card__label {
      grid-column: 2;
      grid-row: 1;
      margin: 0;
      align-self: center;
    }

    .admin-page-kpi-row .stat-card__value {
      grid-column: 1 / -1;
      grid-row: 2;
      width: 100%;
      margin: 0;
      align-self: center;
      justify-self: center;
      text-align: center;
    }

    .admin-page-kpi-row .stat-card__trend {
      grid-column: 1 / -1;
      grid-row: 3;
      margin: 0;
      justify-self: center;
    }

    .admin-page-subtle {
      color: var(--p-color);
    }

    .admin-action-group {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .admin-soft-card {
      background: #f8fafa;
      border: 1px solid var(--border-color-2);
      border-radius: 12px;
      padding: 16px;
    }

    .admin-form-card .form-label {
      font-weight: 600;
      color: var(--text-heading-color);
      margin-bottom: 8px;
    }

    .admin-form-card .form-control,
    .admin-form-card .form-select {
      min-height: 48px;
      border: 1px solid var(--border-color-2);
      border-radius: 14px;
      padding: 12px 14px;
      box-shadow: none;
    }

    .admin-form-card .form-control:focus,
    .admin-form-card .form-select:focus {
      border-color: rgba(252, 160, 40, 0.65);
      box-shadow: 0 0 0 0.2rem rgba(252, 160, 40, 0.12);
    }

    .admin-meta-list {
      display: grid;
      gap: 12px;
    }

    .admin-meta-list__item {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      padding-bottom: 12px;
      border-bottom: 1px solid rgba(233, 233, 232, 0.85);
    }

    .admin-meta-list__item:last-child {
      padding-bottom: 0;
      border-bottom: 0;
    }

    .admin-meta-list__label {
      margin: 0;
      font-size: 12px;
      color: var(--p-color);
    }

    .admin-meta-list__value {
      margin: 0;
      font-size: 13px;
      font-weight: 600;
      color: var(--text-heading-color);
      text-align: right;
    }

    @media (max-width: 767.98px) {
      .admin-page-kpi-row .stat-card {
        min-height: auto;
      }

      .admin-meta-list__item {
        display: block;
      }

      .admin-meta-list__value {
        text-align: left;
        margin-top: 4px;
      }
    }

<?php if ($page_inline_styles !== ''): ?>
<?php echo $page_inline_styles . PHP_EOL; ?>
<?php endif; ?>
  </style>
</head>
<body class="dashboard-body" data-active-nav="<?php echo dashboard_escape($page_active_nav); ?>">
  <?php include __DIR__ . '/sidebar_admin.php'; ?>
  <div class="sidebar-overlay"></div>

  <div class="dashboard-wrapper">
    <?php include __DIR__ . '/header.php'; ?>

    <main class="dashboard-content main-content">
      <div class="container-fluid px-0">
