<?php require_once __DIR__ . '/header.php'; ?>
<?php $__av = auth()->user()?->avatar_url ?? ''; $__avSrc = !empty($__av) ? (str_starts_with($__av,'http')||str_starts_with($__av,'/')?$__av:'/storage/'.$__av) : '../../asset/images/profiles/1.jpg'; ?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title"><strong>PROFIL INSTITUTIONNEL</strong></h1>
                    <p class="text-muted mb-0">Gérez les informations de votre institution et suivez vos performances.</p>
                </div>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">INSTITUTION</a></li>
                        <li class="breadcrumb-item active" aria-current="page">PROFIL</li>
                    </ol>
                </div>
            </div>

            <!-- STATS -->
            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fs-12 fw-semibold">Projets Financés</p>
                                    <h3 class="mb-1 number-font" id="stat-projets">0</h3>
                                    <span class="text-muted fs-12">Engagement total</span>
                                </div>
                                <div class="avatar avatar-lg bg-success-transparent rounded-circle text-success">
                                    <i class="bi bi-briefcase fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fs-12 fw-semibold">Portefeuille Actif</p>
                                    <h3 class="mb-1 number-font" id="stat-portefeuille">0 FCFA</h3>
                                    <span class="text-muted fs-12">Encours actuel</span>
                                </div>
                                <div class="avatar avatar-lg bg-primary-transparent rounded-circle text-primary">
                                    <i class="bi bi-cash-stack fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fs-12 fw-semibold">ROI Moyen</p>
                                    <h3 class="mb-1 number-font" id="stat-roi">0%</h3>
                                    <span class="text-muted fs-12">Performance annuelle</span>
                                </div>
                                <div class="avatar avatar-lg bg-info-transparent rounded-circle text-info">
                                    <i class="bi bi-graph-up-arrow fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <p class="text-muted mb-1 text-uppercase fs-12 fw-semibold">Score Confiance</p>
                                    <h3 class="mb-1 number-font">A+</h3>
                                    <span class="text-muted fs-12">Rating plateforme</span>
                                </div>
                                <div class="avatar avatar-lg bg-warning-transparent rounded-circle text-warning">
                                    <i class="bi bi-shield-check fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- INFOS -->
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom-0 d-flex justify-content-between align-items-center">
                            <h4 class="card-title fw-bold fs-15">Informations Générales</h4>
                            <button class="btn btn-sm btn-primary rounded-pill px-3" id="btn-edit-profile"><i class="fe fe-edit me-1"></i> Modifier</button>
                        </div>
                        <div class="card-body">
                            <form id="form-profile">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nom de l'Institution</label>
                                        <input type="text" class="form-control" name="nom_institution" id="input-inst-name" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Représentant (Utilisateur)</label>
                                        <input type="text" class="form-control" name="name" id="input-user-name" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email de Contact</label>
                                        <input type="email" class="form-control" name="email" id="input-email" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Téléphone</label>
                                        <input type="text" class="form-control" name="telephone" id="input-phone" disabled>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Adresse Siège</label>
                                        <input type="text" class="form-control" name="adresse" id="input-address" disabled>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Site Web</label>
                                        <input type="url" class="form-control" name="site_web" id="input-website" disabled>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Description / Mission</label>
                                        <textarea class="form-control" name="description" id="input-description" rows="3" disabled></textarea>
                                    </div>
                                </div>
                                <div class="mt-4 d-none" id="edit-actions">
                                    <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
                                    <button type="button" class="btn btn-secondary" id="btn-cancel-edit">Annuler</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- AVATAR & LOGO & HISTORY -->
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom-0"><h4 class="card-title fw-bold fs-15">Photo Utilisateur</h4></div>
                        <div class="card-body text-center">
                            <div class="avatar avatar-xxl brround mb-3 overflow-hidden border" style="width:120px;height:120px;">
                                <img src="<?php echo htmlspecialchars($__avSrc, ENT_QUOTES, 'UTF-8'); ?>" id="user-avatar-img" class="w-100 h-100 object-fit-cover">
                            </div>
                            <input type="file" id="user-avatar-upload" class="d-none" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                            <br>
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-2" onclick="document.getElementById('user-avatar-upload').click()">Changer la photo</button>
                            <div id="user-avatar-feedback" class="small d-none mt-2"></div>
                        </div>
                    </div>
                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom-0"><h4 class="card-title fw-bold fs-15">Logo Officiel</h4></div>
                        <div class="card-body text-center">
                            <div class="avatar avatar-xxl brround mb-3 border" style="width:120px;height:120px;overflow:hidden;">
                                <img src="" id="logo-img" class="w-100 h-100 object-fit-cover">
                            </div>
                            <input type="file" id="logo-upload" class="d-none" accept="image/*">
                            <br>
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 mt-2" onclick="document.getElementById('logo-upload').click()">Changer le logo</button>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-header border-bottom-0"><h4 class="card-title fw-bold fs-15">Audit Activité</h4></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <tbody id="history-body">
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

<script src="js/profils.js?v=<?php echo filemtime(__DIR__.'/js/profils.js'); ?>"></script>

<?php require_once __DIR__ . '/footer.php'; ?>
