<?php
require_once __DIR__ . '/components/porteur_helpers.php';
$porteurData = require __DIR__ . '/components/porteur_data.php';
$projects = $porteurData['projects'];

$totalProjects = count($projects);
$fundingProjects = 0;
$fundedProjects = 0;
$draftProjects = 0;

foreach ($projects as $project) {
  if ($project['status_label'] === 'En financement') {
    $fundingProjects++;
  } elseif ($project['status_label'] === 'Finance') {
    $fundedProjects++;
  } elseif ($project['status_label'] === 'Brouillon') {
    $draftProjects++;
  }
}

$page_title = 'Mes projets';
$page_subtitle = 'Suivez l avancement et les actions prioritaires de votre portefeuille.';
$page_active_nav = 'my-projects';
$page_document_title = 'Mes projets - ALOGOTO';
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

    .porteur-project-cards {
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
          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-folder2';
            $stat_title = 'Projets actifs';
            $stat_value = (string) $totalProjects;
            $stat_trend = '+1 ce mois';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-arrow-up-right';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.15)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-graph-up-arrow';
            $stat_title = 'En financement';
            $stat_value = (string) $fundingProjects;
            $stat_trend = 'Pipeline ouvert';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-activity';
            $stat_icon_bg = 'rgba(0, 72, 220, 0.12)';
            $stat_icon_color = 'var(--primary-color-2)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-check2-circle';
            $stat_title = 'Finances';
            $stat_value = (string) $fundedProjects;
            $stat_trend = 'Dossiers complets';
            $stat_trend_class = 'is-up';
            $stat_trend_icon = 'bi-check2';
            $stat_icon_bg = 'rgba(0, 196, 134, 0.12)';
            $stat_icon_color = 'var(--primary-color-1)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>

          <div class="col-12 col-md-6 col-xl-3">
            <?php
            $stat_icon = 'bi-pencil-square';
            $stat_title = 'Brouillons';
            $stat_value = (string) $draftProjects;
            $stat_trend = 'A finaliser';
            $stat_trend_class = 'is-neutral';
            $stat_trend_icon = 'bi-hourglass-split';
            $stat_icon_bg = 'rgba(252, 160, 40, 0.18)';
            $stat_icon_color = 'var(--primary-color-3)';
            include __DIR__ . '/components/stat_card.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20">
          <div class="col-12">
            <?php
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle">
  <thead>
    <tr>
      <th>ID projet</th>
      <th>Nom du projet</th>
      <th>Secteur</th>
      <th>Montant demande</th>
      <th>Montant finance</th>
      <th>Statut</th>
      <th>Date de creation</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($projects as $project): ?>
    <tr>
      <td><?php echo porteur_escape($project['id']); ?></td>
      <td><?php echo porteur_escape($project['name']); ?></td>
      <td><?php echo porteur_escape($project['sector']); ?></td>
      <td><?php echo porteur_escape(porteur_format_fcfa($project['requested_amount'])); ?></td>
      <td><?php echo porteur_escape(porteur_format_fcfa($project['funded_amount'])); ?></td>
      <td><span class="status-badge <?php echo porteur_escape($project['status_class']); ?>"><?php echo porteur_escape($project['status_label']); ?></span></td>
      <td><?php echo porteur_escape($project['created_at']); ?></td>
      <td>
        <div class="porteur-table-actions">
          <a href="dashboard_porteur.php" class="btn-dashboard outline sm">
            <i class="bi bi-eye"></i>
            <span>Voir</span>
          </a>
          <a href="creer_projet.php" class="dashboard-btn-primary btn-dashboard primary sm">
            <i class="bi bi-pencil-square"></i>
            <span>Modifier</span>
          </a>
        </div>
      </td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Portefeuille projets';
            $table_id = 'porteurProjectsList';
            $table_search_placeholder = 'Rechercher un projet';
            $table_pagination = '<button class="dashboard-page-btn page-btn active">1</button><button class="dashboard-page-btn page-btn">2</button><button class="dashboard-page-btn page-btn">3</button>';
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>

        <div class="row dashboard-gap-20 porteur-project-cards">
<?php foreach ($projects as $project): ?>
          <div class="col-12 col-md-6 col-xl-4">
            <section class="dashboard-card hover-lift h-100">
              <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                <div>
                  <h6 class="mb-1"><?php echo porteur_escape($project['name']); ?></h6>
                  <p class="porteur-page-subtle mb-0"><?php echo porteur_escape($project['location']); ?> • <?php echo porteur_escape($project['duration']); ?></p>
                </div>
                <span class="status-badge <?php echo porteur_escape($project['status_class']); ?>"><?php echo porteur_escape($project['status_label']); ?></span>
              </div>
              <p class="porteur-page-subtle mb-3"><?php echo porteur_escape($project['description']); ?></p>
              <div class="porteur-meta-list">
                <div class="porteur-meta-list__item">
                  <p class="porteur-meta-list__label">Montant cible</p>
                  <p class="porteur-meta-list__value"><?php echo porteur_escape(porteur_format_fcfa($project['requested_amount'])); ?></p>
                </div>
                <div class="porteur-meta-list__item">
                  <p class="porteur-meta-list__label">Montant acquis</p>
                  <p class="porteur-meta-list__value"><?php echo porteur_escape(porteur_format_fcfa($project['funded_amount'])); ?></p>
                </div>
                <div class="porteur-meta-list__item">
                  <p class="porteur-meta-list__label">Institution lead</p>
                  <p class="porteur-meta-list__value"><?php echo porteur_escape($project['institution']); ?></p>
                </div>
              </div>
              <div class="mt-3">
                <div class="d-flex justify-content-between mb-2">
                  <small class="porteur-page-subtle">Progression</small>
                  <small class="fw-semibold"><?php echo porteur_escape((string) $project['progress']); ?>%</small>
                </div>
                <div class="dashboard-progress">
                  <div class="dashboard-progress__bar bg-success-token" style="width: <?php echo porteur_escape((string) $project['progress']); ?>%;"></div>
                </div>
              </div>
            </section>
          </div>
<?php endforeach; ?>
        </div>
        </div>
<?php include __DIR__ . '/components/porteur_page_end.php'; ?>
