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
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">En Cours</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-in-progress">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-info-transparent text-info">
                                        <i class="fe fe-refresh-cw"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-info fw-bold"><i class="fa fa-spinner"></i> actifs</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Défauts de Paiement</p>
                                    <h3 class="mb-0 number-font fw-bold text-danger" id="kpi-defaults">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-danger-transparent text-danger">
                                        <i class="fe fe-x-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-danger fw-bold"><i class="fa fa-ban"></i> critiques</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Historique des Remboursements</h3>
                            <span class="badge bg-primary" id="remb-count">0</span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-sm-6 col-md-4"><input type="text" id="search-input" class="form-control" placeholder="Rechercher..."></div>
                                <div class="col-12 col-sm-6 col-md-3"><select id="filter-status" class="form-control"><option value="">Tous</option><option value="paid">Payé</option><option value="partial">Partiel</option><option value="late">En retard</option><option value="defaulted">Défaut</option></select></div>
                                <div class="col-12 col-sm-6 col-md-3"><button id="apply-filters" class="btn btn-primary w-100">Filtrer</button></div>
                                <div class="col-12 col-sm-6 col-md-2"><button class="btn btn-success w-100"><i class="fe fe-download me-1"></i>Export</button></div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead><tr>
                                        <th class="border-bottom-0">Réf.</th><th class="border-bottom-0">Projet</th>
                                        <th class="border-bottom-0">Porteur</th><th class="border-bottom-0">Montant</th>
                                        <th class="border-bottom-0">Payé</th><th class="border-bottom-0">Reste</th>
                                        <th class="border-bottom-0">Statut</th><th class="border-bottom-0">Actions</th>
                                    </tr></thead>
                                    <tbody id="remb-tbody"></tbody>
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
<?php require_once __DIR__ . '/footer.php'; ?>
<script src="js/remboursementadmin.js"></script>
