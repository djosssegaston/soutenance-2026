<?php
require_once __DIR__ . '/components/porteur_helpers.php';

// Page accessible sans authentification
$user = null;

$projects = [];
if (isset($porteurData['projects']) && is_array($porteurData['projects'])) {
    $projects = $porteurData['projects'];
} else {
    $projects = \App\Models\Project::where('user_id', $user->id)->get()->map(function ($project) {
        $statusMap = [
            'draft' => ['label' => 'Brouillon', 'class' => 'status-draft draft'],
            'submitted' => ['label' => 'Soumis', 'class' => 'status-submitted submitted'],
            'under_admin_review' => ['label' => 'En révision admin', 'class' => 'status-processing processing'],
            'admin_validated' => ['label' => 'Validé par admin', 'class' => 'status-approved approved'],
            'admin_rejected' => ['label' => 'Rejeté par admin', 'class' => 'status-rejected rejected'],
            'under_institution_review' => ['label' => 'En analyse institution', 'class' => 'status-processing processing'],
            'interview_scheduled' => ['label' => 'Entretien planifié', 'class' => 'status-processing processing'],
            'interview_confirmed' => ['label' => 'Entretien confirmé', 'class' => 'status-approved approved'],
            'documents_requested' => ['label' => 'Documents demandés', 'class' => 'status-processing processing'],
            'institution_accepted' => ['label' => 'Accepté par institution', 'class' => 'status-approved approved'],
            'institution_rejected' => ['label' => 'Rejeté par institution', 'class' => 'status-rejected rejected'],
            'funded' => ['label' => 'Financé', 'class' => 'status-funded funded'],
            'active' => ['label' => 'En cours', 'class' => 'status-funded funded'],
            'completed' => ['label' => 'Terminé', 'class' => 'status-approved approved'],
            'suspended' => ['label' => 'Suspendu', 'class' => 'status-rejected rejected'],
            'cancelled' => ['label' => 'Annulé', 'class' => 'status-rejected rejected'],
        ];
        $status = $statusMap[$project->statut] ?? ['label' => $project->statut, 'class' => 'status-draft draft'];
        $canEditOrDelete = $project->statut === 'draft' || $project->statut === 'admin_rejected';

        return [
            'id' => 'PRJ-' . str_pad($project->id, 3, '0', STR_PAD_LEFT),
            'db_id' => $project->id,
            'name' => $project->titre,
            'sector' => $project->secteur,
            'description' => $project->description,
            'requested_amount' => $project->montant_demande,
            'funded_amount' => $project->montant_finance,
            'status' => $project->statut,
            'status_label' => $status['label'],
            'status_class' => $status['class'],
            'created_at' => $project->created_at->format('d M Y'),
            'duration' => $project->duree ?? '',
            'location' => $project->localisation ?? '',
            'progress' => 0,
            'institution' => '',
            'can_submit' => $project->statut === 'draft' || $project->statut === 'admin_rejected',
            'can_edit' => $canEditOrDelete,
            'can_delete' => $canEditOrDelete,
        ];
    })->toArray();
}

$totalProjects = count($projects);
$submittedProjects = $projects ? count(array_filter($projects, fn($p) => $p['status'] !== 'draft')) : 0;
$fundedProjects = $projects ? count(array_filter($projects, fn($p) => in_array($p['status'], ['funded', 'active', 'completed']))) : 0;
$rejectedProjects = $projects ? count(array_filter($projects, fn($p) => in_array($p['status'], ['admin_rejected', 'institution_rejected']))) : 0;
$draftProjects = $projects ? count(array_filter($projects, fn($p) => $p['status'] === 'draft')) : 0;

$page_title = 'Mes projets';
$page_subtitle = 'Suivez l avancement et les actions prioritaires de votre portefeuille.';
$page_active_nav = 'my-projects';
$page_document_title = 'Mes projets - ALOGOTO';
$page_inline_styles = <<<'CSS'
    .porteur-page-stack {
      display: grid;
      gap: 1.75rem;
      padding-top: 0.5rem;
      padding-bottom: 0.5rem;
    }

    .porteur-page-stack > .row {
      margin-top: 0 !important;
      margin-bottom: 0 !important;
    }

    .porteur-project-cards {
      --bs-gutter-y: 1.5rem;
    }

    @media (max-width: 767.98px) {
      .porteur-page-stack {
        gap: 1.25rem;
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
      }
    }
CSS;
$page_inline_scripts = <<<'JS'
(function () {
  var modal = document.getElementById('projectDetailModal');
  if (!modal) {
    return;
  }
  modal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    if (!button) {
      return;
    }
    var name = button.getAttribute('data-project-name') || '-';
    var sector = button.getAttribute('data-project-sector') || '-';
    var location = button.getAttribute('data-project-location') || '-';
    var amount = button.getAttribute('data-project-amount') || '-';
    var status = button.getAttribute('data-project-status') || '-';
    var statusClass = button.getAttribute('data-project-status-class') || 'status-submitted submitted';

    document.getElementById('modalProjectName').textContent = name;
    document.getElementById('modalProjectSector').textContent = sector;
    document.getElementById('modalProjectLocation').textContent = location;
    document.getElementById('modalProjectAmount').textContent = amount;
    var statusEl = document.getElementById('modalProjectStatus');
    statusEl.textContent = status;
    statusEl.className = 'status-badge ' + statusClass;
  });
})();

function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || window.csrfToken || '';
}

function projectAction(action, projectId) {
  var csrf = getCsrfToken();
  var url = '';
  var options = {
    headers: {
      'X-CSRF-TOKEN': csrf,
      'Accept': 'application/json'
    },
    credentials: 'same-origin'
  };

  if (action === 'submit') {
    url = '/dashboard/porteur/projects/' + projectId + '/submit';
    options.method = 'POST';
  } else if (action === 'delete') {
    if (!confirm('Supprimer ce projet ? Cette action est definitive.')) {
      return;
    }
    url = '/dashboard/porteur/projects/' + projectId;
    options.method = 'DELETE';
  } else {
    return;
  }

  fetch(url, options)
    .then(function (response) {
      if (!response.ok) {
        return response.json().then(function (data) {
          var msg = data && data.message ? data.message : ('HTTP ' + response.status);
          throw new Error(msg);
        }).catch(function () {
          throw new Error('HTTP ' + response.status);
        });
      }
      return response.json();
    })
    .then(function (data) {
      if (data.success) {
        showFeedback('success', data.message || 'Action effectuee', function(){ window.location.reload(); });
      } else {
        showFeedback('error', data.message || 'Action impossible');
      }
    })
    .catch(function (error) {
      console.error('Error:', error);
      showFeedback('error', error.message || 'Erreur inconnue');
    });
}

(function paginateTable() {
  var card = document.querySelector('[data-dashboard-table-card=\"porteurProjectsList\"]');
  if (!card) { return; }
  var table = card.querySelector('#porteurProjectsList table');
  var tbody = table ? table.querySelector('tbody') : null;
  var buttons = card.querySelectorAll('.page-btn');
  if (!table || !tbody || !buttons.length) { return; }

  var perPage = parseInt(table.getAttribute('data-items-per-page') || '10', 10);
  var rows = Array.from(tbody.querySelectorAll('tr'));

  function render(page) {
    buttons.forEach(function (btn) { btn.classList.remove('active'); });
    if (buttons[page - 1]) {
      buttons[page - 1].classList.add('active');
    }

    rows.forEach(function (row, idx) {
      var pageNum = Math.floor(idx / perPage) + 1;
      row.style.display = pageNum === page ? '' : 'none';
    });
  }

  buttons.forEach(function (btn, idx) {
    btn.addEventListener('click', function () {
      render(idx + 1);
    });
  });

  render(1);
})();
JS;

include __DIR__ . '/components/porteur_page_start.php';
?>
        <div class="porteur-page-stack">
        <div class="row dashboard-gap-16 porteur-page-kpi-row">
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-folder2';
            $stat_title = 'Total soumis';
            $stat_value = (string) $submittedProjects;
            $stat_trend = 'Dossiers transmis';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-send-check';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.15)';
            $stat_icon_color = 'var(--primary-color-1)';
            $stat_compact_header = true;
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-graph-up-arrow';
            $stat_title = 'Total finances';
            $stat_value = (string) $fundedProjects;
            $stat_trend = 'Accords obtenus';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-check2-circle';
            $stat_icon_bg = 'rgba(0, 72, 220, 0.12)';
            $stat_icon_color = 'var(--primary-color-2)';
            $stat_compact_header = true;
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-check2-circle';
            $stat_title = 'Total rejetes';
            $stat_value = (string) $rejectedProjects;
            $stat_trend = 'Decisions finales';
            $stat_trend_class = 'is-neutral';
            $stat_trend_icon = 'bi-x-circle';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.12)';
            $stat_icon_color = 'var(--primary-color-1)';
            $stat_compact_header = true;
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-pencil-square';
            $stat_title = 'Brouillons';
            $stat_value = (string) $draftProjects;
            $stat_trend = 'A finaliser';
            $stat_trend_class = 'is-neutral';
            $stat_trend_icon = 'bi-hourglass-split';
            $stat_icon_bg = 'rgba(252, 160, 40, 0.18)';
            $stat_icon_color = 'var(--primary-color-3)';
            $stat_compact_header = true;
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20">
          <div class="col-12">
<?php
            // Pagination config
            $items_per_page = isset($items_per_page) ? (int) $items_per_page : 10;
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle" data-items-per-page="<?php echo (int) $items_per_page; ?>">
  <thead>
    <tr>
      <th>ID projet</th>
      <th>Nom du projet</th>
      <th>Secteur</th>
      <th>Montant demande</th>
      <th>Montant finance</th>
      <th>Statut</th>
      <th>Date de creation</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($projects as $project): ?>
    <tr>
      <td><?php echo porteur_escape($project['id']); ?></td>
      <td><?php echo porteur_escape($project['name']); ?></td>
      <td><?php echo porteur_escape($project['sector']); ?></td>
      <td><?php echo porteur_escape(porteur_format_fcfa($project['requested_amount'])); ?></td>
      <td><?php echo porteur_escape(porteur_format_fcfa($project['status'] === 'draft' ? 0 : $project['funded_amount'])); ?></td>
      <td><span class="status-badge <?php echo $project['status_class']; ?>"><?php echo porteur_escape($project['status_label']); ?></span></td>
      <td><?php echo porteur_escape($project['created_at']); ?></td>
      <td>
        <div class="porteur-table-actions">
          <button type="button" class="btn-dashboard outline sm" data-bs-toggle="modal" data-bs-target="#projectDetailModal" data-project-id="<?php echo porteur_escape($project['id']); ?>" data-project-name="<?php echo porteur_escape($project['name']); ?>" data-project-sector="<?php echo porteur_escape($project['sector']); ?>" data-project-location="<?php echo porteur_escape($project['location']); ?>" data-project-amount="<?php echo porteur_escape(porteur_format_fcfa($project['requested_amount'])); ?>" data-project-status="<?php echo porteur_escape($project['status_label']); ?>" data-project-status-class="<?php echo porteur_escape($project['status_class']); ?>">
            <i class="bi bi-eye"></i>
            <span>Voir</span>
          </button>
<?php if (!empty($project['can_submit'])): ?>
          <button type="button" class="btn-dashboard sm btn-success" style="background-color:#198754;border-color:#198754;" <?php echo empty($project['can_submit']) ? 'disabled' : ''; ?> onclick="projectAction('submit', <?php echo (int) $project['db_id']; ?>)">
            <i class="bi bi-send"></i>
            <span>Soumettre</span>
          </button>
<?php endif; ?>
<?php if (!empty($project['can_edit'])): ?>
          <a href="<?php echo porteur_escape(dashboard_url('creer_projet.php')); ?>?id=<?php echo (int) $project['db_id']; ?>" class="btn-dashboard outline sm">
            <i class="bi bi-pencil-square"></i>
            <span>Modifier</span>
          </a>
<?php endif; ?>
<?php if (!empty($project['can_delete'])): ?>
          <button type="button" class="btn-dashboard sm btn-danger text-white" style="background-color:#dc3545;border-color:#dc3545;" onclick="projectAction('delete', <?php echo (int) $project['db_id']; ?>)">
            <i class="bi bi-trash"></i>
            <span>Supprimer</span>
          </button>
<?php endif; ?>
        </div>
      </td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Portefeuille projets';
            $table_id = 'porteurProjectsList';
            $table_search_placeholder = 'Rechercher un projet';
            // Calculate pagination
            $items_per_page = 10;
            $total_pages = ceil(count($projects) / $items_per_page);
            $pagination_html = '';
            if ($total_pages > 1) {
                for ($i = 1; $i <= $total_pages; $i++) {
                    $active = ($i === 1) ? 'active' : '';
                    $pagination_html .= '<button class="dashboard-page-btn page-btn ' . $active . '">' . $i . '</button>';
                }
            }
            $table_pagination = $pagination_html ?: '<button class="dashboard-page-btn page-btn active">1</button>';
            $table_show_filter = false;
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20 porteur-project-cards">
<?php foreach ($projects as $project): ?>
          <div class="col-12 col-md-6 col-xl-4">
            <section class="dashboard-card hover-lift h-100">
              <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div>
                  <h6 class="mb-1"><?php echo porteur_escape($project['name']); ?></h6>
                  <p class="porteur-page-subtle mb-0"><?php echo porteur_escape($project['location']); ?> • <?php echo porteur_escape($project['duration']); ?></p>
                </div>
                <span class="status-badge <?php echo porteur_escape($project['status_class']); ?>"><?php echo porteur_escape($project['status_label']); ?></span>
              </div>
              <p class="porteur-page-subtle mb-3"><?php echo porteur_escape($project['description']); ?></p>
              <div class="porteur-meta-list">
                <div class="porteur-meta-list__item">
                  <p class="porteur-meta-list__label">Montant cible</p>
                  <p class="porteur-meta-list__value"><?php echo porteur_escape(porteur_format_fcfa($project['requested_amount'])); ?></p>
                </div>
                <div class="porteur-meta-list__item">
                  <p class="porteur-meta-list__label">Montant acquis</p>
                  <p class="porteur-meta-list__value"><?php echo porteur_escape(porteur_format_fcfa($project['funded_amount'])); ?></p>
                </div>
                <div class="porteur-meta-list__item">
                  <p class="porteur-meta-list__label">Institution lead</p>
                  <p class="porteur-meta-list__value"><?php echo porteur_escape($project['institution']); ?></p>
                </div>
              </div>
              <div class="mt-3">
                <div class="d-flex justify-content-between mb-2">
                  <small class="porteur-page-subtle">Pourcentage remboursement</small>
                  <small class="fw-semibold"><?php echo porteur_escape((string) $project['progress']); ?>%</small>
                </div>
                <div class="dashboard-progress">
                  <div class="dashboard-progress__bar bg-success-token" style="width: <?php echo porteur_escape((string) $project['progress']); ?>%;"></div>
                </div>
              </div>
              <div class="porteur-table-actions mt-3">
                <button type="button" class="btn-dashboard outline sm" data-bs-toggle="modal" data-bs-target="#projectDetailModal" data-project-id="<?php echo porteur_escape($project['id']); ?>" data-project-name="<?php echo porteur_escape($project['name']); ?>" data-project-sector="<?php echo porteur_escape($project['sector']); ?>" data-project-location="<?php echo porteur_escape($project['location']); ?>" data-project-amount="<?php echo porteur_escape(porteur_format_fcfa($project['requested_amount'])); ?>" data-project-status="<?php echo porteur_escape($project['status_label']); ?>" data-project-status-class="<?php echo porteur_escape($project['status_class']); ?>">
                  <i class="bi bi-eye"></i>
                  <span>Voir</span>
                </button>
<?php if (!empty($project['can_submit'])): ?>
                <button type="button" class="btn-dashboard sm btn-success" style="background-color:#198754;border-color:#198754;" <?php echo empty($project['can_submit']) ? 'disabled' : ''; ?> onclick="projectAction('submit', <?php echo (int) $project['db_id']; ?>)">
                  <i class="bi bi-send"></i>
                  <span>Soumettre</span>
                </button>
<?php endif; ?>
<?php if (!empty($project['can_edit'])): ?>
                <a href="<?php echo porteur_escape(dashboard_url('creer_projet.php')); ?>?id=<?php echo (int) $project['db_id']; ?>" class="btn-dashboard outline sm">
                  <i class="bi bi-pencil-square"></i>
                  <span>Modifier</span>
                </a>
<?php endif; ?>
<?php if (!empty($project['can_delete'])): ?>
                <button type="button" class="btn-dashboard sm btn-danger text-white" style="background-color:#dc3545;border-color:#dc3545;" onclick="projectAction('delete', <?php echo (int) $project['db_id']; ?>)">
                  <i class="bi bi-trash"></i>
                  <span>Supprimer</span>
                </button>
<?php endif; ?>
              </div>
            </section>
          </div>
<?php endforeach; ?>
        </div>
        </div>

        <div class="modal fade" id="projectDetailModal" tabindex="-1" aria-labelledby="projectDetailModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="projectDetailModalLabel">Detail du projet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <p class="porteur-page-subtle mb-1">Projet</p>
                    <h6 id="modalProjectName" class="mb-0">-</h6>
                  </div>
                  <div class="col-12 col-md-6">
                    <p class="porteur-page-subtle mb-1">Secteur</p>
                    <h6 id="modalProjectSector" class="mb-0">-</h6>
                  </div>
                  <div class="col-12 col-md-6">
                    <p class="porteur-page-subtle mb-1">Localisation</p>
                    <h6 id="modalProjectLocation" class="mb-0">-</h6>
                  </div>
                  <div class="col-12 col-md-6">
                    <p class="porteur-page-subtle mb-1">Montant demande</p>
                    <h6 id="modalProjectAmount" class="mb-0">-</h6>
                  </div>
                  <div class="col-12">
                    <p class="porteur-page-subtle mb-1">Statut financement</p>
                    <span id="modalProjectStatus" class="status-badge status-funded funded">-</span>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn-dashboard outline" data-bs-dismiss="modal">Fermer</button>
              </div>
            </div>
          </div>
        </div>
<?php include __DIR__ . '/components/porteur_page_end.php'; ?>
