<?php 
require_once __DIR__ . '/header.php'; 
?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title text-uppercase fw-bold">Supervision des Financements</h1>
                    <p class="text-muted mb-0">Suivi des décaissements, investissements institutionnels et flux financiers de la plateforme.</p>
                </div>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Financements</li>
                    </ol>
                </div>
            </div>

            <!-- KPI PRINCIPAUX -->
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Volume Total Financé</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-total-volume">0 FCFA</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-success-transparent text-success">
                                        <i class="fe fe-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-success fw-bold"><i class="fa fa-chevron-up"></i> cumul plateforme</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Financements Actifs</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-active-funding">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-primary-transparent text-primary">
                                        <i class="fe fe-trending-up"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-primary fw-bold"><i class="fa fa-chart-line"></i> en cours</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">En Attente de Décaissement</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-pending-disbursement">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-warning-transparent text-warning">
                                        <i class="fe fe-clock"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-warning fw-bold"><i class="fa fa-clock-o"></i> à traiter</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Incidents Paiement</p>
                                    <h3 class="mb-0 number-font fw-bold text-danger" id="kpi-payment-incidents">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-danger-transparent text-danger">
                                        <i class="fe fe-alert-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-danger fw-bold"><i class="fa fa-exclamation-triangle"></i> à résoudre</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FILTRES -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Filtres et Recherche</h3></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <input type="text" id="search-input" class="form-control" placeholder="Rechercher par projet, institution...">
                                </div>
                                <div class="col-12 col-sm-6 col-md-2">
                                    <select id="filter-type" class="form-control select2">
                                        <option value="">Tous les types</option>
                                        <option value="investment">Investissement</option>
                                        <option value="loan">Prêt</option>
                                        <option value="grant">Subvention</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-2">
                                    <select id="filter-status" class="form-control select2">
                                        <option value="">Tous les statuts</option>
                                        <option value="pending">En attente</option>
                                        <option value="disbursed">Décaissé</option>
                                        <option value="completed">Terminé</option>
                                        <option value="failed">Échoué</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-2">
                                    <input type="date" id="filter-date-from" class="form-control" placeholder="Date début">
                                </div>
                                <div class="col-12 col-md-3 d-flex gap-2">
                                    <button id="apply-filters" class="btn btn-primary w-100"><i class="fe fe-filter me-1"></i>Filtrer</button>
                                    <button id="export-finance" class="btn btn-success w-100"><i class="fe fe-download me-1"></i>Export</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLEAU PRINCIPAL -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Historique des Financements</h3>
                            <span class="badge bg-primary" id="finance-count">0 opérations</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom" id="finance-table">
                                    <thead>
                                        <tr>
                                            <th class="border-bottom-0">Réf.</th>
                                            <th class="border-bottom-0">Projet</th>
                                            <th class="border-bottom-0">Institution</th>
                                            <th class="border-bottom-0">Montant</th>
                                            <th class="border-bottom-0">Type</th>
                                            <th class="border-bottom-0">Statut</th>
                                            <th class="border-bottom-0">Date</th>
                                            <th class="border-bottom-0">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="finance-tbody">
                                        <tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div id="pagination-container" class="mt-4 d-flex justify-content-center"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRAPHIQUES -->
            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Flux Financiers Mensuels</h3></div>
                        <div class="card-body"><div id="finance-flow-chart"></div></div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="card-header"><h3 class="card-title">Répartition par Type</h3></div>
                        <div class="card-body"><div id="finance-type-chart"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DÉTAIL FINANCEMENT -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalDetailTitle">Détail du Financement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detail-loading" class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2 text-muted">Chargement...</p>
                </div>
                <div id="detail-content" style="display:none;">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <h6 class="text-muted text-uppercase fs-12 fw-semibold">Projet</h6>
                            <p class="fw-medium fs-16" id="dd-project">—</p>
                            <div class="text-muted" id="dd-description">—</div>
                            <div class="mt-3">
                                <h6 class="text-muted text-uppercase fs-12 fw-semibold">Porteur</h6>
                                <p class="mb-0" id="dd-porteur">—</p>
                                <small class="text-muted" id="dd-porteur-email">—</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center py-3">
                                    <h6 class="text-muted text-uppercase fs-11 fw-semibold">Statut</h6>
                                    <span class="badge fs-13 px-3 py-2" id="dd-statut">—</span>
                                    <hr class="my-2">
                                    <h6 class="text-muted text-uppercase fs-11 fw-semibold">Référence</h6>
                                    <p class="mb-0 fw-medium" id="dd-reference">—</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <div class="card border">
                                <div class="card-body text-center py-3">
                                    <h6 class="text-muted text-uppercase fs-11 fw-semibold">Montant Demande</h6>
                                    <p class="fs-16 fw-bold text-primary mb-0" id="dd-montant-demande">0 FCFA</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border">
                                <div class="card-body text-center py-3">
                                    <h6 class="text-muted text-uppercase fs-11 fw-semibold">Montant Valide</h6>
                                    <p class="fs-16 fw-bold text-success mb-0" id="dd-montant-valide">0 FCFA</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border">
                                <div class="card-body text-center py-3">
                                    <h6 class="text-muted text-uppercase fs-11 fw-semibold">Mensualite</h6>
                                    <p class="fs-16 fw-bold text-info mb-0" id="dd-montant-mensuel">0 FCFA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-3">
                            <h6 class="text-muted text-uppercase fs-12 fw-semibold">Taux Interet</h6>
                            <p class="fw-medium" id="dd-taux">—</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted text-uppercase fs-12 fw-semibold">Duree</h6>
                            <p class="fw-medium" id="dd-duree">—</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted text-uppercase fs-12 fw-semibold">Progression</h6>
                            <p class="fw-medium" id="dd-progression">—</p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted text-uppercase fs-12 fw-semibold">Reste Du</h6>
                            <p class="fw-medium text-danger" id="dd-reste">—</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <h6 class="text-muted text-uppercase fs-12 fw-semibold">Date Financement</h6>
                            <p class="mb-0" id="dd-date-financement">—</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted text-uppercase fs-12 fw-semibold">Date Validation</h6>
                            <p class="mb-0" id="dd-date-validation">—</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted text-uppercase fs-12 fw-semibold">Date Decaissement</h6>
                            <p class="mb-0" id="dd-date-decaissement">—</p>
                        </div>
                    </div>
                    <div class="mt-3" id="dd-conditions-row" style="display:none;">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Conditions</h6>
                        <p class="text-muted" id="dd-conditions">—</p>
                    </div>
                    <div class="mt-3" id="dd-commentaires-row" style="display:none;">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Commentaires</h6>
                        <p class="text-muted" id="dd-commentaires">—</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL REÇU -->
<div class="modal fade" id="recuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Reçu de Financement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="recu-loading" class="text-center py-5" style="display:none;">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2 text-muted">Chargement...</p>
                </div>
                <div id="recu-content">
                    <div class="text-center mb-4">
                        <i class="fe fe-file-text fs-40 text-primary"></i>
                        <h4 class="mt-2" id="recu-reference">FIN-00000</h4>
                    </div>
                    <table class="table table-sm table-borderless">
                        <tr><td class="text-muted">Projet</td><td class="fw-medium text-end" id="recu-projet">—</td></tr>
                        <tr><td class="text-muted">Institution</td><td class="fw-medium text-end" id="recu-institution">—</td></tr>
                        <tr><td class="text-muted">Porteur</td><td class="fw-medium text-end" id="recu-porteur">—</td></tr>
                        <tr><td class="text-muted">Montant</td><td class="fw-bold text-success text-end fs-16" id="recu-montant">0 FCFA</td></tr>
                        <tr><td class="text-muted">Taux</td><td class="fw-medium text-end" id="recu-taux">—</td></tr>
                        <tr><td class="text-muted">Durée</td><td class="fw-medium text-end" id="recu-duree">—</td></tr>
                        <tr><td class="text-muted">Statut</td><td class="text-end" id="recu-statut">—</td></tr>
                        <tr><td class="text-muted">Date</td><td class="fw-medium text-end" id="recu-date">—</td></tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fe fe-printer me-1"></i>Imprimer</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
<script src="js/financementadmin.js"></script>
