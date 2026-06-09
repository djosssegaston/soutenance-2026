<?php require_once __DIR__ . '/header.php'; ?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title"><strong>CENTRE DE SÉCURITÉ</strong></h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">INSTITUTION</a></li>
                        <li class="breadcrumb-item active" aria-current="page">SÉCURITÉ</li>
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
                                    <span class="text-muted text-uppercase fs-12 fw-bold">SESSIONS ACTIVES</span>
                                    <h2 class="mb-0 mt-1" id="stat-sessions">0</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-primary">
                                        <i class="bi bi-laptop fs-20"></i>
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
                                    <span class="text-muted text-uppercase fs-12 fw-bold">DERNIER MDP</span>
                                    <h2 class="mb-0 mt-1 fs-6" id="stat-pwd-date">--</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-success">
                                        <i class="bi bi-key fs-20"></i>
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
                                    <span class="text-muted text-uppercase fs-12 fw-bold">NIVEAU SÉCURITÉ</span>
                                    <h2 class="mb-0 mt-1">ÉLEVÉ</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-info">
                                        <i class="bi bi-shield-lock fs-20"></i>
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
                                    <span class="text-muted text-uppercase fs-12 fw-bold">AUDIT LOGS</span>
                                    <h2 class="mb-0 mt-1" id="stat-logs">0</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-warning">
                                        <i class="bi bi-clock-history fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- TABS -->
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="d-flex flex-wrap border-bottom">
                                <a class="nav-link px-4 py-3 active border-bottom border-primary border-2 fw-bold" id="tab-password" href="javascript:void(0)" onclick="switchSection('password')">
                                    <i class="bi bi-key me-2"></i>Mot de passe
                                </a>
                                <a class="nav-link px-4 py-3 text-muted" id="tab-sessions" href="javascript:void(0)" onclick="switchSection('sessions')">
                                    <i class="bi bi-laptop me-2"></i>Sessions actives
                                </a>
                                <a class="nav-link px-4 py-3 text-muted" id="tab-logs" href="javascript:void(0)" onclick="switchSection('logs')">
                                    <i class="bi bi-clock-history me-2"></i>Journal d'activité
                                </a>
                            </div>

                            <div class="p-4">
                                <!-- PASSWORD -->
                                <div id="section-password">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <h5 class="fw-bold mb-4">Changement de mot de passe</h5>
                                            <form id="form-password">
                                                <div class="mb-3">
                                                    <label class="form-label">Mot de passe actuel</label>
                                                    <input type="password" class="form-control" name="current_password" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nouveau mot de passe</label>
                                                    <input type="password" class="form-control" name="password" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Confirmer nouveau mot de passe</label>
                                                    <input type="password" class="form-control" name="password_confirmation" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Mettre à jour le mot de passe</button>
                                            </form>
                                        </div>
                                        <div class="col-lg-6 border-start">
                                            <h5 class="fw-bold mb-4">Historique récent</h5>
                                            <ul class="list-group list-group-flush" id="pwd-history">
                                                <!-- Dynamique -->
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div id="section-sessions" class="d-none">
                                    <h5 class="fw-bold mb-4">Appareils & Sessions en cours</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-4">Appareil</th>
                                                    <th>IP</th>
                                                    <th>Dernière activité</th>
                                                    <th class="pe-4">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sessions-body">
                                                <!-- Dynamique -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- LOGS -->
                                <div id="section-logs" class="d-none">
                                    <h5 class="fw-bold mb-4">Journal d'audit sécurité</h5>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="ps-4">Action</th>
                                                    <th>IP</th>
                                                    <th class="pe-4">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody id="logs-body">
                                                <!-- Dynamique -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/securite.js"></script>

<?php require_once __DIR__ . '/footer.php'; ?>
