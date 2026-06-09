<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$institutionData = require __DIR__ . '/components/institution_data.php';

$page_title = 'Securite';
$page_subtitle = 'Renforcez la protection et le suivi des acces.';
$page_active_nav = 'security';
$page_document_title = 'Securite institution - ALOGOTO';

include __DIR__ . '/components/institution_page_start.php';
?>
        <div class="row dashboard-gap-20 mt-2 mb-4">
          <div class="col-12 col-lg-7">
            <section class="dashboard-card institution-soft-card">
              <p class="institution-page-subtle mb-1">Protection du compte</p>
              <h6 class="mb-2">Authentification multi-facteurs</h6>
              <p class="mb-0">Activez la 2FA pour securiser les validations de financement et de remboursement.</p>
            </section>
          </div>
          <div class="col-12 col-lg-5">
            <section class="dashboard-card institution-form-card">
              <div class="dashboard-card__head mb-3">
                <h6>Alertes de securite</h6>
              </div>
              <form data-ajax-form="true" data-ajax-url="<?php echo dashboard_escape(dashboard_url('ajax/mock_action.php')); ?>" data-ajax-feedback="#securityFeedback">
                <div class="mb-3">
                  <label class="form-label">Email d'alerte</label>
                  <input type="email" class="form-control" value="securite@institution.com" />
                </div>
                <div class="mb-3">
                  <label class="form-label">Notifications</label>
                  <select class="form-select">
                    <option>Temps reel</option>
                    <option>Resume quotidien</option>
                  </select>
                </div>
                <button type="submit" class="dashboard-btn-primary btn-dashboard primary">
                  <span>Mettre a jour</span>
                </button>
              </form>
              <p id="securityFeedback" class="institution-page-subtle mt-3" hidden></p>
            </section>
          </div>
        </div>
<?php include __DIR__ . '/components/institution_page_end.php'; ?>
