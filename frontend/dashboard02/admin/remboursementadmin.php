<?php require_once __DIR__ . '/header.php'; ?>
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title text-uppercase fw-bold">Suivi des Remboursements</h1>
                    <p class="text-muted mb-0">Supervision des paiements, taux de recouvrement et incidents.</p>
                </div>
                <div><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Admin</a></li><li class="breadcrumb-item active">Remboursements</li></ol></div>
            </div>

            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Total Remboursé</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-total-repaid">0 FCFA</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-success-transparent text-success">
                                        <i class="fe fe-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-success fw-bold"><i class="fa fa-check-circle"></i> cumulé</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Taux Recouvrement</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-recovery-rate">0%</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-primary-transparent text-primary">
                                        <i class="fe fe-percent"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-primary fw-bold"><i class="fa fa-percent"></i> global</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Projets</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-in-progress">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-info-transparent text-info">
                                        <i class="fe fe-briefcase"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-info fw-bold"><i class="fa fa-folder"></i> avec remboursements</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Remboursements</p>
                                    <h3 class="mb-0 number-font fw-bold text-danger" id="kpi-defaults">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-success-transparent text-success">
                                        <i class="fe fe-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-success fw-bold"><i class="fa fa-check"></i> effectués</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Projets avec Remboursements</h3>
                            <span class="badge bg-primary" id="projects-count">0 projets</span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-sm-8 col-md-4"><input type="text" id="search-input" class="form-control" placeholder="Rechercher par projet ou porteur..."></div>
                                <div class="col-12 col-sm-4 col-md-2"><button id="apply-filters" class="btn btn-primary w-100">Filtrer</button></div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead><tr>
                                        <th class="border-bottom-0">Projet</th>
                                        <th class="border-bottom-0">Porteur</th>
                                        <th class="border-bottom-0 text-center">Remboursements</th>
                                        <th class="border-bottom-0">Total Dû</th>
                                        <th class="border-bottom-0">Payé</th>
                                        <th class="border-bottom-0">Restant</th>
                                        <th class="border-bottom-0">Actions</th>
                                    </tr></thead>
                                    <tbody id="projects-tbody"></tbody>
                                </table>
                            </div>
                            <div id="pagination-container" class="mt-4 d-flex justify-content-center"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL REMBOURSEMENTS PROJET -->
<div class="modal fade" id="repaymentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="repaymentsModalLabel">Remboursements du Projet</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-loader" class="text-center py-5" style="display:none;">
                    <div class="spinner-border text-white" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
                <div id="modal-content">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold" id="modal-project-title"></h6>
                            <small class="text-muted" id="modal-porteur-name"></small>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>N°</th>
                                    <th>Montant Total</th>
                                    <th>Montant Payé</th>
                                    <th>Reste</th>
                                    <th>Date Échéance</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody id="modal-repayments-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
<script src="js/remboursementadmin.js"></script>
