<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$adminData = require __DIR__ . '/components/admin_data.php';
$profile = $adminData['profile'];

$page_title = 'Profil';
$page_subtitle = 'Consultez la fiche administrateur et les informations de supervision.';
$page_active_nav = 'profile';
$page_document_title = 'Profil admin - ALOGOTO';

include __DIR__ . '/components/admin_page_start.php';
?>
        <div class="dashboard-page-stack">
          <div class="row dashboard-gap-20">
            <div class="col-12 col-xl-4">
              <section class="dashboard-card h-100">
                <div class="text-center mb-4">
                  <span class="dashboard-profile-avatar-lg mb-3"><?php echo dashboard_escape($profile['initials']); ?></span>
                  <h4 class="mb-1"><?php echo dashboard_escape($profile['name']); ?></h4>
                  <p class="admin-page-subtle mb-0"><?php echo dashboard_escape($profile['role']); ?></p>
                </div>

                <div class="admin-meta-list">
                  <div class="admin-meta-list__item">
                    <p class="admin-meta-list__label">Equipe</p>
                    <p class="admin-meta-list__value"><?php echo dashboard_escape($profile['team']); ?></p>
                  </div>
                  <div class="admin-meta-list__item">
                    <p class="admin-meta-list__label">Localisation</p>
                    <p class="admin-meta-list__value"><?php echo dashboard_escape($profile['location']); ?></p>
                  </div>
                  <div class="admin-meta-list__item">
                    <p class="admin-meta-list__label">Membre depuis</p>
                    <p class="admin-meta-list__value"><?php echo dashboard_escape($profile['joined']); ?></p>
                  </div>
                </div>
              </section>
            </div>

            <div class="col-12 col-xl-8">
              <section class="dashboard-card admin-form-card mb-4">
                <div class="dashboard-card__head mb-3">
                  <h6>Informations administrateur</h6>
                </div>
                <div class="row g-3">
                  <div class="col-12 col-md-6">
                    <label class="form-label">Identifiant</label>
                    <input type="text" class="form-control" value="<?php echo dashboard_escape($profile['user_id']); ?>" readonly />
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label">Nom</label>
                    <input type="text" class="form-control" value="<?php echo dashboard_escape($profile['name']); ?>" readonly />
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="<?php echo dashboard_escape($profile['email']); ?>" readonly />
                  </div>
                  <div class="col-12 col-md-6">
                    <label class="form-label">Telephone</label>
                    <input type="text" class="form-control" value="<?php echo dashboard_escape($profile['phone']); ?>" readonly />
                  </div>
                  <div class="col-12">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" value="<?php echo dashboard_escape($profile['role']); ?>" readonly />
                  </div>
                </div>
              </section>

              <section class="dashboard-card">
                <div class="dashboard-card__head mb-3">
                  <h6>Mission</h6>
                </div>
                <p class="mb-3"><?php echo dashboard_escape($profile['bio']); ?></p>
                <div class="row g-3">
                  <div class="col-12 col-md-4">
                    <div class="admin-soft-card h-100">
                      <p class="admin-page-subtle mb-1">Notifications critiques</p>
                      <h6 class="mb-0">3 actives</h6>
                    </div>
                  </div>
                  <div class="col-12 col-md-4">
                    <div class="admin-soft-card h-100">
                      <p class="admin-page-subtle mb-1">Litiges ouverts</p>
                      <h6 class="mb-0">2 dossiers</h6>
                    </div>
                  </div>
                  <div class="col-12 col-md-4">
                    <div class="admin-soft-card h-100">
                      <p class="admin-page-subtle mb-1">Session actuelle</p>
                      <h6 class="mb-0">Plateau, Abidjan</h6>
                    </div>
                  </div>
                </div>
              </section>
            </div>
          </div>
        </div>
<?php include __DIR__ . '/components/admin_page_end.php'; ?>
