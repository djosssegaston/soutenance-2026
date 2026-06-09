<?php
require_once __DIR__ . '/header.php';

// Données passées par le contrôleur via DashboardPagesController
$repayments = $repayments ?? collect();
$stats = $stats ?? [
    'total_rembourse' => 0,
    'total_restant' => 0,
    'taux_remboursement' => 0,
    'en_retard' => 0,
    'total_echeances' => 0,
];

// Calculer les échéances à venir (statut en_attente et date future)
$echeancesAVenir = $repayments->filter(function ($r) {
    return $r->statut === 'en_attente' && $r->date_echeance && $r->date_echeance->isFuture();
})->count();
$echeancesPayees = $repayments->where('statut', 'paye')->count();
?>

<!--{ app content start }-->
<div class="main-content app-content mt-0">
    <div class="side-app">
        <!--{ container start }-->
        <div class="main-container container-fluid">
            <!--{ PAGE HEADER START }-->
            <div class="page-header">
                <h1 class="page-title">ÉCHÉANCES</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">ACCEUIL</a></li>
                        <li class="breadcrumb-item active" aria-current="page">ÉCHÉANCES</li>
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
                                    <span class="text-muted text-uppercase fs-12 fw-bold">TOTAL ÉCHÉANCES</span>
                                    <h2 class="mb-0 mt-1"><?php echo $stats['total_echeances']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-primary">
                                        <i class="bx bx-calendar-check fs-20"></i>
                                    </span>
                                </div>
                                <div class="progress progress-sm mt-3 w-100">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
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
                                    <span class="text-muted text-uppercase fs-12 fw-bold">ÉCHÉANCES À VENIR</span>
                                    <h2 class="mb-0 mt-1"><?php echo $echeancesAVenir; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-warning">
                                        <i class="bx bx-calendar-event fs-20"></i>
                                    </span>
                                </div>
                                <div class="progress progress-sm mt-3 w-100">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
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
                                    <span class="text-muted text-uppercase fs-12 fw-bold">ÉCHÉANCES EN RETARD</span>
                                    <h2 class="mb-0 mt-1"><?php echo $stats['en_retard']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-danger">
                                        <i class="bx bx-error fs-20"></i>
                                    </span>
                                </div>
                                <div class="progress progress-sm mt-3 w-100">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 80%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
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
                                    <span class="text-muted text-uppercase fs-12 fw-bold">RÉGULARITÉ</span>
                                    <h2 class="mb-0 mt-1"><?php echo $stats['taux_remboursement']; ?>%</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-success">
                                        <i class="bx bx-line-chart fs-20"></i>
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

            <!--{ row-2 start}-->
            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="card">
                        <div class="card-header justify-content-between">
                            <h4 class="card-title">Calendrier des Échéances</h4>
                            <div class="d-flex gap-2">
                                <input type="text" class="form-control form-control-sm" id="searchEcheance" placeholder="Rechercher..." onkeyup="filterEcheances()">
                                <select class="form-select form-select-sm" id="filterStatus" onchange="filterByStatus()">
                                    <option value="all">Tous les statuts</option>
                                    <option value="paye">Payé</option>
                                    <option value="en_attente">À venir</option>
                                    <option value="en_retard">En retard</option>
                                </select>
                                <select class="form-select form-select-sm" id="sortBy" onchange="sortEcheances()">
                                    <option value="proche">Plus proche</option>
                                    <option value="lointain">Plus lointain</option>
                                    <option value="amount_asc">Montant croissant</option>
                                    <option value="amount_desc">Montant décroissant</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table text-nowrap mb-0" id="echeancesTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">N°</th>
                                            <th scope="col">PROJET</th>
                                            <th scope="col">INSTITUTION</th>
                                            <th scope="col">MONTANT TOTAL</th>
                                            <th scope="col">MONTANT PAYÉ</th>
                                            <th scope="col">RESTE À PAYER</th>
                                            <th scope="col">DATE ÉCHÉANCE</th>
                                            <th scope="col">JOURS RESTANTS</th>
                                            <th scope="col">STATUT</th>
                                            <th scope="col">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="echeancesBody">
                                        <?php
                                        $index = 0;
                                        foreach ($repayments as $repayment):
                                            $index++;
                                            $statusMap = [
                                                'paye' => ['label' => 'Payé', 'color' => 'success'],
                                                'en_attente' => ['label' => 'À venir', 'color' => 'warning'],
                                                'en_retard' => ['label' => 'En retard', 'color' => 'danger'],
                                            ];
                                            $status = $statusMap[$repayment->statut] ?? ['label' => $repayment->statut, 'color' => 'info'];
                                            $montantPaye = (int) ($repayment->montant_total - $repayment->montant_restant);
                                            $dateEcheance = $repayment->date_echeance ? $repayment->date_echeance->format('d M Y') : 'N/A';

                                            // Calcul des jours restants
                                            $joursRestants = '';
                                            $joursClasse = '';
                                            if ($repayment->date_echeance) {
                                                if ($repayment->statut === 'paye') {
                                                    $joursRestants = 'Payé';
                                                    $joursClasse = 'text-success';
                                                } elseif ($repayment->date_echeance->isFuture()) {
                                                    $diff = (int) now()->diffInDays($repayment->date_echeance, false);
                                                    $joursRestants = $diff . ' jour' . ($diff > 1 ? 's' : '');
                                                    $joursClasse = $diff <= 7 ? 'text-danger fw-bold' : ($diff <= 30 ? 'text-warning' : 'text-success');
                                                } elseif ($repayment->date_echeance->isPast()) {
                                                    $diff = (int) now()->diffInDays($repayment->date_echeance, false);
                                                    $joursRestants = '-' . $diff . ' jour' . ($diff > 1 ? 's' : '');
                                                    $joursClasse = 'text-danger fw-bold';
                                                } elseif ($repayment->date_echeance->isToday()) {
                                                    $joursRestants = 'Aujourd\'hui';
                                                    $joursClasse = 'text-warning fw-bold';
                                                }
                                            }
                                        ?>
                                        <tr data-status="<?php echo $repayment->statut; ?>"
                                            data-project="<?php echo strtolower(htmlspecialchars($repayment->project->titre ?? '', ENT_QUOTES, 'UTF-8')); ?>"
                                            data-institution="<?php echo strtolower(htmlspecialchars($repayment->institution->nom ?? '', ENT_QUOTES, 'UTF-8')); ?>"
                                            data-amount="<?php echo $repayment->montant_total; ?>"
                                            data-date="<?php echo $repayment->date_echeance ? $repayment->date_echeance->timestamp : 0; ?>">
                                            <td><?php echo $index; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-md bg-primary-transparent rounded-circle me-2">
                                                        <span class="fw-bold"><?php echo strtoupper(substr($repayment->project->titre ?? 'N/A', 0, 2)); ?></span>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-medium lh-1"><?php echo htmlspecialchars($repayment->project->titre ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></span>
                                                        <small class="text-muted"><?php echo $repayment->project->created_at ? $repayment->project->created_at->format('d M Y') : 'N/A'; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($repayment->institution->nom ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <span class="text-primary fw-medium"><?php echo number_format($repayment->montant_total, 0, ',', ' '); ?> FCFA</span>
                                            </td>
                                            <td>
                                                <span class="text-success fw-medium"><?php echo number_format($montantPaye, 0, ',', ' '); ?> FCFA</span>
                                            </td>
                                            <td>
                                                <span class="text-warning fw-medium"><?php echo number_format($repayment->montant_restant, 0, ',', ' '); ?> FCFA</span>
                                            </td>
                                            <td><?php echo $dateEcheance; ?></td>
                                            <td>
                                                <span class="<?php echo $joursClasse; ?>"><?php echo $joursRestants; ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $status['color']; ?>-transparent rounded-pill text-<?php echo $status['color']; ?> p-2 px-3"><?php echo $status['label']; ?></span>
                                            </td>
                                            <td>
                                                <div class="g-1">
                                                    <?php if ($repayment->statut !== 'paye'): ?>
                                                    <button class="btn text-primary btn-sm btn-payer" data-id="<?php echo $repayment->id; ?>" data-amount="<?php echo $repayment->montant_restant ?? $repayment->montant_total; ?>">
                                                        <i class="fe fe-credit-card fs-14"></i> Payer
                                                    </button>
                                                    <?php else: ?>
                                                    <span class="text-muted">Confirmé</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if ($repayments->isEmpty()): ?>
                                        <tr id="noEcheancesRow">
                                            <td colspan="10" class="text-center py-5">
                                                <i class="bx bx-folder-open fs-1 text-muted"></i>
                                                <h5 class="mt-3 text-muted">Aucune échéance pour le moment</h5>
                                                <p class="text-muted">Les échéances apparaîtront ici une fois vos projets financés.</p>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center mt-3" id="echeancesPagination"></div>
            <!--{ row-2 end}-->
        </div>
        <!--{ container end }-->
    </div>
</div>
<!--{ app content end }-->

<!-- Modal Paiement FedaPay -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Paiement Échéance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="payment-loading" style="display: none;">
                    <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        <p class="mt-2">Redirection vers FedaPay...</p>
                    </div>
                </div>
                <div id="payment-content">
                    <p>Montant à payer : <strong id="payment-amount"></strong> FCFA</p>
                    <div id="payment-error" class="alert alert-danger" style="display: none;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirm-payment">Confirmer et Payer</button>
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

// Variables globales
const csrfToken = '<?php echo $csrf_token ?? ""; ?>';
let currentRepaymentId = null;
let paymentModal = null;

// Filtrage par statut
function filterByStatus() {
    const val = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('#echeancesBody tr[data-status]');
    rows.forEach(row => {
        const match = val === 'all' || row.dataset.status === val;
        row.dataset.filtered = match ? 'false' : 'true';
        row.style.display = match ? '' : 'none';
    });
    checkEmptyState();
    resetPagination('echeancesBody', 'echeancesPagination');
}

// Recherche
function filterEcheances() {
    const val = document.getElementById('searchEcheance').value.toLowerCase();
    const rows = document.querySelectorAll('#echeancesBody tr[data-status]');
    rows.forEach(row => {
        const project = row.dataset.project;
        const institution = row.dataset.institution;
        const match = project.includes(val) || institution.includes(val);
        row.dataset.filtered = match ? 'false' : 'true';
        row.style.display = match ? '' : 'none';
    });
    checkEmptyState();
    resetPagination('echeancesBody', 'echeancesPagination');
}

// Tri
function sortEcheances() {
    const sortBy = document.getElementById('sortBy').value;
    const tbody = document.getElementById('echeancesBody');
    const rows = Array.from(tbody.querySelectorAll('tr[data-status]'));

    rows.sort((a, b) => {
        if (sortBy === 'proche') {
            return parseInt(a.dataset.date) - parseInt(b.dataset.date);
        } else if (sortBy === 'lointain') {
            return parseInt(b.dataset.date) - parseInt(a.dataset.date);
        } else if (sortBy === 'amount_asc') {
            return parseFloat(a.dataset.amount) - parseFloat(b.dataset.amount);
        } else if (sortBy === 'amount_desc') {
            return parseFloat(b.dataset.amount) - parseFloat(a.dataset.amount);
        }
        return 0;
    });

    rows.forEach(row => tbody.appendChild(row));
    reindexRows();
    resetPagination('echeancesBody', 'echeancesPagination');
}

// Renuméroter les lignes
function reindexRows() {
    const rows = document.querySelectorAll('#echeancesBody tr[data-status]');
    rows.forEach((row, idx) => {
        if (row.cells && row.cells[0]) {
            row.cells[0].textContent = idx + 1;
        }
    });
}

// Vérifier si aucun résultat
function checkEmptyState() {
    const visibleRows = document.querySelectorAll('#echeancesBody tr[data-status]:not([style*="display: none"])');
    const noResultsRow = document.getElementById('noResultsRow');

    if (visibleRows.length === 0 && !document.getElementById('noEcheancesRow')) {
        if (!noResultsRow) {
            const tr = document.createElement('tr');
            tr.id = 'noResultsRow';
            tr.innerHTML = '<td colspan="10" class="text-center py-3 text-muted">Aucune échéance ne correspond à vos critères.</td>';
            document.getElementById('echeancesBody').appendChild(tr);
        }
    } else if (visibleRows.length > 0 && noResultsRow) {
        noResultsRow.remove();
    }
}

// Gestion du bouton Payer
document.addEventListener('DOMContentLoaded', function() {
    paymentModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('paymentModal'));
    
    // Attacher les boutons de paiement
    document.querySelectorAll('.btn-payer').forEach(button => {
        button.addEventListener('click', function() {
            currentRepaymentId = this.getAttribute('data-id');
            const amount = this.getAttribute('data-amount');
            
            document.getElementById('payment-amount').textContent = new Intl.NumberFormat('fr-FR').format(amount);
            document.getElementById('payment-error').style.display = 'none';
            document.getElementById('payment-content').style.display = 'block';
            document.getElementById('payment-loading').style.display = 'none';
            
            paymentModal.show();
        });
    });
    
    // Confirmer le paiement
    document.getElementById('confirm-payment').addEventListener('click', function() {
        if (!currentRepaymentId) return;
        
        document.getElementById('payment-content').style.display = 'none';
        document.getElementById('payment-loading').style.display = 'block';
        document.getElementById('payment-error').style.display = 'none';
        
        fetch(`/api/v1/porteur/echeances/${currentRepaymentId}/fedapay`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.payment_url;
            } else {
                document.getElementById('payment-error').textContent = data.message || 'Erreur lors du paiement';
                document.getElementById('payment-error').style.display = 'block';
                document.getElementById('payment-content').style.display = 'block';
                document.getElementById('payment-loading').style.display = 'none';
            }
        })
        .catch(error => {
            document.getElementById('payment-error').textContent = 'Erreur de connexion';
            document.getElementById('payment-error').style.display = 'block';
            document.getElementById('payment-content').style.display = 'block';
            document.getElementById('payment-loading').style.display = 'none';
        });
    });
    
    // Initialiser les tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    // Initialiser la pagination
    updatePagination('echeancesBody', 'echeancesPagination');
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
