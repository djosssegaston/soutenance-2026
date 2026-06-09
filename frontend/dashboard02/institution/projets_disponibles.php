<?php 
require_once __DIR__ . '/header.php'; 
?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Projets Disponibles</h1>
                    <p class="text-muted mb-0">Découvrez et analysez les opportunités de financement validées.</p>
                </div>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Projets Disponibles</li>
                    </ol>
                </div>
            </div>
            <!-- PAGE-HEADER END -->

            <!-- STATS CARDS -->
            <div class="row" id="stats-container">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="mt-2">
                                    <h6 class="">Total Projets</h6>
                                    <h2 class="mb-0 number-font" id="stat-total-projects">...</h2>
                                </div>
                                <div class="ms-auto">
                                    <div class="chart-wrapper">
                                        <canvas id="total-projects-chart" class="h-8 w-9 chart-dropshadow"></canvas>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mt-4"> <span class="text-success"><i class="fa fa-arrow-up"></i></span> Projets validés admin</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="mt-2">
                                    <h6 class="">Volume Recherché</h6>
                                    <h2 class="mb-0 number-font" id="stat-total-amount">...</h2>
                                </div>
                                <div class="ms-auto">
                                    <div class="chart-wrapper">
                                        <canvas id="total-amount-chart" class="h-8 w-9 chart-dropshadow"></canvas>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mt-4"> <span class="text-info"><i class="fa fa-info-circle"></i></span> FCFA Total</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="mt-2">
                                    <h6 class="">Nouveaux Projets</h6>
                                    <h2 class="mb-0 number-font" id="stat-urgent-projects">...</h2>
                                </div>
                                <div class="ms-auto">
                                    <div class="chart-wrapper">
                                        <canvas id="urgent-projects-chart" class="h-8 w-9 chart-dropshadow"></canvas>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mt-4"> <span class="text-warning"><i class="fa fa-clock-o"></i></span> 7 derniers jours</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="mt-2">
                                    <h6 class="">Secteurs Actifs</h6>
                                    <h2 class="mb-0 number-font" id="stat-active-sectors">...</h2>
                                </div>
                                <div class="ms-auto">
                                    <div class="chart-wrapper">
                                        <canvas id="active-sectors-chart" class="h-8 w-9 chart-dropshadow"></canvas>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted mt-4"> <span class="text-success"><i class="fa fa-check"></i></span> Diversifiés</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FILTERS & SEARCH -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-end g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Recherche</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="search-input" placeholder="Titre, porteur, secteur...">
                                        <button class="btn btn-primary" id="search-btn"><i class="fe fe-search"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Secteur</label>
                                    <select class="form-control select2" id="filter-secteur">
                                        <option value="">Tous les secteurs</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Ville</label>
                                    <select class="form-control select2" id="filter-ville">
                                        <option value="">Toutes les villes</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Montant Max</label>
                                    <input type="number" class="form-control" id="filter-montant-max" placeholder="Ex: 1000000">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-secondary w-100" id="reset-filters">Réinitialiser</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PROJECT LIST -->
            <div class="row" id="projects-list">
                <!-- Loader -->
                <div class="col-12 text-center py-5" id="projects-loader">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-2 text-muted">Récupération des opportunités...</p>
                </div>
            </div>

            <!-- PAGINATION -->
            <div class="row">
                <div class="col-12">
                    <nav aria-label="Page navigation" id="pagination-container" class="d-flex justify-content-center mt-4">
                        <!-- Dynamique -->
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETAILS PROJET -->
<div class="modal fade" id="modal-project-details" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Détails du Projet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Dynamique -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-warning" id="btn-analyze">Lancer l'Analyse</button>
                <button type="button" class="btn btn-primary" id="btn-discuss">Ouvrir Discussion</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

<!-- SCRIPTS DYNAMIQUES -->
<script src="js/projets_disponibles.js"></script>
