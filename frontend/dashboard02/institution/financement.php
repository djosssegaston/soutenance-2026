<?php require_once __DIR__ . '/header.php'; ?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <!-- PAGE-HEADER -->
        <div class="page-header">
            <h1 class="page-title">Gestion des Financements & Portefeuille</h1>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Institution</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Financements</li>
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
                                <h6 class="">Total Engagé</h6>
                                <h2 class="mb-0 number-font" id="stat-total-funded">0 FCFA</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.10" data-thickness="2" data-color="#09ad95">
                                    <div class="chart-circle-value text-success"><i class="fe fe-briefcase"></i></div>
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
                                <h6 class="">Projets Actifs</h6>
                                <h2 class="mb-0 number-font" id="stat-active-count">0</h2>
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
                                <h6 class="">En attente</h6>
                                <h2 class="mb-0 number-font" id="stat-pending-count">0</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.10" data-thickness="2" data-color="#f7b731">
                                    <div class="chart-circle-value text-warning"><i class="fe fe-clock"></i></div>
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
                                <h6 class="">ROI Moyen</h6>
                                <h2 class="mb-0 number-font" id="stat-avg-roi">0%</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.10" data-thickness="2" data-color="#e82646">
                                    <div class="chart-circle-value text-danger"><i class="fe fe-trending-up"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- STATS CARDS END -->

        <!-- ACTIONS & FILTERS -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Statut du Financement</label>
                                <select class="form-control select2" id="filter-status">
                                    <option value="">Tous les statuts</option>
                                    <option value="awaiting_borrower_plan">Attente plan porteur</option>
                                    <option value="awaiting_imf_validation">Attente validation IMF</option>
                                    <option value="approved">Approuvé</option>
                                    <option value="disbursed">Décaissé / Actif</option>
                                    <option value="rejected">Rejeté</option>
                                    <option value="completed">Terminé</option>
                                </select>
                            </div>
                            <div class="col-md-8 text-end">
                                <button class="btn btn-primary me-2" id="search-btn"><i class="fe fe-search"></i> Filtrer</button>
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-propose-funding"><i class="fe fe-plus"></i> Nouvelle Proposition</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FUNDINGS LIST -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Suivi des Engagements Financiers</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover text-nowrap mb-0 align-middle" id="fundings-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Projet & Porteur</th>
                                        <th>Montant Proposé</th>
                                        <th>Taux / Durée</th>
                                        <th>Progression Projet</th>
                                        <th>Statut</th>
                                        <th class="pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="fundings-list">
                                    <!-- Dynamique -->
                                </tbody>
                            </table>
                        </div>
                        <div id="pagination-container" class="mt-4"></div>
                        <div id="fundings-loader" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2">Chargement du portefeuille...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL PROPOSE FUNDING -->
<div class="modal fade" id="modal-propose-funding" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Proposition de Financement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-propose-funding">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Sélectionner un projet analysé</label>
                            <select class="form-control select2-modal" name="project_id" id="select-project" required style="width: 100%">
                                <!-- Chargé dynamiquement -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Montant à investir (FCFA)</label>
                            <input type="number" class="form-control" name="montant_propose" placeholder="Ex: 5000000" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Taux d'intérêt annuel (%)</label>
                            <input type="number" step="0.01" class="form-control" name="taux_interet" placeholder="Ex: 8.5" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Durée du prêt (Mois)</label>
                            <input type="number" class="form-control" name="duree" placeholder="Ex: 24" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Conditions / Commentaires</label>
                            <textarea class="form-control" name="commentaires" rows="3" placeholder="Garanties exigées, différé de paiement, etc."></textarea>
                        </div>
                    </div>
                    <div class="alert alert-info py-2">
                        <i class="fe fe-info me-2"></i> La proposition sera envoyée au porteur pour validation. Une fois acceptée, vous pourrez procéder au décaissement.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Envoyer la proposition</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DETAILS FUNDING -->
<div class="modal fade" id="modal-funding-details" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Suivi du Dossier de Financement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-details-body">
                <!-- Dynamique -->
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-info" id="btn-docs"><i class="fe fe-file-text"></i> Documents</button>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success d-none" id="btn-approve-plan"><i class="fe fe-check"></i> Approuver le plan</button>
                    <button type="button" class="btn btn-danger d-none" id="btn-reject-plan"><i class="fe fe-x"></i> Rejeter</button>
                    <button type="button" class="btn btn-warning d-none" id="btn-request-revision"><i class="fe fe-edit"></i> Demander révision</button>
                    <button type="button" class="btn btn-warning d-none" id="btn-disburse"><i class="fe fe-send"></i> Valider Décaissement</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/financement.js"></script>

<?php require_once __DIR__ . '/footer.php'; ?>
