<?php
require_once __DIR__ . '/header.php';
?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <!-- PAGE HEADER -->
            <div class="page-header">
                <h1 class="page-title text-uppercase fw-bold">Tableau de Bord Admin</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Supervision Centrale</li>
                    </ol>
                </div>
            </div>

            <!-- ALERTES CRITIQUES -->
            <!-- <div class="row mb-4">
                <div class="col-12" id="alerts-container">
                </div>
            </div> -->

            <!-- KPI CARDS - PROJETS & FINANCES -->
            <div class="row">
                <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Projets Plateforme</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-total-projects">0</h3>
                                </div>
                                <div class="col-auto d-none d-sm-flex">
                                    <div class="card-icon bg-primary-transparent text-primary">
                                        <i class="fe fe-layers"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11">
                                <span class="badge bg-primary-transparent text-primary" id="kpi-active-projects">0</span>
                                <span class="badge bg-success-transparent text-success" id="kpi-validated-projects">0</span>
                                <span class="badge bg-danger-transparent text-danger ms-1" id="kpi-suspended-projects">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Volume Financé</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-total-funded">0</h3>
                                </div>
                                <div class="col-auto d-none d-sm-flex">
                                    <div class="card-icon bg-success-transparent text-success">
                                        <i class="fe fe-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11">
                                <span class="text-success fw-bold" id="kpi-total-repaid">0 FCFA</span>
                                <span class="d-none" id="kpi-over-funded">0</span>
                                <span class="d-none" id="kpi-late-repayments">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Utilisateurs</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-total-users">0</h3>
                                </div>
                                <div class="col-auto d-none d-sm-flex">
                                    <div class="card-icon bg-info-transparent text-info">
                                        <i class="fe fe-users"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11">
                                <span id="kpi-total-institutions">0</span> Inst. | <span id="kpi-total-porteurs">0</span> Port. | <span id="kpi-total-suspended-users" class="text-danger">0</span> Suspendus
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-md-6 col-lg-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Supervision</p>
                                    <h3 class="mb-0 number-font fw-bold text-danger" id="kpi-critical-projects">0</h3>
                                </div>
                                <div class="col-auto d-none d-sm-flex">
                                    <div class="card-icon bg-danger-transparent text-danger">
                                        <i class="fe fe-shield"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11">
                                <span id="kpi-active-disputes" class="text-danger fw-bold">0</span> litiges
                                <span class="d-none" id="kpi-repayment-defaults">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ANALYTICS - ROW 1 -->
            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom">
                            <h3 class="card-title fw-bold fs-14">Évolution Globale de la Plateforme</h3>
                        </div>
                        <div class="card-body">
                            <div id="chart-evolution" class="chart-sh"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom">
                            <h3 class="card-title fw-bold fs-14">Répartition par Secteurs</h3>
                        </div>
                        <div class="card-body">
                            <div id="chart-sectors" class="chart-sh"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ANALYTICS - ROW 2 -->
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title fw-bold fs-14">Distribution des Statuts</h3>
                        </div>
                        <div class="card-body">
                            <div id="chart-status" class="chart-sh"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title fw-bold fs-14">Analyse du Risque Portefeuille</h3>
                        </div>
                        <div class="card-body">
                            <div id="chart-risks" class="chart-sh"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="card border-top border-primary border-5">
                        <div class="card-header border-bottom">
                            <h3 class="card-title fw-bold fs-14">Monitoring Système</h3>
                        </div>
                        <div class="card-body p-0" id="system-status-container">
                            <!-- Dynamique -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- RECENT PROJECTS & ACTIVITY -->
            <div class="row">
                <div class="col-xl-8 col-lg-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header justify-content-between border-bottom">
                            <h3 class="card-title fw-bold fs-15">Derniers Projets Soumis</h3>
                            <div class="card-options">
                                <a href="projets.php" class="btn btn-primary btn-sm rounded-pill">Gestion complète</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover text-nowrap mb-0 align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Projet</th>
                                            <th>Porteur</th>
                                            <th>Montant</th>
                                            <th>Risque</th>
                                            <th>Statut</th>
                                            <th class="pe-4 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recent-projects-tbody">
                                        <!-- Dynamique -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom">
                            <h3 class="card-title fw-bold fs-15">Flux d'Activité Temps Réel</h3>
                        </div>
                        <div class="card-body pb-0">
                            <ul class="task-list" id="activity-timeline">
                                <!-- Dynamique -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODULE ACTIONS RAPIDES -->
            <div class="row mb-5">
                <div class="col-12">
                    <div class="card border-0 bg-white shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="mb-4 fw-bold text-uppercase fs-13">Actions Rapides Administrateur</h5>
                            <div class="row g-2">
                                <div class="col-6 col-sm-4 col-md-4 col-lg-2">
                                    <a href="projets.php" class="btn btn-outline-primary w-100 py-3 fs-12">
                                        <i class="fe fe-check-square d-block fs-18 mb-2"></i> Valider Projets
                                    </a>
                                </div>
                                <div class="col-6 col-sm-4 col-md-4 col-lg-2">
                                    <a href="utilisateurs.php" class="btn btn-outline-info w-100 py-3 fs-12">
                                        <i class="fe fe-user-plus d-block fs-18 mb-2"></i> Gérer Utilisateurs
                                    </a>
                                </div>
                                <div class="col-6 col-sm-4 col-md-4 col-lg-2">
                                    <a href="remboursementadmin.php" class="btn btn-outline-warning w-100 py-3 fs-12">
                                        <i class="fe fe-trending-up d-block fs-18 mb-2"></i> Voir Litiges
                                    </a>
                                </div>
                                <div class="col-6 col-sm-4 col-md-4 col-lg-2">
                                    <a href="parametresadmin.php" class="btn btn-outline-secondary w-100 py-3 fs-12">
                                        <i class="fe fe-settings d-block fs-18 mb-2"></i> Configuration
                                    </a>
                                </div>
                                <div class="col-6 col-sm-4 col-md-4 col-lg-2">
                                    <button class="btn btn-outline-success w-100 py-3 fs-12" id="btn-export-global">
                                        <i class="fe fe-download d-block fs-18 mb-2"></i> Rapport Global
                                    </button>
                                </div>
                                <div class="col-6 col-sm-4 col-md-4 col-lg-2">
                                    <button class="btn btn-outline-danger w-100 py-3 fs-12" id="btn-maintenance">
                                        <i class="fe fe-lock d-block fs-18 mb-2"></i> Maintenance
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DÉTAIL PROJET -->
<div class="modal fade" id="projectDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modal-detail-title">Détail Projet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Projet</h6>
                        <div class="fw-medium fs-16" id="md-description">—</div>
                        <div class="mt-3">
                            <h6 class="text-muted text-uppercase fs-12 fw-semibold">Porteur</h6>
                            <p class="mb-0" id="md-owner">—</p>
                            <small class="text-muted" id="md-email">—</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light border-0">
                            <div class="card-body text-center py-3">
                                <h6 class="text-muted text-uppercase fs-11 fw-semibold">Statut</h6>
                                <span class="badge fs-13 px-3 py-2 bg-primary" id="md-statut">—</span>
                                <hr class="my-2">
                                <h6 class="text-muted text-uppercase fs-11 fw-semibold">Réf.</h6>
                                <p class="mb-0 fw-medium" id="md-ref">—</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Secteur</h6>
                        <p class="fw-medium" id="md-secteur">—</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Montant</h6>
                        <p class="fw-bold text-success" id="md-montant">—</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold">Créé le</h6>
                        <p class="mb-0" id="md-created">—</p>
                        <h6 class="text-muted text-uppercase fs-12 fw-semibold mt-2">Mis à jour le</h6>
                        <p class="mb-0" id="md-updated">—</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-success" id="md-validate-btn" style="display:none;"><i class="fe fe-check me-1"></i>Valider</button>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="js/dashboard-admin.js"></script>

<?php require_once __DIR__ . '/footer.php'; ?>
<?php if (session('welcome')): ?>
<script>document.addEventListener('DOMContentLoaded', () => ALOGOTO.success('Bienvenue Administrateur'));</script>
<?php endif; ?>
