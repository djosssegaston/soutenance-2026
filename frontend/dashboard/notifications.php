<?php
require_once __DIR__ . '/components/porteur_helpers.php';
$porteurData = require __DIR__ . '/components/porteur_data.php';
$notifications = $porteurData['notifications'];

$page_title = 'Notifications';
$page_subtitle = 'Gardez une vue chronologique des alertes et evenements du compte.';
$page_active_nav = 'notifications';
$page_document_title = 'Notifications - ALOGOTO';

include __DIR__ . '/components/porteur_page_start.php';
?>
        <div class="dashboard-page-stack">
        <div class="row dashboard-gap-16 porteur-page-kpi-row">
          <div class="col-12 col-md-6 col-xl-4">
            <?php
            $stat_icon = 'bi-bell';
            $stat_title = 'Notifications du jour';
            $stat_value = '4';
            $stat_trend = 'Flux en temps reel';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-broadcast';
            $stat_icon_bg = 'rgba(252, 160, 40, 0.18)';
            $stat_icon_color = 'var(--primary-color-3)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-4">
            <?php
            $stat_icon = 'bi-cash-coin';
            $stat_title = 'Paiements suivis';
            $stat_value = '2';
            $stat_trend = 'Alertes financieres';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-arrow-up-right';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.15)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-4">
            <?php
            $stat_icon = 'bi-envelope';
            $stat_title = 'Messages recents';
            $stat_value = '3';
            $stat_trend = 'Interactions en attente';
            $stat_trend_class = 'is-neutral';
            $stat_trend_icon = 'bi-chat-left-dots';
            $stat_icon_bg = 'rgba(0, 72, 220, 0.12)';
            $stat_icon_color = 'var(--primary-color-2)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20 dashboard-notification-stack">
          <div class="col-12 col-xl-8">
            <section class="dashboard-card">
              <div class="dashboard-card__head mb-3">
                <h6>Timeline des notifications</h6>
              </div>
              <ul class="dashboard-timeline">
<?php foreach ($notifications as $notification): ?>
                <li>
                  <span class="dashboard-timeline-dot <?php echo porteur_escape($notification['variant']); ?>"></span>
                  <h6><?php echo porteur_escape($notification['title']); ?></h6>
                  <p><?php echo porteur_escape($notification['message']); ?></p>
                  <time><?php echo porteur_escape($notification['time']); ?></time>
                </li>
<?php endforeach; ?>
              </ul>
            </section>
          </div>

          <div class="col-12 col-xl-4">
            <section class="dashboard-card mb-4">
              <div class="dashboard-card__head mb-3">
                <h6>Priorites du jour</h6>
              </div>
              <div class="porteur-soft-card mb-3">
                <h6 class="mb-1">Echeance a confirmer</h6>
                <p class="mb-0">Le remboursement du 15 Mar 2026 doit etre valide avant 18h.</p>
              </div>
              <div class="porteur-soft-card">
                <h6 class="mb-1">Document a suivre</h6>
                <p class="mb-0">Le previsionnel financier attend encore la relecture de l administrateur.</p>
              </div>
            </section>

            <section class="dashboard-card">
              <div class="dashboard-card__head mb-3">
                <h6>Canaux actifs</h6>
              </div>
              <div class="porteur-meta-list">
                <div class="porteur-meta-list__item">
                  <p class="porteur-meta-list__label">Financement</p>
                  <p class="porteur-meta-list__value">2 alertes</p>
                </div>
                <div class="porteur-meta-list__item">
                  <p class="porteur-meta-list__label">Documents</p>
                  <p class="porteur-meta-list__value">1 validation</p>
                </div>
                <div class="porteur-meta-list__item">
                  <p class="porteur-meta-list__label">Messagerie</p>
                  <p class="porteur-meta-list__value">1 nouveau message</p>
                </div>
              </div>
            </section>
          </div>
        </div>
        </div>
<?php include __DIR__ . '/components/porteur_page_end.php'; ?>
