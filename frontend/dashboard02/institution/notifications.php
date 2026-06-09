<?php require_once __DIR__ . '/header.php'; ?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title"><strong>CENTRE DE NOTIFICATIONS</strong></h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">INSTITUTION</a></li>
                        <li class="breadcrumb-item active" aria-current="page">NOTIFICATIONS</li>
                    </ol>
                </div>
            </div>

            <!-- STATS -->
            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">NON LUES</span>
                                    <h2 class="mb-0 mt-1" id="stat-unread">0</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-warning">
                                        <i class="bi bi-bell fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">LUES</span>
                                    <h2 class="mb-0 mt-1" id="stat-read">0</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-success">
                                        <i class="bi bi-check-circle fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">ARCHIVÉES</span>
                                    <h2 class="mb-0 mt-1" id="stat-archived">0</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-info">
                                        <i class="bi bi-archive fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">TOTAL</span>
                                    <h2 class="mb-0 mt-1" id="stat-total">0</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-primary">
                                        <i class="bi bi-bell-fill fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLE -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom-0 d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h4 class="card-title fs-15 mb-0 fw-bold">Alertes Institutionnelles</h4>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <ul class="nav nav-tabs border-0" id="notificationTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active fw-bold" href="javascript:void(0)" onclick="switchTab('all')">Non archivées</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link fw-bold" href="javascript:void(0)" onclick="switchTab('unread')">Non lues</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link fw-bold" href="javascript:void(0)" onclick="switchTab('archived')">Archivées</a>
                                    </li>
                                </ul>
                                <button class="btn btn-sm btn-outline-primary" id="markAllReadBtn">
                                    <i class="fe fe-check-circle"></i> Tout marquer lu
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover text-nowrap mb-0 align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">TYPE</th>
                                            <th>TITRE</th>
                                            <th>CONTENU</th>
                                            <th>STATUT</th>
                                            <th>DATE</th>
                                            <th class="pe-4">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="notificationsBody">
                                        <!-- Dynamique -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3" id="pagination-container"></div>
                            <div id="loader" class="text-center py-5" style="display: none;">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODALS -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détail de la notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script src="js/notifications-crud.js"></script>

<?php require_once __DIR__ . '/footer.php'; ?>
