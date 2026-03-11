<?php
require_once __DIR__ . '/components/porteur_helpers.php';
$porteurData = require __DIR__ . '/components/porteur_data.php';
$profile = $porteurData['profile'];

$page_title = 'Profil';
$page_subtitle = 'Consultez vos informations personnelles et la fiche organisation.';
$page_active_nav = 'profile';
$page_document_title = 'Profil - ALOGOTO';

include __DIR__ . '/components/porteur_page_start.php';
?>
        <div class="row dashboard-gap-20 mt-2">
          <div class="col-12 col-xl-4">
            <section class="dashboard-card h-100">
              <div class="text-center mb-4">
                <span class="porteur-avatar-lg mb-3"><?php echo porteur_escape($profile['photo_initials']); ?></span>
                <h4 class="mb-1"><?php echo porteur_escape($profile['name']); ?></h4>
                <p class="porteur-page-subtle mb-0"><?php echo porteur_escape($profile['role']); ?></p>
              </div>

              <div class="porteur-meta-list">
                <div class="porteur-meta-list__item">
                  <p class="porteur-meta-list__label">Organisation</p>
                  <p class="porteur-meta-list__value"><?php echo porteur_escape($profile['organisation']); ?></p>
                </div>
                <div class="porteur-meta-list__item">
                  <p class="porteur-meta-list__label">Localisation</p>
                  <p class="porteur-meta-list__value"><?php echo porteur_escape($profile['location']); ?></p>
                </div>
                <div class="porteur-meta-list__item">
                  <p class="porteur-meta-list__label">Membre depuis</p>
                  <p class="porteur-meta-list__value"><?php echo porteur_escape($profile['joined']); ?></p>
                </div>
              </div>

              <div class="text-center mt-4">
                <button type="button" class="dashboard-btn-primary btn-dashboard primary">
                  <i class="bi bi-pencil-square"></i>
                  <span>Modifier profil</span>
                </button>
              </div>
            </section>
          </div>

          <div class="col-12 col-xl-8">
            <section class="dashboard-card porteur-form-card mb-4">
              <div class="dashboard-card__head mb-3">
                <h6>Informations utilisateur</h6>
              </div>
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label">Nom</label>
                  <input type="text" class="form-control" value="<?php echo porteur_escape($profile['name']); ?>" readonly />
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" value="<?php echo porteur_escape($profile['email']); ?>" readonly />
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label">Telephone</label>
                  <input type="text" class="form-control" value="<?php echo porteur_escape($profile['phone']); ?>" readonly />
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label">Organisation</label>
                  <input type="text" class="form-control" value="<?php echo porteur_escape($profile['organisation']); ?>" readonly />
                </div>
                <div class="col-12">
                  <label class="form-label">Photo profil</label>
                  <div class="porteur-soft-card d-flex align-items-center justify-content-between gap-3">
                    <span class="porteur-page-subtle">Avatar actuel base sur les initiales du compte.</span>
                    <button type="button" class="btn-dashboard outline sm">
                      <i class="bi bi-image"></i>
                      <span>Changer</span>
                    </button>
                  </div>
                </div>
              </div>
            </section>

            <section class="dashboard-card">
              <div class="dashboard-card__head mb-3">
                <h6>Presentation</h6>
              </div>
              <p class="mb-3"><?php echo porteur_escape($profile['bio']); ?></p>
              <div class="row g-3">
                <div class="col-12 col-md-4">
                  <div class="porteur-soft-card h-100">
                    <p class="porteur-page-subtle mb-1">Projet phare</p>
                    <h6 class="mb-0">Cooperative Cacao Abidjan</h6>
                  </div>
                </div>
                <div class="col-12 col-md-4">
                  <div class="porteur-soft-card h-100">
                    <p class="porteur-page-subtle mb-1">Financement mobilise</p>
                    <h6 class="mb-0">19.25M FCFA</h6>
                  </div>
                </div>
                <div class="col-12 col-md-4">
                  <div class="porteur-soft-card h-100">
                    <p class="porteur-page-subtle mb-1">Taux de reponse</p>
                    <h6 class="mb-0">96%</h6>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </div>
<?php include __DIR__ . '/components/porteur_page_end.php'; ?>
