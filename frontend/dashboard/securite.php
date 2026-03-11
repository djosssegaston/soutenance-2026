<?php
require_once __DIR__ . '/components/porteur_helpers.php';
$porteurData = require __DIR__ . '/components/porteur_data.php';
$securityLogs = $porteurData['security_logs'];

$page_title = 'Securite';
$page_subtitle = 'Renforcez la protection du compte et suivez les connexions recentes.';
$page_active_nav = 'security';
$page_document_title = 'Securite - ALOGOTO';

include __DIR__ . '/components/porteur_page_start.php';
?>
        <div class="row dashboard-gap-20 mt-2 mb-4">
          <div class="col-12 col-xl-7">
            <section class="dashboard-card porteur-form-card h-100">
              <div class="dashboard-card__head mb-3">
                <h6>Changer mot de passe</h6>
              </div>
              <form class="row g-3" action="#" method="post">
                <div class="col-12">
                  <label class="form-label" for="currentPassword">Mot de passe actuel</label>
                  <input type="password" class="form-control" id="currentPassword" value="password" />
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label" for="newPassword">Nouveau mot de passe</label>
                  <input type="password" class="form-control is-valid" id="newPassword" value="Alogoto2026!" />
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label" for="confirmPassword">Confirmer</label>
                  <input type="password" class="form-control is-valid" id="confirmPassword" value="Alogoto2026!" />
                </div>
                <div class="col-12">
                  <button type="submit" class="dashboard-btn-primary btn-dashboard primary">
                    <i class="bi bi-shield-lock"></i>
                    <span>Mettre a jour</span>
                  </button>
                </div>
              </form>
            </section>
          </div>

          <div class="col-12 col-xl-5">
            <section class="dashboard-card h-100">
              <div class="dashboard-card__head mb-3">
                <h6>Double authentification</h6>
              </div>
              <div class="porteur-soft-card mb-3">
                <div class="d-flex justify-content-between align-items-center gap-3">
                  <div>
                    <h6 class="mb-1">2FA par application</h6>
                    <p class="mb-0">Placeholder fonctionnel pour activer une verification supplementaire.</p>
                  </div>
                  <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="twoFactorSwitch" />
                  </div>
                </div>
              </div>
              <p class="porteur-page-subtle mb-0">Activez la 2FA pour securiser les validations de documents et les operations sensibles.</p>
            </section>
          </div>
        </div>

        <div class="row dashboard-gap-20">
          <div class="col-12">
            <?php
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle">
  <thead>
    <tr>
      <th>Appareil</th>
      <th>Localisation</th>
      <th>Adresse IP</th>
      <th>Date et heure</th>
      <th>Statut</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($securityLogs as $log): ?>
    <tr>
      <td><?php echo porteur_escape($log['device']); ?></td>
      <td><?php echo porteur_escape($log['location']); ?></td>
      <td><?php echo porteur_escape($log['ip']); ?></td>
      <td><?php echo porteur_escape($log['time']); ?></td>
      <td><span class="status-badge <?php echo porteur_escape($log['status_class']); ?>"><?php echo porteur_escape($log['status']); ?></span></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Historique connexions';
            $table_id = 'porteurSecurityLogs';
            $table_search_placeholder = 'Rechercher une connexion';
            $table_pagination = '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>
<?php include __DIR__ . '/components/porteur_page_end.php'; ?>
