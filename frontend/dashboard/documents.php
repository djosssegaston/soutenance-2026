<?php
require_once __DIR__ . '/components/porteur_helpers.php';
$porteurData = require __DIR__ . '/components/porteur_data.php';
$documents = $porteurData['documents'];

$validDocuments = 0;
$pendingDocuments = 0;
$revisionDocuments = 0;

foreach ($documents as $document) {
  if ($document['status_label'] === 'Valide') {
    $validDocuments++;
  } elseif ($document['status_label'] === 'En attente') {
    $pendingDocuments++;
  } else {
    $revisionDocuments++;
  }
}

$page_title = 'Documents';
$page_subtitle = 'Centralisez vos pieces et suivez leur validation.';
$page_active_nav = 'documents';
$page_document_title = 'Documents - ALOGOTO';
$page_inline_styles = <<<'CSS'
    .porteur-page-stack {
      display: grid;
      gap: 1.75rem;
      padding-top: 0.5rem;
      padding-bottom: 0.5rem;
    }

    .porteur-page-stack > .row {
      margin-top: 0 !important;
      margin-bottom: 0 !important;
    }

    .porteur-documents-main {
      --bs-gutter-y: 1.5rem;
    }

    @media (max-width: 767.98px) {
      .porteur-page-stack {
        gap: 1.25rem;
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
      }
    }
CSS;

include __DIR__ . '/components/porteur_page_start.php';
?>
        <div class="porteur-page-stack">
        <div class="row dashboard-gap-16 porteur-page-kpi-row">
          <div class="col-12 col-md-6 col-xl-4">
            <?php
            $stat_icon = 'bi-file-earmark-check';
            $stat_title = 'Documents valides';
            $stat_value = (string) $validDocuments;
            $stat_trend = 'Conformes';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-check2-circle';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.15)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-4">
            <?php
            $stat_icon = 'bi-hourglass-split';
            $stat_title = 'En attente';
            $stat_value = (string) $pendingDocuments;
            $stat_trend = 'A suivre';
            $stat_trend_class = 'is-neutral';
            $stat_trend_icon = 'bi-clock-history';
            $stat_icon_bg = 'rgba(252, 160, 40, 0.18)';
            $stat_icon_color = 'var(--primary-color-3)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
          <div class="col-12 col-md-6 col-xl-4">
            <?php
            $stat_icon = 'bi-arrow-repeat';
            $stat_title = 'A mettre a jour';
            $stat_value = (string) $revisionDocuments;
            $stat_trend = 'Priorite';
            $stat_trend_class = 'is-down';
            $stat_trend_icon = 'bi-exclamation-triangle';
            $stat_icon_bg = 'rgba(243, 81, 32, 0.12)';
            $stat_icon_color = 'var(--primary-color-4)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20 porteur-documents-main">
          <div class="col-12">
            <section class="dashboard-card content-card dashboard-table-card">
              <div class="dashboard-table-card__header card-title">
                <h6>Gestion documentaire</h6>

                <div class="dashboard-table-card__actions">
                  <form id="documentsSearch" class="dashboard-inline-search" data-ajax="true" action="#" method="get">
                    <i class="bi bi-search"></i>
                    <input type="search" name="table-search" placeholder="Rechercher un document" aria-label="Rechercher un document" />
                  </form>
                  <button type="button" class="dashboard-btn-primary btn-dashboard primary">
                    <i class="bi bi-cloud-upload"></i>
                    <span>Uploader document</span>
                  </button>
                </div>
              </div>

              <div class="dashboard-table-wrapper table-responsive">
                <table class="table dashboard-table data-table align-middle">
                  <thead>
                    <tr>
                      <th>Nom du document</th>
                      <th>Type</th>
                      <th>Projet associe</th>
                      <th>Statut validation</th>
                      <th>Date upload</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
<?php foreach ($documents as $document): ?>
                    <tr>
                      <td><?php echo porteur_escape($document['name']); ?></td>
                      <td><?php echo porteur_escape($document['type']); ?></td>
                      <td><?php echo porteur_escape($document['project']); ?></td>
                      <td><span class="status-badge <?php echo porteur_escape($document['status_class']); ?>"><?php echo porteur_escape($document['status_label']); ?></span></td>
                      <td><?php echo porteur_escape($document['uploaded_at']); ?></td>
                      <td>
                        <div class="porteur-table-actions">
                          <button type="button" class="btn-dashboard outline sm">
                            <i class="bi bi-eye"></i>
                            <span>Voir</span>
                          </button>
                          <button type="button" class="btn-dashboard outline sm text-danger">
                            <i class="bi bi-trash"></i>
                            <span>Supprimer</span>
                          </button>
                        </div>
                      </td>
                    </tr>
<?php endforeach; ?>
                  </tbody>
                </table>
              </div>

              <div class="dashboard-pagination pagination-dashboard">
                <button class="dashboard-page-btn page-btn active">1</button>
                <button class="dashboard-page-btn page-btn">2</button>
                <button class="dashboard-page-btn page-btn">3</button>
              </div>
            </section>
          </div>

          <div class="col-12">
            <section class="dashboard-card">
              <div class="dashboard-card__head mb-3">
                <h6>Bonnes pratiques de dossier</h6>
              </div>
              <div class="row g-3">
                <div class="col-12 col-md-4">
                  <div class="porteur-soft-card h-100">
                    <p class="porteur-page-subtle mb-1">Formats recommandes</p>
                    <h6 class="mb-2">PDF, XLSX, JPG</h6>
                    <p class="mb-0">Privilegier des noms de fichiers clairs et une version datee.</p>
                  </div>
                </div>
                <div class="col-12 col-md-4">
                  <div class="porteur-soft-card h-100">
                    <p class="porteur-page-subtle mb-1">Verification admin</p>
                    <h6 class="mb-2">24 a 48 heures</h6>
                    <p class="mb-0">Les documents legaux et financiers sont priorises dans la file de controle.</p>
                  </div>
                </div>
                <div class="col-12 col-md-4">
                  <div class="porteur-soft-card h-100">
                    <p class="porteur-page-subtle mb-1">Prochaine action</p>
                    <h6 class="mb-2">Mettre a jour la maquette</h6>
                    <p class="mb-0">Le support produit du projet digital doit etre aligne avec la nouvelle roadmap.</p>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </div>
        </div>
<?php include __DIR__ . '/components/porteur_page_end.php'; ?>
