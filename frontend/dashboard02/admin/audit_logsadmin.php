<?php require_once __DIR__ . '/header.php'; ?>
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title text-uppercase fw-bold">Journal d'Audit</h1>
                    <p class="text-muted mb-0">Traçabilité complète des actions sensibles sur la plateforme.</p>
                </div>
                <div><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Admin</a></li><li class="breadcrumb-item active">Audit Logs</li></ol></div>
            </div>

            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Actions Aujourd'hui</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-today">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-primary-transparent text-primary">
                                        <i class="fe fe-activity"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-primary fw-bold"><i class="fa fa-clock-o"></i> dernières 24h</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Validations</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-validations">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-success-transparent text-success">
                                        <i class="fe fe-check-square"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-success fw-bold"><i class="fa fa-check"></i> approuvées</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Rejets</p>
                                    <h3 class="mb-0 number-font fw-bold text-warning" id="kpi-rejections">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-warning-transparent text-warning">
                                        <i class="fe fe-x-square"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-warning fw-bold"><i class="fa fa-times"></i> refusées</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Alertes Sécurité</p>
                                    <h3 class="mb-0 number-font fw-bold text-danger" id="kpi-security">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-danger-transparent text-danger">
                                        <i class="fe fe-shield"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-danger fw-bold"><i class="fa fa-shield"></i> détectées</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Historique des Actions</h3>
                            <span class="badge bg-primary" id="logs-count">0 entrées</span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-sm-6 col-md-3"><input type="text" id="search-input" class="form-control" placeholder="Rechercher par utilisateur, action..."></div>
                                <div class="col-12 col-sm-6 col-md-2"><select id="filter-action" class="form-control"><option value="">Toutes actions</option><option value="validate">Validation</option><option value="reject">Rejet</option><option value="suspend">Suspension</option><option value="login">Connexion</option><option value="update">Modification</option></select></div>
                                <div class="col-12 col-sm-6 col-md-2"><select id="filter-level" class="form-control"><option value="">Tous niveaux</option><option value="info">Info</option><option value="warning">Warning</option><option value="critical">Critique</option></select></div>
                                <div class="col-12 col-sm-6 col-md-2"><input type="date" id="filter-date" class="form-control"></div>
                                <div class="col-12 col-md-3 d-flex gap-2">
                                    <button id="apply-filters" class="btn btn-primary w-100">Filtrer</button>
                                    <button class="btn btn-success w-100"><i class="fe fe-download me-1"></i>Export</button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead><tr>
                                        <th class="border-bottom-0">Date / Heure</th>
                                        <th class="border-bottom-0">Utilisateur</th>
                                        <th class="border-bottom-0">Action</th>
                                        <th class="border-bottom-0">Cible</th>
                                        <th class="border-bottom-0">Niveau</th>
                                        <th class="border-bottom-0">IP</th>
                                        <th class="border-bottom-0">Détails</th>
                                    </tr></thead>
                                    <tbody id="logs-tbody"></tbody>
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
<script src="js/audit_logsadmin.js"></script>
