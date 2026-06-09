<?php
require_once __DIR__ . '/header.php';

$repayments = $repayments ?? collect();
$stats = $stats ?? [
    'total_rembourse' => 0,
    'total_restant' => 0,
    'taux_remboursement' => 0,
    'en_retard' => 0,
    'total_echeances' => 0,
];

$repaymentsPayees = $repayments->where('statut', 'paye');
$nbRemboursements = $repaymentsPayees->count();
$totalPaye = (float) $repaymentsPayees->sum('montant_total');
$dernierPaiement = $repaymentsPayees->sortByDesc('date_paiement')->first();
$dateDernierPaiement = $dernierPaiement && $dernierPaiement->date_paiement
    ? $dernierPaiement->date_paiement->format('d M Y')
    : 'Aucun';
?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title"><strong>HISTORIQUE DES REMBOURSEMENTS</strong></h1>
                    <p class="text-muted mb-0">Consultez l'ensemble des remboursements effectués sur vos projets.</p>
                </div>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">ACCEUIL</a></li>
                        <li class="breadcrumb-item active" aria-current="page">HISTORIQUE REMBOURSEMENTS</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">TOTAL REMBOURSÉ</span>
                                    <h2 class="mb-0 mt-1 fs-5"><?php echo number_format($stats['total_rembourse'], 0, ',', ' '); ?> FCFA</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-success">
                                        <i class="bx bx-cash fs-20"></i>
                                    </span>
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
                                    <span class="text-muted text-uppercase fs-12 fw-bold">REMB. EFFECTUÉS</span>
                                    <h2 class="mb-0 mt-1"><?php echo $nbRemboursements; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-info">
                                        <i class="bx bx-check-double fs-20"></i>
                                    </span>
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
                                    <span class="text-muted text-uppercase fs-12 fw-bold">DERNIER PAIEMENT</span>
                                    <h2 class="mb-0 mt-1 fs-6"><?php echo $dateDernierPaiement; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-primary">
                                        <i class="bx bx-calendar-check fs-20"></i>
                                    </span>
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
                                    <span class="text-muted text-uppercase fs-12 fw-bold">TAUX DE DISCIPLINE</span>
                                    <h2 class="mb-0 mt-1"><?php echo $stats['taux_remboursement']; ?>%</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-warning">
                                        <i class="bx bx-line-chart fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="card">
                        <div class="card-header justify-content-between">
                            <h4 class="card-title"><i class="bx bx-history me-2"></i>Historique des remboursements</h4>
                            <div class="d-flex gap-2 flex-wrap">
                                <input type="text" class="form-control form-control-sm" id="searchRepayment" placeholder="Rechercher..." onkeyup="filterRepayments()" style="min-width:140px;">
                                <select class="form-select form-select-sm" id="filterStatus" onchange="filterByStatus()">
                                    <option value="all">Tous</option>
                                    <option value="paye">Payé</option>
                                    <option value="en_retard">En retard</option>
                                </select>
                                <select class="form-select form-select-sm" id="sortBy" onchange="sortRepayments()">
                                    <option value="recent">Plus récent</option>
                                    <option value="oldest">Plus ancien</option>
                                    <option value="amount_asc">Montant croissant</option>
                                    <option value="amount_desc">Montant décroissant</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table text-nowrap mb-0" id="repaymentsTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">N°</th>
                                            <th scope="col">PROJET</th>
                                            <th scope="col">INSTITUTION</th>
                                            <th scope="col">MONTANT</th>
                                            <th scope="col">DATE PAIEMENT</th>
                                            <th scope="col">MÉTHODE</th>
                                            <th scope="col">RÉFÉRENCE</th>
                                            <th scope="col">STATUT</th>
                                        </tr>
                                    </thead>
                                    <tbody id="repaymentsBody">
                                        <?php
                                        $index = 0;
                                        foreach ($repayments as $repayment):
                                            $index++;
                                            $statusMap = [
                                                'paye' => ['label' => 'Payé', 'color' => 'success'],
                                                'en_attente' => ['label' => 'En attente', 'color' => 'warning'],
                                                'en_retard' => ['label' => 'En retard', 'color' => 'danger'],
                                            ];
                                            $status = $statusMap[$repayment->statut] ?? ['label' => $repayment->statut, 'color' => 'info'];
                                            $montantPaye = (int) ($repayment->montant_total - $repayment->montant_restant);
                                            $datePaiement = $repayment->date_paiement ? $repayment->date_paiement->format('d M Y') : ($repayment->date_echeance ? $repayment->date_echeance->format('d M Y') : 'N/A');
                                        ?>
                                        <tr data-status="<?php echo $repayment->statut; ?>"
                                            data-project="<?php echo strtolower(htmlspecialchars($repayment->project->titre ?? '', ENT_QUOTES, 'UTF-8')); ?>"
                                            data-institution="<?php echo strtolower(htmlspecialchars($repayment->institution->nom ?? '', ENT_QUOTES, 'UTF-8')); ?>"
                                            data-amount="<?php echo $repayment->montant_total; ?>"
                                            data-date="<?php echo ($repayment->date_paiement ?? $repayment->date_echeance)?->timestamp ?? 0; ?>">
                                            <td><?php echo $index; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-md bg-primary-transparent rounded-circle me-2">
                                                        <span class="fw-bold"><?php echo strtoupper(substr($repayment->project->titre ?? 'N/A', 0, 2)); ?></span>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-medium lh-1"><?php echo htmlspecialchars($repayment->project->titre ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></span>
                                                        <small class="text-muted"><?php echo $repayment->project->created_at ? $repayment->project->created_at->format('d M Y') : ''; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($repayment->institution->nom ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <span class="fw-medium text-success"><?php echo number_format($montantPaye, 0, ',', ' '); ?> FCFA</span>
                                            </td>
                                            <td><?php echo $datePaiement; ?></td>
                                            <td><?php echo htmlspecialchars($repayment->methode_paiement ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><small class="text-muted"><?php echo htmlspecialchars($repayment->transaction_reference ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></small></td>
                                            <td>
                                                <span class="badge bg-<?php echo $status['color']; ?>-transparent rounded-pill text-<?php echo $status['color']; ?> p-2 px-3"><?php echo $status['label']; ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if ($repayments->isEmpty()): ?>
                                        <tr id="noRepaymentsRow">
                                            <td colspan="8" class="text-center py-5">
                                                <i class="bx bx-folder-open fs-1 text-muted"></i>
                                                <h5 class="mt-3 text-muted">Aucun remboursement pour le moment</h5>
                                                <p class="text-muted">Les remboursements effectués apparaîtront ici.</p>
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
            <div class="d-flex justify-content-center mt-3" id="repaymentsPagination"></div>
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

const csrfToken = '<?php echo $csrf_token ?? ""; ?>';

function filterByStatus() {
    const val = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('#repaymentsBody tr[data-status]');
    rows.forEach(row => {
        const match = val === 'all' || row.dataset.status === val;
        row.dataset.filtered = match ? 'false' : 'true';
        row.style.display = match ? '' : 'none';
    });
    checkEmptyState();
    resetPagination('repaymentsBody', 'repaymentsPagination');
}

function filterRepayments() {
    const val = document.getElementById('searchRepayment').value.toLowerCase();
    const rows = document.querySelectorAll('#repaymentsBody tr[data-status]');
    rows.forEach(row => {
        const project = row.dataset.project;
        const institution = row.dataset.institution;
        const match = project.includes(val) || institution.includes(val);
        row.dataset.filtered = match ? 'false' : 'true';
        row.style.display = match ? '' : 'none';
    });
    checkEmptyState();
    resetPagination('repaymentsBody', 'repaymentsPagination');
}

function sortRepayments() {
    const sortBy = document.getElementById('sortBy').value;
    const tbody = document.getElementById('repaymentsBody');
    const rows = Array.from(tbody.querySelectorAll('tr[data-status]'));

    rows.sort((a, b) => {
        if (sortBy === 'recent') return parseInt(b.dataset.date) - parseInt(a.dataset.date);
        if (sortBy === 'oldest') return parseInt(a.dataset.date) - parseInt(b.dataset.date);
        if (sortBy === 'amount_asc') return parseFloat(a.dataset.amount) - parseFloat(b.dataset.amount);
        if (sortBy === 'amount_desc') return parseFloat(b.dataset.amount) - parseFloat(a.dataset.amount);
        return 0;
    });

    rows.forEach(row => tbody.appendChild(row));
    reindexRows();
    resetPagination('repaymentsBody', 'repaymentsPagination');
}

function reindexRows() {
    const rows = document.querySelectorAll('#repaymentsBody tr[data-status]');
    rows.forEach((row, idx) => {
        if (row.cells && row.cells[0]) row.cells[0].textContent = idx + 1;
    });
}

function checkEmptyState() {
    const visibleRows = document.querySelectorAll('#repaymentsBody tr[data-status]:not([style*="display: none"])');
    const noResultsRow = document.getElementById('noResultsRow');
    if (visibleRows.length === 0 && !document.getElementById('noRepaymentsRow')) {
        if (!noResultsRow) {
            const tr = document.createElement('tr');
            tr.id = 'noResultsRow';
            tr.innerHTML = '<td colspan="8" class="text-center py-3 text-muted">Aucun remboursement ne correspond à vos critères.</td>';
            document.getElementById('repaymentsBody').appendChild(tr);
        }
    } else if (visibleRows.length > 0 && noResultsRow) {
        noResultsRow.remove();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    updatePagination('repaymentsBody', 'repaymentsPagination');
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
