<?php
require_once __DIR__ . '/components/dashboard_helpers.php';
$page_title = 'Rapports';
$page_subtitle = 'Analysez les tendances financières et exportez vos synthèses de portefeuille.';
$page_active_nav = 'reports';
$page_document_title = 'Rapports - ALOGOTO';

include __DIR__ . '/components/institution_page_start.php';
?>
        <div class="row dashboard-gap-20 mt-2 mb-4">
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
              <div>
                <h6 class="mb-1">Centre de reporting</h6>
                <p class="institution-page-subtle mb-0">Investissements mensuels, ROI et performance des projets financés.</p>
              </div>
              <button type="button" class="dashboard-btn-primary btn-dashboard primary">
                <i class="bi bi-download"></i>
                <span>Exporter le rapport</span>
              </button>
            </div>
          </div>
        </div>

        <div class="row dashboard-gap-20 mt-4 mb-4">
          <div class="col-12 col-xl-6">
            <section class="dashboard-card chart-card">
              <div class="dashboard-card__head">
                <h6>Investissements mensuels</h6>
              </div>
              <div class="chart-holder chart-holder-sm">
                <canvas id="institutionInvestmentsChart"></canvas>
              </div>
            </section>
          </div>
          <div class="col-12 col-xl-6">
            <section class="dashboard-card chart-card">
              <div class="dashboard-card__head">
                <h6>ROI du portefeuille</h6>
              </div>
              <div class="chart-holder chart-holder-sm">
                <canvas id="institutionRoiChart"></canvas>
              </div>
            </section>
          </div>
        </div>

        <div class="row dashboard-gap-20 mt-4 mb-5">
          <div class="col-12">
            <section class="dashboard-card chart-card">
              <div class="dashboard-card__head">
                <h6>Performance des projets</h6>
              </div>
              <div class="chart-holder">
                <canvas id="institutionProjectPerformanceChart"></canvas>
              </div>
            </section>
          </div>
        </div>
<?php include __DIR__ . '/components/institution_page_end.php'; ?>
