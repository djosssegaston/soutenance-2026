<?php require_once __DIR__ . '/header.php'; ?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <!-- PAGE-HEADER -->
        <div class="page-header">
            <h1 class="page-title">Gestion des Remboursements & Suivi de Portefeuille</h1>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Institution</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Remboursements</li>
                </ol>
            </div>
        </div>
        <!-- PAGE-HEADER END -->

        <!-- STATS CARDS -->
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="mt-2">
                                <h6 class="">Total Remboursé</h6>
                                <h2 class="mb-0 number-font" id="stat-total-paid">0 FCFA</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.10" data-thickness="2" data-color="#09ad95">
                                    <div class="chart-circle-value text-success"><i class="fe fe-check-circle"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="mt-2">
                                <h6 class="">Reste à Recouvrer</h6>
                                <h2 class="mb-0 number-font" id="stat-total-remaining">0 FCFA</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.10" data-thickness="2" data-color="#05c3fb">
                                    <div class="chart-circle-value text-primary"><i class="fe fe-trending-down"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="mt-2">
                                <h6 class="">Taux de Recouvrement</h6>
                                <h2 class="mb-0 number-font" id="stat-recovery-rate">0%</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.10" data-thickness="2" data-color="#f7b731">
                                    <div class="chart-circle-value text-warning"><i class="fe fe-pie-chart"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="mt-2">
                                <h6 class="">Retards Critiques</h6>
                                <h2 class="mb-0 number-font text-danger" id="stat-total-late">0</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.10" data-thickness="2" data-color="#e82646">
                                    <div class="chart-circle-value text-danger"><i class="fe fe-alert-circle"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- STATS CARDS END -->

        <!-- FILTERS -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Statut Paiement</label>
                                <select class="form-control select2" id="filter-status">
                                    <option value="">Tous les statuts</option>
                                    <option value="en_attente">En attente</option>
                                    <option value="paye">Payé</option>
                                    <option value="retard">En retard</option>
                                    <option value="litige">En litige</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Niveau de Risque</label>
                                <select class="form-control select2" id="filter-risk">
                                    <option value="">Tous les risques</option>
                                    <option value="faible">Faible</option>
                                    <option value="moyen">Moyen</option>
                                    <option value="eleve">Élevé</option>
                                    <option value="critique">Critique</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary" id="search-btn"><i class="fe fe-search"></i> Filtrer</button>
                                <button class="btn btn-secondary" id="reset-filters"><i class="fe fe-refresh-cw"></i> Réinitialiser</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- REPAYMENTS LIST -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Portefeuille Actif & Échéanciers</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover text-nowrap mb-0 align-middle" id="repayments-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Projet / Porteur</th>
                                        <th>Montant Échéance</th>
                                        <th>Reste à Payer</th>
                                        <th>Date Échéance</th>
                                        <th>Risque</th>
                                        <th>Statut</th>
                                        <th class="pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="repayments-list">
                                    <!-- Dynamique -->
                                </tbody>
                            </table>
                        </div>
                        <div id="pagination-container" class="mt-4"></div>
                        <div id="repayments-loader" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2">Chargement du portefeuille...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETAILS & ACTIONS -->
<div class="modal fade" id="modal-repayment-details" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Suivi du Remboursement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Dynamique -->
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-danger" id="btn-open-dispute"><i class="fe fe-alert-triangle"></i> Ouvrir un Litige</button>
                </div>
                <div>
                    <button type="button" class="btn btn-success" id="btn-validate-repayment"><i class="fe fe-check-circle"></i> Valider la Réception</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/remboursement.js"></script>

<?php require_once __DIR__ . '/footer.php'; ?>
