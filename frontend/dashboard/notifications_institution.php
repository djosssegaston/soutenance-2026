<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$institutionData = require __DIR__ . '/components/institution_data.php';
$notifications = $institutionData['notifications'];

$page_title = 'Notifications';
$page_subtitle = 'Consultez les nouveaux projets, remboursements et messages entrants.';
$page_active_nav = 'notifications';
$page_document_title = 'Notifications institution - ALOGOTO';

include __DIR__ . '/components/institution_page_start.php';
?>
        <div class="dashboard-page-stack">
        <div class="row dashboard-gap-20 dashboard-notification-stack">
          <div class="col-12 col-xl-8">
            <section class="dashboard-card">
              <div class="dashboard-card__head mb-3">
                <h6>Flux des notifications</h6>
              </div>
              <ul class="dashboard-timeline">
<?php foreach ($notifications as $notification): ?>
                <li>
                  <span class="dashboard-timeline-dot <?php echo dashboard_escape($notification['variant']); ?>"></span>
                  <h6><?php echo dashboard_escape($notification['title']); ?></h6>
                  <p><?php echo dashboard_escape($notification['message']); ?></p>
                  <time><?php echo dashboard_escape($notification['time']); ?></time>
                </li>
<?php endforeach; ?>
              </ul>
            </section>
          </div>
          <div class="col-12 col-xl-4">
            <section class="dashboard-card">
              <div class="dashboard-card__head mb-3">
                <h6>Résumé</h6>
              </div>
              <div class="institution-meta-list">
                <div class="institution-meta-list__item">
                  <p class="institution-meta-list__label">Nouveaux projets</p>
                  <p class="institution-meta-list__value">1</p>
                </div>
                <div class="institution-meta-list__item">
                  <p class="institution-meta-list__label">Remboursements</p>
                  <p class="institution-meta-list__value">1</p>
                </div>
                <div class="institution-meta-list__item">
                  <p class="institution-meta-list__label">Messages</p>
                  <p class="institution-meta-list__value">1</p>
                </div>
              </div>
            </section>
          </div>
        </div>
        </div>
<?php include __DIR__ . '/components/institution_page_end.php'; ?>
