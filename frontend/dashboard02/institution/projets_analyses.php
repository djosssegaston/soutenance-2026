<?php require_once __DIR__ . '/header.php'; ?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <!-- PAGE-HEADER -->
        <div class="page-header">
            <h1 class="page-title">Centre d'Analyse Décisionnelle</h1>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Institution</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Projets Analysés</li>
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
                                <h6 class="">Analyses en cours</h6>
                                <h2 class="mb-0 number-font" id="stat-pending">0</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.10" data-thickness="2" data-color="#05c3fb">
                                    <div class="chart-circle-value text-primary"><i class="fe fe-activity"></i></div>
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
                                <h6 class="">Projets Approuvés</h6>
                                <h2 class="mb-0 number-font" id="stat-approved">0</h2>
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
                                <h6 class="">Risque Élevé (>70)</h6>
                                <h2 class="mb-0 number-font text-danger" id="stat-high-risk">0</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.10" data-thickness="2" data-color="#e82646">
                                    <div class="chart-circle-value text-danger"><i class="fe fe-alert-triangle"></i></div>
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
                                <h6 class="">Engagement Potentiel</h6>
                                <h2 class="mb-0 number-font" id="stat-potential-amount">0 FCFA</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.10" data-thickness="2" data-color="#f7b731">
                                    <div class="chart-circle-value text-warning"><i class="fe fe-dollar-sign"></i></div>
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
                                <label class="form-label">Rechercher</label>
                                <input type="text" class="form-control" id="search-input" placeholder="Titre du projet...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Statut Analyse</label>
                                <select class="form-control select2" id="filter-status">
                                    <option value="">Tous les statuts</option>
                                    <option value="en_analyse">En cours d'analyse</option>
                                    <option value="approuve">Approuvé</option>
                                    <option value="rejete">Rejeté</option>
                                    <option value="info_demandee">Infos demandées</option>
                                    <option value="entretien_planifie">Entretien planifié</option>
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

        <!-- ANALYSES LIST -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Portefeuille d'Analyses Décisionnelles</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover text-nowrap mb-0 align-middle" id="analyses-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="wd-15p ps-4">Projet & Porteur</th>
                                        <th class="wd-15p">Score Risque</th>
                                        <th class="wd-15p">Solvabilité</th>
                                        <th class="wd-15p">Note Globale</th>
                                        <th class="wd-10p">Statut</th>
                                        <th class="wd-15p pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="analyses-list">
                                    <!-- Dynamique -->
                                </tbody>
                            </table>
                        </div>
                        <div id="pagination-container" class="mt-4"></div>
                        <div id="analyses-loader" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2">Chargement des analyses...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETAILS ANALYSE -->
<div class="modal fade" id="modal-analysis-details" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Dossier d'Analyse</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Dynamique -->
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div id="analysis-actions-left">
                    <button type="button" class="btn btn-info" id="btn-request-info"><i class="fe fe-help-circle"></i> Demander Infos</button>
                    <button type="button" class="btn btn-warning" id="btn-schedule-interview"><i class="fe fe-calendar"></i> Programmer RDV</button>
                    <button type="button" class="btn btn-success" id="btn-propose-funding" style="display: none;"><i class="fe fe-briefcase"></i> Proposer un financement</button>
                </div>
                <div>
                    <button type="button" class="btn btn-danger" id="btn-reject"><i class="fe fe-x-circle"></i> Rejeter</button>
                    <button type="button" class="btn btn-success" id="btn-approve"><i class="fe fe-check-circle"></i> Approuver</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/projets_analyses.js?v=<?php echo filemtime(__DIR__.'/js/projets_analyses.js'); ?>"></script>

<?php require_once __DIR__ . '/footer.php'; ?>
