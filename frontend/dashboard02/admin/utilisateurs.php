<?php 
require_once __DIR__ . '/header.php'; 
?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title text-uppercase fw-bold">Gestion des Utilisateurs</h1>
                    <p class="text-muted mb-0">Supervision, activation et contrôle des comptes utilisateurs de la plateforme.</p>
                </div>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Utilisateurs</li>
                    </ol>
                </div>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- KPI PRINCIPAUX -->
            <div class="row" id="kpi-container">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Total Utilisateurs</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-total-users">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-primary-transparent text-primary">
                                        <i class="fe fe-users"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-success fw-bold"><i class="fa fa-chevron-up"></i> tous profils</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Porteurs de Projet</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-porteurs">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-info-transparent text-info">
                                        <i class="fe fe-briefcase"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-info fw-bold"><i class="fa fa-briefcase"></i> actifs</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Institutions</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-institutions">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-warning-transparent text-warning">
                                        <i class="fe fe-home"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-warning fw-bold"><i class="fa fa-building"></i> enregistrées</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Comptes Suspendus</p>
                                    <h3 class="mb-0 number-font fw-bold text-danger" id="kpi-suspended">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-danger-transparent text-danger">
                                        <i class="fe fe-user-x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-danger fw-bold"><i class="fa fa-exclamation-triangle"></i> à vérifier</span>
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
                                <div class="col-12 col-sm-6 col-md-4">
                                    <input type="text" id="search-input" class="form-control" placeholder="Rechercher par nom, email...">
                                </div>
                                <div class="col-12 col-sm-6 col-md-2">
                                    <select id="filter-role" class="form-control select2">
                                        <option value="">Tous les rôles</option>
                                        <option value="porteur">Porteur</option>
                                        <option value="institution">Institution</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-6 col-md-2">
                                    <select id="filter-status" class="form-control select2">
                                        <option value="">Tous les statuts</option>
                                        <option value="active">Actif</option>
                                        <option value="suspended">Suspendu</option>
                                        <option value="pending">En attente</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button id="apply-filters" class="btn btn-primary w-100"><i class="fe fe-filter me-1"></i>Filtrer</button>
                                    <button id="export-users" class="btn btn-success w-100"><i class="fe fe-download me-1"></i>Export</button>
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
                            <h3 class="card-title">Annuaire des Utilisateurs</h3>
                            <div class="card-options">
                                <span class="badge bg-primary me-2" id="users-count">0 utilisateurs</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom" id="users-table">
                                    <thead>
                                        <tr>
                                            <th class="wd-15p border-bottom-0">Utilisateur</th>
                                            <th class="wd-15p border-bottom-0">Contact</th>
                                            <th class="wd-10p border-bottom-0">Rôle</th>
                                            <th class="wd-10p border-bottom-0">Statut</th>
                                            <th class="wd-15p border-bottom-0">Inscription</th>
                                            <th class="wd-10p border-bottom-0">Dernière Activité</th>
                                            <th class="wd-25p border-bottom-0">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="users-tbody">
                                        <!-- Dynamique -->
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
                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Inscriptions Mensuelles</h3>
                        </div>
                        <div class="card-body">
                            <div id="registrations-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Répartition par Rôle</h3>
                        </div>
                        <div class="card-body">
                            <div id="roles-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DÉTAILS UTILISATEUR -->
<div class="modal fade" id="modal-user-admin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="user-modal-title">Détail Utilisateur</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Infos Utilisateur -->
                    <div class="col-md-7">
                        <div id="user-modal-content">
                            <!-- Dynamique -->
                        </div>
                        <h5 class="mt-4 border-bottom pb-2">Projets Associés</h5>
                        <div id="user-projects-list">
                            <!-- Dynamique -->
                        </div>
                    </div>
                    <!-- Actions -->
                    <div class="col-md-5 border-start">
                        <div class="card shadow-none border">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">Actions Administratives</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Note / Motif</label>
                                    <textarea id="user-admin-comment" class="form-control" rows="3" placeholder="Motif de l'action..."></textarea>
                                </div>
                                <div class="d-grid gap-2">
                                    <button id="btn-activate-user" class="btn btn-success"><i class="fe fe-check-circle me-2"></i>Activer Compte</button>
                                    <button id="btn-suspend-user" class="btn btn-warning"><i class="fe fe-pause-circle me-2"></i>Suspendre Compte</button>
                                    <button id="btn-delete-user" class="btn btn-danger"><i class="fe fe-trash-2 me-2"></i>Supprimer Compte</button>
                                    <button id="btn-reset-password" class="btn btn-info"><i class="fe fe-key me-2"></i>Réinitialiser Mot de Passe</button>
                                </div>
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
<script src="js/utilisateurs.js"></script>
