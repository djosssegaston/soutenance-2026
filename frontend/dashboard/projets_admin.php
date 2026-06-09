<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$projects = \App\Models\Project::with('owner')->get()->map(function ($project) {
    $statusMap = [
        'draft' => ['label' => 'Brouillon', 'class' => 'status-draft draft'],
        'submitted' => ['label' => 'Soumis', 'class' => 'status-submitted submitted'],
        'under_admin_review' => ['label' => 'En révision', 'class' => 'status-processing processing'],
        'admin_validated' => ['label' => 'Validé par admin', 'class' => 'status-approved approved'],
        'admin_rejected' => ['label' => 'Rejeté', 'class' => 'status-rejected rejected'],
        'under_institution_review' => ['label' => 'En analyse institution', 'class' => 'status-processing processing'],
        'institution_accepted' => ['label' => 'Accepté par institution', 'class' => 'status-approved approved'],
        'institution_rejected' => ['label' => 'Rejeté par institution', 'class' => 'status-rejected rejected'],
        'funded' => ['label' => 'Financé', 'class' => 'status-funded funded'],
        'active' => ['label' => 'En cours', 'class' => 'status-funded funded'],
        'completed' => ['label' => 'Terminé', 'class' => 'status-approved approved'],
        'suspended' => ['label' => 'Suspendu', 'class' => 'status-rejected rejected'],
        'cancelled' => ['label' => 'Annulé', 'class' => 'status-rejected rejected'],
    ];
    $status = $statusMap[$project->statut] ?? ['label' => $project->statut, 'class' => 'status-submitted submitted'];

    return [
        'id' => 'PRJ-' . str_pad($project->id, 3, '0', STR_PAD_LEFT),
        'name' => $project->titre,
        'carrier' => $project->owner->name ?? 'N/A',
        'sector' => $project->secteur,
        'amount' => $project->montant_demande,
        'status_label' => $status['label'],
        'status_class' => $status['class'],
        'row_id' => $project->id,
        'validate_url' => '/dashboard/admin/projects/' . $project->id . '/validate',
        'reject_url' => '/dashboard/admin/projects/' . $project->id . '/reject',
    ];
})->toArray();

$page_title = 'Projets';
$page_subtitle = 'Pilotez les validations, refus et contrôles sur l’ensemble des dossiers.';
$page_active_nav = 'projects';
$page_document_title = 'Projets admin - ALOGOTO';
$page_inline_scripts = <<<'JS'
function validateProject(projectId) {
  var commentaire = prompt('Ajouter un commentaire (optionnel):');
  if (commentaire !== null) {
    performAction(projectId, '/dashboard/admin/projects/' + projectId + '/validate', commentaire);
  }
}

function rejectProject(projectId) {
  var commentaire = prompt('Raison du refus (obligatoire):');
  if (commentaire) {
    performAction(projectId, '/dashboard/admin/projects/' + projectId + '/reject', commentaire);
  }
}

function performAction(projectId, url, commentaire) {
  fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    },
    body: JSON.stringify({ commentaire: commentaire })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      var statusEl = document.getElementById('projectStatus-' + projectId);
      if (statusEl) {
        statusEl.textContent = data.status_label;
        statusEl.className = 'status-badge ' + data.status_class;
      }
      alert('Action effectuée avec succès!');
    } else {
      alert('Erreur: ' + (data.message || 'Impossible d\'effectuer l\'action'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Erreur lors de l\'action');
  });
}

document.addEventListener('DOMContentLoaded', function() {
  // Attach click handlers to validate and reject buttons
  document.querySelectorAll('[data-action="validate"]').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const projectId = this.getAttribute('data-project-id');
      validateProject(projectId);
    });
  });

  document.querySelectorAll('[data-action="reject"]').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const projectId = this.getAttribute('data-project-id');
      rejectProject(projectId);
    });
  });
});
JS;
include __DIR__ . '/components/admin_page_start.php';
?>
        <div class="row dashboard-gap-20 mt-2 mb-5">
          <div class="col-12">
            <?php
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle" data-items-per-page="10">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nom</th>
      <th>Porteur</th>
      <th>Secteur</th>
      <th>Montant</th>
      <th>Statut</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($projects as $project): ?>
    <tr>
      <td><?php echo dashboard_escape($project['id']); ?></td>
      <td><?php echo dashboard_escape($project['name']); ?></td>
      <td><?php echo dashboard_escape($project['carrier']); ?></td>
      <td><?php echo dashboard_escape($project['sector']); ?></td>
      <td><?php echo dashboard_escape(dashboard_format_fcfa($project['amount'])); ?></td>
      <td><span id="projectStatus-<?php echo dashboard_escape($project['row_id']); ?>" class="status-badge <?php echo dashboard_escape($project['status_class']); ?>"><?php echo dashboard_escape($project['status_label']); ?></span></td>
      <td>
        <div class="admin-action-group">
          <button type="button" class="dashboard-btn-primary btn-dashboard primary sm" data-action="validate" data-project-id="<?php echo dashboard_escape($project['row_id']); ?>"><i class="bi bi-check2"></i><span>Valider</span></button>
          <button type="button" class="btn-dashboard outline sm text-danger" data-action="reject" data-project-id="<?php echo dashboard_escape($project['row_id']); ?>"><i class="bi bi-x-circle"></i><span>Refuser</span></button>
          <button type="button" class="btn-dashboard outline sm"><i class="bi bi-eye"></i><span>Voir détail</span></button>
        </div>
      </td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Pipeline projets';
            $table_id = 'adminProjects';
            $table_search_placeholder = 'Rechercher un projet';
            $table_auto_paginate = true;
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>

        <div class="modal fade" id="adminProjectDetailModal" tabindex="-1" aria-labelledby="adminProjectDetailModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <div>
                  <p class="dashboard-card__subtitle mb-1">Details du projet</p>
                  <h5 class="modal-title" id="adminProjectDetailModalLabel">Projet</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <p class="dashboard-meta">ID projet</p>
                    <h6 id="modalProjectId">-</h6>
                  </div>
                  <div class="col-12 col-md-6">
                    <p class="dashboard-meta">Porteur</p>
                    <h6 id="modalProjectCarrier">-</h6>
                  </div>
                  <div class="col-12 col-md-6">
                    <p class="dashboard-meta">Secteur</p>
                    <h6 id="modalProjectSector">-</h6>
                  </div>
                  <div class="col-12 col-md-6">
                    <p class="dashboard-meta">Montant demande</p>
                    <h6 id="modalProjectAmount">-</h6>
                  </div>
                  <div class="col-12 col-md-6">
                    <p class="dashboard-meta">Localisation</p>
                    <h6 id="modalProjectLocation">-</h6>
                  </div>
                  <div class="col-12 col-md-6">
                    <p class="dashboard-meta">Duree</p>
                    <h6 id="modalProjectDuration">-</h6>
                  </div>
                  <div class="col-12 col-md-6">
                    <p class="dashboard-meta">Date de soumission</p>
                    <h6 id="modalProjectSubmitted">-</h6>
                  </div>
                  <div class="col-12 col-md-6">
                    <p class="dashboard-meta">Statut</p>
                    <h6 id="modalProjectStatus">-</h6>
                  </div>
                  <div class="col-12">
                    <p class="dashboard-meta">Description</p>
                    <p id="modalProjectDescription" class="mb-0">-</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

<?php $page_inline_scripts = <<<JS
(function(){
  var buttons = document.querySelectorAll('.js-project-detail');
  buttons.forEach(function(btn){
    btn.addEventListener('click', function(){
      document.getElementById('adminProjectDetailModalLabel').textContent = btn.getAttribute('data-project-name') || 'Projet';
      document.getElementById('modalProjectId').textContent = btn.getAttribute('data-project-id') || '-';
      document.getElementById('modalProjectCarrier').textContent = btn.getAttribute('data-project-carrier') || '-';
      document.getElementById('modalProjectSector').textContent = btn.getAttribute('data-project-sector') || '-';
      document.getElementById('modalProjectAmount').textContent = btn.getAttribute('data-project-amount') || '-';
      document.getElementById('modalProjectLocation').textContent = btn.getAttribute('data-project-location') || '-';
      document.getElementById('modalProjectDuration').textContent = btn.getAttribute('data-project-duration') || '-';
      document.getElementById('modalProjectSubmitted').textContent = btn.getAttribute('data-project-submitted') || '-';
      document.getElementById('modalProjectStatus').textContent = btn.getAttribute('data-project-status') || '-';
      document.getElementById('modalProjectDescription').textContent = btn.getAttribute('data-project-description') || '-';
    });
  });
})();
JS;
include __DIR__ . '/components/admin_page_end.php'; ?>
