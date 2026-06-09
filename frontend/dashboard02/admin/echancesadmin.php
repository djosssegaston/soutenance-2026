<?php require_once __DIR__ . '/header.php'; ?>
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title text-uppercase fw-bold">Suivi des Échéances</h1>
                    <p class="text-muted mb-0">Calendrier des remboursements et alertes de retard.</p>
                </div>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Échéances</li>
                    </ol>
                </div>
            </div>

            <!-- KPIs -->
            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Projets</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-total-projects">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-primary-transparent text-primary">
                                        <i class="fe fe-briefcase"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-primary fw-bold"><i class="fa fa-folder"></i> avec échéances</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Payées</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-total-paid">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-success-transparent text-success">
                                        <i class="fe fe-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-success fw-bold"><i class="fa fa-check"></i> échéances conformes</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">En Retard</p>
                                    <h3 class="mb-0 number-font fw-bold text-danger" id="kpi-total-overdue">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-danger-transparent text-danger">
                                        <i class="fe fe-alert-triangle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-danger fw-bold"><i class="fa fa-exclamation-circle"></i> critique</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Total Échéances</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-total-repayments">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-warning-transparent text-warning">
                                        <i class="fe fe-credit-card"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-warning fw-bold"><i class="fa fa-list"></i> enregistrées</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLEAU PROJETS -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Projets avec Échéances</h3>
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
                                        <th class="border-bottom-0 text-center">Échéances</th>
                                        <th class="border-bottom-0">Total Dû</th>
                                        <th class="border-bottom-0">Payé</th>
                                        <th class="border-bottom-0">Restant</th>
                                        <th class="border-bottom-0 text-center">Retard</th>
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

<!-- MODAL ÉCHÉANCES PROJET -->
<div class="modal fade" id="echeancesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="echeancesModalLabel">Échéances du Projet</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-loader" class="text-center py-5" style="display:none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
                <div id="modal-content">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold" id="modal-project-title"></h6>
                            <small class="text-muted" id="modal-porteur-name"></small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">Institution</small>
                            <p class="fw-bold mb-0" id="modal-institution-name"></p>
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
                            <tbody id="modal-echeances-body"></tbody>
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
<script src="js/echeancesadmin.js"></script>
