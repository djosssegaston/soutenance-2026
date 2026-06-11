<?php
require_once __DIR__ . '/header.php';

// Les variables $documents et $projectIds sont passées par le contrôleur DashboardPagesController
// Pas de fallback nécessaire - le contrôleur s'en charge

// Calcul des statistiques dynamiques
$totalDocs = $documents->count();
$validatedDocs = $documents->where('statut_validation', 'valide')->count();
$pendingDocs = $documents->where('statut_validation', 'en_attente')->count();
$rejectedDocs = $documents->where('statut_validation', 'rejete')->count();

// Grouper les documents par projet
$projects = $documents->groupBy(function ($doc) {
    return $doc->project_id;
})->map(function ($docs, $projectId) {
    $first = $docs->first();
    return [
        'id' => $projectId,
        'titre' => $first?->project?->titre ?? '',
        'docs_count' => $docs->count(),
        'validated_count' => $docs->where('statut_validation', 'valide')->count(),
        'pending_count' => $docs->where('statut_validation', 'en_attente')->count(),
        'rejected_count' => $docs->where('statut_validation', 'rejete')->count(),
        'documents' => $docs->map(function ($doc) {
            return [
                'id' => $doc->id,
                'fichier' => $doc->fichier,
                'type' => $doc->type,
                'statut_validation' => $doc->statut_validation,
                'uploaded_at' => $doc->uploaded_at?->toIso8601String(),
                'created_at' => $doc->created_at?->toIso8601String(),
                'raison_rejet' => $doc->raison_rejet,
                'project_titre' => $doc->project?->titre ?? '',
            ];
        })->values(),
    ];
})->values();
?>

<!-- Styles spécifiques à la page documents -->
<style>
/* Toast notifications */
.toast-notification {
    position: fixed; top: 20px; right: 20px; z-index: 9999;
    min-width: 300px; max-width: 400px;
    background: white; border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    transform: translateX(120%); transition: transform 0.4s ease;
}
.toast-notification.show { transform: translateX(0); }
.toast-notification.success { border-left: 4px solid #00C486; }
.toast-notification.error { border-left: 4px solid #F35120; }

/* Badges pour les jours écoulés */
.days-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px; border-radius: 12px;
    font-size: 11px; font-weight: 600;
}
.days-warning { background: rgba(252, 160, 40, 0.15); color: #FCA028; }
.days-danger { background: rgba(243, 81, 32, 0.15); color: #F35120; }

.search-box { max-width: 250px; }
</style>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            
            <!-- PAGE HEADER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title"><strong>Documents</strong></h1>
                    <p class="text-muted mb-0">Gérez, visualisez et téléchargez tous vos documents par projet.</p>
                </div>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Accueil</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Documents</li>
                    </ol>
                </div>
            </div>

            <!-- STATISTIQUES -->
            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fs-12 fw-semibold">Total documents</p>
                                    <h3 class="mb-1 number-font"><?php echo $totalDocs; ?></h3>
                                    <span class="text-muted fs-12">Tous projets confondus</span>
                                </div>
                                <div class="avatar avatar-lg bg-success-transparent rounded-circle text-success">
                                    <i class="bi bi-folder-check fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fs-12 fw-semibold">Validés</p>
                                    <h3 class="mb-1 number-font"><?php echo $validatedDocs; ?></h3>
                                    <span class="text-muted fs-12">Prêts à utiliser</span>
                                </div>
                                <div class="avatar avatar-lg bg-primary-transparent rounded-circle text-primary">
                                    <i class="bi bi-patch-check fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fs-12 fw-semibold">En attente</p>
                                    <h3 class="mb-1 number-font"><?php echo $pendingDocs; ?></h3>
                                    <span class="text-muted fs-12">En cours d'examen</span>
                                </div>
                                <div class="avatar avatar-lg bg-warning-transparent rounded-circle text-warning">
                                    <i class="bi bi-hourglass-split fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fs-12 fw-semibold">Rejetés</p>
                                    <h3 class="mb-1 number-font"><?php echo $rejectedDocs; ?></h3>
                                    <span class="text-muted fs-12">À renvoyer</span>
                                </div>
                                <div class="avatar avatar-lg bg-danger-transparent rounded-circle text-danger">
                                    <i class="bi bi-x-circle fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BARRE D'ACTIONS -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body py-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <h5 class="mb-1">Gestion de mes documents (<?php echo $totalDocs; ?>)</h5>
                                    <p class="text-muted mb-0 fs-13">Retrouvez, gérez et uploadez vos documents liés à vos projets</p>
                                </div>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <div class="position-relative">
                                        <i class="bi bi-search position-absolute" style="left:10px; top:50%; transform:translateY(-50%); z-index:10;"></i>
                                        <input type="text" class="form-control ps-5" id="searchDocs" 
                                        placeholder="Rechercher un projet..." style="width:250px;"
                                        onkeyup="filterProjects()">
                                    </div>
                                    <button type="button" class="btn btn-outline-info" onclick="location.reload()">
                                        <i class="bi bi-arrow-clockwise me-1"></i>Actualiser
                                    </button>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                                        <i class="bi bi-cloud-upload me-1"></i>Ajouter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PROJETS AVEC DOCUMENTS -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header justify-content-between">
                            <h4 class="card-title"><i class="bi bi-folder me-2"></i>Mes Projets</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($documents->isEmpty()): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <h5 class="mt-3 text-muted">Aucun document uploadé</h5>
                                    <p class="text-muted">Cliquez sur "Ajouter" pour commencer à uploadez vos fichiers.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="projectsTable">
                                        <thead>
                                            <tr>
                                                <th>Projet</th>
                                                <th class="text-center">Total Docs</th>
                                                <th class="text-center">Validés</th>
                                                <th class="text-center">En attente</th>
                                                <th class="text-center">Rejetés</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="projectsBody">
                                            <?php foreach ($projects as $project): ?>
                                            <tr data-titre="<?php echo strtolower(htmlspecialchars($project['titre'], ENT_QUOTES, 'UTF-8')); ?>">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-folder2-open text-primary me-2 fs-5"></i>
                                                        <span class="fw-medium"><?php echo htmlspecialchars($project['titre'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                    </div>
                                                </td>
                                                <td class="text-center"><span class="badge bg-secondary"><?php echo $project['docs_count']; ?></span></td>
                                                <td class="text-center"><span class="badge bg-success"><?php echo $project['validated_count']; ?></span></td>
                                                <td class="text-center"><span class="badge bg-warning"><?php echo $project['pending_count']; ?></span></td>
                                                <td class="text-center"><?php echo $project['rejected_count'] > 0 ? '<span class="badge bg-danger">' . $project['rejected_count'] . '</span>' : '<span class="text-muted">0</span>'; ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary consulter-btn"
                                                            data-id="<?php echo $project['id']; ?>"
                                                            data-titre="<?php echo htmlspecialchars($project['titre'], ENT_QUOTES, 'UTF-8'); ?>">
                                                        <i class="bi bi-eye me-1"></i> Consulter
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Ajouter -->
<div class="modal fade" id="addDocumentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/dashboard/porteur/documents/upload" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-cloud-upload me-2"></i>Ajouter un document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Projet <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-select" required>
                            <option value="">Sélectionnez un projet</option>
                            <?php
                            $user = auth()->user();
                            if ($user):
                                $userProjects = $user->projects()->get();
                                foreach ($userProjects as $proj):
                            ?>
                                <option value="<?php echo $proj->id; ?>"><?php echo htmlspecialchars($proj->titre, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="">Sélectionnez</option>
                            <option value="piece_identite">Pièce d'identité</option>
                            <option value="photo_identite">Photo d'identité</option>
                            <option value="justificatif_residence">Justificatif de résidence</option>
                            <option value="business_plan">Business plan</option>
                            <option value="plan_activite">Plan d'activité</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fichier <span class="text-danger">*</span></label>
                        <input type="file" name="fichier" class="form-control" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <small class="text-muted">Formats: PDF, JPG, PNG, DOC, DOCX (Max: 10 Mo)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Uploader</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Remplacer -->
<div class="modal fade" id="replaceDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="replaceForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-arrow-repeat me-2"></i>Remplacer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Remplacer le document : <strong id="replaceDocType"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nouveau fichier <span class="text-danger">*</span></label>
                        <input type="file" name="fichier" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Remplacer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Supprimer -->
<div class="modal fade" id="deleteDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="_method" value="DELETE">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Confirmer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Supprimer le document :</p>
                    <p><strong id="deleteDocName"></strong> ?</p>
                    <p class="text-danger"><small>Cette action est irréversible.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Documents Projet -->
<div class="modal fade" id="projetDocsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-folder me-2"></i>Documents du Projet</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold" id="modal-projet-titre"></h6>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Fichier</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="modal-docs-body"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Données projets JSON pour JS -->
<script id="projects-data" type="application/json"><?php echo json_encode($projects); ?></script>

<!-- Toast Notification -->
<div class="toast-notification" id="toastNotification">
    <div class="p-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi fs-4" id="toastIcon"></i>
            <div class="flex-grow-1">
                <strong id="toastTitle"></strong>
                <div class="small" id="toastMessage"></div>
            </div>
            <button type="button" class="btn-close" onclick="hideToast()"></button>
        </div>
    </div>
</div>

<script>
const projectsData = JSON.parse(document.getElementById('projects-data').textContent);

function filterProjects() {
    const val = document.getElementById('searchDocs').value.toLowerCase();
    const rows = document.querySelectorAll('#projectsBody tr[data-titre]');
    rows.forEach(row => {
        const titre = row.dataset.titre;
        row.style.display = titre.includes(val) ? '' : 'none';
    });
}

function openProjetDocs(projectId, titre) {
    document.getElementById('modal-projet-titre').textContent = titre;

    const project = projectsData.find(p => String(p.id) === String(projectId));
    const docs = project ? project.documents : [];

    const tbody = document.getElementById('modal-docs-body');
    tbody.innerHTML = '';

    if (docs.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Aucun document pour ce projet.</td></tr>';
    } else {
        docs.forEach(doc => {
            const statusBadges = {
                'valide': '<span class="badge bg-success-transparent text-success">Validé</span>',
                'en_attente': '<span class="badge bg-warning-transparent text-warning">En attente</span>',
                'rejete': '<span class="badge bg-danger-transparent text-danger">Rejeté</span>',
            };
            let statusHtml = statusBadges[doc.statut_validation] || '<span class="badge bg-secondary">' + doc.statut_validation + '</span>';

            const uploadedAt = doc.uploaded_at || doc.created_at;
            if (doc.statut_validation === 'en_attente' && uploadedAt) {
                const days = Math.floor((Date.now() - new Date(uploadedAt).getTime()) / (1000 * 60 * 60 * 24));
                statusHtml += '<br><span class="days-badge ' + (days > 7 ? 'days-danger' : 'days-warning') + '">' + days + ' jour(s)</span>';
            } else if (doc.statut_validation === 'rejete') {
                statusHtml += '<br><span class="days-badge days-danger">Rejeté</span>';
                if (doc.raison_rejet) {
                    statusHtml += '<br><small class="text-danger" title="' + doc.raison_rejet + '">' + (doc.raison_rejet.length > 30 ? doc.raison_rejet.substring(0, 30) + '...' : doc.raison_rejet) + '</small>';
                }
            }

            const dateStr = uploadedAt ? new Date(uploadedAt).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i>
                        <div>
                            <strong>${doc.fichier.split('/').pop()}</strong>
                            ${uploadedAt ? '<br><small class="text-muted">' + new Date(uploadedAt).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' }) + '</small>' : ''}
                        </div>
                    </div>
                </td>
                <td><span class="badge bg-info-transparent text-info">${doc.type}</span></td>
                <td>${statusHtml}</td>
                <td>${dateStr}</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="/documents/secure/project/${doc.id}/view" class="btn btn-outline-info" title="Voir"><i class="bi bi-eye"></i></a>
                        <a href="/documents/secure/project/${doc.id}/download" class="btn btn-outline-primary" title="Télécharger"><i class="bi bi-download"></i></a>
                        <button type="button" class="btn btn-outline-warning" title="Remplacer" onclick="showReplaceModal(${doc.id}, '${doc.type.replace(/'/g, "\\'")}')"><i class="bi bi-arrow-repeat"></i></button>
                        <button type="button" class="btn btn-outline-danger" title="Supprimer" onclick="confirmDelete(${doc.id}, '${doc.fichier.split('/').pop().replace(/'/g, "\\'")}')"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    bootstrap.Modal.getOrCreateInstance(document.getElementById('projetDocsModal')).show();
}

// Modal remplacement
function showReplaceModal(docId, docType) {
    document.getElementById('replaceDocType').textContent = docType;
    document.getElementById('replaceForm').action = '/dashboard/porteur/documents/' + docId + '/replace';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('replaceDocumentModal')).show();
}

// Modal suppression
function confirmDelete(docId, docName) {
    document.getElementById('deleteDocName').textContent = docName;
    document.getElementById('deleteForm').action = '/dashboard/porteur/documents/' + docId;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteDocumentModal')).show();
}

// Toast notification
function showToast(type, title, message) {
    const toast = document.getElementById('toastNotification');
    document.getElementById('toastIcon').className = 'bi fs-4 bi-' + (type === 'success' ? 'check-circle' : 'exclamation-circle');
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMessage').textContent = message;
    toast.className = 'toast-notification ' + type + ' show';
    setTimeout(() => { hideToast(); }, 4000);
}

function hideToast() {
    document.getElementById('toastNotification').classList.remove('show');
}

// Afficher les messages PHP
<?php if (session()->has('success')): ?>
showToast('success', 'Succès', '<?php echo str_replace("'", "\\'", session('success')); ?>');
<?php elseif (session()->has('error')): ?>
showToast('error', 'Erreur', '<?php echo str_replace("'", "\\'", session('error')); ?>');
<?php endif; ?>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.consulter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            openProjetDocs(this.dataset.id, this.dataset.titre);
        });
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
