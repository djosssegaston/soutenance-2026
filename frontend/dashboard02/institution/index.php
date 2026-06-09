<?php
require_once __DIR__ . '/header.php';
?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <!-- PAGE HEADER -->
            <div class="page-header">
                <h1 class="page-title text-uppercase fw-bold">Tableau de Bord</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Analyse Financière</li>
                    </ol>
                </div>
            </div>

            <!-- ALERTES CRITIQUES (Sober and dynamic) -->
            <div id="critical-alerts-container" class="row d-none mb-4">
                <div class="col-12">
                    <div class="alert alert-warning border-0 shadow-sm border-start border-warning border-4" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fe fe-alert-circle fs-24 me-3 text-warning"></i>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading mb-0 fw-bold text-dark">Surveillance Active</h6>
                                <p class="mb-0 fs-13 text-muted" id="critical-alerts-text">Chargement...</p>
                            </div>
                            <a href="remboursement.php" class="btn btn-warning btn-sm fw-bold">Consulter</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI CARDS (Sober design with icons) -->
            <div class="row">
                <!-- Financement -->
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Total Financé</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-total-funded">0 FCFA</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-primary-transparent text-primary">
                                        <i class="fe fe-briefcase"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span class="text-success fw-bold"><i class="fe fe-arrow-up"></i></span>
                                <span id="kpi-active-projects">0 Projets actifs</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Remboursement -->
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Taux Encaissement</p>
                                    <h3 class="mb-0 number-font fw-bold" id="kpi-repayment-rate">0%</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-success-transparent text-success">
                                        <i class="fe fe-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span id="kpi-total-repaid">0 FCFA encaissés</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Risques -->
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Projets à Risque</p>
                                    <h3 class="mb-0 number-font fw-bold text-danger" id="kpi-at-risk">0</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-danger-transparent text-danger">
                                        <i class="fe fe-alert-triangle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span id="kpi-late-repayments" class="text-danger">0 retards actifs</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ROI -->
                <div class="col-sm-6 col-md-6 col-lg-6 col-xl-3">
                    <div class="card cockpit-card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <p class="text-muted mb-1 fs-12 text-uppercase fw-semibold">Rendement (ROI)</p>
                                    <h3 class="mb-0 number-font fw-bold text-primary" id="kpi-roi">0%</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-icon bg-info-transparent text-info">
                                        <i class="fe fe-trending-up"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 fs-11 text-muted">
                                <span id="kpi-analyzed-projects">0 analyses effectuées</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ANALYTICS CHARTS -->
            <div class="row">
                <div class="col-lg-8 col-md-12">
                    <div class="card chart-card shadow-sm border-0">
                        <div class="card-header border-bottom">
                            <h3 class="card-title fw-bold fs-14">Évolution Portefeuille</h3>
                        </div>
                        <div class="card-body">
                            <div id="chart-funding-evolution" class="chart-sh"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="card chart-card shadow-sm border-0">
                        <div class="card-header border-bottom">
                            <h3 class="card-title fw-bold fs-14">Répartition Secteurs</h3>
                        </div>
                        <div class="card-body">
                            <div id="chart-sector-distribution" class="chart-sh"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ANALYTICS CHARTS ROW 2 -->
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="card-header border-bottom text-white bg-primary">
                            <h3 class="card-title fw-bold text-white">État des Remboursements</h3>
                        </div>
                        <div class="card-body">
                            <div id="chart-repayment-status" class="chart-sh"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="card border-top border-danger border-5">
                        <div class="card-header border-bottom">
                            <h3 class="card-title fw-bold text-danger">Exposition au Risque Crédit</h3>
                        </div>
                        <div class="card-body">
                            <div id="chart-risk-exposure" class="chart-sh"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RECENT PROJECTS & TIMELINE -->
            <div class="row">
                <!-- Tableau Projets Récents -->
                <div class="col-xl-8 col-lg-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header justify-content-between border-bottom-0">
                            <h3 class="card-title fw-bold fs-15">Projets en Revue Institutionnelle</h3>
                            <div class="card-options">
                                <a href="projets_disponibles.php" class="btn btn-primary btn-sm rounded-pill">Voir tout</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover text-nowrap mb-0 align-middle">
                                    <thead class="bg-light">
                                        <tr class="border-top-0">
                                            <th class="border-bottom-0 ps-4">Projet</th>
                                            <th class="border-bottom-0">Porteur</th>
                                            <th class="border-bottom-0">Montant</th>
                                            <th class="border-bottom-0">Risque</th>
                                            <th class="border-bottom-0">Statut</th>
                                            <th class="border-bottom-0 pe-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dashboard-recent-projects">
                                        <!-- Dynamique -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activité Récente Timeline -->
                <div class="col-xl-4 col-lg-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom-0">
                            <h3 class="card-title fw-bold fs-15">Flux d'Activité</h3>
                        </div>
                        <div class="card-body pb-0">
                            <ul class="task-list" id="dashboard-activity-timeline">
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
                            <h5 class="mb-4 fw-bold">Actions de Supervision</h5>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <a href="projets_disponibles.php" class="btn btn-white btn-quick-action shadow-sm w-100 py-3 text-start">
                                        <i class="fe fe-search me-2 text-primary fs-16"></i> Analyser un projet
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="financement.php" class="btn btn-white btn-quick-action shadow-sm w-100 py-3 text-start" style="border-left-color: #198754 !important;">
                                        <i class="fe fe-briefcase me-2 text-success fs-16"></i> Portefeuille Actif
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="remboursement.php" class="btn btn-white btn-quick-action shadow-sm w-100 py-3 text-start" style="border-left-color: #fca028 !important;">
                                        <i class="fe fe-trending-up me-2 text-warning fs-16"></i> Suivre Paiements
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="messages.php" class="btn btn-white btn-quick-action shadow-sm w-100 py-3 text-start" style="border-left-color: #0dcaf0 !important;">
                                        <i class="fe fe-message-square me-2 text-info fs-16"></i> Messagerie
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DASHBOARD SCRIPTS -->
<script src="js/dashboard-charts.js"></script>
<script src="js/dashboard-main.js"></script>

<?php
require_once __DIR__ . '/footer.php';
if (session('welcome')): ?>
<script>document.addEventListener('DOMContentLoaded', () => ALOGOTO.success('Bienvenue Institution Financière'));</script>
<?php endif; ?>
