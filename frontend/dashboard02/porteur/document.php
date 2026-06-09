<?php
require_once __DIR__ . '/header.php';

// Les variables $documents et $projectIds sont passées par le contrôleur DashboardPagesController
// Pas de fallback nécessaire - le contrôleur s'en charge

// Calcul des statistiques dynamiques
$totalDocs = $documents->count();
$validatedDocs = $documents->where('statut_validation', 'valide')->count();
$pendingDocs = $documents->where('statut_validation', 'en_attente')->count();
$rejectedDocs = $documents->where('statut_validation', 'rejete')->count();
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
                                               placeholder="Rechercher..." style="width:250px;"
                                               onkeyup="filterDocuments()">
                                    </div>
                                    <select class="form-select w-auto" id="filterStatus" onchange="filterByStatus()">
                                        <option value="all">Tous les statuts</option>
                                        <option value="valide">Validés</option>
                                        <option value="en_attente">En attente</option>
                                        <option value="rejete">Rejetés</option>
                                    </select>
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

            <!-- TABLEAU DES DOCUMENTS -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header justify-content-between">
                            <h4 class="card-title">Liste des documents</h4>
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
                                    <table class="table table-hover table-striped" id="documentsTable">
                                        <thead>
                                            <tr>
                                                <th>Fichier</th>
                                                <th>Type</th>
                                                <th>Projet</th>
                                                <th>Statut</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="documentsBody">
                                            <?php foreach ($documents as $index => $doc): ?>
                                                <?php
                                                $statusClass = match($doc->statut_validation) {
                                                    'valide' => 'bg-success-transparent text-success',
                                                    'en_attente' => 'bg-warning-transparent text-warning',
                                                    'rejete' => 'bg-danger-transparent text-danger',
                                                    default => 'bg-secondary-transparent text-secondary',
                                                };
                                                $statusLabel = match($doc->statut_validation) {
                                                    'valide' => 'Validé',
                                                    'en_attente' => 'En attente',
                                                    'rejete' => 'Rejeté',
                                                    default => ucfirst($doc->statut_validation),
                                                };
                                                $daysElapsed = $doc->uploaded_at 
                                                    ? now()->diffInDays($doc->uploaded_at) 
                                                    : ($doc->created_at ? now()->diffInDays($doc->created_at) : null);
                                                ?>
                                                <tr data-status="<?php echo $doc->statut_validation; ?>"
                                                    data-name="<?php echo strtolower(htmlspecialchars(basename($doc->fichier), ENT_QUOTES, 'UTF-8')); ?>"
                                                    data-type="<?php echo strtolower($doc->type); ?>"
                                                    data-project="<?php echo strtolower(htmlspecialchars($doc->project->titre ?? '', ENT_QUOTES, 'UTF-8')); ?>">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                                            <div>
                                                                <strong><?php echo htmlspecialchars(basename($doc->fichier), ENT_QUOTES, 'UTF-8'); ?></strong>
                                                                <?php if ($doc->uploaded_at): ?>
                                                                    <br><small class="text-muted"><?php echo $doc->uploaded_at->diffForHumans(); ?></small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info-transparent text-info"><?php echo htmlspecialchars($doc->type, ENT_QUOTES, 'UTF-8'); ?></span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($doc->project->titre ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td>
                                                        <span class="badge <?php echo $statusClass; ?>">
                                                            <?php echo $statusLabel; ?>
                                                        </span>
                                                        <?php if ($doc->statut_validation === 'en_attente' && $daysElapsed !== null): ?>
                                                            <br><span class="days-badge <?php echo $daysElapsed > 7 ? 'days-danger' : 'days-warning'; ?>">
                                                                <?php echo $daysElapsed; ?> jour(s)
                                                            </span>
                                                        <?php elseif ($doc->statut_validation === 'rejete'): ?>
                                                            <br><span class="days-badge days-danger">
                                                                Rejeté
                                                            </span>
                                                            <?php if ($doc->raison_rejet): ?>
                                                                <br><small class="text-danger" title="<?php echo htmlspecialchars($doc->raison_rejet, ENT_QUOTES, 'UTF-8'); ?>">
                                                                    <?php echo htmlspecialchars(substr($doc->raison_rejet, 0, 30), ENT_QUOTES, 'UTF-8') . (strlen($doc->raison_rejet) > 30 ? '...' : ''); ?>
                                                                </small>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        if ($doc->uploaded_at) {
                                                            echo $doc->uploaded_at->format('d/m/Y H:i');
                                                        } elseif ($doc->created_at) {
                                                            echo $doc->created_at->format('d/m/Y H:i');
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <a href="/documents/secure/project/<?php echo $doc->id; ?>/view" 
                                                               class="btn btn-outline-info" title="Voir">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                            <a href="/documents/secure/project/<?php echo $doc->id; ?>/download" 
                                                               class="btn btn-outline-primary" title="Télécharger">
                                                                <i class="bi bi-download"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-outline-warning" 
                                                                    title="Remplacer"
                                                                    onclick="showReplaceModal(<?php echo $doc->id; ?>, '<?php echo str_replace("'", "\\'", htmlspecialchars($doc->type, ENT_QUOTES, 'UTF-8')); ?>')">
                                                                <i class="bi bi-arrow-repeat"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger" 
                                                                    title="Supprimer"
                                                                    onclick="confirmDelete(<?php echo $doc->id; ?>, '<?php echo str_replace("'", "\\'", htmlspecialchars(basename($doc->fichier), ENT_QUOTES, 'UTF-8')); ?>')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-center mt-3" id="documentsPagination"></div>
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
const PAGINATION_ROWS = 10;

function updatePagination(tableBodyId, paginationId) {
    const tbody = document.getElementById(tableBodyId);
    const nav = document.getElementById(paginationId);
    if (!tbody || !nav) return;

    const rows = Array.from(tbody.children).filter(row => {
        if (row.dataset && row.dataset.filtered === 'true') return false;
        if (row.querySelector('td[colspan]')) return false;
        return true;
    });

    const totalPages = Math.max(1, Math.ceil(rows.length / PAGINATION_ROWS));
    let currentPage = parseInt(nav.dataset.currentPage || '1', 10);
    if (currentPage > totalPages) currentPage = totalPages;
    nav.dataset.currentPage = currentPage;

    Array.from(tbody.children).forEach(row => {
        if (row.dataset && row.dataset.filtered === 'true') {
            row.style.display = 'none';
        } else if (!row.querySelector('td[colspan]')) {
            const idx = rows.indexOf(row);
            if (idx !== -1) {
                row.style.display = (idx >= (currentPage - 1) * PAGINATION_ROWS && idx < currentPage * PAGINATION_ROWS) ? '' : 'none';
            }
        }
    });

    if (totalPages <= 1) { nav.innerHTML = ''; return; }

    let html = '<nav aria-label="Pagination"><ul class="pagination pagination-sm justify-content-center mb-0">';
    html += `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="return paginationGo('${tableBodyId}','${paginationId}',${currentPage - 1})"><i class="bi bi-chevron-left"></i></a></li>`;
    for (let i = 1; i <= totalPages; i++) {
        html += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="return paginationGo('${tableBodyId}','${paginationId}',${i})">${i}</a></li>`;
    }
    html += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="return paginationGo('${tableBodyId}','${paginationId}',${currentPage + 1})"><i class="bi bi-chevron-right"></i></a></li>`;
    html += '</ul></nav>';
    nav.innerHTML = html;
}

function paginationGo(tableBodyId, paginationId, page) {
    const nav = document.getElementById(paginationId);
    if (nav) nav.dataset.currentPage = page;
    updatePagination(tableBodyId, paginationId);
    return false;
}

function resetPagination(tableBodyId, paginationId) {
    const nav = document.getElementById(paginationId);
    if (nav) nav.dataset.currentPage = 1;
    updatePagination(tableBodyId, paginationId);
}

// Filtrage par statut
function filterByStatus() {
    const val = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('#documentsTable tbody tr');
    rows.forEach(row => {
        const match = val === 'all' || row.dataset.status === val;
        row.dataset.filtered = match ? 'false' : 'true';
        row.style.display = match ? '' : 'none';
    });
    resetPagination('documentsBody', 'documentsPagination');
}

// Recherche
function filterDocuments() {
    const val = document.getElementById('searchDocs').value.toLowerCase();
    const rows = document.querySelectorAll('#documentsTable tbody tr');
    rows.forEach(row => {
        const name = row.dataset.name;
        const type = row.dataset.type;
        const project = row.dataset.project;
        const match = name.includes(val) || type.includes(val) || project.includes(val);
        row.dataset.filtered = match ? 'false' : 'true';
        row.style.display = match ? '' : 'none';
    });
    resetPagination('documentsBody', 'documentsPagination');
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
    updatePagination('documentsBody', 'documentsPagination');
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
