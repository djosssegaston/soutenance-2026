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

// Grouper les remboursements par projet
$projects = $repaymentsPayees->groupBy(function ($r) {
    return $r->project_id;
})->map(function ($paiements, $projectId) {
    $first = $paiements->first();
    $totalPaye = (float) $paiements->sum('montant_total');
    return [
        'id' => $projectId,
        'titre' => $first?->project?->titre ?? '',
        'institution_nom' => $first?->institution?->nom ?? '',
        'paiements_count' => $paiements->count(),
        'total_paye' => $totalPaye,
        'dernier_paiement' => $paiements->sortByDesc('date_paiement')->first()?->date_paiement?->format('d M Y') ?? '',
        'paiements' => $paiements->values(),
    ];
})->values();
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
                            <h4 class="card-title"><i class="bx bx-briefcase me-2"></i>Mes Projets</h4>
                            <div class="d-flex gap-2 flex-wrap">
                                <input type="text" class="form-control form-control-sm" id="searchProjet" placeholder="Rechercher un projet..." onkeyup="filterProjects()" style="min-width:140px;">
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table text-nowrap mb-0" id="projectsTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">PROJET</th>
                                            <th scope="col">INSTITUTION</th>
                                            <th scope="col" class="text-center">PAIEMENTS</th>
                                            <th scope="col">TOTAL REMBOURSÉ</th>
                                            <th scope="col">DERNIER PAIEMENT</th>
                                            <th scope="col">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="projectsBody">
                                        <?php foreach ($projects as $project): ?>
                                        <tr data-titre="<?php echo strtolower(htmlspecialchars($project['titre'], ENT_QUOTES, 'UTF-8')); ?>"
                                            data-institution="<?php echo strtolower(htmlspecialchars($project['institution_nom'], ENT_QUOTES, 'UTF-8')); ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-md bg-primary-transparent rounded-circle me-2">
                                                        <span class="fw-bold"><?php echo strtoupper(substr($project['titre'], 0, 2)); ?></span>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-medium lh-1"><?php echo htmlspecialchars($project['titre'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($project['institution_nom'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td class="text-center"><span class="badge bg-success"><?php echo $project['paiements_count']; ?></span></td>
                                            <td><span class="fw-medium text-success"><?php echo number_format($project['total_paye'], 0, ',', ' '); ?> FCFA</span></td>
                                            <td><?php echo $project['dernier_paiement']; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary consulter-btn"
                                                        data-id="<?php echo $project['id']; ?>"
                                                        data-titre="<?php echo htmlspecialchars($project['titre'], ENT_QUOTES, 'UTF-8'); ?>"
                                                        data-institution="<?php echo htmlspecialchars($project['institution_nom'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <i class="fe fe-eye me-1"></i> Détails
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if ($projects->isEmpty()): ?>
                                        <tr id="noProjectsRow">
                                            <td colspan="6" class="text-center py-5">
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
        </div>
    </div>
</div>

<!-- Modal Détails Paiements Projet -->
<div class="modal fade" id="projetPaiementsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bx bx-history me-2"></i>Paiements du Projet</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold" id="modal-projet-titre"></h6>
                        <small class="text-muted" id="modal-projet-institution"></small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table text-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>MONTANT</th>
                                <th>DATE PAIEMENT</th>
                                <th>MÉTHODE</th>
                                <th>RÉFÉRENCE</th>
                                <th>STATUT</th>
                            </tr>
                        </thead>
                        <tbody id="modal-paiements-body"></tbody>
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

<script>
const projectsData = JSON.parse(document.getElementById('projects-data').textContent);

function filterProjects() {
    const val = document.getElementById('searchProjet').value.toLowerCase();
    const rows = document.querySelectorAll('#projectsBody tr[data-titre]');
    rows.forEach(row => {
        const titre = row.dataset.titre;
        const institution = row.dataset.institution;
        row.style.display = (titre.includes(val) || institution.includes(val)) ? '' : 'none';
    });
}

function openProjetPaiements(projectId, titre, institution) {
    document.getElementById('modal-projet-titre').textContent = titre;
    document.getElementById('modal-projet-institution').textContent = 'Institution : ' + institution;

    const project = projectsData.find(p => String(p.id) === String(projectId));
    const paiements = project ? project.paiements : [];

    const tbody = document.getElementById('modal-paiements-body');
    tbody.innerHTML = '';

    if (paiements.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Aucun paiement pour ce projet.</td></tr>';
    } else {
        paiements.forEach((item, i) => {
            const montantPaye = (item.montant_total || 0) - (item.montant_restant || 0);
            const datePaiement = item.date_paiement ? new Date(item.date_paiement).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : (item.date_echeance ? new Date(item.date_echeance).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : '');

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${i + 1}</td>
                <td class="fw-medium text-success">${new Intl.NumberFormat('fr-FR').format(montantPaye)} FCFA</td>
                <td>${datePaiement}</td>
                <td>${item.methode_paiement || ''}</td>
                <td><small class="text-muted">${item.transaction_reference || ''}</small></td>
                <td><span class="badge bg-success-transparent rounded-pill text-success p-2 px-3">Payé</span></td>
            `;
            tbody.appendChild(tr);
        });
    }

    bootstrap.Modal.getOrCreateInstance(document.getElementById('projetPaiementsModal')).show();
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.consulter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            openProjetPaiements(this.dataset.id, this.dataset.titre, this.dataset.institution);
        });
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
