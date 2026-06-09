<?php
require_once __DIR__ . '/header.php';

$fundingStats = $fundingStats ?? [];
?>
<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <div>
                    <h1 class="page-title"><strong>FINANCEMENT</strong></h1>
                    <p class="text-muted mb-0">Suivez le niveau de financement de vos projets et les institutions partenaires.</p>
                </div>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">ACCEUIL</a></li>
                        <li class="breadcrumb-item active" aria-current="page">FINANCEMENT</li>
                    </ol>
                </div>
            </div>

            <div class="row" id="statsRow">
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">MONTANT DEMANDÉ</span>
                                    <h2 class="mb-0 mt-1 fs-5" id="montantDemande">-- FCFA</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-primary"><i class="bi bi-cash-stack fs-20"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">MONTANT FINANCÉ</span>
                                    <h2 class="mb-0 mt-1 fs-5" id="montantFinance">-- FCFA</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-success"><i class="bi bi-check-circle fs-20"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">RESTE À FINANCER</span>
                                    <h2 class="mb-0 mt-1 fs-5" id="montantRestant">-- FCFA</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-warning"><i class="bi bi-hourglass-split fs-20"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">INSTITUTIONS</span>
                                    <h2 class="mb-0 mt-1" id="institutionsCount">--</h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-info"><i class="bi bi-building fs-20"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                <h5 class="fw-bold mb-0"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Progression du financement</h5>
                                <span class="badge bg-primary-transparent text-primary fs-14 px-3 py-2" id="progressionLabel">0%</span>
                            </div>
                            <div class="progress" style="height:24px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressionBar" role="progressbar" style="width:0%;background:var(--porteur-accent, #0d6efd);"></div>
                            </div>
                            <div class="d-flex flex-wrap justify-content-between mt-2">
                                <small class="text-muted" id="progressDetail">Aucun financement pour le moment</small>
                                <small class="text-muted" id="projectsFundedCount">0 projet financé</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h4 class="card-title mb-0"><i class="bi bi-building me-2"></i>Institutions financeuses</h4>
                            <div class="d-flex flex-wrap gap-2">
                                <input type="text" class="form-control form-control-sm" id="searchFunding" placeholder="Rechercher..." style="width:160px;">
                                <select class="form-select form-select-sm" id="filterFundingStatus">
                                    <option value="all">Tous les statuts</option>
                                    <option value="awaiting_borrower_plan">Attente plan porteur</option>
                                    <option value="awaiting_imf_validation">En attente validation IMF</option>
                                    <option value="approved">Approuvé</option>
                                    <option value="disbursed">Décaissé / Actif</option>
                                    <option value="rejected">Rejeté</option>
                                    <option value="completed">Terminé</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="fundingTableLoader" class="text-center py-5"><div class="spinner-border text-primary"></div></div>
                            <div class="table-responsive">
                                <table class="table text-nowrap mb-0" id="fundingTable" style="opacity:0.4;pointer-events:none;">
                                    <thead>
                                        <tr>
                                            <th>INSTITUTION</th>
                                            <th>PROJET</th>
                                            <th>MONTANT</th>
                                            <th>TAUX/DURÉE</th>
                                            <th>DATE</th>
                                            <th>STATUT</th>
                                            <th>ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="fundingBody"></tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3" id="fundingPagination"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><i class="bi bi-clock-history me-2"></i>Historique financement</h4>
                        </div>
                        <div class="card-body p-0">
                            <div id="historyLoader" class="text-center py-5"><div class="spinner-border text-primary"></div></div>
                            <ul class="list-group list-group-flush d-none" id="historyList"></ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <h4 class="card-title mb-0"><i class="bi bi-file-earmark-text me-2"></i>Documents liés</h4>
                        </div>
                        <div class="card-body p-0">
                            <div id="documentsLoader" class="text-center py-5"><div class="spinner-border text-primary"></div></div>
                            <div class="table-responsive">
                                <table class="table text-nowrap mb-0" id="documentsTable" style="opacity:0.4;pointer-events:none;">
                                    <thead>
                                        <tr>
                                            <th>DOCUMENT</th>
                                            <th>PROJET</th>
                                            <th>TYPE</th>
                                            <th>DATE</th>
                                            <th>ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody id="documentsBody"></tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3" id="documentsPagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="planModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-earmark-text me-2"></i>Définir votre plan de remboursement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="planForm">
                <div class="modal-body">
                    <input type="hidden" name="funding_id" id="planFundingId">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Montant mensuel (FCFA)</label>
                                <input type="number" class="form-control form-control-lg" name="montant_mensuel" id="planMontantMensuel" placeholder="Ex: 450000" min="1" required>
                                <div class="form-text">Combien souhaitez-vous rembourser chaque mois ?</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jour de remboursement</label>
                                <select class="form-control form-control-lg" name="jour_remboursement" id="planJourRemboursement" required>
                                    <option value="">Choisir un jour</option>
                                    <option value="1">1er du mois</option>
                                    <option value="5">5 du mois</option>
                                    <option value="8">8 du mois</option>
                                    <option value="9">9 du mois</option>
                                    <option value="10">10 du mois</option>
                                    <option value="15">15 du mois</option>
                                    <option value="20">20 du mois</option>
                                    <option value="25">25 du mois</option>
                                    <option value="28">28 du mois</option>
                                    <option value="30">30 du mois</option>
                                </select>
                                <div class="form-text">À quelle date souhaitez-vous effectuer vos paiements chaque mois ?</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center gap-2 py-3 mb-0">
                                <i class="bi bi-info-circle fs-5 flex-shrink-0"></i>
                                <span>Une fois votre plan soumis, l'institution validera et procédera au décaissement. Un échéancier sera généré automatiquement.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Soumettre le plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalTitle">Confirmer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"><p id="confirmModalMessage"></p></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn" id="confirmModalBtn">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<script>
const PAGINATION_ROWS = 10;

function updatePagination(tableBodyId, paginationId) {
    const tbody = document.getElementById(tableBodyId);
    const nav = document.getElementById(paginationId);
    if (!tbody || !nav) return;

    const rows = Array.from(tbody.children).filter(row => {
        if (row.dataset && row.dataset.filtered === 'true') return false;
        if (row.querySelector('td[colspan]')) return false;
        return true;
    });

    const totalPages = Math.max(1, Math.ceil(rows.length / PAGINATION_ROWS));
    let currentPage = parseInt(nav.dataset.currentPage || '1', 10);
    if (currentPage > totalPages) currentPage = totalPages;
    nav.dataset.currentPage = currentPage;

    Array.from(tbody.children).forEach(row => {
        if (row.dataset && row.dataset.filtered === 'true') {
            row.style.display = 'none';
        } else if (!row.querySelector('td[colspan]')) {
            const idx = rows.indexOf(row);
            if (idx !== -1) {
                row.style.display = (idx >= (currentPage - 1) * PAGINATION_ROWS && idx < currentPage * PAGINATION_ROWS) ? '' : 'none';
            }
        }
    });

    if (totalPages <= 1) { nav.innerHTML = ''; return; }

    let html = '<nav aria-label="Pagination"><ul class="pagination pagination-sm justify-content-center mb-0">';
    html += `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="return paginationGo('${tableBodyId}','${paginationId}',${currentPage - 1})"><i class="bi bi-chevron-left"></i></a></li>`;
    for (let i = 1; i <= totalPages; i++) {
        html += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="return paginationGo('${tableBodyId}','${paginationId}',${i})">${i}</a></li>`;
    }
    html += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="return paginationGo('${tableBodyId}','${paginationId}',${currentPage + 1})"><i class="bi bi-chevron-right"></i></a></li>`;
    html += '</ul></nav>';
    nav.innerHTML = html;
}

function paginationGo(tableBodyId, paginationId, page) {
    const nav = document.getElementById(paginationId);
    if (nav) nav.dataset.currentPage = page;
    updatePagination(tableBodyId, paginationId);
    return false;
}

function resetPagination(tableBodyId, paginationId) {
    const nav = document.getElementById(paginationId);
    if (nav) nav.dataset.currentPage = 1;
    updatePagination(tableBodyId, paginationId);
}

const csrfToken = '<?php echo $csrf_token ?? ""; ?>';
var confirmModal = null;
var confirmCallback = null;

document.getElementById('confirmModalBtn').addEventListener('click', function() {
    if (confirmCallback) { confirmCallback(); confirmCallback = null; }
    confirmModal.hide();
});

function formatMoney(n) {
    return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA';
}

function loadStats() {
    return fetch('/dashboard/porteur/financement/stats', {
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        var el;
        el = document.getElementById('montantDemande'); if (el) el.textContent = formatMoney(d.montant_demande);
        el = document.getElementById('montantFinance'); if (el) el.textContent = formatMoney(d.montant_finance);
        el = document.getElementById('montantRestant'); if (el) el.textContent = formatMoney(d.montant_restant);
        el = document.getElementById('institutionsCount'); if (el) el.textContent = d.institutions_count;

        var bar = document.getElementById('progressionBar');
        var label = document.getElementById('progressionLabel');
        var detail = document.getElementById('progressDetail');
        var funded = document.getElementById('projectsFundedCount');

        if (bar) bar.style.width = d.progression + '%';
        if (label) label.textContent = d.progression + '%';

        if (d.progression >= 100) {
            if (bar) bar.style.background = '#198754';
            if (detail) { detail.textContent = 'Objectif de financement atteint !'; detail.className = 'text-success fw-semibold'; }
        } else if (d.progression > 0) {
            if (bar) bar.style.background = 'var(--porteur-accent, #0d6efd)';
            if (detail) { detail.textContent = d.montant_restant > 0 ? 'Reste ' + formatMoney(d.montant_restant) + ' à financer' : 'Intégralement financé'; detail.className = 'text-muted'; }
        } else {
            if (bar) bar.style.width = '0%';
            if (detail) { detail.textContent = 'Aucun financement pour le moment'; detail.className = 'text-muted'; }
        }

        if (funded) funded.textContent = d.projets_avec_financement + ' projet' + (d.projets_avec_financement > 1 ? 's' : '') + ' financé' + (d.projets_avec_financement > 1 ? 's' : '') + ' sur ' + d.total_projets;
    })
    .catch(function(err) {
        /* elements may not exist on all pages, ignore */
    });
}

function loadFundings() {
    return fetch('/dashboard/porteur/financement/data', {
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var tbody = document.getElementById('fundingBody');
        var loader = document.getElementById('fundingTableLoader');
        var table = document.getElementById('fundingTable');

        loader.classList.add('d-none');
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5"><i class="bi bi-building fs-1 text-muted"></i><h5 class="mt-3 text-muted">Aucun financement pour le moment</h5><p class="text-muted">Les institutions apparaîtront ici une fois vos projets financés.</p></td></tr>';
        } else {
            data.forEach(function(f) {
                var initials = f.institution_nom.split(' ').map(function(w){return w[0];}).join('').substring(0,2).toUpperCase();
                var row = document.createElement('tr');
                row.setAttribute('data-status', f.statut_raw);
                row.setAttribute('data-project', f.project_titre.toLowerCase());
                row.setAttribute('data-institution', f.institution_nom.toLowerCase());
                row.id = 'funding-row-' + f.id;

                var tauxDuree = (f.taux_interet || f.taux_interet === 0) && f.duree ? f.taux_interet + '% / ' + f.duree + ' mois' : '-';

                var actionsHtml = '';
                if (f.statut_raw === 'awaiting_borrower_plan') {
                    actionsHtml =
                        '<button class="btn btn-sm btn-success me-1" onclick="event.stopPropagation();openPlanModal(\'' + f.id + '\')"><i class="bi bi-check-lg"></i></button>' +
                        '<button class="btn btn-sm btn-danger" onclick="event.stopPropagation();refuseFunding(\'' + f.id + '\', \'' + csrfToken + '\')"><i class="bi bi-x-lg"></i></button>';
                } else if (f.statut_raw === 'awaiting_imf_validation') {
                    actionsHtml = '<span class="text-warning small"><i class="bi bi-clock"></i> En attente validation IMF</span>';
                } else if (f.statut_raw === 'approved') {
                    actionsHtml = '<span class="text-info small"><i class="bi bi-check-circle"></i> Approuvé</span>';
                } else if (f.statut_raw === 'disbursed' || f.statut_raw === 'active') {
                    actionsHtml = '<span class="text-success small"><i class="bi bi-check-circle"></i> Actif</span>';
                } else if (f.statut_raw === 'completed') {
                    actionsHtml = '<span class="text-success small"><i class="bi bi-check-all"></i> Terminé</span>';
                } else if (f.statut_raw === 'rejected') {
                    actionsHtml = '<span class="text-danger small"><i class="bi bi-x-circle"></i> Rejeté</span>';
                } else {
                    actionsHtml = '<span class="text-muted small">' + escHtml(f.statut) + '</span>';
                }

                row.innerHTML =
                    '<td><div class="d-flex align-items-center"><div class="avatar avatar-md bg-info-transparent rounded-circle me-2"><span class="fw-bold text-info">' + initials + '</span></div><div class="d-flex flex-column"><span class="fw-medium lh-1">' + escHtml(f.institution_nom) + '</span></div></div></td>' +
                    '<td><span class="fw-medium">' + escHtml(f.project_titre) + '</span></td>' +
                    '<td><span class="fw-medium text-success">' + formatMoney(f.montant) + '</span></td>' +
                    '<td>' + tauxDuree + '</td>' +
                    '<td>' + (f.date_financement || '-') + '</td>' +
                    '<td><span class="badge bg-' + f.statut_color + '-transparent rounded-pill text-' + f.statut_color + ' p-2 px-3">' + f.statut + '</span></td>' +
                    '<td>' + actionsHtml + '</td>';
                tbody.appendChild(row);
            });
        }

        table.style.removeProperty('opacity');
        table.style.removeProperty('pointer-events');
        updatePagination('fundingBody', 'fundingPagination');
    });
}

function loadHistory() {
    return fetch('/dashboard/porteur/financement/history', {
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var list = document.getElementById('historyList');
        var loader = document.getElementById('historyLoader');

        loader.classList.add('d-none');
        list.innerHTML = '';
        list.classList.remove('d-none');

        if (data.length === 0) {
            list.innerHTML = '<li class="list-group-item text-center py-5 text-muted"><i class="bi bi-clock-history fs-1 d-block mb-3"></i>Aucun événement enregistré</li>';
        } else {
            data.forEach(function(item) {
                var li = document.createElement('li');
                li.className = 'list-group-item border-0 py-3 border-bottom';
                li.innerHTML =
                    '<div class="d-flex align-items-start gap-3"><span class="avatar avatar-md rounded-circle bg-' + item.color + '-transparent text-' + item.color + ' flex-shrink-0"><i class="' + item.icon + ' fs-5"></i></span><div class="flex-grow-1 min-w-0"><h6 class="mb-1">' + escHtml(item.action) + '</h6><p class="mb-0 text-muted small">' + escHtml(item.project_titre) + (item.detail ? ' — ' + escHtml(item.detail) : '') + '</p><small class="text-muted"><i class="bi bi-person me-1"></i>' + escHtml(item.acteur) + ' · ' + item.date + '</small></div></div>';
                list.appendChild(li);
            });
        }
    });
}

function loadDocuments() {
    return fetch('/dashboard/porteur/financement/documents', {
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var tbody = document.getElementById('documentsBody');
        var loader = document.getElementById('documentsLoader');
        var table = document.getElementById('documentsTable');

        loader.classList.add('d-none');
        tbody.innerHTML = '';

        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5"><i class="bi bi-file-earmark-text fs-1 text-muted"></i><h5 class="mt-3 text-muted">Aucun document lié</h5><p class="text-muted">Les documents (contrats, conventions, reçus) apparaîtront ici.</p></td></tr>';
        } else {
            data.forEach(function(doc) {
                var typeIcon = 'bi-file-earmark';
                var typeColor = 'primary';
                if (doc.type === 'contrat') { typeIcon = 'bi-file-earmark-text'; typeColor = 'warning'; }
                else if (doc.type === 'convention') { typeIcon = 'bi-file-earmark-check'; typeColor = 'info'; }
                else if (doc.type === 'preuve') { typeIcon = 'bi-file-earmark-image'; typeColor = 'success'; }
                else if (doc.type === 'recu') { typeIcon = 'bi-receipt'; typeColor = 'secondary'; }

                var row = document.createElement('tr');
                row.innerHTML =
                    '<td><div class="d-flex align-items-center"><span class="avatar avatar-md bg-' + typeColor + '-transparent rounded-circle me-2"><i class="bi ' + typeIcon + ' fs-5 text-' + typeColor + '"></i></span><span class="fw-medium">' + escHtml(doc.nom) + '</span></div></td>' +
                    '<td>' + escHtml(doc.project_titre) + '</td>' +
                    '<td><span class="badge bg-' + typeColor + '-transparent text-' + typeColor + ' text-uppercase">' + escHtml(doc.type) + '</span></td>' +
                    '<td>' + doc.date + '</td>' +
                    '<td>' +
                        (doc.download_url ? '<a href="' + doc.download_url + '" class="btn btn-sm btn-outline-primary me-1" title="Télécharger"><i class="bi bi-download"></i></a>' : '') +
                        (doc.view_url ? '<a href="' + doc.view_url + '" class="btn btn-sm btn-outline-info" title="Voir"><i class="bi bi-eye"></i></a>' : '') +
                    '</td>';
                tbody.appendChild(row);
            });
        }

        table.style.removeProperty('opacity');
        table.style.removeProperty('pointer-events');
        updatePagination('documentsBody', 'documentsPagination');
    });
}

function escHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

async function acceptFunding(id, token) {
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
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
        });
        const data = await response.json();

        if (response.ok) {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.success(data.message || 'Proposition acceptée.');
            loadFundings();
            loadStats();
        } else {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.error(data.message || 'Erreur lors de l\'acceptation.');
        }
    } catch (error) {
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur de connexion.');
    }
}

function openPlanModal(fundingId) {
    document.getElementById('planFundingId').value = fundingId;
    document.getElementById('planMontantMensuel').value = '';
    document.getElementById('planJourRemboursement').value = '';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('planModal')).show();
}

async function submitPlan(fundingId, montantMensuel, jourRemboursement) {
    try {
        const response = await fetch('/api/v1/porteur/financements/' + fundingId + '/soumettre-plan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                montant_mensuel: montantMensuel,
                jour_remboursement: jourRemboursement
            })
        });

        const data = await response.json();

        if (response.ok) {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.success(data.message || 'Plan de remboursement soumis avec succès.');
            loadFundings();
            loadStats();
            return true;
        } else {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.error(data.message || 'Erreur lors de la soumission du plan.');
            return false;
        }
    } catch (error) {
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur de connexion.');
        return false;
    }
}

async function refuseFunding(id, token) {
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
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
        });
        const data = await response.json();

        if (response.ok) {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.success(data.message || 'Proposition refusée.');
            loadFundings();
            loadStats();
        } else {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.error(data.message || 'Erreur lors du refus.');
        }
    } catch (error) {
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur de connexion.');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    confirmModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('confirmModal'));

    loadStats().then(loadFundings).then(loadHistory).then(loadDocuments).catch(function(err) {
        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur de chargement des données.');
    });

    document.getElementById('searchFunding').addEventListener('keyup', function() {
        var val = this.value.toLowerCase();
        var rows = document.querySelectorAll('#fundingBody tr[data-project]');
        rows.forEach(function(row) {
            var project = row.getAttribute('data-project') || '';
            var institution = row.getAttribute('data-institution') || '';
            var match = project.includes(val) || institution.includes(val);
            row.dataset.filtered = match ? 'false' : 'true';
            row.style.display = match ? '' : 'none';
        });
        resetPagination('fundingBody', 'fundingPagination');
    });

    document.getElementById('planForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        var fundingId = document.getElementById('planFundingId').value;
        var montantMensuel = document.getElementById('planMontantMensuel').value;
        var jourRemboursement = document.getElementById('planJourRemboursement').value;

        if (!montantMensuel || montantMensuel <= 0) {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Veuillez saisir un montant mensuel valide.');
            return;
        }
        if (!jourRemboursement) {
            if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Veuillez choisir un jour de remboursement.');
            return;
        }

        var ok = await submitPlan(fundingId, montantMensuel, jourRemboursement);
        if (ok) {
            bootstrap.Modal.getInstance(document.getElementById('planModal')).hide();
        }
    });

    document.getElementById('filterFundingStatus').addEventListener('change', function() {
        var val = this.value;
        var rows = document.querySelectorAll('#fundingBody tr[data-status]');
        rows.forEach(function(row) {
            var match = val === 'all' || row.getAttribute('data-status') === val;
            row.dataset.filtered = match ? 'false' : 'true';
            row.style.display = match ? '' : 'none';
        });
        resetPagination('fundingBody', 'fundingPagination');
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
