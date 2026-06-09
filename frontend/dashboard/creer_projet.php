<?php
require_once __DIR__ . '/components/porteur_helpers.php';

// Page accessible sans authentification
$user = null;
$project = null;
$isEditing = false;
$error_message = '';
$projectId = isset($_GET['id']) ? (int) $_GET['id'] : null;
if ($projectId && $user) {
    $project = \App\Models\Project::where('id', $projectId)->where('user_id', $user->id)->first();
    $isEditing = (bool) $project;
}

$page_title = $isEditing ? 'Modifier le projet' : 'Soumettre projet';
$page_subtitle = $isEditing ? 'Mettez a jour votre dossier avant soumission.' : 'Constituez un dossier complet avant soumission aux institutions.';
$page_active_nav = 'create-project';
$page_document_title = ($isEditing ? 'Modifier projet' : 'Soumettre projet') . ' - ALOGOTO';
$page_inline_styles = <<<'CSS'
    .porteur-checklist {
      list-style: none;
      padding: 0;
      margin: 0;
      display: grid;
      gap: 12px;
    }

    .porteur-checklist li {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      color: var(--text-heading-color);
    }

    .porteur-checklist i {
      color: var(--primary-color-1);
      margin-top: 2px;
    }

    .porteur-form-helper {
      font-size: 12px;
      color: var(--p-color);
    }
CSS;
$page_inline_scripts = <<<'JS'
(function () {
  var checklist = document.getElementById('submissionChecklist');
  if (!checklist) {
    return;
  }
  var url = checklist.getAttribute('data-checklist-url');
  if (!url) {
    return;
  }

  function renderItems(items) {
    if (!items || !items.length) {
      checklist.innerHTML = '<li><i class="bi bi-exclamation-circle"></i><span>Aucune etape a afficher.</span></li>';
      return;
    }
    checklist.innerHTML = items.map(function (item) {
      var icon = item.completed ? 'bi-check-circle-fill' : 'bi-circle';
      return '<li><i class="bi ' + icon + '"></i><span>' + item.label + '</span></li>';
    }).join('');
  }

  fetch(url, { cache: 'no-store' })
    .then(function (response) { return response.json(); })
    .then(function (data) { renderItems(data.items || []); })
    .catch(function () {
      checklist.innerHTML = '<li><i class="bi bi-exclamation-circle"></i><span>Impossible de charger la checklist.</span></li>';
    });
})();

function validateForm(event) {
  event.preventDefault();
  
  var form = document.getElementById('projectForm');
  
  if (!form.checkValidity() === false) {
    event.stopPropagation();
  }
  
  form.classList.add('was-validated');
  
  if (form.checkValidity()) {
    form.submit();
  }
  
  return false;
}

function submitForm(action) {
  var form = document.getElementById('projectForm');
  document.getElementById('formAction').value = action;
  
  if (action === 'brouillon') {
    form.submit();
  } else {
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      alert('Veuillez remplir tous les champs obligatoires avant de soumettre.');
      return false;
    }
    
    if (confirm('Êtes-vous sûr de vouloir soumettre ce projet aux institutions de financement ?')) {
      form.submit();
    }
  }
}

document.addEventListener('DOMContentLoaded', function() {
  var form = document.getElementById('projectForm');
  if (form) {
    form.addEventListener('submit', validateForm);
  }
});
JS;
$formValues = [
    'name' => $project ? ($project->titre ?? '') : '',
    'description' => $project ? ($project->description ?? '') : '',
    'sector' => $project ? ($project->secteur ?? '') : '',
    'amount' => $project ? ($project->montant_demande ?? '') : '',
    'duration' => $project ? ($project->duree ?? '') : '',
    'location' => $project ? ($project->localisation ?? '') : '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'soumettre';
    $statut = ($action === 'brouillon') ? 'brouillon' : 'soumis';
    $statut_soumission = ($action === 'brouillon') ? 'brouillon' : 'soumis';

    $formValues = [
        'name' => $_POST['projectName'] ?? '',
        'description' => $_POST['projectDescription'] ?? '',
        'sector' => $_POST['projectSector'] ?? '',
        'amount' => $_POST['projectAmount'] ?? '',
        'duration' => $_POST['projectDuration'] ?? '',
        'location' => $_POST['projectLocation'] ?? '',
    ];

    // Basic server-side validation for required fields and uploads
    if (trim($formValues['name']) === '' || strlen($formValues['name']) < 3) {
        $error_message = 'Le nom du projet doit contenir au moins 3 caracteres.';
    } elseif (trim($formValues['description']) === '' || strlen($formValues['description']) < 20) {
        $error_message = 'La description doit contenir au moins 20 caracteres.';
    } elseif (trim($formValues['sector']) === '') {
        $error_message = 'Le secteur est obligatoire.';
    } elseif (!is_numeric($formValues['amount']) || (int) $formValues['amount'] < 1) {
        $error_message = 'Le montant doit être un nombre positif.';
    } elseif (trim($formValues['duration']) === '') {
        $error_message = 'La durée est obligatoire.';
    } elseif (trim($formValues['location']) === '') {
        $error_message = 'La localisation est obligatoire.';
    } elseif (empty($_FILES['projectImage']['name'])) {
        $error_message = 'Une image du projet est requise.';
    } elseif (empty($_FILES['projectDocs']['name']) || (is_array($_FILES['projectDocs']['name']) && count(array_filter((array) $_FILES['projectDocs']['name'])) === 0)) {
        $error_message = 'Au moins un document doit être téléversé.';
    }

    $projectId = isset($_POST['project_id']) ? (int) $_POST['project_id'] : null;

    if ($error_message === '' && $projectId) {
        $project = \App\Models\Project::where('id', $projectId)->where('user_id', $user->id)->first();
        if (!$project) {
            $error_message = 'Projet introuvable ou non autorise.';
        } elseif ($project->statut_validation_admin === 'valide' || $project->statut === 'finance') {
            $error_message = 'Projet deja valide ou finance. Modification impossible.';
        } else {
            $project->update([
                'titre' => $formValues['name'],
                'description' => $formValues['description'],
                'secteur' => $formValues['sector'],
                'montant_demande' => (int) $formValues['amount'],
                'duree' => $formValues['duration'],
                'localisation' => $formValues['location'],
                'statut' => $statut,
                'statut_soumission' => $statut_soumission,
            ]);

            header("Location: mes_projets.php");
            exit;
        }
    } else {
        \App\Models\Project::create([
            'user_id' => $user->id,
            'titre' => $formValues['name'],
            'description' => $formValues['description'],
            'secteur' => $formValues['sector'],
            'montant_demande' => (int) $formValues['amount'],
            'duree' => $formValues['duration'],
            'localisation' => $formValues['location'],
            'statut' => $statut,
            'statut_soumission' => $statut_soumission,
        ]);

        header("Location: mes_projets.php");
        exit;
    }
}

include __DIR__ . '/components/porteur_page_start.php';
?>
        <div class="row dashboard-gap-20 mt-2">
          <div class="col-12 col-xl-8">
            <section class="dashboard-card content-card porteur-form-card">
              <div class="dashboard-card__head mb-3">
                <h6><?php echo $isEditing ? 'Modifier le projet' : 'Soumettre projet'; ?></h6>
              </div>

              <?php if ($error_message !== ''): ?>
                <div class="alert alert-danger" role="alert"><?php echo porteur_escape($error_message); ?></div>
              <?php endif; ?>

              <form class="row g-3" action="<?php echo porteur_escape(dashboard_url('creer_projet.php') . ($isEditing ? '?id=' . (int) $projectId : '')); ?>" method="post" enctype="multipart/form-data" id="projectForm" onsubmit="return validateForm(event)">
                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>" />
                <input type="hidden" name="action" id="formAction" value="soumettre" />
                <?php if ($isEditing): ?>
                  <input type="hidden" name="project_id" value="<?php echo (int) $projectId; ?>" />
                <?php endif; ?>
                <div class="col-12 col-md-6">
                  <label class="form-label" for="projectName">Nom du projet <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="projectName" name="projectName" value="<?php echo porteur_escape($formValues['name'] ?? ''); ?>" required />
                  <div class="invalid-feedback">Le nom du projet est obligatoire.</div>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label" for="projectSector">Secteur <span class="text-danger">*</span></label>
                  <?php
                    $sector = $formValues['sector'] ?? '';
                    $sectorNormalized = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $sector));
                    $isSector = function ($value) use ($sector, $sectorNormalized) {
                      $valueNorm = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $value));
                      return $sector === $value || $sectorNormalized === $valueNorm;
                    };
                  ?>
                  <select class="form-select" id="projectSector" name="projectSector" required>
                    <option value="" <?php echo $isSector('') ? 'selected' : ''; ?>>Sélectionner un secteur</option>
                    <option value="Agriculture" <?php echo $isSector('Agriculture') ? 'selected' : ''; ?>>Agriculture</option>
                    <option value="Commerce" <?php echo $isSector('Commerce') ? 'selected' : ''; ?>>Commerce</option>
                    <option value="Technologie" <?php echo $isSector('Technologie') ? 'selected' : ''; ?>>Technologie</option>
                    <option value="Santé" <?php echo $isSector('Sante') || $isSector('Santé') ? 'selected' : ''; ?>>Santé</option>
                  </select>
                  <div class="invalid-feedback">Veuillez sélectionner un secteur.</div>
                </div>

                <div class="col-12">
                  <label class="form-label" for="projectDescription">Description <span class="text-danger">*</span></label>
                  <textarea class="form-control" id="projectDescription" name="projectDescription" required minlength="20"><?php echo porteur_escape($formValues['description'] ?? ''); ?></textarea>
                  <div class="invalid-feedback">La description est obligatoire (minimum 20 caractères).</div>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label" for="projectAmount">Montant recherche (FCFA) <span class="text-danger">*</span></label>
                  <input type="number" class="form-control" id="projectAmount" name="projectAmount" value="<?php echo porteur_escape($formValues['amount'] ?? ''); ?>" required min="1" />
                  <div class="invalid-feedback">Veuillez saisir un montant valide.</div>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label" for="projectDuration">Durée du projet <span class="text-danger">*</span></label>
                  <select class="form-select" id="projectDuration" name="projectDuration" required>
                    <?php $duration = $formValues['duration'] ?? ''; ?>
                    <option value="" <?php echo $duration === '' ? 'selected' : ''; ?>>Sélectionner une durée</option>
                    <option value="court_terme" <?php echo $duration === 'court_terme' ? 'selected' : ''; ?>>Court terme (0-6 mois)</option>
                    <option value="moyen_terme" <?php echo $duration === 'moyen_terme' ? 'selected' : ''; ?>>Moyen terme (6-18 mois)</option>
                    <option value="long_terme" <?php echo $duration === 'long_terme' ? 'selected' : ''; ?>>Long terme (18+ mois)</option>
                  </select>
                  <div class="invalid-feedback">Veuillez sélectionner une durée.</div>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label" for="projectLocation">Localisation <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="projectLocation" name="projectLocation" value="<?php echo porteur_escape($formValues['location'] ?? ''); ?>" required />
                  <div class="invalid-feedback">La localisation est obligatoire.</div>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label" for="projectImage">Image du projet</label>
                  <input type="file" class="form-control" id="projectImage" name="projectImage" accept="image/*" />
                  <div class="form-text porteur-form-helper">Ajoutez un visuel clair (logo, photo terrain ou prototype).</div>
                </div>

                <div class="col-12">
                  <label class="form-label" for="projectDocs">Documents associés</label>
                  <input type="file" class="form-control" id="projectDocs" name="projectDocs[]" multiple />
                  <div class="form-text porteur-form-helper">Business plan, documents légaux, prévisionnels, photos terrain.</div>
                </div>

                <div class="col-12 pt-2">
                  <div class="porteur-action-group">
                    <button type="button" class="dashboard-btn-primary btn-dashboard primary" onclick="submitForm('soumettre')">
                      <i class="bi bi-send"></i>
                      <span>Soumettre le projet</span>
                    </button>
                    <button type="button" class="btn-dashboard outline" onclick="submitForm('brouillon')">
                      <i class="bi bi-save"></i>
                      <span>Enregistrer en brouillon</span>
                    </button>
                  </div>
                </div>
              </form>
            </section>
          </div>

          <div class="col-12 col-xl-4">
            <section class="dashboard-card mb-4">
              <div class="dashboard-card__head mb-3">
                <h6>Checklist de soumission</h6>
              </div>
              <ul class="porteur-checklist" id="submissionChecklist" data-checklist-url="<?php echo htmlspecialchars(dashboard_asset('data/checklist_soumission.json'), ENT_QUOTES, 'UTF-8'); ?>">
                <li><i class="bi bi-arrow-repeat"></i><span>Chargement de la checklist...</span></li>
              </ul>
            </section>

            <section class="dashboard-card">
              <div class="dashboard-card__head mb-3">
                <h6>Apercu du dossier</h6>
              </div>
              <div class="porteur-soft-card">
                <p class="porteur-page-subtle mb-2">Score de completion</p>
                <h4 class="mb-2">82%</h4>
                <div class="dashboard-progress mb-3">
                  <div class="dashboard-progress__bar bg-warning-token" style="width: 82%;"></div>
                </div>
                <p class="porteur-page-subtle mb-0">Ajoutez une lettre d engagement fournisseur pour atteindre un dossier premium.</p>
              </div>
            </section>
          </div>
        </div>
<?php include __DIR__ . '/components/porteur_page_end.php'; ?>
