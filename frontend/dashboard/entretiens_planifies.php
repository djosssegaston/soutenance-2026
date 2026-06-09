<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$institutionData = require __DIR__ . '/components/institution_data.php';

$interviews = [
  [
    'id' => 1,
    'project' => 'Plateforme EdTech Dakar',
    'carrier' => 'Aminata Diop',
    'date' => '18 Mar 2026 - 10:30',
    'channel' => 'Messagerie plateforme',
    'status' => 'Planifie',
  ],
  [
    'id' => 2,
    'project' => 'Clinique Mobile Bamako',
    'carrier' => 'Mamadou Diallo',
    'date' => '20 Mar 2026 - 14:00',
    'channel' => 'Email',
    'status' => 'En attente',
  ],
];

$page_title = 'Entretiens planifies';
$page_subtitle = 'Coordonnez les rendez-vous avec les porteurs.';
$page_active_nav = 'interviews';
$page_document_title = 'Entretiens planifies - ALOGOTO';

include __DIR__ . '/components/institution_page_start.php';
?>
        <div class="row dashboard-gap-20 mt-2 mb-4">
          <div class="col-12 col-lg-7">
            <?php
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle" data-items-per-page="10">
  <thead>
    <tr>
      <th>Projet</th>
      <th>Porteur</th>
      <th>Date entretien</th>
      <th>Canal</th>
      <th>Statut</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($interviews as $index => $interview): ?>
    <tr>
      <td><?php echo dashboard_escape($interview['project']); ?></td>
      <td><?php echo dashboard_escape($interview['carrier']); ?></td>
      <td><?php echo dashboard_escape($interview['date']); ?></td>
      <td><?php echo dashboard_escape($interview['channel']); ?></td>
      <td><span id="interviewStatus<?php echo $index; ?>" class="status-badge status-submitted submitted"><?php echo dashboard_escape($interview['status']); ?></span></td>
      <td>
        <div class="institution-action-group">
          <form method="POST" action="/dashboard/institution/interviews/confirm" class="inline-form" style="display:inline;">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="project_id" value="<?php echo dashboard_escape($interview['id']); ?>">
            <button type="submit" class="btn-dashboard outline sm">
              <span>Confirmer</span>
            </button>
          </form>
          <form method="POST" action="/dashboard/institution/interviews/request-docs" class="inline-form" style="display:inline;">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="project_id" value="<?php echo dashboard_escape($interview['id']); ?>">
            <button type="submit" class="btn-dashboard outline sm">
              <span>Demander documents</span>
            </button>
          </form>
        </div>
      </td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Entretiens planifies';
            $table_id = 'institutionInterviews';
            $table_search_placeholder = 'Rechercher un entretien';
            $table_show_filter = false;
            $table_auto_paginate = true;
            include __DIR__ . '/components/data_table.html';
            ?>
            <p id="interviewFeedback" class="institution-page-subtle mt-3" hidden></p>
          </div>

          <div class="col-12 col-lg-5">
            <section class="dashboard-card institution-form-card">
              <div class="dashboard-card__head mb-3">
                <h6>Planifier un entretien</h6>
              </div>
              <form method="POST" action="/dashboard/institution/interviews/schedule">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                  <label class="form-label">Projet</label>
                  <select class="form-select" name="project_id" required>
                    <option value="">Sélectionner un projet</option>
                    <?php foreach ($interviews as $interview): ?>
                      <option value="<?php echo dashboard_escape($interview['id']); ?>"><?php echo dashboard_escape($interview['project']); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Date et heure</label>
                  <input type="datetime-local" class="form-control" name="interview_date" required />
                </div>
                <div class="mb-3">
                  <label class="form-label">Canal</label>
                  <select class="form-select" name="channel" required>
                    <option value="messagerie">Messagerie plateforme</option>
                    <option value="email">Email</option>
                    <option value="telephone">Téléphone</option>
                  </select>
                </div>
                <div class="institution-action-group">
                  <button type="submit" class="dashboard-btn-primary btn-dashboard primary">
                    <span>Envoyer proposition</span>
                  </button>
                  <button type="submit" formaction="/dashboard/institution/interviews/notify" class="btn-dashboard outline">
                    <span>Notifier porteur</span>
                  </button>
                </div>
              </form>
            </section>
          </div>
        </div>
<?php include __DIR__ . '/components/institution_page_end.php'; ?>
