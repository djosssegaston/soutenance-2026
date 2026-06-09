<?php require_once __DIR__ . '/header.php'; ?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <!-- PAGE-HEADER -->
        <div class="page-header">
            <h1 class="page-title">Entretiens & Rendez-vous Planifiés</h1>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Institution</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Entretiens</li>
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
                                <h6 class="">Aujourd'hui</h6>
                                <h2 class="mb-0 number-font" id="stat-today">0</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.10" data-thickness="2" data-color="#05c3fb">
                                    <div class="chart-circle-value text-primary"><i class="fe fe-calendar"></i></div>
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
                                <h6 class="">Confirmés</h6>
                                <h2 class="mb-0 number-font" id="stat-confirmed">0</h2>
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
                                <h6 class="">En attente</h6>
                                <h2 class="mb-0 number-font" id="stat-pending">0</h2>
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
                                <h6 class="">Annulés</h6>
                                <h2 class="mb-0 number-font text-danger" id="stat-cancelled">0</h2>
                            </div>
                            <div class="ms-auto">
                                <div class="chart-circle chart-circle-xs" data-value="0.10" data-thickness="2" data-color="#e82646">
                                    <div class="chart-circle-value text-danger"><i class="fe fe-x-circle"></i></div>
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
                            <div class="col-md-3">
                                <label class="form-label">Filtrer par statut</label>
                                <select class="form-control select2" id="filter-status">
                                    <option value="">Tous les statuts</option>
                                    <option value="programme">Programmé</option>
                                    <option value="confirme">Confirmé</option>
                                    <option value="termine">Terminé</option>
                                    <option value="annule">Annulé</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date précise</label>
                                <input type="date" class="form-control" id="filter-date">
                            </div>
                            <div class="col-md-6 text-end">
                                <button class="btn btn-primary me-2" id="search-btn"><i class="fe fe-search"></i> Filtrer</button>
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-create-interview"><i class="fe fe-plus"></i> Nouvel Entretien</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- INTERVIEWS LIST -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="card-title">Agenda des Rencontres</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover text-nowrap mb-0 align-middle" id="interviews-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Date & Heure</th>
                                        <th>Projet / Porteur</th>
                                        <th>Type / Lieu</th>
                                        <th>Analyste</th>
                                        <th>Statut</th>
                                        <th class="pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="interviews-list">
                                    <!-- Dynamique -->
                                </tbody>
                            </table>
                        </div>
                        <div id="pagination-container" class="mt-4"></div>
                        <div id="interviews-loader" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2">Chargement des entretiens...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CREATE INTERVIEW -->
<div class="modal fade" id="modal-create-interview" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Programmer un nouvel entretien</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-create-interview">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Projet à analyser</label>
                            <select class="form-control select2-modal" name="project_id" id="select-project" required style="width: 100%">
                                <!-- Chargé dynamiquement -->
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Titre de l'entretien</label>
                            <input type="text" class="form-control" name="titre" placeholder="Ex: Entretien technique - Validation garanties" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="date_entretien" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Heure</label>
                            <input type="time" class="form-control" name="heure_entretien" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-control" name="type_entretien" required>
                                <option value="physique">Physique (Siège)</option>
                                <option value="visio">Visioconférence</option>
                                <option value="telephonique">Téléphonique</option>
                                <option value="terrain">Visite de terrain</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lieu / Lien Visio</label>
                            <input type="text" class="form-control" name="lieu" placeholder="Adresse ou lien réunion">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description / Consignes pour le porteur</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer l'entretien</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DETAILS & COMPTE RENDU -->
<div class="modal fade" id="modal-interview-details" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de l'Entretien</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-details-body">
                <!-- Dynamique -->
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div id="interview-actions-left">
                    <button type="button" class="btn btn-info" id="btn-convocation"><i class="fe fe-download"></i> Convocation PDF</button>
                    <button type="button" class="btn btn-success" id="btn-confirm"><i class="fe fe-check"></i> Confirmer Présence</button>
                </div>
                <div>
                    <button type="button" class="btn btn-danger" id="btn-cancel"><i class="fe fe-x"></i> Annuler</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/entretiens_planifies.js"></script>

<?php require_once __DIR__ . '/footer.php'; ?>
