/**
 * Projets Disponibles - Institution Dashboard
 */

const API_BASE = '/api/v1';
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    // Initial Load
    fetchStats();
    fetchFilters();
    fetchProjects();

    // Event Listeners
    document.getElementById('search-btn').addEventListener('click', () => {
        currentPage = 1;
        fetchProjects();
    });

    document.getElementById('search-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            currentPage = 1;
            fetchProjects();
        }
    });

    document.getElementById('filter-secteur').addEventListener('change', () => {
        currentPage = 1;
        fetchProjects();
    });

    document.getElementById('filter-ville').addEventListener('change', () => {
        currentPage = 1;
        fetchProjects();
    });

    document.getElementById('reset-filters').addEventListener('click', () => {
        document.getElementById('search-input').value = '';
        document.getElementById('filter-secteur').value = '';
        document.getElementById('filter-ville').value = '';
        document.getElementById('filter-montant-max').value = '';
        $('.select2').val(null).trigger('change');
        currentPage = 1;
        fetchProjects();
    });
});

/**
 * Récupère les statistiques globales
 */
async function fetchStats() {
    try {
        const response = await fetch(`${API_BASE}/institution/projets/statistiques`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        if (!response.ok) return;
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) return;

        const data = await response.json();

        document.getElementById('stat-total-projects').textContent = data.total_disponibles;
        document.getElementById('stat-total-amount').textContent = formatMoney(data.total_recherche);
        document.getElementById('stat-urgent-projects').textContent = data.projets_urgents;
        document.getElementById('stat-active-sectors').textContent = data.secteurs_actifs.length;
    } catch (error) {
        console.error('Error fetching stats:', error);
    }
}

/**
 * Récupère les filtres (secteurs, villes)
 */
async function fetchFilters() {
    try {
        const response = await fetch(`${API_BASE}/institution/projets/filtres`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        if (!response.ok) return;
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) return;

        const data = await response.json();

        const secteurSelect = document.getElementById('filter-secteur');
        const villeSelect = document.getElementById('filter-ville');

        data.secteurs.forEach(secteur => {
            const option = new Option(secteur, secteur);
            secteurSelect.add(option);
        });

        data.villes.forEach(ville => {
            const option = new Option(ville, ville);
            villeSelect.add(option);
        });

        if ($.fn.select2) {
            $('.select2').select2();
        }
    } catch (error) {
        console.error('Error fetching filters:', error);
    }
}

/**
 * Récupère la liste des projets
 */
async function fetchProjects(page = 1) {
    currentPage = page;
    const loader = document.getElementById('projects-loader');
    const container = document.getElementById('projects-list');
    
    if (loader) loader.style.display = 'block';
    
    const search = document.getElementById('search-input').value;
    const secteur = document.getElementById('filter-secteur').value;
    const ville = document.getElementById('filter-ville').value;
    const montantMax = document.getElementById('filter-montant-max').value;

    try {
        const response = await fetch(`${API_BASE}/institution/projets?page=${page}&search=${search}&secteur=${secteur}&ville=${ville}&montant_max=${montantMax}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            const text = await response.text();
            console.error('Non-JSON response received:', text);
            throw new Error(`Le serveur a renvoyé une réponse invalide (HTML au lieu de JSON).`);
        }

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `Erreur ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        renderProjects(data.data);
        renderPagination(data);
    } catch (error) {
        console.error('Error fetching projects:', error);
        container.innerHTML = `<div class="col-12 text-center text-danger">
            <i class="fe fe-alert-triangle fs-30 d-block mb-2"></i>
            ${error.message || 'Erreur lors du chargement des projets.'}
        </div>`;
    } finally {
        if (loader) loader.style.display = 'none';
    }
}

/**
 * Affiche les cartes de projets
 */
function renderProjects(projects) {
    const container = document.getElementById('projects-list');
    container.innerHTML = '';

    if (projects.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center py-5">
                <div class="mb-3">
                    <i class="fe fe-folder-minus fs-50 text-muted"></i>
                </div>
                <h5>Aucun projet disponible</h5>
                <p class="text-muted">Essayez de modifier vos filtres ou revenez plus tard.</p>
            </div>
        `;
        return;
    }

    projects.forEach(project => {
        const riskColor = getRiskColor(project.risk_score);
        const card = `
            <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <span class="avatar avatar-md bg-light text-primary rounded-circle me-3">
                                <i class="fe fe-package"></i>
                            </span>
                            <div>
                                <h6 class="mb-0 fw-bold fs-14">${project.titre}</h6>
                                <small class="text-muted">${project.porteur} • ${project.secteur}</small>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fs-12 text-muted">Financement</span>
                                <span class="fs-12 fw-bold text-dark">${project.progression}%</span>
                            </div>
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: ${project.progression}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="fs-11 text-muted">${formatMoney(project.montant_finance)}</span>
                                <span class="fs-11 fw-bold text-dark">${formatMoney(project.budget_total)}</span>
                            </div>
                        </div>
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <div class="p-2 border border-light rounded text-center bg-light">
                                    <small class="text-muted d-block mb-1">Risque</small>
                                    <span class="badge badge-soft-${riskColor}">${project.risk_score}/100</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border border-light rounded text-center bg-light">
                                    <small class="text-muted d-block mb-1">Viabilité</small>
                                    <span class="fw-bold text-dark fs-13">${project.viability}</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary-light" onclick="viewProjectDetails(${project.id})">
                                <i class="fe fe-eye me-1"></i> Voir Détails
                            </button>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary flex-fill" onclick="analyzeProject(${project.id})">
                                    <i class="fe fe-activity me-1"></i> Analyser
                                </button>
                                <button class="btn btn-outline-success flex-fill" onclick="openDiscussion(${project.id})">
                                    <i class="fe fe-message-circle"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        `;
        container.innerHTML += card;
    });
}

/**
 * Affiche la pagination
 */
function renderPagination(data) {
    const container = document.getElementById('pagination-container');
    if (!container) return;

    let html = '<ul class="pagination pagination-rounded">';
    
    // Précédent
    html += `
        <li class="page-item ${data.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="fetchProjects(${data.current_page - 1})">
                <i class="fe fe-chevron-left"></i>
            </a>
        </li>
    `;

    // Pages
    data.links.forEach(link => {
        if (link.url && !link.label.includes('Previous') && !link.label.includes('Next')) {
            html += `
                <li class="page-item ${link.active ? 'active' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="fetchProjects(${link.label})">${link.label}</a>
                </li>
            `;
        }
    });

    // Suivant
    html += `
        <li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="fetchProjects(${data.current_page + 1})">
                <i class="fe fe-chevron-right"></i>
            </a>
        </li>
    `;

    html += '</ul>';
    container.innerHTML = html;
}

/**
 * Voir les détails d'un projet
 */
async function viewProjectDetails(id) {
    try {
        const response = await fetch(`${API_BASE}/institution/projets/${id}`);
        
        if (!response.ok) {
            if (response.status === 403) {
                const errData = await response.json().catch(() => ({}));
                ModalHelper.alert(
                    '<i class="bi bi-exclamation-circle me-2 text-warning"></i> Projet Indisponible',
                    errData.message || 'Ce projet n\'est plus disponible. Il a peut-être été financé ou retiré par son porteur.'
                );
                fetchProjects(currentPage);
                return;
            }
            ALOGOTO.error('Erreur lors du chargement des détails.');
            return;
        }
        
        const project = await response.json();

        const modalBody = document.getElementById('modal-body');
        const modalTitle = document.getElementById('modal-title');
        
        modalTitle.textContent = project.titre;
        
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-7">
                    <h6>Description</h6>
                    <p class="text-muted">${project.description || 'Aucune description fournie.'}</p>
                    
                    <h6 class="mt-4">Documents Porteur</h6>
                    <ul class="list-group list-group-flush">
                        ${project.documents.map(doc => `
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <i class="fe fe-file-text me-2 text-primary"></i>
                                    <span>${doc.type || doc.nom || 'Document'}</span>
                                </div>
                                <a href="/documents/secure/project/${doc.id}/view" class="btn btn-sm btn-outline-primary" title="Voir"><i class="fe fe-eye"></i></a>
                            </li>
                        `).join('') || '<li class="list-group-item px-0 text-muted">Aucun document</li>'}
                    </ul>
                </div>
                <div class="col-md-5 border-start">
                    <h6>Analyse de Risque</h6>
                    <div class="p-3 bg-light rounded mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Score Risque:</span>
                            <span class="fw-bold text-${getRiskColor(project.analyse.risk_score)}">${project.analyse.risk_score}/100</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Crédibilité:</span>
                            <span class="fw-bold text-success">${project.analyse.credibility_score}/100</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Viabilité:</span>
                            <span class="fw-bold text-primary">${project.analyse.viability}</span>
                        </div>
                    </div>

                    <h6>Anomalies Détectées</h6>
                    ${project.analyse.anomalies.length > 0 ? `
                        <ul class="text-danger small ps-3">
                            ${project.analyse.anomalies.map(a => `<li>${a}</li>`).join('')}
                        </ul>
                    ` : '<p class="text-success small">Aucune anomalie critique détectée.</p>'}

                    <h6 class="mt-4">Finances</h6>
                    <div class="p-3 border rounded">
                        <div class="mb-2">
                            <small class="text-muted d-block">Montant Demandé</small>
                            <span class="h5 mb-0">${formatMoney(project.finances.montant_demande)}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Montant Déjà Financé</small>
                            <span class="fw-bold text-success">${formatMoney(project.finances.montant_finance)}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const analyzeBtn = document.getElementById('btn-analyze');
        analyzeBtn.onclick = () => analyzeProject(id);

        const discussBtn = document.getElementById('btn-discuss');
        discussBtn.onclick = () => openDiscussion(id);

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-project-details'));
        modal.show();
    } catch (error) {
        console.error('Error fetching project details:', error);
        ModalHelper.error('Erreur', 'Erreur lors du chargement des détails.');
    }
}

/**
 * Lancer l'analyse
 */
async function analyzeProject(id) {
    const modalEl = document.getElementById('modal-project-details');
    const modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    const { isConfirmed } = await ALOGOTO.confirm(
        'Lancer une analyse',
        'Souhaitez-vous lancer une analyse officielle sur ce projet ?',
        'Oui, lancer l\'analyse',
        'Annuler'
    );
    if (!isConfirmed) return;

    try {
        const response = await fetch(`${API_BASE}/institution/projets/${id}/analyse`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        });
        const data = await response.json();

        if (response.ok) {
            ALOGOTO.success('Analyse lancée avec succès. Le projet est maintenant dans votre pipeline d\'analyse.');
            fetchProjects(currentPage);
        } else {
            ALOGOTO.error(data.message || 'Erreur lors du lancement de l\'analyse.');
        }
    } catch (error) {
        console.error('Error analyzing project:', error);
        ALOGOTO.error('Une erreur est survenue.');
    }
}

/**
 * Ouvrir discussion
 */
function openDiscussion(projectId) {
    // Redirection vers le chat avec le projet sélectionné
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
