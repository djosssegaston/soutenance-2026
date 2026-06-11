<?php
require_once __DIR__ . '/header.php';

use Illuminate\Support\Facades\Crypt;

function encryptId($id): string {
    return strtr(Crypt::encryptString((string)$id), '+/', '-_');
}

// Les projets sont passés par le contrôleur DashboardPagesController
$projects = $projects ?? collect();

// Calcul des statistiques
$totalSoumis = $projects->filter(function($p) {
    return in_array($p->statut, ['submitted', 'under_admin_review', 'admin_validated']);
})->count();

$totalFinances = $projects->filter(function($p) {
    $aFundingDecaisse = $p->financements->contains(fn($f) => in_array($f->statut, ['disbursed', 'active']));
    return in_array($p->statut, ['funded', 'active', 'completed']) || $aFundingDecaisse;
})->count();

$totalRejetes = $projects->filter(function($p) {
    return in_array($p->statut, ['admin_rejected', 'institution_rejected']);
})->count();

$totalBrouillons = $projects->filter(function($p) {
    return $p->statut === 'draft';
})->count();

$projectsJson = $projects->map(function ($p) {
    $statusEnum = $p->statusEnum();
    $aFundingDecaisse = $p->financements->contains(fn($f) => in_array($f->statut, ['disbursed', 'active']));
    $aFundingPropose = $p->financements->contains(fn($f) => in_array($f->statut, ['proposed', 'awaiting_borrower_plan', 'awaiting_imf_validation']));
    return [
        'id' => $p->id,
        'encrypted_id' => encryptId($p->id),
        'titre' => $p->titre,
        'secteur' => $p->secteur ?? '',
        'montant_demande' => (float) $p->montant_demande,
        'montant_finance' => (float) ($p->montant_finance ?? 0),
        'statut' => $p->statut,
        'statut_label' => $statusEnum->isFinanced() || $aFundingDecaisse ? 'Projet déjà financé' : ($aFundingPropose ? 'Financement proposé' : $statusEnum->label()),
        'statut_color' => $statusEnum->color(),
        'description' => $p->description ?? '',
        'created_at' => $p->created_at ? $p->created_at->format('d M Y') : '',
        'updated_at' => $p->updated_at ? $p->updated_at->format('d M Y') : '',
        'institution_nom' => $p->financements->first()?->institution?->nom ?? '',
        'progression' => $p->montant_demande > 0 ? (int) round((($p->montant_finance ?? 0) / $p->montant_demande) * 100) : 0,
        'can_edit' => $statusEnum->canOwnerModify() || $statusEnum === \App\Enums\ProjectStatus::UNDER_ADMIN_REVIEW,
    ];
})->values();
?>
<script id="projectsData" type="application/json"><?php echo json_encode($projectsJson, JSON_UNESCAPED_UNICODE); ?></script>

            <!--{ app content start }-->
            <div class="main-content app-content mt-0">
                <div class="side-app">
                    <!--{ container start }-->
                    <div class="main-container container-fluid">
                        <!--{ PAGE HEADER START }-->
                        <div class="page-header">
                            <h1 class="page-title">MES PROJETS</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">ACCEUIL</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">MES PROJETS</li>
                                </ol>
                            </div>
                        </div>
                        <!--{ PAGE HEADER END }-->

                        <!--{ row-1 start}-->
                        <div class="row">
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                    <div class="card-body">
                                       <div class="media align-items-center flex-wrap">
                                          <div class="media-body">
                                             <span class="text-muted text-uppercase fs-12 fw-bold">TOTAL SOUMIS</span>
                                             <h2 class="mb-0 mt-1"><?php echo $totalSoumis; ?></h2>
                                          </div>
                                          <div class="align-self-center">
                                             <span class="avatar avatar-md brround  bg-primary">
                                                <i class="bx bx-cart fs-20"></i>
                                             </span>
                                          </div>
                                          <div class="progress progress-sm mt-3 w-100">
                                             <div class="progress-bar bg-primary" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                   <div class="card-body">
                                      <div class="media align-items-center flex-wrap">
                                         <div class="media-body">
                                            <span class="text-muted text-uppercase fs-12 fw-bold">TOTAL FINANCIER</span>
                                            <h2 class="mb-0 mt-1"><?php echo $totalFinances; ?></h2>
                                         </div>
                                         <div class="align-self-center">
                                            <span class="avatar avatar-md brround  bg-info">
                                               <i class="bx bxs-shopping-bags fs-20"></i>
                                            </span>
                                         </div>
                                         <div class="progress progress-sm mt-3 w-100">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                         </div>
                                      </div>
                                   </div>
                                </div>
                             </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="card">
                                   <div class="card-body">
                                      <div class="media align-items-center flex-wrap">
                                         <div class="media-body">
                                            <span class="text-muted text-uppercase fs-12 fw-bold">TOTAL REJETÉS</span>
                                            <h2 class="mb-0 mt-1"><?php echo $totalRejetes; ?></h2>
                                         </div>
                                         <div class="align-self-center">
                                            <span class="avatar avatar-md brround  bg-warning">
                                               <i class="bx bx-money fs-20"></i>
                                            </span>
                                         </div>
                                         <div class="progress progress-sm mt-3 w-100">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: 80%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                         </div>
                                      </div>
                                   </div>
                                </div>
                             </div>
                              <div class="col-md-6 col-xl-3">
                                 <div class="card">
                                    <div class="card-body">
                                       <div class="media align-items-center flex-wrap">
                                          <div class="media-body">
                                             <span class="text-muted text-uppercase fs-12 fw-bold">BROUILLONS</span>
                                             <h2 class="mb-0 mt-1"><?php echo $totalBrouillons; ?></h2>
                                          </div>
                                          <div class="align-self-center">
                                             <span class="avatar avatar-md brround  bg-success">
                                                <i class="bx bx-user-plus fs-20"></i>
                                             </span>
                                          </div>
                                          <div class="progress progress-sm mt-3 w-100">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 95%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                        </div>
                        <!--{ row-1 end}-->
                         <!-- ajouter un projet debut -->
                         <div class="row">
                             <div class="col-12">
                                 <div class="card mb-2">
                                     <div class="card-body">
                                         <div class="grid-wrap-header">
                                             <ul class="gridfilter-list">
                                             </ul>
                                             <div><a class="btn btn-primary" href="creer_projet.php"> <i class="ti ti-plus mr-5"></i>Ajouter Project</a>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <!-- ajouter un projet fin -->
                       <!--{ row-3 start}-->
                         <div class="row">
                            <div class="col-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header justify-content-between">
                                        <h4 class="card-title">Projet Recent</h4>
                                        <div class="d-flex gap-2">
                                            <input type="text" class="form-control form-control-sm" id="searchProject" placeholder="Rechercher..." onkeyup="filterProjects()">
                                            <select class="form-select form-select-sm" id="filterStatus" onchange="filterByStatus()">
                                                <option value="all">Tous les statuts</option>
                                                <option value="draft">Brouillon</option>
                                                <option value="submitted">Soumis</option>
                                                <option value="under_admin_review">En révision admin</option>
                                                <option value="admin_rejected">Rejeté par admin</option>
                                                <option value="admin_validated">Validé par admin</option>
                                                <option value="under_institution_review">En analyse institution</option>
                                                <option value="institution_rejected">Rejeté par institution</option>
                                                <option value="institution_accepted">Accepté par institution</option>
                                                <option value="funded">Financé</option>
                                                <option value="active">En cours</option>
                                                <option value="completed">Terminé</option>
                                            </select>
                                            <select class="form-select form-select-sm" id="sortBy" onchange="sortProjects()">
                                                <option value="recent">Plus récent</option>
                                                <option value="oldest">Plus ancien</option>
                                                <option value="amount_asc">Montant croissant</option>
                                                <option value="amount_desc">Montant décroissant</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table text-nowrap mb-0" id="projectsTable">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">N°</th>
                                                        <th scope="col">PROJET</th>
                                                        <th scope="col">SECTEUR</th>
                                                        <th scope="col">MONTANT</th>
                                                        <th scope="col">STATUT</th>
                                                        <th scope="col">PROGRESSION</th>
                                                        <th scope="col">ACTIONS</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="projectsBody">
                                                    <?php
                                                    $index = 0;
                                                    foreach ($projects as $project):
                                                        $index++;
                                                        $statusEnum = $project->statusEnum();
                                                        $aFundingDecaisse = $project->financements->contains(fn($f) => in_array($f->statut, ['disbursed', 'active']));
$aFundingPropose = $project->financements->contains(fn($f) => in_array($f->statut, ['proposed', 'awaiting_borrower_plan', 'awaiting_imf_validation']));
$statusLabel = $statusEnum->isFinanced() || $aFundingDecaisse ? 'Projet déjà financé' : ($aFundingPropose ? 'Financement proposé' : $statusEnum->label());
                                                        $statusColor = $statusEnum->color();
                                                        $funded = (int) $project->montant_finance;
                                                        $progress = $project->montant_demande > 0 ? (int) round(($funded / $project->montant_demande) * 100) : 0;
                                                        $canEdit = $statusEnum->canOwnerModify();
                                                        $canDelete = $statusEnum->canOwnerModify();
                                                        $canSubmit = $statusEnum->canOwnerSubmit();
                                                    ?>
                                                    <tr data-status="<?php echo $project->statut; ?>"
                                                        data-name="<?php echo strtolower(htmlspecialchars($project->titre, ENT_QUOTES, 'UTF-8')); ?>"
                                                        data-sector="<?php echo strtolower(htmlspecialchars($project->secteur ?? '', ENT_QUOTES, 'UTF-8')); ?>"
                                                        data-amount="<?php echo $project->montant_demande; ?>"
                                                        data-date="<?php echo $project->created_at ? $project->created_at->timestamp : 0; ?>">
                                                        <td><?php echo $index; ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar avatar-md bg-primary-transparent rounded-circle me-2">
                                                                    <span class="fw-bold"><?php echo strtoupper(substr($project->titre, 0, 2)); ?></span>
                                                                </div>
                                                                <div class="d-flex flex-column">
                                                                    <span class="fw-medium lh-1"><?php echo htmlspecialchars($project->titre, ENT_QUOTES, 'UTF-8'); ?></span>
                                                                    <small class="text-muted"><?php echo $project->created_at ? $project->created_at->format('d M Y') : ''; ?></small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge bg-info-transparent rounded-pill text-info p-2 me-3">
                                                                    <i class="fe fe-tag"></i>
                                                                </span>
                                                                <?php echo htmlspecialchars($project->secteur ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="text-muted lh-1">
                                                                <span class="text-primary fw-medium"><?php echo number_format($project->montant_demande, 0, ',', ' '); ?> FCFA</span>
                                                                <?php if ($funded > 0): ?>
                                                                <br><small class="text-success"><?php echo number_format($funded, 0, ',', ' '); ?> FCFA financés</small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $statusColor; ?>-transparent rounded-pill text-<?php echo $statusColor; ?> p-2 px-3"><?php echo $statusLabel; ?></span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="progress progress-sm flex-grow-1" style="height:6px; min-width:80px;">
                                                                    <div class="progress-bar bg-<?php echo $progress >= 100 ? 'success' : 'primary'; ?>"
                                                                         role="progressbar"
                                                                         style="width: <?php echo min($progress, 100); ?>%"
                                                                         aria-valuenow="<?php echo $progress; ?>"
                                                                         aria-valuemin="0"
                                                                         aria-valuemax="100"></div>
                                                                </div>
                                                                <span class="fs-12 text-muted"><?php echo $progress; ?>%</span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="g-1">
                                                                <?php if ($canEdit): ?>
                                                                <a class="btn text-primary btn-sm" data-bs-toggle="tooltip" data-bs-original-title="Modifier" href="creer_projet.php?id=<?php echo encryptId($project->id); ?>">
                                                                    <i class="fe fe-edit fs-14"></i>
                                                                </a>
                                                                <?php endif; ?>
                                                                <a class="btn text-info btn-sm" data-bs-toggle="tooltip" data-bs-original-title="Voir les détails" href="javascript:void(0)" onclick="viewProjectDetails(<?php echo $project->id; ?>)">
                                                                    <i class="fe fe-eye fs-14"></i>
                                                                </a>
                                                                <?php if ($canSubmit): ?>
<form method="POST" action="/dashboard/porteur/projects/<?php echo $project->id; ?>/submit" style="display:inline;">
                                                                     <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                                     <button type="submit" class="btn text-success btn-sm btn-submit-project" data-bs-toggle="tooltip" data-bs-original-title="Soumettre">
                                                                         <i class="fe fe-send fs-14"></i>
                                                                     </button>
                                                                 </form>
                                                                <?php endif; ?>
                                                                <?php if ($canDelete): ?>
                                                                 <form method="POST" action="/dashboard/porteur/projects/<?php echo $project->id; ?>" style="display:inline;">
                                                                     <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                                                     <input type="hidden" name="_method" value="DELETE">
                                                                     <button type="submit" class="btn text-danger btn-sm" data-bs-toggle="tooltip" data-bs-original-title="Supprimer" data-confirm="Supprimer ce projet ?" data-confirm-text="Oui, supprimer">
                                                                        <i class="fe fe-trash-2 fs-14"></i>
                                                                    </button>
                                                                </form>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                    <?php if ($projects->isEmpty()): ?>
                                                    <tr id="noProjectsRow">
                                                        <td colspan="7" class="text-center py-5">
                                                            <i class="bx bx-folder-open fs-1 text-muted"></i>
                                                            <h5 class="mt-3 text-muted">Aucun projet pour le moment</h5>
                                                            <p class="text-muted">Cliquez sur "Ajouter Project" pour créer votre premier projet.</p>
                                                        </td>
                                                    </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center mt-3" id="projectsPagination"></div>

                         </div>
                        <!--{ row-3 end}-->
                    </div>
                    <!--{ container end }-->
                </div>
            </div>
        </div>

<!-- MODAL DÉTAIL PROJET -->
<div class="modal fade" id="projectDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalProjectTitle">Détail du Projet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-lg-8 col-md-7">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Titre</h6>
                        <p class="fw-medium text-break" id="md-title">—</p>

                        <h6 class="text-muted text-uppercase fs-12 fw-semibold mt-3">Description</h6>
                        <div class="text-muted text-break" id="md-description">—</div>
                    </div>
                    <div class="col-lg-4 col-md-5">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body text-center py-3 d-flex flex-column justify-content-center h-100">
                                <h6 class="text-muted text-uppercase fs-11 fw-semibold">Statut</h6>
                                <span class="badge fs-13 px-3 py-2" id="md-status">—</span>
                                <hr class="my-2">
                                <h6 class="text-muted text-uppercase fs-11 fw-semibold">Secteur</h6>
                                <p class="mb-0 fw-medium" id="md-secteur">—</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Montant Demandé</h6>
                        <p class="fs-18 fw-bold text-primary mb-0 text-break" id="md-montant-demande">—</p>
                    </div>
                    <div class="col-sm-6">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Montant Financé</h6>
                        <p class="fs-18 fw-bold text-success mb-0 text-break" id="md-montant-finance">—</p>
                    </div>
                    <div class="col-12">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Progression du Financement</h6>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:10px;">
                                <div class="progress-bar" id="md-progress-bar" role="progressbar" style="width:0%"></div>
                            </div>
                            <span class="fw-bold fs-14" id="md-progress-text">0%</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Date de Création</h6>
                        <p class="mb-0 text-break" id="md-created">—</p>
                    </div>
                    <div class="col-sm-6">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Dernière Mise à Jour</h6>
                        <p class="mb-0 text-break" id="md-updated">—</p>
                    </div>
                    <div class="col-12" id="md-institution-row" style="display:none;">
                        <hr>
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Institution Partenaire</h6>
                        <p class="mb-0 fw-medium text-break" id="md-institution">—</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a href="#" class="btn btn-primary" id="md-edit-btn"><i class="fe fe-edit me-1"></i>Modifier</a>
            </div>
        </div>
    </div>
</div>

<script>
// Pagination
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
    const rows = document.querySelectorAll('#projectsBody tr[data-status]');
    rows.forEach(row => {
        const match = val === 'all' || row.dataset.status === val;
        row.dataset.filtered = match ? 'false' : 'true';
        row.style.display = match ? '' : 'none';
    });
    checkEmptyState();
    resetPagination('projectsBody', 'projectsPagination');
}

// Recherche
function filterProjects() {
    const val = document.getElementById('searchProject').value.toLowerCase();
    const rows = document.querySelectorAll('#projectsBody tr[data-status]');
    rows.forEach(row => {
        const name = row.dataset.name;
        const sector = row.dataset.sector;
        const match = name.includes(val) || sector.includes(val);
        row.dataset.filtered = match ? 'false' : 'true';
        row.style.display = match ? '' : 'none';
    });
    checkEmptyState();
    resetPagination('projectsBody', 'projectsPagination');
}

// Tri
function sortProjects() {
    const sortBy = document.getElementById('sortBy').value;
    const tbody = document.getElementById('projectsBody');
    const rows = Array.from(tbody.querySelectorAll('tr[data-status]'));

    rows.sort((a, b) => {
        if (sortBy === 'recent') {
            return parseInt(b.dataset.date) - parseInt(a.dataset.date);
        } else if (sortBy === 'oldest') {
            return parseInt(a.dataset.date) - parseInt(b.dataset.date);
        } else if (sortBy === 'amount_asc') {
            return parseFloat(a.dataset.amount) - parseFloat(b.dataset.amount);
        } else if (sortBy === 'amount_desc') {
            return parseFloat(b.dataset.amount) - parseFloat(a.dataset.amount);
        }
        return 0;
    });

    rows.forEach(row => tbody.appendChild(row));
    reindexRows();
    resetPagination('projectsBody', 'projectsPagination');
}

// Renuméroter les lignes
function reindexRows() {
    const rows = document.querySelectorAll('#projectsBody tr[data-status]');
    rows.forEach((row, idx) => {
        if (row.cells[0]) {
            row.cells[0].textContent = idx + 1;
        }
    });
}

// Vérifier si aucun résultat
function checkEmptyState() {
    const visibleRows = document.querySelectorAll('#projectsBody tr[data-status]:not([style*="display: none"])');
    const noResultsRow = document.getElementById('noResultsRow');

    if (visibleRows.length === 0 && !document.getElementById('noProjectsRow')) {
        if (!noResultsRow) {
            const tr = document.createElement('tr');
            tr.id = 'noResultsRow';
            tr.innerHTML = '<td colspan="7" class="text-center py-3 text-muted">Aucun projet ne correspond à vos critères.</td>';
            document.getElementById('projectsBody').appendChild(tr);
        }
    } else if (visibleRows.length > 0 && noResultsRow) {
        noResultsRow.remove();
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    // Initialiser la pagination
    updatePagination('projectsBody', 'projectsPagination');

    // Soumission AJAX pour le bouton Soumettre
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-submit-project');
        if (!btn) return;
        e.preventDefault();
        var form = btn.closest('form');
        if (!form) return;
        var projectId = form.action.split('/').filter(Boolean).pop();
        var projectName = form.closest('tr')?.querySelector('.fw-medium.lh-1')?.textContent?.trim() || 'ce projet';
        ModalHelper.confirm(
            '<i class="bi bi-send me-2 text-success"></i> Soumettre le projet',
            'Voulez-vous soumettre <strong>' + projectName + '</strong> pour validation ?',
            'Oui, soumettre',
            'Annuler',
            'btn-success'
        ).then(function(confirmed) {
            if (!confirmed) return;
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || '',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams(new FormData(form))
            }).then(function(r) { return r.json(); }).then(function(data) {
                if (data && data.success) {
                    if (typeof ALOGOTO !== 'undefined') ALOGOTO.success('Projet soumis avec succes.');
                    setTimeout(function() { window.location.reload(); }, 800);
                } else {
                    var msg = (data && data.message) || 'Erreur lors de la soumission.';
                    if (typeof ALOGOTO !== 'undefined') ALOGOTO.error(msg);
                }
            }).catch(function() {
                if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur lors de la soumission.');
            });
        });
    });
});

// --- MODAL DÉTAIL PROJET ---
function viewProjectDetails(projectId) {
    var projectsData = document.getElementById('projectsData');
    if (!projectsData) return;
    var projects = JSON.parse(projectsData.textContent);
    var p = projects.find(function(item) { return item.id === projectId; });
    if (!p) return;

    document.getElementById('modalProjectTitle').textContent = p.titre;
    document.getElementById('md-title').textContent = p.titre;
    document.getElementById('md-description').innerHTML = p.description || 'Aucune description';
    document.getElementById('md-secteur').textContent = p.secteur;

    var statusBadge = document.getElementById('md-status');
    statusBadge.textContent = p.statut_label;
    statusBadge.className = 'badge fs-13 px-3 py-2 bg-' + (p.statut_color || 'secondary');

    document.getElementById('md-montant-demande').textContent = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', minimumFractionDigits: 0 }).format(p.montant_demande);
    document.getElementById('md-montant-finance').textContent = p.montant_finance > 0 ? new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', minimumFractionDigits: 0 }).format(p.montant_finance) : '—';

    var progress = p.progression || 0;
    document.getElementById('md-progress-bar').style.width = progress + '%';
    document.getElementById('md-progress-text').textContent = progress + '%';

    document.getElementById('md-created').textContent = p.created_at;
    document.getElementById('md-updated').textContent = p.updated_at;

    var instRow = document.getElementById('md-institution-row');
    var instEl = document.getElementById('md-institution');
    if (p.institution_nom) {
        instRow.style.display = '';
        instEl.textContent = p.institution_nom;
    } else {
        instRow.style.display = 'none';
    }

    var editBtn = document.getElementById('md-edit-btn');
    if (p.can_edit) {
        editBtn.style.display = '';
        editBtn.href = 'creer_projet.php?id=' + (p.encrypted_id || p.id);
    } else {
        editBtn.style.display = 'none';
    }

    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('projectDetailModal'));
    modal.show();
}
</script>

<?php
require_once __DIR__ . '/footer.php';
?>
