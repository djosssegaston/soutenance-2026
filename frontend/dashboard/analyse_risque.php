<?php
require_once __DIR__ . '/components/dashboard_helpers.php';

$page_title = 'Analyse de risque';
$page_subtitle = 'Comparez les scores de risque, la qualité des dossiers et le rendement attendu.';
$page_active_nav = 'analyzed-projects';
$page_document_title = 'Analyse de risque - ALOGOTO';

include __DIR__ . '/components/institution_page_start.php';
?>
<div class="row dashboard-gap-16 mt-2 mb-4">
  <div class="col-12">
    <section class="dashboard-card">
      <div class="dashboard-card__body text-center py-5">
        <i class="bi bi-bar-chart-line fs-1 text-muted d-block mb-3"></i>
        <h5 class="text-muted">Module d'analyse de risque en cours de déploiement</h5>
        <p class="text-muted mb-4">Vous serez redirigé automatiquement vers la liste des projets disponibles.</p>
        <a href="<?php echo htmlspecialchars(dashboard_url('projets_disponibles.php'), ENT_QUOTES, 'UTF-8'); ?>" class="dashboard-btn-primary btn-dashboard primary sm">Accéder aux projets</a>
      </div>
    </section>
  </div>
</div>
<script>
setTimeout(function() {
  window.location.href = '<?php echo dashboard_url('projets_disponibles.php'); ?>';
}, 5000);
</script>
<?php include __DIR__ . '/components/institution_page_end.php'; ?>
