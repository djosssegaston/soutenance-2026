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

// Grouper les échéances par projet
$projects = $repayments->groupBy(function ($r) {
    return $r->project_id;
})->map(function ($echeances, $projectId) {
    $first = $echeances->first();
    $totalDue = (float) $echeances->sum('montant_total');
    $totalPaid = (float) $echeances->sum(function ($r) {
        return (float) ($r->montant_total - $r->montant_restant);
    });
    return [
        'id' => $projectId,
        'titre' => $first?->project?->titre ?? '',
        'institution_nom' => $first?->institution?->nom ?? '',
        'echeances_count' => $echeances->count(),
        'total_due' => $totalDue,
        'total_paid' => $totalPaid,
        'total_remaining' => $totalDue - $totalPaid,
        'overdue_count' => $echeances->whereIn('statut', ['en_retard', 'overdue'])->count(),
        'echeances' => $echeances->values(),
    ];
})->values();
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
                            <h4 class="card-title"><i class="bx bx-briefcase me-2"></i>Mes Projets Financés</h4>
                            <div class="d-flex gap-2">
                                <input type="text" class="form-control form-control-sm" id="searchProjet" placeholder="Rechercher un projet..." onkeyup="filterProjects()">
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table text-nowrap mb-0" id="projectsTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">PROJET</th>
                                            <th scope="col">INSTITUTION</th>
                                            <th scope="col" class="text-center">ÉCHÉANCES</th>
                                            <th scope="col">TOTAL DÛ</th>
                                            <th scope="col">PAYÉ</th>
                                            <th scope="col">RESTANT</th>
                                            <th scope="col" class="text-center">RETARD</th>
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
                                            <td class="text-center"><span class="badge bg-info"><?php echo $project['echeances_count']; ?></span></td>
                                            <td><span class="fw-medium"><?php echo number_format($project['total_due'], 0, ',', ' '); ?> FCFA</span></td>
                                            <td><span class="text-success fw-medium"><?php echo number_format($project['total_paid'], 0, ',', ' '); ?> FCFA</span></td>
                                            <td><span class="text-warning fw-medium"><?php echo number_format($project['total_remaining'], 0, ',', ' '); ?> FCFA</span></td>
                                            <td class="text-center">
                                                <?php if ($project['overdue_count'] > 0): ?>
                                                <span class="badge bg-danger"><?php echo $project['overdue_count']; ?></span>
                                                <?php else: ?>
                                                <span class="badge bg-light text-muted">0</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary consulter-btn"
                                                        data-id="<?php echo $project['id']; ?>"
                                                        data-titre="<?php echo htmlspecialchars($project['titre'], ENT_QUOTES, 'UTF-8'); ?>"
                                                        data-institution="<?php echo htmlspecialchars($project['institution_nom'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <i class="fe fe-eye me-1"></i> Consulter
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if ($projects->isEmpty()): ?>
                                        <tr id="noProjectsRow">
                                            <td colspan="8" class="text-center py-5">
                                                <i class="bx bx-folder-open fs-1 text-muted"></i>
                                                <h5 class="mt-3 text-muted">Aucun projet financé pour le moment</h5>
                                                <p class="text-muted">Les projets apparaîtront ici une fois vos financements actifs.</p>
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
            <!--{ row-2 end}-->
        </div>
        <!--{ container end }-->
    </div>
</div>
<!--{ app content end }-->

<!-- Modal Facture / Reçu -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fs-4 fw-bold" id="receiptModalLabel">Facture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3" id="receiptBody">
                <div class="text-center py-5" id="receiptLoader">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p class="text-muted">Chargement de la facture...</p>
                </div>
                <div id="receiptContent" style="display:none;"></div>
            </div>
            <div class="modal-footer border-0 pt-0" id="receiptFooter" style="display:none;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="btnPrintReceipt">
                    <i class="fe fe-printer me-1"></i> Imprimer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Paiement -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-center d-block border-0 pb-0" style="position:relative;">
                <h5 class="modal-title fs-4 fw-bold" id="paymentModalLabel">Paiement Échéance</h5>
                <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Étape 1 : confirmation initiale -->
                <div id="payment-step-init">
                    <p>Montant à payer : <strong id="payment-amount"></strong> FCFA</p>
                    <div id="payment-error" class="alert alert-danger" style="display: none;"></div>
                </div>
                <!-- Étape 2 : préloader avant affichage du formulaire -->
                <div id="payment-step-preloader" style="display:none;">
                    <div class="text-center py-4">
                        <div class="d-inline-block" style="width:60px;height:60px;background: url('../../asset/images/fade-stagger-squares%20(2).svg') center/contain no-repeat;"></div>
                        <p class="mt-3 text-muted fs-14">Préparation du paiement...</p>
                    </div>
                </div>
                <!-- Étape 3 : formulaire de paiement bypass -->
                <div id="payment-step-bypass" style="display:none;">
                    <div id="bypass-form-container"></div>
                </div>
                <!-- Étape 4 : statut du paiement (affiché 1.5s avant redirection) -->
                <div id="payment-step-status" style="display:none;">
                    <div class="text-center py-4" id="payment-status-content"></div>
                </div>
            </div>
            <div class="modal-footer" id="payment-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="paymentBtnCancel">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirm-payment">Confirmer et Payer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Échéances Projet -->
<div class="modal fade" id="projetEcheancesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bx bx-calendar me-2"></i>Échéances du Projet</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-projet-info" class="d-flex justify-content-between align-items-center mb-3">
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
                                <th>MONTANT TOTAL</th>
                                <th>MONTANT PAYÉ</th>
                                <th>RESTE À PAYER</th>
                                <th>DATE ÉCHÉANCE</th>
                                <th>JOURS RESTANTS</th>
                                <th>STATUT</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody id="modal-echeances-body"></tbody>
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
// Variables globales
const csrfToken = '<?php echo $csrf_token ?? ""; ?>';
let currentRepaymentId = null;
let paymentModal = null;

// Données projets chargées depuis le script JSON
const projectsData = JSON.parse(document.getElementById('projects-data').textContent);

// Recherche de projets
function filterProjects() {
    const val = document.getElementById('searchProjet').value.toLowerCase();
    const rows = document.querySelectorAll('#projectsBody tr[data-titre]');
    rows.forEach(row => {
        const titre = row.dataset.titre;
        const institution = row.dataset.institution;
        const match = titre.includes(val) || institution.includes(val);
        row.style.display = match ? '' : 'none';
    });
}

// Ouvrir la modale des échéances d'un projet
function openProjetEcheances(projectId, titre, institution) {
    document.getElementById('modal-projet-titre').textContent = titre;
    document.getElementById('modal-projet-institution').textContent = 'Institution : ' + institution;

    const project = projectsData.find(p => String(p.id) === String(projectId));
    const echeances = project ? project.echeances : [];

    const tbody = document.getElementById('modal-echeances-body');
    tbody.innerHTML = '';

    if (echeances.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Aucune échéance pour ce projet.</td></tr>';
    } else {
        const statusMap = {
            'paye': ['success', 'Payé', false],
            'en_attente': ['warning', 'À venir', true],
            'en_retard': ['danger', 'En retard', true],
            'overdue': ['danger', 'En retard', true],
            'upcoming': ['info', 'Prochaine', true],
            'failed': ['danger', 'Annulé', true],
            'cancelled': ['danger', 'Annulé', true],
            'partial': ['secondary', 'Partielle', true],
        };

        const now = new Date();

        echeances.forEach((item, i) => {
            const [color, label, canPay] = statusMap[item.statut] || ['info', item.statut, false];
            const montantPaye = (item.montant_total || 0) - (item.montant_restant || 0);
            const dateEcheance = item.date_echeance ? new Date(item.date_echeance) : null;
            let joursRestants = '';
            let joursClasse = '';

            if (dateEcheance) {
                if (item.statut === 'paye') {
                    joursRestants = 'Payé';
                    joursClasse = 'text-success';
                } else {
                    const diff = Math.ceil((dateEcheance - now) / (1000 * 60 * 60 * 24));
                    if (diff > 0) {
                        joursRestants = diff + ' jour' + (diff > 1 ? 's' : '');
                        joursClasse = diff <= 7 ? 'text-danger fw-bold' : (diff <= 30 ? 'text-warning' : 'text-success');
                    } else if (diff === 0) {
                        joursRestants = 'Aujourd\'hui';
                        joursClasse = 'text-warning fw-bold';
                    } else {
                        joursRestants = diff + ' jour' + (Math.abs(diff) > 1 ? 's' : '');
                        joursClasse = 'text-danger fw-bold';
                    }
                }
            }

            const actionHtml = item.statut === 'paye'
                ? `<button class="btn text-success btn-sm btn-facture" data-id="${item.id}"><i class="fe fe-file-text fs-14"></i> Facture</button>`
                : (canPay
                    ? `<button class="btn text-primary btn-sm btn-payer" data-id="${item.id}" data-amount="${item.montant_restant || item.montant_total}"><i class="fe fe-credit-card fs-14"></i> Payer</button>`
                    : '<span class="text-muted">—</span>');

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${i + 1}</td>
                <td class="fw-medium">${new Intl.NumberFormat('fr-FR').format(item.montant_total || 0)} FCFA</td>
                <td class="text-success fw-medium">${new Intl.NumberFormat('fr-FR').format(montantPaye)} FCFA</td>
                <td class="text-warning fw-medium">${new Intl.NumberFormat('fr-FR').format(item.montant_restant || 0)} FCFA</td>
                <td>${dateEcheance ? dateEcheance.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }) : ''}</td>
                <td><span class="${joursClasse}">${joursRestants}</span></td>
                <td><span class="badge bg-${color}-transparent rounded-pill text-${color} p-2 px-3">${label}</span></td>
                <td>${actionHtml}</td>
            `;
            tbody.appendChild(tr);
        });

        // Attacher les boutons Payer dans la modale
        tbody.querySelectorAll('.btn-payer').forEach(btn => {
            btn.addEventListener('click', function() {
                const modal = bootstrap.Modal.getInstance(document.getElementById('projetEcheancesModal'));
                if (modal) modal.hide();
                setTimeout(() => {
                    currentRepaymentId = this.getAttribute('data-id');
                    const amount = this.getAttribute('data-amount');
                    document.getElementById('payment-amount').textContent = new Intl.NumberFormat('fr-FR').format(amount);
                    document.getElementById('payment-error').style.display = 'none';
                    document.getElementById('payment-step-init').style.display = 'block';
                    document.getElementById('payment-step-preloader').style.display = 'none';
                    document.getElementById('payment-step-bypass').style.display = 'none';
                    document.getElementById('paymentBtnCancel').style.display = 'inline-block';
                    document.getElementById('confirm-payment').style.display = 'inline-block';
                    document.getElementById('confirm-payment').disabled = false;
                    paymentModal.show();
                }, 300);
            });
        });

        // Attacher les boutons Facture
        tbody.querySelectorAll('.btn-facture').forEach(btn => {
            btn.addEventListener('click', function() {
                const repaymentId = this.getAttribute('data-id');
                showReceipt(repaymentId);
                window.open(`/api/v1/repayments/receipt/pdf/${repaymentId}`, '_blank');
            });
        });
    }

    bootstrap.Modal.getOrCreateInstance(document.getElementById('projetEcheancesModal')).show();
}

// Gestion du bouton Payer (échéances directes)
document.addEventListener('DOMContentLoaded', function() {
    paymentModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('paymentModal'));
    const modalEl = document.getElementById('paymentModal');

    // Consulter projet → ouvre la modale échéances
    document.querySelectorAll('.consulter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            openProjetEcheances(this.dataset.id, this.dataset.titre, this.dataset.institution);
        });
    });

    // Confirmer le paiement
    document.getElementById('confirm-payment').addEventListener('click', function() {
        if (!currentRepaymentId) return;

        document.getElementById('payment-step-init').style.display = 'none';
        document.getElementById('payment-step-preloader').style.display = 'block';
        document.getElementById('paymentBtnCancel').style.display = 'none';
        document.getElementById('confirm-payment').style.display = 'none';

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
                const paymentUrl = data.payment_url;
                const transactionId = data.transaction_id;

                if (paymentUrl && paymentUrl.indexOf('bypass-confirm') !== -1) {
                    const bypassFormHtml = buildBypassForm(transactionId, data.amount);

                    setTimeout(function() {
                        document.getElementById('payment-step-preloader').style.display = 'none';
                        document.getElementById('payment-step-bypass').style.display = 'block';
                        document.getElementById('bypass-form-container').innerHTML = bypassFormHtml;
                        document.getElementById('paymentBtnCancel').style.display = 'inline-block';
                    }, 2500);
                } else {
                    window.location.href = paymentUrl;
                }
            } else {
                document.getElementById('payment-error').textContent = data.message || 'Erreur lors du paiement';
                document.getElementById('payment-error').style.display = 'block';
                document.getElementById('payment-step-init').style.display = 'block';
                document.getElementById('payment-step-preloader').style.display = 'none';
                document.getElementById('confirm-payment').style.display = 'inline-block';
                document.getElementById('paymentBtnCancel').style.display = 'inline-block';
            }
        })
        .catch(error => {
            document.getElementById('payment-error').textContent = 'Erreur de connexion';
            document.getElementById('payment-error').style.display = 'block';
            document.getElementById('payment-step-init').style.display = 'block';
            document.getElementById('payment-step-preloader').style.display = 'none';
            document.getElementById('confirm-payment').style.display = 'inline-block';
            document.getElementById('paymentBtnCancel').style.display = 'inline-block';
        });
    });

    function buildBypassForm(transactionId, amount) {
        const formatted = new Intl.NumberFormat('fr-FR').format(amount || 0);
        return `
            <div class="bypass-checkout">
                <style>
                    .bypass-checkout .bypass-amount {
                        background: #fff8f0;
                        border: 2px solid #fde4c8;
                        border-radius: 12px;
                        padding: 16px 18px;
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    .bypass-checkout .bypass-amount .ba-label {
                        font-size: 12px;
                        color: #6b7a8f;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                        font-weight: 600;
                    }
                    .bypass-checkout .bypass-amount .ba-value {
                        font-size: 24px;
                        font-weight: 700;
                        color: #d35400;
                    }
                    .bypass-checkout .bypass-amount .ba-value .ba-currency {
                        font-size: 14px;
                        color: #6b7a8f;
                        font-weight: 500;
                    }
                    .bypass-checkout .form-group {
                        margin-bottom: 16px;
                    }
                    .bypass-checkout .form-group label {
                        display: block;
                        font-size: 13px;
                        font-weight: 600;
                        color: #3d4a5c;
                        margin-bottom: 5px;
                    }
                    .bypass-checkout .form-group .input-wrapper {
                        position: relative;
                    }
                    .bypass-checkout .form-group .input-wrapper .prefix {
                        position: absolute;
                        left: 12px;
                        top: 50%;
                        transform: translateY(-50%);
                        color: #6b7a8f;
                        font-size: 13px;
                        font-weight: 500;
                        pointer-events: none;
                    }
                    .bypass-checkout .form-group input {
                        width: 100%;
                        padding: 10px 12px 10px 46px;
                        border: 2px solid #e0e5ec;
                        border-radius: 10px;
                        font-size: 15px;
                        color: #2d3748;
                        outline: none;
                        transition: border-color 0.2s;
                        background: #fff;
                    }
                    .bypass-checkout .form-group input:focus {
                        border-color: #e67e22;
                        box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.12);
                    }
                    .bypass-checkout .form-group input.has-error {
                        border-color: #dc3545;
                    }
                    .bypass-checkout .form-group .hint {
                        font-size: 11px;
                        color: #6b7a8f;
                        margin-top: 4px;
                    }
                    .bypass-checkout .form-group .hint .success-hint { color: #1a7d36; }
                    .bypass-checkout .form-group .hint .fail-hint { color: #dc3545; }
                    .bypass-checkout .bypass-actions {
                        display: flex;
                        gap: 10px;
                        margin-top: 20px;
                    }
                    .bypass-checkout .bypass-actions .btn-submit {
                        flex: 2;
                        padding: 12px;
                        border: none;
                        border-radius: 10px;
                        font-size: 15px;
                        font-weight: 600;
                        color: #fff;
                        background: linear-gradient(135deg, #e67e22, #d35400);
                        cursor: pointer;
                        transition: transform 0.15s, box-shadow 0.15s;
                    }
                    .bypass-checkout .bypass-actions .btn-submit:hover {
                        transform: translateY(-1px);
                        box-shadow: 0 4px 15px rgba(211, 84, 0, 0.3);
                    }
                    .bypass-checkout .bypass-actions .btn-cancel-bypass {
                        flex: 1;
                        padding: 12px;
                        border: 2px solid #e0e5ec;
                        border-radius: 10px;
                        font-size: 13px;
                        font-weight: 500;
                        color: #6b7a8f;
                        background: #fff;
                        cursor: pointer;
                        transition: border-color 0.2s, color 0.2s;
                        text-align: center;
                        text-decoration: none;
                    }
                    .bypass-checkout .bypass-actions .btn-cancel-bypass:hover {
                        border-color: #dc3545;
                        color: #dc3545;
                    }
                </style>
                <div class="bypass-amount">
                    <div class="ba-label">Montant à payer</div>
                    <div class="ba-value">${formatted} <span class="ba-currency">XOF</span></div>
                </div>
                <form method="POST" action="/payment/bypass-confirm/${transactionId}">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <div class="form-group">
                        <label>Numéro de téléphone <span style="color:#dc3545;">*</span></label>
                        <div class="input-wrapper">
                            <span class="prefix">+229</span>
                            <input type="tel" name="phone" class="bypass-phone-input"
                                   value="0168552584" placeholder="XX XX XX XX"
                                   maxlength="10" required>
                        </div>
                        <div class="hint">
                            <i class="bi bi-info-circle"></i> Vous recevrez une demande de confirmation sur votre mobile
                        </div>
                    </div>
                    <div class="bypass-actions">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-check-circle"></i> Payer ${formatted} XOF
                        </button>
                        <a href="/payment/bypass-cancel/${transactionId}" class="btn-cancel-bypass">
                            <i class="bi bi-x-circle"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        `;
    }

    modalEl.addEventListener('submit', function(e) {
        const form = e.target.closest('#bypass-form-container form');
        if (!form) return;
        e.preventDefault();

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            const isSuccess = data.success;
            const message = data.message;
            const redirectUrl = data.redirect_url;

            const icon = isSuccess ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
            const color = isSuccess ? '#1a7d36' : '#dc3545';
            const bg = isSuccess ? '#e8f5e9' : '#fbe9e7';
            const border = isSuccess ? '#a5d6a7' : '#ffab91';

            document.getElementById('payment-step-bypass').style.display = 'none';
            document.getElementById('payment-footer').style.display = 'none';

            const statusContent = document.getElementById('payment-status-content');
            statusContent.innerHTML = `
                <div style="font-size:56px;color:${color};margin-bottom:12px;">
                    <i class="bi ${icon}"></i>
                </div>
                <div style="font-size:18px;font-weight:600;color:${color};padding:12px 20px;
                            background:${bg};border:2px solid ${border};border-radius:12px;
                            display:inline-block;max-width:100%;">
                    ${message}
                </div>
            `;
            document.getElementById('payment-step-status').style.display = 'block';
            document.getElementById('paymentModalLabel').textContent = isSuccess ? 'Paiement réussi' : 'Paiement échoué';

            setTimeout(function() {
                window.location.href = redirectUrl;
            }, 1500);
        })
        .catch(function() {
            document.getElementById('payment-step-status').style.display = 'none';
            document.getElementById('payment-step-bypass').style.display = 'block';
            document.getElementById('payment-footer').style.display = 'flex';
        });
    });

    modalEl.addEventListener('hidden.bs.modal', function () {
        document.getElementById('payment-step-bypass').style.display = 'none';
        document.getElementById('payment-step-status').style.display = 'none';
        document.getElementById('payment-footer').style.display = 'flex';
        document.getElementById('bypass-form-container').innerHTML = '';
        document.getElementById('paymentModalLabel').textContent = 'Paiement Échéance';
    });

    // Facture / Reçu
    document.getElementById('btnPrintReceipt').addEventListener('click', function() {
        window.print();
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new Bootstrap.Tooltip(tooltipTriggerEl);
    });
});

async function showReceipt(repaymentId) {
    const projetModal = bootstrap.Modal.getInstance(document.getElementById('projetEcheancesModal'));
    if (projetModal) projetModal.hide();

    const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
    document.getElementById('receiptLoader').style.display = 'block';
    document.getElementById('receiptContent').style.display = 'none';
    document.getElementById('receiptFooter').style.display = 'none';
    modal.show();

    try {
        const response = await fetch(`/api/v1/repayments/receipt/by-echeance/${repaymentId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error('Erreur lors du chargement de la facture.');
        }

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Erreur lors du chargement de la facture.');
        }

        const r = data.receipt;
        const statutLabel = r.repayment.statut_label;
        const statutColor = r.repayment.statut === 'paye' ? 'success' : 'secondary';

        document.getElementById('receiptContent').innerHTML = `
            <div class="receipt-wrapper">
                <style>
                    .receipt-wrapper { font-family: 'Courier New', monospace; }
                    .receipt-header { text-align: center; border-bottom: 2px dashed #dee2e6; padding-bottom: 16px; margin-bottom: 20px; }
                    .receipt-header h3 { font-weight: 700; color: #e67e22; margin-bottom: 4px; }
                    .receipt-header .receipt-number { font-size: 13px; color: #6b7a8f; }
                    .receipt-body .row-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dotted #e9ecef; }
                    .receipt-body .row-item .label { color: #6b7a8f; font-weight: 500; }
                    .receipt-body .row-item .value { font-weight: 600; color: #2d3748; text-align: right; }
                    .receipt-total { background: #fff8f0; border: 2px solid #fde4c8; border-radius: 10px; padding: 16px; margin-top: 16px; }
                    .receipt-total .row-item { border-bottom: none; padding: 6px 0; }
                    .receipt-total .row-item.total { border-top: 2px solid #e67e22; margin-top: 6px; padding-top: 10px; }
                    .receipt-total .row-item.total .value { font-size: 18px; color: #d35400; }
                    .receipt-footer { text-align: center; margin-top: 20px; padding-top: 16px; border-top: 2px dashed #dee2e6; font-size: 12px; color: #9aa9bb; }
                    @media (max-width: 576px) {
                        .receipt-body .row-item { flex-direction: column; padding: 6px 0; gap: 2px; }
                        .receipt-body .row-item .value { text-align: left; }
                        .receipt-header { padding-bottom: 10px; margin-bottom: 12px; }
                        .receipt-header h3 { font-size: 16px; }
                        .receipt-total { padding: 10px; }
                        .receipt-footer { font-size: 10px; }
                    }
                    @media (max-width: 768px) {
                        .receipt-body .row-item { padding: 6px 0; }
                        .receipt-body .row-item .label { font-size: 12px; }
                        .receipt-body .row-item .value { font-size: 13px; }
                    }
                    @media print {
                        body { background: #fff !important; }
                        .receipt-wrapper { max-width: 100%; }
                        .modal-header, .modal-footer, .btn-close { display: none !important; }
                        .modal-content { border: none !important; box-shadow: none !important; }
                    }
                </style>
                <div class="receipt-header">
                    <h3>ALOGOTO</h3>
                    <div class="receipt-number">Reçu de paiement N° <strong>${r.receipt_number}</strong></div>
                    <div style="font-size:12px;color:#6b7a8f;">Date d'émission : ${r.date}</div>
                </div>
                <div class="receipt-body">
                    <h6 style="font-weight:600;margin-bottom:12px;color:#2d3748;">Projet</h6>
                    <div class="row-item">
                        <span class="label">Titre</span>
                        <span class="value">${r.project.title}</span>
                    </div>
                    <div class="row-item">
                        <span class="label">Code projet</span>
                        <span class="value">${r.project.code}</span>
                    </div>

                    <h6 style="font-weight:600;margin:16px 0 12px;color:#2d3748;">Porteur</h6>
                    <div class="row-item">
                        <span class="label">Nom</span>
                        <span class="value">${r.porteur.name}</span>
                    </div>
                    <div class="row-item">
                        <span class="label">Email</span>
                        <span class="value">${r.porteur.email}</span>
                    </div>

                    <h6 style="font-weight:600;margin:16px 0 12px;color:#2d3748;">Paiement</h6>
                    <div class="row-item">
                        <span class="label">Montant total</span>
                        <span class="value">${new Intl.NumberFormat('fr-FR').format(r.repayment.montant_total)} FCFA</span>
                    </div>
                    <div class="row-item">
                        <span class="label">Montant remboursé</span>
                        <span class="value">${new Intl.NumberFormat('fr-FR').format(r.repayment.montant_rembourse)} FCFA</span>
                    </div>
                    <div class="row-item">
                        <span class="label">Date d'échéance</span>
                        <span class="value">${r.repayment.date_echeance}</span>
                    </div>
                    <div class="row-item">
                        <span class="label">Date de paiement</span>
                        <span class="value">${r.repayment.date_paiement}</span>
                    </div>
                    <div class="row-item">
                        <span class="label">Statut</span>
                        <span class="value"><span class="badge bg-${statutColor}">${statutLabel}</span></span>
                    </div>
                </div>
                <div class="receipt-total">
                    <div class="row-item">
                        <span class="label">Montant versé</span>
                        <span class="value" style="font-size:16px;">${new Intl.NumberFormat('fr-FR').format(r.repayment.montant_rembourse)} FCFA</span>
                    </div>
                </div>
                <div class="receipt-footer">
                    Généré par ${r.generated_by} &bull; ${r.date}<br>
                    <strong>Alogoto</strong> &mdash; Solution de financement participatif
                </div>
            </div>
        `;

        document.getElementById('receiptLoader').style.display = 'none';
        document.getElementById('receiptContent').style.display = 'block';
        document.getElementById('receiptFooter').style.display = 'flex';
    } catch (error) {
        document.getElementById('receiptLoader').innerHTML = `
            <i class="fe fe-alert-triangle fs-40 text-danger mb-3 d-block"></i>
            <p class="text-danger">${error.message || 'Erreur lors du chargement de la facture.'}</p>
        `;
        console.error('Receipt error:', error);
    }
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
