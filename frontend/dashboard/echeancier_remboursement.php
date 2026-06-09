<?php
require_once __DIR__ . '/components/porteur_helpers.php';
$porteurData = require __DIR__ . '/components/porteur_data.php';
$repayments = $porteurData['repayments'];

$page_title = 'Echeancier de remboursement';
$page_subtitle = 'Visualisez les echeances prevues et les confirmations institutionnelles.';
$page_active_nav = 'repayment-schedule';
$page_document_title = 'Echeancier - ALOGOTO';

include __DIR__ . '/components/porteur_page_start.php';
?>
        <div class="row dashboard-gap-20 mt-2">
          <div class="col-12">
            <?php
            ob_start();
            ?>
<table class="table dashboard-table data-table align-middle" data-items-per-page="10">
  <thead>
    <tr>
      <th>Date echeance</th>
      <th>Montant</th>
      <th>Statut paiement</th>
      <th>Confirmation institution</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($repayments as $repayment): ?>
<?php
  $confirmation = $repayment['status_label'] === 'Paye' ? 'Paiement enregistre par institution' : 'En attente de confirmation';
?>
    <tr>
      <td><?php echo porteur_escape($repayment['date']); ?></td>
      <td><?php echo porteur_escape(porteur_format_fcfa($repayment['amount'], false)); ?></td>
      <td><span class="status-badge <?php echo porteur_escape($repayment['status_class']); ?>"><?php echo porteur_escape($repayment['status_label']); ?></span></td>
      <td><?php echo porteur_escape($confirmation); ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php
            $table_html = ob_get_clean();
            $table_title = 'Echeancier de remboursement';
            $table_id = 'repaymentScheduleTable';
            $table_search_placeholder = 'Rechercher une echeance';
            $table_show_filter = false;
            $table_auto_paginate = true;
            include __DIR__ . '/components/data_table.html';
            ?>
          </div>
        </div>
<?php include __DIR__ . '/components/porteur_page_end.php'; ?>

<script>
$(document).ready(function() {
  const apiBaseUrl = '<?php echo backend_url('api/v1/'); ?>';
  const token = localStorage.getItem('alogoto_api_token');
  if (!token) return;

  const headers = {
    'Authorization': 'Bearer ' + token,
    'Accept': 'application/json'
  };

  $.ajax({
    url: apiBaseUrl + 'porteur/financements',
    headers: headers,
    success: function(data) {
      if (!data.fundings || data.fundings.length === 0) return;

      const tbody = $('#repaymentScheduleTable tbody');
      tbody.empty();
      let hasEcheances = false;

      data.fundings.forEach(function(funding) {
        if (funding.echeances && funding.echeances.length > 0) {
          hasEcheances = true;
          const statusLabels = {
            'pending': { label: 'À venir', class: 'status-submitted submitted' },
            'upcoming': { label: 'Prochaine', class: 'status-funding funding' },
            'paid': { label: 'Payée', class: 'status-approved approved' },
            'overdue': { label: 'En retard', class: 'status-late late' },
            'partial': { label: 'Partielle', class: 'status-funding funding' }
          };

          funding.echeances.forEach(function(echeance) {
            const st = statusLabels[echeance.statut] || { label: echeance.statut, class: 'status-draft draft' };
            const confirmation = echeance.statut === 'paid' ? 'Paiement confirmé' : 'En attente';

            const row = `<tr>
              <td>${echeance.date_echeance || 'N/A'}</td>
              <td>${parseFloat(echeance.montant_total || 0).toLocaleString('fr-FR')} FCFA</td>
              <td><span class="status-badge ${st.class}">${st.label}</span></td>
              <td>${confirmation}</td>
            </tr>`;
            tbody.append(row);
          });
        }
      });

      if (!hasEcheances) {
        tbody.html('<tr><td colspan="4" class="text-center">Aucune échéance trouvée</td></tr>');
      }
    },
    error: function() {
      console.log('Using demo data for echeances');
    }
  });
});
</script>
