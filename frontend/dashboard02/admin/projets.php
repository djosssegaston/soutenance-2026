<?php 
require_once __DIR__ . '/header.php'; 
?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title text-uppercase fw-bold">Supervision Globale des Projets</h1>
                    <p class="text-muted mb-0">Contrôle, validation, risques et monitoring analytique de la plateforme.</p>
                </div>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Projets</li>
                    </ol>
                </div>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- KPI PRINCIPAUX -->
            <div class="row" id="kpi-container">
                <!-- Projets Stats -->
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Total Projets</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-total-projects">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-primary-transparent text-primary">
                                        <i class="fe fe-briefcase"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-success fw-bold"><i class="fa fa-chevron-up"></i> plateforme</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Validation Stats -->
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">En Attente</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-pending-validation">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-warning-transparent text-warning">
                                        <i class="fe fe-clipboard"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-warning fw-bold"><i class="fa fa-clock-o"></i> à valider</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Risk Stats -->
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Risques Élevés</p>
                                    <h3 class="mb-0 number-font fw-bold text-danger" id="kpi-high-risk">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-danger-transparent text-danger">
                                        <i class="fe fe-alert-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-danger fw-bold"><i class="fa fa-exclamation-triangle"></i> critiques</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Finance Stats -->
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Volume Financé</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-total-funded">0 FCFA</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-success-transparent text-success">
                                        <i class="fe fe-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-success fw-bold"><i class="fa fa-money"></i> total</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FILTRES AVANCÉS -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Filtres et Recherche</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-sm-6 col-md-3">
                                    <input type="text" id="search-input" class="form-control" placeholder="Rechercher par titre, porteur...">
                                </div>
                                <div class="col-12 col-sm-6 col-md-2">
                                    <select id="filter-status" class="form-control select2">
                                        <option value="">Tous les statuts</option>
                                        <option value="draft">Brouillon</option>
                                        <option value="submitted">Soumis</option>
                                        <option value="admin_validated">Validé Admin</option>
                                        <option value="active">Actif</option>
                                        <option value="suspended">Suspendu</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-2">
                                    <select id="filter-sector" class="form-control select2">
                                        <option value="">Tous les secteurs</option>
                                        <option value="Agriculture">Agriculture</option>
                                        <option value="Commerce">Commerce</option>
                                        <option value="Technologie">Technologie</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-2">
                                    <select id="filter-risk" class="form-control select2">
                                        <option value="">Tous les risques</option>
                                        <option value="faible">Faible</option>
                                        <option value="moyen">Moyen</option>
                                        <option value="élevé">Élevé</option>
                                        <option value="critique">Critique</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-3 d-flex gap-2">
                                    <button id="apply-filters" class="btn btn-primary w-100">Filtrer</button>
                                    <button id="export-excel" class="btn btn-success w-100">Export Excel</button>
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
                            <h3 class="card-title">Portefeuille de Projets Plateforme</h3>
                            <div class="card-options">
                                <span class="badge bg-primary me-2" id="projects-count">0 projets</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom" id="projects-table">
                                    <thead>
                                        <tr>
                                            <th class="wd-15p border-bottom-0">Projet</th>
                                            <th class="wd-15p border-bottom-0">Porteur / Institution</th>
                                            <th class="wd-15p border-bottom-0">Financement</th>
                                            <th class="wd-10p border-bottom-0">Statut</th>
                                            <th class="wd-10p border-bottom-0">Risque</th>
                                            <th class="wd-10p border-bottom-0">Dates</th>
                                            <th class="wd-25p border-bottom-0">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="projects-tbody">
                                        <!-- Dynamique -->
                                    </tbody>
                                </table>
                            </div>
                            <div id="pagination-container" class="mt-4 d-flex justify-content-center"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRAPHIQUES ANALYTIQUES -->
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Évolution des Projets (Année en cours)</h3>
                        </div>
                        <div class="card-body">
                            <div id="evolution-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Répartition Secteurs</h3>
                        </div>
                        <div class="card-body">
                            <div id="sectors-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Statuts Projets</h3>
                        </div>
                        <div class="card-body">
                            <div id="status-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DÉTAILS & ACTIONS -->
<div class="modal fade" id="modal-project-admin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="admin-modal-title">Détail & Audit Projet</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Infos Gauche -->
                    <div class="col-md-8">
                        <div id="admin-modal-content">
                            <!-- Dynamique -->
                        </div>
                        
                        <h5 class="mt-4 border-bottom pb-2">Historique d'Audit & Activité</h5>
                        <div class="timeline-wrapper timeline-wrapper-primary" id="admin-timeline">
                            <!-- Dynamique -->
                        </div>
                    </div>
                    <!-- Actions Droite -->
                    <div class="col-md-4 border-start">
                        <div class="card shadow-none border">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">Contrôle Admin</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Note d'audit / Commentaire</label>
                                    <textarea id="admin-comment" class="form-control" rows="3" placeholder="Indiquez le motif de votre décision..."></textarea>
                                </div>
                                <div class="d-grid gap-2">
                                    <button id="btn-validate" class="btn btn-success"><i class="fe fe-check-circle me-2"></i>Valider Projet</button>
                                    <button id="btn-reject" class="btn btn-danger"><i class="fe fe-x-circle me-2"></i>Rejeter Projet</button>
                                    <button id="btn-suspend" class="btn btn-warning"><i class="fe fe-pause-circle me-2"></i>Suspendre Projet</button>
                                    <button id="btn-audit" class="btn btn-info"><i class="fe fe-search me-2"></i>Ouvrir Audit Profond</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card shadow-none border">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">Documents & Preuves</h6>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush" id="admin-docs-list">
                                    <!-- Dynamique -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

<!-- SCRIPTS DYNAMIQUES -->
<script src="js/projets.js?v=<?= filemtime(__DIR__.'/js/projets.js') ?: 1 ?>"></script>
