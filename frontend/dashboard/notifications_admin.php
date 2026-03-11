<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$adminData = require __DIR__ . '/components/admin_data.php';
$notifications = $adminData['notifications'];

$page_title = 'Notifications';
$page_subtitle = 'Supervisez les alertes globales et la diffusion des événements plateforme.';
$page_active_nav = 'notifications';
$page_document_title = 'Notifications admin - ALOGOTO';

include __DIR__ . '/components/admin_page_start.php';
?>
        <div class="dashboard-page-stack">
        <div class="row dashboard-gap-20 dashboard-notification-stack">
          <div class="col-12 col-xl-8">
            <section class="dashboard-card">
              <div class="dashboard-card__head mb-3">
                <h6>Gestion notifications globales</h6>
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
            <section class="dashboard-card admin-form-card">
              <div class="dashboard-card__head mb-3">
                <h6>Paramètres de diffusion</h6>
              </div>
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label">Canal prioritaire</label>
                  <select class="form-select">
                    <option>Email + In-app</option>
                    <option>In-app uniquement</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Niveau d’alerte</label>
                  <select class="form-select">
                    <option>Élevé</option>
                    <option>Moyen</option>
                    <option>Faible</option>
                  </select>
                </div>
                <div class="col-12">
                  <button type="button" class="dashboard-btn-primary btn-dashboard primary">
                    <i class="bi bi-bell"></i>
                    <span>Enregistrer</span>
                  </button>
                </div>
              </div>
            </section>
          </div>
        </div>
        </div>
<?php include __DIR__ . '/components/admin_page_end.php'; ?>
