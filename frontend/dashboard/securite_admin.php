<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$adminData = require __DIR__ . '/components/admin_data.php';
$sessions = $adminData['sessions'];

$page_title = 'Sécurité';
$page_subtitle = 'Pilotez les accès, l’authentification et les sessions actives de l’administration.';
$page_active_nav = 'security';
$page_document_title = 'Sécurité admin - ALOGOTO';

include __DIR__ . '/components/admin_page_start.php';
?>
        <div class="row dashboard-gap-20 mt-2 mb-4">
          <div class="col-12 col-xl-6">
            <section class="dashboard-card admin-form-card h-100">
              <div class="dashboard-card__head mb-3">
                <h6>Gestion des accès</h6>
              </div>
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label">Rôle d’administration</label>
                  <select class="form-select">
                    <option>Administrateur principal</option>
                    <option>Administrateur conformité</option>
                    <option>Administrateur support</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Politique de mots de passe</label>
                  <select class="form-select">
                    <option>Renouvellement tous les 90 jours</option>
                    <option>Renouvellement tous les 60 jours</option>
                  </select>
                </div>
              </div>
            </section>
          </div>
          <div class="col-12 col-xl-6">
            <section class="dashboard-card h-100">
              <div class="dashboard-card__head mb-3">
                <h6>Authentification</h6>
              </div>
              <div class="admin-soft-card mb-3">
                <div class="d-flex justify-content-between align-items-center gap-3">
                  <div>
                    <h6 class="mb-1">Double authentification</h6>
                    <p class="mb-0">Active pour les rôles critiques et l’accès back-office.</p>
                  </div>
                  <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" checked />
                  </div>
                </div>
              </div>
              <p class="admin-page-subtle mb-0">Les codes de secours et l’historique des authentifications sont centralisés pour audit.</p>
            </section>
          </div>
        </div>

        <div class="row dashboard-gap-20 mt-4 mb-5">
          <div class="col-12">
            <?php
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle">
  <thead>
    <tr>
      <th>Utilisateur</th>
      <th>Appareil</th>
      <th>Adresse IP</th>
      <th>Statut</th>
      <th>Date</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($sessions as $session): ?>
    <tr>
      <td><?php echo dashboard_escape($session['user']); ?></td>
      <td><?php echo dashboard_escape($session['device']); ?></td>
      <td><?php echo dashboard_escape($session['ip']); ?></td>
      <td><span class="status-badge <?php echo dashboard_escape($session['status_class']); ?>"><?php echo dashboard_escape($session['status_label']); ?></span></td>
      <td><?php echo dashboard_escape($session['time']); ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Sessions actives';
            $table_id = 'adminSecuritySessions';
            $table_search_placeholder = 'Rechercher une session';
            $table_pagination = '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>
<?php include __DIR__ . '/components/admin_page_end.php'; ?>
