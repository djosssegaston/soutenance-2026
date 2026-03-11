<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$page_title = 'Paramètres';
$page_subtitle = 'Configurez les règles de plateforme, les seuils et les secteurs autorisés.';
$page_active_nav = 'settings';
$page_document_title = 'Paramètres - ALOGOTO';

include __DIR__ . '/components/admin_page_start.php';
?>
        <div class="row dashboard-gap-20 mt-2 mb-4">
          <div class="col-12 col-xl-6">
            <section class="dashboard-card admin-form-card h-100">
              <div class="dashboard-card__head mb-3">
                <h6>Frais plateforme</h6>
              </div>
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label">Commission standard</label>
                  <input type="number" class="form-control" value="3.5" />
                </div>
                <div class="col-12">
                  <label class="form-label">Commission institution premium</label>
                  <input type="number" class="form-control" value="2.1" />
                </div>
              </div>
            </section>
          </div>
          <div class="col-12 col-xl-6">
            <section class="dashboard-card admin-form-card h-100">
              <div class="dashboard-card__head mb-3">
                <h6>Seuil financement</h6>
              </div>
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label">Montant minimum</label>
                  <input type="number" class="form-control" value="500000" />
                </div>
                <div class="col-12">
                  <label class="form-label">Montant maximum</label>
                  <input type="number" class="form-control" value="50000000" />
                </div>
              </div>
            </section>
          </div>
        </div>

        <div class="row dashboard-gap-20 mt-4 mb-5">
          <div class="col-12">
            <section class="dashboard-card admin-form-card">
              <div class="dashboard-card__head mb-3">
                <h6>Gestion secteurs</h6>
              </div>
              <div class="row g-3">
                <div class="col-12 col-md-4">
                  <input type="text" class="form-control" value="Agriculture" />
                </div>
                <div class="col-12 col-md-4">
                  <input type="text" class="form-control" value="Technologie" />
                </div>
                <div class="col-12 col-md-4">
                  <input type="text" class="form-control" value="Santé" />
                </div>
                <div class="col-12">
                  <button type="button" class="dashboard-btn-primary btn-dashboard primary">
                    <i class="bi bi-save"></i>
                    <span>Enregistrer les paramètres</span>
                  </button>
                </div>
              </div>
            </section>
          </div>
        </div>
<?php include __DIR__ . '/components/admin_page_end.php'; ?>
