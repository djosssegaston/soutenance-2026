/**
 * Projets Analysés - Institution Dashboard
 */

const API_BASE = '/api/v1';
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    // Initial Load
    fetchStats();
    fetchAnalyses();

    // Event Listeners
    document.getElementById('search-btn').addEventListener('click', () => {
        currentPage = 1;
        fetchAnalyses();
    });

    document.getElementById('reset-filters').addEventListener('click', () => {
        document.getElementById('search-input').value = '';
        document.getElementById('filter-status').value = '';
        $('.select2').val(null).trigger('change');
        currentPage = 1;
        fetchAnalyses();
    });
});

/**
 * Récupère les statistiques
 */
async function fetchStats() {
    try {
        const response = await fetch(`${API_BASE}/institution/analyses/stats`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) return;
        const data = await response.json();

        document.getElementById('stat-pending').textContent = data.en_cours;
        document.getElementById('stat-approved').textContent = data.approuves;
        document.getElementById('stat-high-risk').textContent = data.risque_eleve;
        document.getElementById('stat-potential-amount').textContent = formatMoney(data.montant_potentiel);
    } catch (error) {
        console.error('Error fetching stats:', error);
    }
}

/**
 * Récupère la liste des analyses
 */
async function fetchAnalyses(page = 1) {
    currentPage = page;
    const loader = document.getElementById('analyses-loader');
    const container = document.getElementById('analyses-list');
    
    if (loader) loader.style.display = 'block';
    if (container) container.innerHTML = '';

    const search = document.getElementById('search-input').value;
    const statut = document.getElementById('filter-status').value;

    try {
        const response = await fetch(`${API_BASE}/institution/analyses?page=${page}&search=${search}&statut=${statut}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        if (!response.ok) throw new Error('Erreur réseau');
        const data = await response.json();

        renderAnalyses(data.data);
        renderPagination(data);
    } catch (error) {
        console.error('Error fetching analyses:', error);
        container.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Erreur lors du chargement des analyses.</td></tr>';
    } finally {
        if (loader) loader.style.display = 'none';
    }
}

/**
 * Affiche les lignes du tableau
 */
function renderAnalyses(analyses) {
    const container = document.getElementById('analyses-list');
    container.innerHTML = '';

    if (analyses.length === 0) {
        container.innerHTML = '<tr><td colspan="6" class="text-center py-5 text-muted">Aucune analyse trouvée.</td></tr>';
        return;
    }

    analyses.forEach(analysis => {
        const riskColor = getRiskColor(analysis.risk_score);
        const statusBadge = getStatusBadge(analysis.statut);
        
        const row = `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="avatar avatar-md bg-primary-transparent text-primary rounded-circle">
                                <i class="fe fe-file-text"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">${analysis.project.titre}</h6>
                            <small class="text-muted">${analysis.project.owner.name} • ${analysis.project.secteur}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 me-2">
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-${riskColor}" style="width: ${analysis.risk_score}%"></div>
                            </div>
                        </div>
                        <span class="badge bg-${riskColor}-transparent text-${riskColor}">${analysis.risk_score}%</span>
                    </div>
                </td>
                <td>
                    <span class="fw-semibold">${analysis.score_solvabilite}/100</span>
                </td>
                <td>
                    <div class="h6 mb-0 fw-bold text-primary">${analysis.note_globale}/100</div>
                </td>
                <td>${statusBadge}</td>
                <td>
                    <div class="btn-list">
                        <button class="btn btn-sm btn-primary" onclick="event.stopPropagation();viewAnalysisDetails(${analysis.id})">
                            <i class="fe fe-eye"></i> Dossier
                        </button>
                        <button class="btn btn-sm btn-icon btn-info" onclick="event.stopPropagation();openDiscussion(${analysis.project.id})">
                            <i class="fe fe-message-circle"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        container.innerHTML += row;
    });
}

/**
 * Affiche la pagination
 */
function renderPagination(data) {
    const container = document.getElementById('pagination-container');
    if (!container) return;

    let html = '<ul class="pagination pagination-rounded">';
    data.links.forEach(link => {
        if (link.url) {
            const label = link.label.replace('&laquo; Previous', '<i class="fe fe-chevron-left"></i>').replace('Next &raquo;', '<i class="fe fe-chevron-right"></i>');
            const pageNum = link.url.split('page=')[1];
            html += `
                <li class="page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="fetchAnalyses(${pageNum})">${label}</a>
                </li>
            `;
        }
    });
    html += '</ul>';
    container.innerHTML = html;
}

/**
 * Détails complets de l'analyse
 */
async function viewAnalysisDetails(id) {
    try {
        const response = await fetch(`${API_BASE}/institution/analyses/${id}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const analysis = await response.json();

        const modalBody = document.getElementById('modal-body');
        document.getElementById('modal-title').textContent = `Dossier: ${analysis.project.titre}`;
        
        var docListHtml = '';
        if (analysis.project.documents && analysis.project.documents.length > 0) {
            docListHtml = '<ul class="list-group list-group-flush">' +
                analysis.project.documents.map(function(d) {
                    return '<li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 border-bottom">' +
                        '<span><i class="fe fe-file-text me-2 text-primary"></i>' + escHtml(d.type || 'Document') + '</span>' +
                        '<a href="/documents/secure/project/' + d.id + '/view" class="btn btn-sm btn-outline-primary" title="Voir"><i class="fe fe-eye"></i></a>' +
                    '</li>';
                }).join('') +
            '</ul>';
        } else {
            docListHtml = '<p class="text-muted">Aucun document</p>';
        }

        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-none border">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">Informations Financières & Projet</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-6">
                                    <small class="text-muted d-block">Montant Demandé</small>
                                    <span class="h4 fw-bold">${formatMoney(analysis.project.montant_demande)}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Secteur d'Activité</small>
                                    <span class="badge bg-primary-transparent text-primary">${analysis.project.secteur}</span>
                                </div>
                            </div>
                            <h6>Description du projet</h6>
                            <p class="text-muted">${analysis.project.description || 'N/A'}</p>

                            <h6 class="mt-4">Documents Attachés</h6>
                            ${docListHtml}
                        </div>
                    </div>

                    <div class="card shadow-none border mt-3">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">Historique des Décisions</h6>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                ${analysis.histories.map(h => `
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <strong>${h.action.toUpperCase()}</strong>
                                            <small class="text-muted">${new Date(h.created_at).toLocaleString()}</small>
                                        </div>
                                        <div class="small">${h.details}</div>
                                        <div class="text-muted fs-11 mt-1">Par: ${h.auteur}</div>
                                    </li>
                                `).join('')}
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-none border bg-primary-transparent">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Récapitulatif des Scores</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Risque:</span>
                                <span class="fw-bold text-${getRiskColor(analysis.risk_score)}">${analysis.risk_score}/100</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Crédibilité:</span>
                                <span class="fw-bold">${analysis.score_credibilite}/100</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Solvabilité:</span>
                                <span class="fw-bold">${analysis.score_solvabilite}/100</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="h6 mb-0">Note Globale:</span>
                                <span class="h6 mb-0 fw-bold text-primary">${analysis.note_globale}/100</span>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-none border mt-3">
                        <div class="card-header bg-info-transparent">
                            <h6 class="card-title mb-0">Recommandation Système</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0 fw-semibold">${analysis.recommandation || 'En attente d\'analyse complète.'}</p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label">Ajouter un commentaire / Décision</label>
                        <textarea class="form-control" id="decision-comment" rows="4" placeholder="Justifiez votre décision..."></textarea>
                    </div>
                </div>
            </div>
        `;

        // Set actions based on status
        const isApproved = analysis.statut === 'approuve';
        document.getElementById('btn-approve').style.display = isApproved ? 'none' : '';
        document.getElementById('btn-reject').style.display = isApproved ? 'none' : '';
        document.getElementById('btn-request-info').style.display = isApproved ? 'none' : '';
        document.getElementById('btn-schedule-interview').style.display = isApproved ? 'none' : '';

        let financeBtn = document.getElementById('btn-propose-funding');
        if (!financeBtn) {
            financeBtn = document.createElement('button');
            financeBtn.type = 'button';
            financeBtn.className = 'btn btn-success';
            financeBtn.id = 'btn-propose-funding';
            financeBtn.innerHTML = '<i class="fe fe-briefcase"></i> Proposer un financement';
            document.getElementById('analysis-actions-left').appendChild(financeBtn);
        }
        financeBtn.style.display = (isApproved && analysis.peut_etre_finance !== false) ? '' : 'none';
        financeBtn.onclick = () => {
            bootstrap.Modal.getInstance(document.getElementById('modal-analysis-details')).hide();
            window.location.href = `financement.php?project_id=${analysis.project.id}`;
        };

        document.getElementById('btn-approve').onclick = () => handleDecision(id, 'approve');
        document.getElementById('btn-reject').onclick = () => handleDecision(id, 'reject');
        document.getElementById('btn-request-info').onclick = () => handleDecision(id, 'request-info');
        document.getElementById('btn-schedule-interview').onclick = async () => {
            const modalEl = document.getElementById('modal-analysis-details');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            const { value: date } = await Swal.fire({
                title: 'Planifier un entretien',
                input: 'text',
                inputPlaceholder: 'Date et heure (ex: 2024-06-15 14:00)',
                showCancelButton: true,
                cancelButtonText: 'Annuler',
                confirmButtonText: 'Planifier',
                reverseButtons: true,
            });
            if (date) handleDecision(id, 'schedule-interview', date);
        };

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-analysis-details'));
        modal.show();
    } catch (error) {
        console.error('Error fetching details:', error);
    }
}

/**
 * Gère les décisions d'approbation/rejet/etc
 */
async function handleDecision(id, action, additionalData = null) {
    const modalEl = document.getElementById('modal-analysis-details');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    const comment = document.getElementById('decision-comment')?.value || '';
    const actionLabels = { approve: 'approuver', reject: 'rejeter', 'request-info': "demander d'info", 'schedule-interview': 'planifier' };
    const { isConfirmed } = await ALOGOTO.confirm(
        'Confirmer l\'action',
        `Êtes-vous sûr de vouloir ${actionLabels[action] || 'effectuer'} cette action ?`,
        'Oui, confirmer',
        'Annuler'
    );
    if (!isConfirmed) return;

    try {
        let body = { commentaire: comment };
        if (action === 'schedule-interview') body.date = additionalData;

        const response = await fetch(`${API_BASE}/institution/analyses/${id}/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(body)
        });

        const result = await response.json();
        if (response.ok) {
            ALOGOTO.success('Action effectuée avec succès.');
            fetchAnalyses(currentPage);
            fetchStats();
        } else {
            ALOGOTO.error(result.message || 'Erreur lors de l\'action.');
        }
    } catch (error) {
        console.error('Error handling decision:', error);
    }
}

function openDiscussion(projectId) {
    window.location.href = `messages.php?project_id=${projectId}`;
}

// Helpers
function formatMoney(amount) {
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF', minimumFractionDigits: 0 }).format(amount);
}

function getRiskColor(score) {
    if (score < 30) return 'success';
    if (score < 60) return 'warning';
    return 'danger';
}

function getStatusBadge(statut) {
    const map = {
        'en_analyse': '<span class="badge bg-primary">En cours</span>',
        'approuve': '<span class="badge bg-success">Approuvé</span>',
        'rejete': '<span class="badge bg-danger">Rejeté</span>',
        'info_demandee': '<span class="badge bg-info">Infos demandées</span>',
        'entretien_planifie': '<span class="badge bg-warning">Entretien planifié</span>'
    };
    return map[statut] || `<span class="badge bg-secondary">${statut}</span>`;
}
