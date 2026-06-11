<?php require_once __DIR__ . '/header.php'; ?>
<?php
/** @var \App\Models\Project $project */
$project = $project ?? null;
if (!$project) {
    echo '<div class="alert alert-danger">Projet introuvable.</div>';
    require_once __DIR__ . '/footer.php';
    return;
}
use \App\Models\ProjectDocument;
$statusEnum = $project->statusEnum();
$statusLabel = $statusEnum->label();
$statusColor = $statusEnum->color();
$funded = (int) $project->montant_finance;
$progress = $project->montant_demande > 0 ? min(100, (int) round(($funded / $project->montant_demande) * 100)) : 0;
$documents = $project->documents ?? collect();
$analyses = $project->analyses ?? collect();
$financements = $project->financements ?? collect();
?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title">DÉTAILS DU PROJET</h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">ACCUEIL</a></li>
                        <li class="breadcrumb-item"><a href="mes_projets.php">MES PROJETS</a></li>
                        <li class="breadcrumb-item active" aria-current="page">DÉTAILS</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><?php echo htmlspecialchars($project->titre, ENT_QUOTES, 'UTF-8'); ?></h4>
                            <span class="badge bg-<?php echo $statusColor; ?>-transparent rounded-pill text-<?php echo $statusColor; ?> p-2 px-3"><?php echo $statusLabel; ?></span>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6 class="fw-bold">Description</h6>
                                <p><?php echo nl2br(htmlspecialchars($project->description ?? 'Aucune description.', ENT_QUOTES, 'UTF-8')); ?></p>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="fw-bold">Secteur</h6>
                                    <p><?php echo htmlspecialchars($project->secteur ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold">Montant demandé</h6>
                                    <p class="text-primary fw-bold fs-5"><?php echo number_format($project->montant_demande, 0, ',', ' '); ?> FCFA</p>
                                </div>
                            </div>

                            <?php if ($project->localisation): ?>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="fw-bold">Localisation</h6>
                                    <p><?php echo htmlspecialchars($project->localisation, ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold">Date de soumission</h6>
                                    <p><?php echo $project->created_at ? $project->created_at->format('d M Y') : ''; ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($financements->isNotEmpty()): ?>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Offres de Financement</h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Institution</th>
                                            <th>Montant</th>
                                            <th>Taux</th>
                                            <th>Durée</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($financements as $f): ?>
                                        <tr id="funding-row-<?php echo $f->id; ?>">
                                            <td><?php echo htmlspecialchars($f->institution->nom ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo number_format($f->montant_propose, 0, ',', ' '); ?> FCFA</td>
                                            <td><?php echo $f->taux_interet; ?>%</td>
                                            <td><?php echo $f->duree; ?> mois</td>
                                            <td id="funding-status-<?php echo $f->id; ?>">
                                                <span class="badge bg-<?php echo $f->statut === 'decaisse' ? 'success' : ($f->statut === 'confirme' ? 'warning' : ($f->statut === 'refuse' ? 'danger' : 'primary')); ?>"><?php echo $f->statut === 'propose' ? 'Proposition reçue' : ucfirst($f->statut); ?></span>
                                            </td>
                                            <td id="funding-actions-<?php echo $f->id; ?>">
                                                <?php if ($f->statut === 'propose'): ?>
                                                <button class="btn btn-sm btn-success me-1" onclick="acceptFunding(<?php echo $f->id; ?>)">
                                                    <i class="fe fe-check"></i> Accepter
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="refuseFunding(<?php echo $f->id; ?>)">
                                                    <i class="fe fe-x"></i> Refuser
                                                </button>
                                                <?php elseif ($f->statut === 'confirme'): ?>
                                                <span class="text-warning small"><i class="fe fe-clock"></i> En attente de décaissement</span>
                                                <?php elseif ($f->statut === 'decaisse'): ?>
                                                <span class="text-success small"><i class="fe fe-check-circle"></i> Décaissé</span>
                                                <?php elseif ($f->statut === 'refuse'): ?>
                                                <span class="text-danger small"><i class="fe fe-x-circle"></i> Refusé</span>
                                                <?php else: ?>
                                                <span class="text-muted small"><?php echo ucfirst($f->statut); ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($documents->isNotEmpty()): ?>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Documents</h4>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <?php foreach ($documents as $doc): ?>
                                <div class="list-group-item d-flex align-items-center">
                                    <i class="fe fe-file-text me-2 fs-16 text-info"></i>
                                    <span class="flex-fill"><?php echo htmlspecialchars($doc->type_document ?? $doc->nom ?? 'Document', ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php if ($doc->fichier): ?>
                                    <a href="/documents/secure/project/<?php echo $doc->id; ?>/view" class="btn btn-sm btn-outline-primary">
                                        <i class="fe fe-eye"></i> Voir
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Progression</h4>
                        </div>
                        <div class="card-body text-center">
                            <div class="chart-circle chart-circle-md mt-2" data-value="<?php echo $progress / 100; ?>" data-thickness="8" data-color="#09ad95">
                                <div class="chart-circle-value">
                                    <h4 class="mb-0"><?php echo $progress; ?>%</h4>
                                </div>
                            </div>
                            <div class="mt-4">
                                <small class="text-muted d-block">Montant demandé</small>
                                <h5 class="fw-bold"><?php echo number_format($project->montant_demande, 0, ',', ' '); ?> FCFA</h5>
                                <small class="text-muted d-block mt-2">Montant financé</small>
                                <h5 class="fw-bold text-success"><?php echo number_format($funded, 0, ',', ' '); ?> FCFA</h5>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Informations</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr><td>Statut</td><td><span class="badge bg-<?php echo $statusColor; ?>"><?php echo $statusLabel; ?></span></td></tr>
                                    <tr><td>Secteur</td><td><?php echo htmlspecialchars($project->secteur ?? '', ENT_QUOTES, 'UTF-8'); ?></td></tr>
                                    <tr><td>Créé le</td><td><?php echo $project->created_at ? $project->created_at->format('d/m/Y') : ''; ?></td></tr>
                                    <?php if ($project->date_soumission): ?>
                                    <tr><td>Soumis le</td><td><?php echo $project->date_soumission->format('d/m/Y'); ?></td></tr>
                                    <?php endif; ?>
                                    <tr><td>Documents</td><td><?php echo $documents->count(); ?> fichier(s)</td></tr>
                                    <tr><td>Offres reçues</td><td><?php echo $financements->count(); ?></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <?php if ($analyses->isNotEmpty()): ?>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Analyses</h4>
                        </div>
                        <div class="card-body">
                            <?php foreach ($analyses as $analysis): ?>
                            <div class="mb-3 p-3 border rounded">
                                <div class="fw-bold"><?php echo htmlspecialchars($analysis->institution->nom ?? 'Institution', ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="small text-muted">Note: <?php echo $analysis->note_risque ?? ''; ?> / 100</div>
                                <div class="small"><?php echo htmlspecialchars(substr($analysis->commentaires ?? '', 0, 200), ENT_QUOTES, 'UTF-8'); ?></div>
                                <span class="badge bg-<?php echo ($analysis->decision ?? '') === 'favorable' ? 'success' : (($analysis->decision ?? '') === 'defavorable' ? 'danger' : 'secondary'); ?> mt-1">
                                    <?php echo ucfirst($analysis->decision ?? 'En attente'); ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.circleProgress) {
        $('.chart-circle').each(function() {
            const val = $(this).data('value') || 0;
            const thickness = $(this).data('thickness') || 5;
            const color = $(this).data('color') || '#09ad95';
            $(this).circleProgress({
                value: Math.min(val, 1),
                thickness: thickness,
                fill: { color: color }
            });
        });
    }
});

const csrfToken = '<?php echo $csrf_token ?? ""; ?>';

async function acceptFunding(id) {
    if (typeof ALOGOTO !== 'undefined') {
        const { isConfirmed } = await ALOGOTO.confirm('Accepter cette offre ?', 'Vous allez accepter cette proposition de financement.', 'Oui, accepter', 'Annuler');
        if (!isConfirmed) return;
    } else {
        const confirmed = await ModalHelper.confirm('<i class="bi bi-check-circle me-2 text-success"></i> Accepter', 'Accepter cette offre ? Vous allez accepter cette proposition de financement.', 'Oui, accepter', 'Annuler', 'btn-success');
        if (!confirmed) return;
    }

    try {
        const response = await fetch('/dashboard/porteur/financement/' + id + '/accept', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok) {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.success(data.message || 'Proposition acceptée.');

            const statusCell = document.getElementById('funding-status-' + id);
            if (statusCell) statusCell.innerHTML = '<span class="badge bg-warning">Accepté (Attente décaissement)</span>';

            const actionsCell = document.getElementById('funding-actions-' + id);
            if (actionsCell) actionsCell.innerHTML = '<span class="text-warning small"><i class="fe fe-clock"></i> En attente de décaissement</span>';
        } else {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.error(data.message || 'Erreur lors de l\'acceptation.');
            else ModalHelper.error('Erreur', data.message || 'Erreur lors de l\'acceptation.');
        }
    } catch (error) {
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur de connexion.');
        else ModalHelper.error('Erreur de connexion', 'Impossible de contacter le serveur.');
    }
}

async function refuseFunding(id) {
    if (typeof ALOGOTO !== 'undefined') {
        const { isConfirmed } = await ALOGOTO.confirm('Refuser cette offre ?', 'Vous allez refuser cette proposition de financement.', 'Oui, refuser', 'Annuler');
        if (!isConfirmed) return;
    } else {
        const confirmed = await ModalHelper.confirm('<i class="bi bi-x-circle me-2 text-danger"></i> Refuser', 'Refuser cette offre ? Vous allez refuser cette proposition de financement.', 'Oui, refuser', 'Annuler', 'btn-danger');
        if (!confirmed) return;
    }

    try {
        const response = await fetch('/dashboard/porteur/financement/' + id + '/refuse', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok) {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.success(data.message || 'Proposition refusée.');

            const statusCell = document.getElementById('funding-status-' + id);
            if (statusCell) statusCell.innerHTML = '<span class="badge bg-danger">Refusé</span>';

            const actionsCell = document.getElementById('funding-actions-' + id);
            if (actionsCell) actionsCell.innerHTML = '<span class="text-danger small"><i class="fe fe-x-circle"></i> Refusé</span>';
        } else {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.error(data.message || 'Erreur lors du refus.');
            else ModalHelper.error('Erreur', data.message || 'Erreur lors du refus.');
        }
    } catch (error) {
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur de connexion.');
        else ModalHelper.error('Erreur de connexion', 'Impossible de contacter le serveur.');
    }
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
