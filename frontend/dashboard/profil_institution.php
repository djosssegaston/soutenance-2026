<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$institutionData = require __DIR__ . '/components/institution_data.php';
$profile = $institutionData['profile'];

$page_title = 'Profil institution';
$page_subtitle = 'Gérez les informations de contact et la fiche institutionnelle.';
$page_active_nav = 'profile';
$page_document_title = 'Profil institution - ALOGOTO';

include __DIR__ . '/components/institution_page_start.php';
?>
        <div class="row dashboard-gap-20 mt-2 mb-5">
          <div class="col-12 col-xl-4">
            <section class="dashboard-card h-100">
              <div class="text-center mb-4">
                <span class="institution-avatar-lg mb-3"><?php echo dashboard_escape($profile['initials']); ?></span>
                <h4 class="mb-1"><?php echo dashboard_escape($profile['name']); ?></h4>
                <p class="institution-page-subtle mb-0"><?php echo dashboard_escape($profile['type']); ?></p>
              </div>
              <div class="institution-meta-list">
                <div class="institution-meta-list__item">
                  <p class="institution-meta-list__label">Responsable</p>
                  <p class="institution-meta-list__value"><?php echo dashboard_escape($profile['manager']); ?></p>
                </div>
                <div class="institution-meta-list__item">
                  <p class="institution-meta-list__label">Téléphone</p>
                  <p class="institution-meta-list__value"><?php echo dashboard_escape($profile['phone']); ?></p>
                </div>
              </div>
              <div class="text-center mt-4">
                <button type="button" class="dashboard-btn-primary btn-dashboard primary">
                  <i class="bi bi-pencil-square"></i>
                  <span>Modifier le profil</span>
                </button>
              </div>
            </section>
          </div>
          <div class="col-12 col-xl-8">
            <section class="dashboard-card institution-form-card">
              <div class="dashboard-card__head mb-3">
                <h6>Informations institutionnelles</h6>
              </div>
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label">Nom institution</label>
                  <input type="text" class="form-control" value="<?php echo dashboard_escape($profile['name']); ?>" readonly />
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" value="<?php echo dashboard_escape($profile['email']); ?>" readonly />
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label">Téléphone</label>
                  <input type="text" class="form-control" value="<?php echo dashboard_escape($profile['phone']); ?>" readonly />
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label">Responsable</label>
                  <input type="text" class="form-control" value="<?php echo dashboard_escape($profile['manager']); ?>" readonly />
                </div>
                <div class="col-12">
                  <label class="form-label">Adresse</label>
                  <input type="text" class="form-control" value="<?php echo dashboard_escape($profile['address']); ?>" readonly />
                </div>
              </div>
            </section>
          </div>
        </div>
<?php include __DIR__ . '/components/institution_page_end.php'; ?>
