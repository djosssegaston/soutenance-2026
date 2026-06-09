<?php
require_once __DIR__ . '/header.php';
use Illuminate\Support\Facades\Crypt;
$editProject = null;
$editMode = false;
$projectId = 0;
if (isset($_GET['id'])) {
    $raw = (string) $_GET['id'];
    // Essayer le décryptage d'abord (nouveau format sécurisé)
    try {
        $projectId = (int) Crypt::decryptString(strtr($raw, '-_', '+/'));
    } catch (\Exception $e) {
        // Fallback: ID numérique brut (ancien format)
        if (ctype_digit($raw)) {
            $projectId = (int) $raw;
        }
    }
}
if ($projectId > 0) {
    try {
        $editProject = \App\Models\Project::with(['documents'])->where('id', $projectId)->where('user_id', auth()->id())->first();
        if ($editProject) $editMode = true;
    } catch (\Exception $e) { $editProject = null; }
}
// Load sectors from DB
$secteursFromDb = [];
try { $secteursFromDb = \App\Models\Secteur::active()->ordered()->get(['id', 'nom']); } catch (\Exception $e) {}
// Load document rules from DB
$docRules = [];
try { $docRules = \App\Models\DocumentRule::active()->forRole('porteur')->forContext('project')->ordered()->get(['slug', 'label', 'description', 'obligatoire', 'types_mime']); } catch (\Exception $e) {}
?><!--{ app content start }-->
            <div class="main-content app-content mt-0">
                <div class="side-app">
                    <!--{ container start }-->
                    <div class="main-container container-fluid">
                        <!--{ PAGE HEADER START }-->
                        <div class="page-header">
                            <h1 class="page-title">CREER UN PROJET</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="javascript:void(0)">ACCEUIL</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">CREER UN PROJET</li>
                                </ol>
                            </div>
                        </div>
                        <!--{ PAGE HEADER END }-->
                        <div class="row">
                            <div class="col-12 col-lg-12">
                                <form id="project-form" enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <!-- { Product Information } start -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-tile mb-0"><strong>INFORMATION DU PROJET</strong></h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label" for="titre">Nom</label>
                                            <input type="text" class="form-control" id="titre"
                                                placeholder="Titre du projet" name="titre"
                                                aria-label="Titre du projet"
                                                value="<?= $editMode ? htmlspecialchars($editProject->titre ?? '', ENT_QUOTES, 'UTF-8') : '' ?>">
                                        </div>
                                        <!-- <div class="row mb-3">
                                            <div class="col"><label class="form-label"
                                                    for="ecommerce-product-sku">Secteur</label>
                                                <select class="form-control" id="ecommerce-product-sku"
                                                    placeholder="Ex: Agriculture" name="secteurprojet" aria-label="Product SKU">
                                                    <option value="">Sélectionnez un secteur</option>
                                                    <option value="agriculture">Agriculture</option>
                                                    <option value="industrie">Industrie</option>
                                                    <option value="services">Services</option>
                                                    <option value="informatique">Informatique</option>
                                                    <option value="commerce">Commerce</option>

                                                </select>
                                            </div> -->
                                           
                                        <div class="row">
                                            <div class="mb-3 col-6">
                                                <label class="form-label" for="secteur">Secteur</label>
                                                <select class="select2 form-select" data-placeholder="Secteur" name="secteur" id="secteur">
                                                    <option value="">Secteur</option>
                                                    <?php if (!empty($secteursFromDb)): ?>
                                                        <?php foreach ($secteursFromDb as $s): ?>
                                                            <option value="<?= htmlspecialchars($s->nom, ENT_QUOTES, 'UTF-8') ?>" <?= $editMode && $editProject->secteur === $s->nom ? 'selected' : '' ?>><?= htmlspecialchars($s->nom, ENT_QUOTES, 'UTF-8') ?></option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <option value="agriculture">Agriculture</option>
                                                        <option value="commerce">Commerce</option>
                                                        <option value="technologie">Technologie</option>
                                                        <option value="sante">Santé</option>
                                                        <option value="education">Éducation</option>
                                                        <option value="transport">Transport</option>
                                                        <option value="immobilier">Immobilier</option>
                                                        <option value="services">Services</option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3 col-6"><label class="form-label"
                                                    for="branche">Branche</label>
                                                <input type="text" class="form-control" id="branche"
                                                    placeholder="Ex: Agricole" name="branche"
                                                    aria-label="Branche du projet"
                                                    value="<?= $editMode ? htmlspecialchars($editProject->branche ?? '', ENT_QUOTES, 'UTF-8') : '' ?>">
                                            </div>
                                        </div>
                                         <div class="mb-3">
                                            <label class="form-label" for="localisation">Localisation</label>
                                            <input type="text" class="form-control" id="localisation"
                                                placeholder="Ville, Région, Pays, Quartier" name="localisation"
                                                aria-label="Localisation du projet"
                                                value="<?= $editMode ? htmlspecialchars($editProject->localisation ?? '', ENT_QUOTES, 'UTF-8') : '' ?>">
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-6">
                                                <label class="form-label" for="montant_demande">Montant Recherché (FCFA)</label>
                                                <input type="number" class="form-control" id="montant_demande"
                                                    placeholder="Ex: 1000000" name="montant_demande"
                                                    aria-label="Montant recherché"
                                                    value="<?= $editMode ? htmlspecialchars($editProject->montant_demande ?? '', ENT_QUOTES, 'UTF-8') : '' ?>">
                                            </div>
                                            
                                            <div class="mb-3 col-6"><label class="form-label"
                                                    for="duree">Durée du projet (en mois)</label>
                                                <input type="number" class="form-control" id="duree"
                                                    placeholder="Ex: 12" name="duree"
                                                    aria-label="Durée du projet"
                                                    value="<?= $editMode ? htmlspecialchars($editProject->duree ?? '', ENT_QUOTES, 'UTF-8') : '' ?>">
                                            </div>
                                        </div>
                                        <!-- { Description } -->
                                        <div>
                                            <label class="form-label">Description <span
                                                    class="text-muted">(Obligatoire)</span></label>
                                            <div class="form-control p-0 pt-1">
                                                <div id="summernote">
                                                    <p>Description du projet</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <!-- { Product Information } end -->
                                <!-- { Media } -->
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 card-title">Fichiers et Autres</h5>
                                        <a href="javascript:void(0);" class="fw-medium" id="project-upload-shortcut">Ajouter un document ou une image</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                                            <div>
                                                <p class="mb-1 fw-semibold">Un seul espace d'upload pour envoyer les pièces une par une.</p>
                                                <small class="text-muted">Sélection fluide, suivi compact et navigation simple entre les documents.</small>
                                            </div>
                                            <div class="text-md-end">
                                                <span id="project-required-count" class="badge bg-primary-transparent text-primary me-2 mb-1">Obligatoires 0/0</span>
                                                <span id="project-optional-count" class="badge bg-light text-dark mb-1">Facultatives 0/0</span>
                                            </div>
                                        </div>

                                        <div class="project-current-card border rounded-3 p-3 mb-3">
                                            <div class="row g-3 align-items-end">
                                                <div class="col-12 col-lg-7">
                                                    <p class="text-muted mb-1">Document en cours</p>
                                                    <h6 class="mb-1" id="project-current-title">Pièce d'identité valide</h6>
                                                    <p class="text-muted mb-2" id="project-current-description"></p>
                                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                                        <span id="project-current-badge" class="badge bg-primary-transparent text-primary">Obligatoire</span>
                                                        <small class="text-muted" id="project-current-formats">Formats acceptés</small>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-lg-5">
                                                    <label for="project-document-selector" class="form-label mb-1">Choisir un document</label>
                                                    <select id="project-document-selector" class="form-select">
                                                        <option value="">Sélectionnez un document</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="project-upload-zone" class="project-upload-zone text-center rounded-3 p-4">
                                            <div class="mb-3">
                                                <i class="bx bx-cloud-upload fs-1 text-primary"></i>
                                            </div>
                                            <p class="fs-4 note mb-2">Glissez-déposez le fichier ici</p>
                                            <small class="text-muted d-block fs-6 mb-3">ou</small>
                                            <button type="button" class="btn bg-primary text-white" id="project-upload-browse">Explorer</button>
                                            <input type="file" id="project-upload-input" class="d-none">
                                        </div>

                                        <div id="project-upload-notice" class="mt-3"></div>
                                        <div id="project-upload-preview" class="mt-3"></div>

                                        <div class="d-flex justify-content-end mt-3">
                                            <button type="button" class="btn btn-primary d-none" id="project-next-button">Document suivant</button>
                                        </div>

                                        <div class="progress progress-sm mt-3">
                                            <div id="project-upload-progress" class="progress-bar bg-primary" role="progressbar" style="width: 0%;"></div>
                                        </div>

                                        <div class="project-status-panel border rounded-3 p-3 mt-4">
                                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                                                <div>
                                                    <h6 class="mb-1">Suivi des documents</h6>
                                                    <small class="text-muted">Vue compacte des pièces déjà ajoutées et de celles encore attendues.</small>
                                                </div>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-primary project-filter-btn active" data-list-filter="required">Obligatoires</button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary project-filter-btn" data-list-filter="optional">Facultatives</button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary project-filter-btn" data-list-filter="all">Toutes</button>
                                                </div>
                                            </div>
                                            <div class="project-upload-list-wrap">
                                                <div id="project-upload-list"></div>
                                            </div>
                                        </div>
                                        <div id="project-upload-storage" class="d-none"></div>
                                    </div>
                                </div>
                                <!-- { Media } end -->
                                <!-- { Variants } -->
                            
                                
                                <!-- { Inventory } -->
                               
                                </div>
                            </div>
                            <div class="col-12 col-lg-12">
                                <!-- { Pricing } -->
                                <!-- <div class="card mb-4"> -->
                                    <!-- <div class="card-header">
                                        <h5 class="card-title mb-0">BUDGET ET PERIODE </h5>
                                    </div> -->
                                    <!-- <div class="card-body"> -->
                                        <!-- { Base Prix } -->
                                        <!-- <div class="mb-3">
                                            <label class="form-label" for="ecommerce-product-price">Montant Récherché (FCFA)</label>
                                            <input type="number" class="form-control" id="ecommerce-product-price"
                                                placeholder="Ex:100 000" name="budgetprojet" aria-label="Product price">
                                        </div> -->
                                        <!-- { Discounted Prix } -->
                                        <!-- <div class="mb-3">
                                            <label class="form-label" for="ecommerce-product-discount-price">Durée du projet (en mois)
                                            </label>
                                            <input type="number" class="form-control"
                                                id="ecommerce-product-discount-price" placeholder="Ex: 12"
                                                name="dureeprojet" aria-label="Product discounted price">
                                        </div> -->
                                        <!-- { Charge tax check box } -->
                                        <!-- <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="price-charge-tax" checked="">
                                            <label class="form-label" for="price-charge-tax">
                                                Charge tax on this product
                                            </label>
                                        </div> -->
                                        <!-- { Instock switch } -->
                                        <!-- <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                            <span class="mb-0 h6">In stock</span>
                                            <div class="w-25 d-flex justify-content-end">
                                                <label class="switch switch-primary switch-sm me-4 pe-2">
                                                    <input type="checkbox" class="switch-input" checked="">
                                                    <span class="switch-toggle-slider">
                                                        <span class="switch-on">
                                                            <span class="switch-off"></span>
                                                        </span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div> -->
                                    <!-- </div> -->
                                <!-- </div> -->
                                <!-- { Pricing } end -->
                                <!-- { Organize } -->
                                <div class="card mb-4">
                                    <!-- <div class="card-header">
                                        <h5 class="card-title mb-0">Organize</h5>
                                    </div> -->
                                    <div class="card-body">
                                        <!-- <div class="mb-3 col">
                                            <label class="form-label mb-1" for="vendor">Vendor</label>
                                            <select id="vendor" class="select2 form-select"
                                                data-placeholder="Select Vendor">
                                                <option value="">Select Vendor</option>
                                                <option value="men-clothing">Men's Clothing</option>
                                                <option value="women-clothing">Women's-clothing</option>
                                                <option value="kid-clothing">Kid's-clothing</option>
                                            </select>
                                        </div>  -->
                                        <!-- <div class="mb-3 col">
                                            <label
                                                class="form-label mb-1 d-flex justify-content-between align-items-center"
                                                for="category-org">
                                                <span>Catégorie</span>
                                                <a href="javascript:void(0);" class="fw-medium">Ajouter new category</a>
                                            </label>
                                            <select id="category-org" class="select2 form-select"
                                                data-placeholder="Select Catégorie">
                                                <option value="">Select Catégorie</option>
                                                <option value="Household">Household</option>
                                                <option value="Management">Management</option>
                                                <option value="Electronics">Electronics</option>
                                                <option value="Office">Office</option>
                                                <option value="Automotive">Automotive</option>
                                            </select>
                                        </div>  -->
                                        <!-- <div class="mb-3 col">
                                            <label class="form-label mb-1" for="collection">Collection
                                            </label>
                                            <select id="collection" class="select2 form-select"
                                                data-placeholder="Collection">
                                                <option value="">Collection</option>
                                                <option value="men-clothing">Men's Clothing</option>
                                                <option value="women-clothing">Women's-clothing</option>
                                                <option value="kid-clothing">Kid's-clothing</option>
                                            </select>
                                        </div> -->
                                        <div class="mb-3 col-12">
                                            <label class="form-label mb-1" for="status-org">Statut
                                            </label>
                                            <select id="status-org" class="select2 form-select"
                                                data-placeholder="Statut" name="statut">
                                                <option value="draft">Brouillon</option>
                                                <option value="published">Publié</option>
                                            </select>
                                        </div>
                                        <!-- <div class="mb-3 col">
                                            <label for="ecommerce-product-tags" class="form-label mb-1">Tags</label>
                                            <select id="Tags-org" class="select2 form-select" data-placeholder="Tags"
                                                multiple>
                                                <option value="">Tags</option>
                                                <option value="Published">Published</option>
                                                <option value="Scheduled">Scheduled</option>
                                                <option value="Inactif">Inactif</option>
                                            </select>
                                        </div> -->
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-tile mb-0"></h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-content-center justify-content-center flex-wrap gap-3">
                                            <button type="button" class="btn btn-secondary" id="btn-annuler">Annuler</button>
                                            <button type="button" class="btn btn-primary-light" id="btn-sauvegarder">Sauvegarder</button>
                                            <button type="button" class="btn btn-success" id="btn-publier">Publier le projet</button>
                                        </div>
                                        <div id="project-form-feedback" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                    <!--{ container end }-->
                </div>
            </div>
            <!--{ app content end }-->
        </div>
    </div>

    
    <style>
        .project-current-card {
            background: var(--custom-white, #fff);
        }

        .project-status-panel {
            background: var(--custom-white, #fff);
        }

        .project-upload-zone {
            border: 2px dashed rgba(var(--bs-primary-rgb), 0.28);
            background: rgba(var(--bs-primary-rgb), 0.04);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .project-upload-zone:hover,
        .project-upload-zone.is-dragging {
            border-color: rgba(var(--bs-primary-rgb), 0.65);
            background: rgba(var(--bs-primary-rgb), 0.09);
        }

        .project-upload-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.15rem;
        }

        .project-upload-preview-line,
        .project-upload-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .project-upload-list-wrap {
            max-height: 260px;
            overflow-y: auto;
            padding-right: 0.25rem;
        }

        .project-upload-item {
            padding: 0.75rem 0;
            border-bottom: 1px dashed rgba(var(--bs-primary-rgb), 0.16);
            transition: color 0.2s ease;
        }

        .project-upload-item:last-child {
            border-bottom: none;
        }

        .project-upload-item.is-active {
            background: rgba(var(--bs-primary-rgb), 0.04);
            border-radius: 0.5rem;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .project-upload-item.is-done .project-upload-icon,
        .project-upload-item.is-done .project-upload-name,
        .project-upload-preview-line .project-upload-icon,
        .project-upload-preview-line .project-upload-name {
            color: var(--bs-success);
        }

        .project-upload-item.is-pending .project-upload-icon,
        .project-upload-item.is-pending .project-upload-name {
            color: var(--bs-danger);
        }

        .project-upload-meta {
            line-height: 1.35;
        }

        .project-filter-btn.active {
            background: var(--bs-primary);
            border-color: var(--bs-primary);
            color: #fff;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editMode = <?= json_encode($editMode) ?>;
            const editProjectId = <?= json_encode($projectId) ?>;
            const documents = <?= json_encode($docRules->map(fn($r) => [
                'id' => $r->slug,
                'label' => $r->label,
                'description' => $r->description ?? '',
                'required' => $r->obligatoire,
                'accept' => '.' . str_replace(',', ',.', $r->types_mime ?? 'pdf,jpg,jpeg,png,doc,docx'),
                'formats' => strtoupper(str_replace(',', ', ', $r->types_mime ?? 'PDF, JPG, JPEG, PNG, DOC, DOCX')),
                'icon' => $r->obligatoire ? 'bx-file' : 'bx-paperclip',
                'max_size' => (int) ($r->max_size ?? 10),
            ])->values()->toArray(), JSON_UNESCAPED_UNICODE) ?>;

            const existingDocs = <?= $editMode ? json_encode($editProject->documents->pluck('fichier', 'type')->toArray(), JSON_UNESCAPED_UNICODE) : '{}' ?>;

            const titleEl = document.getElementById('project-current-title');
            const descriptionEl = document.getElementById('project-current-description');
            const badgeEl = document.getElementById('project-current-badge');
            const formatsEl = document.getElementById('project-current-formats');
            const selectorEl = document.getElementById('project-document-selector');
            const requiredCountEl = document.getElementById('project-required-count');
            const optionalCountEl = document.getElementById('project-optional-count');
            const uploadZoneEl = document.getElementById('project-upload-zone');
            const browseBtnEl = document.getElementById('project-upload-browse');
            const shortcutEl = document.getElementById('project-upload-shortcut');
            const fileInputEl = document.getElementById('project-upload-input');
            const noticeEl = document.getElementById('project-upload-notice');
            const previewEl = document.getElementById('project-upload-preview');
            const nextButtonEl = document.getElementById('project-next-button');
            const progressEl = document.getElementById('project-upload-progress');
            const listEl = document.getElementById('project-upload-list');
            const storageEl = document.getElementById('project-upload-storage');
            const filterButtons = document.querySelectorAll('[data-list-filter]');

            if (!titleEl || !fileInputEl || !listEl || !selectorEl) {
                return;
            }

            const state = {
                activeIndex: 0,
                files: {},
                message: null,
                listFilter: 'required',
                pickerLock: false
            };

            function getDoc(index) {
                return documents[index] || null;
            }

            function getActiveDoc() {
                return getDoc(state.activeIndex);
            }

            function getPendingCount() {
                return documents.filter(function(doc) {
                    return !state.files[doc.id];
                }).length;
            }

            function getFirstPendingIndex() {
                const index = documents.findIndex(function(doc) {
                    return !state.files[doc.id];
                });

                return index === -1 ? documents.length - 1 : index;
            }

            function getNextPendingIndex(startIndex) {
                for (let index = startIndex + 1; index < documents.length; index++) {
                    if (!state.files[documents[index].id]) {
                        return index;
                    }
                }

                return -1;
            }

            function countUploaded(required) {
                return documents.filter(function(doc) {
                    return doc.required === required && state.files[doc.id];
                }).length;
            }

            function escapeHtml(value) {
                return String(value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function formatSize(bytes) {
                if (!bytes) {
                    return '0 Ko';
                }

                if (bytes < 1024) {
                    return bytes + ' octets';
                }

                if (bytes < 1024 * 1024) {
                    return (bytes / 1024).toFixed(1).replace('.0', '') + ' Ko';
                }

                return (bytes / (1024 * 1024)).toFixed(1).replace('.0', '') + ' Mo';
            }

            function acceptsFile(file, accept) {
                const extensions = accept.split(',').map(function(item) {
                    return item.trim().toLowerCase();
                });
                const fileName = file.name.toLowerCase();

                return extensions.some(function(extension) {
                    return fileName.endsWith(extension);
                });
            }

            function getHiddenInput(docId) {
                return storageEl.querySelector('[data-doc-id="' + docId + '"]');
            }

            function ensureHiddenInputs() {
                documents.forEach(function(doc) {
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.name = 'documents[' + doc.id + ']';
                    input.className = 'd-none';
                    input.setAttribute('data-doc-id', doc.id);
                    storageEl.appendChild(input);
                });
            }

            function populateDocumentSelector() {
                selectorEl.innerHTML = documents.map(function(doc, index) {
                    const prefix = doc.required ? 'Obligatoire' : 'Facultatif';
                    return '<option value="' + index + '">' + prefix + ' - ' + escapeHtml(doc.label) + '</option>';
                }).join('');
            }

            function setMessage(type, text) {
                state.message = { type: type, text: text };
            }

            function clearMessage() {
                state.message = null;
            }

            function openPicker() {
                const doc = getActiveDoc();

                if (!doc || state.pickerLock) {
                    return;
                }

                state.pickerLock = true;
                fileInputEl.accept = doc.accept;
                fileInputEl.click();
                window.setTimeout(function() {
                    state.pickerLock = false;
                }, 600);
            }

            function getFileSignature(file) {
                return file.name + '|' + file.size + '|' + (file.lastModified || '0');
            }

            function storeFile(doc, file) {
                const hiddenInput = getHiddenInput(doc.id);

                if (!acceptsFile(file, doc.accept)) {
                    fileInputEl.value = '';
                    setMessage('danger', 'Format non accepté pour "' + doc.label + '". Utilisez : ' + doc.formats + '.');
                    render();
                    return;
                }

                const signature = getFileSignature(file);
                for (const docId in state.files) {
                    if (state.files.hasOwnProperty(docId) && docId !== doc.id) {
                        const existingFile = state.files[docId];
                        if (existingFile && getFileSignature(existingFile) === signature) {
                            const conflictDoc = documents.find(function(d) { return d.id === docId; });
                            fileInputEl.value = '';
                            setMessage('danger', 'Ce fichier est déjà utilisé pour "' + (conflictDoc ? conflictDoc.label : docId) + '". Un même document ne peut pas être assigné à deux pièces différentes.');
                            render();
                            return;
                        }
                    }
                }

                state.files[doc.id] = file;

                if (hiddenInput && typeof DataTransfer !== 'undefined') {
                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    hiddenInput.files = transfer.files;
                }

                fileInputEl.value = '';
                setMessage('success', 'Fichier ajouté pour "' + doc.label + '".');
                render();
            }

            function renderNotice() {
                if (!state.message) {
                    noticeEl.innerHTML = '';
                    return;
                }

                const alertClass = state.message.type === 'success' ? 'alert-success' : 'alert-danger';
                noticeEl.innerHTML = '<div class="alert ' + alertClass + ' mb-0 py-2 px-3">' + escapeHtml(state.message.text) + '</div>';
            }

            function renderPreview(doc) {
                const file = state.files[doc.id];

                if (!file) {
                    previewEl.innerHTML = '';
                    return;
                }

                previewEl.innerHTML = `
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div class="project-upload-preview-line">
                            <i class="bx bx-check-circle project-upload-icon"></i>
                            <div>
                                <div class="project-upload-name fw-semibold">${escapeHtml(file.name)}</div>
                                <small class="text-muted">${formatSize(file.size)}</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="project-replace-button">Remplacer ce fichier</button>
                    </div>
                `;

                const replaceButton = document.getElementById('project-replace-button');

                if (replaceButton) {
                    replaceButton.addEventListener('click', function() {
                        openPicker();
                    });
                }
            }

            function renderList() {
                const activeDoc = getActiveDoc();
                const filteredDocuments = documents.filter(function(doc) {
                    if (state.listFilter === 'required') {
                        return doc.required;
                    }

                    if (state.listFilter === 'optional') {
                        return !doc.required;
                    }

                    return true;
                });

                listEl.innerHTML = filteredDocuments.map(function(doc) {
                    const file = state.files[doc.id];
                    const itemClass = [
                        'project-upload-item',
                        file ? 'is-done' : '',
                        file ? '' : 'is-pending',
                        activeDoc && doc.id === activeDoc.id ? 'is-active' : ''
                    ].join(' ').trim();
                    const iconClass = file ? 'bx-check-circle' : 'bx-x-circle';
                    const primaryText = file ? escapeHtml(file.name) : escapeHtml(doc.label);
                    const secondaryText = file
                        ? escapeHtml(doc.label) + ' • ' + formatSize(file.size)
                        : (doc.required ? 'Obligatoire • en attente' : 'Facultatif • en attente');

                    return `
                        <div class="${itemClass}">
                            <div class="project-upload-icon">
                                <i class="bx ${iconClass}"></i>
                            </div>
                            <div class="project-upload-meta flex-grow-1">
                                <div class="project-upload-name fw-semibold">${primaryText}</div>
                                <small class="text-muted">${secondaryText}</small>
                            </div>
                        </div>
                    `;
                }).join('');

                filterButtons.forEach(function(button) {
                    button.classList.toggle('active', button.getAttribute('data-list-filter') === state.listFilter);
                });
            }

            function render() {
                const doc = getActiveDoc();

                if (!doc) {
                    return;
                }

                const requiredTotal = documents.filter(function(item) {
                    return item.required;
                }).length;
                const optionalTotal = documents.length - requiredTotal;
                const requiredUploaded = countUploaded(true);
                const optionalUploaded = countUploaded(false);
                const progress = Math.round((Object.keys(state.files).length / documents.length) * 100);
                const nextPendingIndex = getNextPendingIndex(state.activeIndex);
                const currentHasFile = !!state.files[doc.id];

                titleEl.textContent = doc.label;
                descriptionEl.textContent = doc.description;
                badgeEl.className = 'badge ' + (doc.required ? 'bg-primary-transparent text-primary' : 'bg-light text-dark');
                badgeEl.textContent = doc.required ? 'Obligatoire' : 'Facultatif';
                formatsEl.textContent = 'Formats acceptés : ' + doc.formats;
                selectorEl.value = String(state.activeIndex);
                requiredCountEl.textContent = 'Obligatoires ' + requiredUploaded + '/' + requiredTotal;
                optionalCountEl.textContent = 'Facultatives ' + optionalUploaded + '/' + optionalTotal;
                progressEl.style.width = progress + '%';
                progressEl.setAttribute('aria-valuenow', progress);
                nextButtonEl.textContent = getPendingCount() > 0 ? 'Document suivant' : 'Tous les documents sont ajoutes';

                nextButtonEl.classList.toggle('d-none', !currentHasFile || nextPendingIndex === -1);
                renderNotice();
                renderPreview(doc);
                renderList();

                if (Object.keys(state.files).length === documents.length) {
                    setMessage('success', 'Tous les documents de la liste ont été ajoutés.');
                    renderNotice();
                    nextButtonEl.classList.add('d-none');
                }
            }

            ensureHiddenInputs();
            populateDocumentSelector();

            browseBtnEl.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                openPicker();
            });

            shortcutEl.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                openPicker();
            });

            selectorEl.addEventListener('change', function() {
                clearMessage();
                state.activeIndex = Number(selectorEl.value);
                render();
            });

            filterButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    state.listFilter = button.getAttribute('data-list-filter');
                    renderList();
                });
            });

            uploadZoneEl.addEventListener('click', function(event) {
                if (event.target.closest('#project-upload-browse')) {
                    return;
                }

                openPicker();
            });

            ['dragenter', 'dragover'].forEach(function(eventName) {
                uploadZoneEl.addEventListener(eventName, function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    uploadZoneEl.classList.add('is-dragging');
                });
            });

            ['dragleave', 'dragend', 'drop'].forEach(function(eventName) {
                uploadZoneEl.addEventListener(eventName, function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    uploadZoneEl.classList.remove('is-dragging');
                });
            });

            uploadZoneEl.addEventListener('drop', function(event) {
                const doc = getActiveDoc();
                const files = event.dataTransfer ? event.dataTransfer.files : null;

                if (!doc || !files || !files.length) {
                    return;
                }

                storeFile(doc, files[0]);
            });

            fileInputEl.addEventListener('change', function() {
                const doc = getActiveDoc();

                if (!doc || !fileInputEl.files || !fileInputEl.files.length) {
                    return;
                }

                storeFile(doc, fileInputEl.files[0]);
            });

            nextButtonEl.addEventListener('click', function() {
                const nextIndex = getNextPendingIndex(state.activeIndex);

                if (nextIndex === -1) {
                    return;
                }

                clearMessage();
                state.activeIndex = nextIndex;
                render();
            });

            // === Button handlers ===
            const btnAnnuler = document.getElementById('btn-annuler');
            const btnSauvegarder = document.getElementById('btn-sauvegarder');
            const btnPublier = document.getElementById('btn-publier');
            const statusSelect = document.getElementById('status-org');
            const formFeedback = document.getElementById('project-form-feedback');

            if (btnAnnuler && btnSauvegarder && btnPublier && formFeedback) {
                function showFeedback(type, message) {
                    formFeedback.innerHTML = '<div class="alert alert-' + type + ' mb-0">' + escapeHtml(message) + '</div>';
                }

                function clearFeedback() {
                    formFeedback.innerHTML = '';
                }

                function updatePublishButton() {
                    const isPublished = statusSelect && statusSelect.value === 'published';
                    btnPublier.disabled = !isPublished;
                    btnPublier.className = 'btn ' + (isPublished ? 'btn-success' : 'btn-secondary');
                }

                btnPublier.disabled = true;
                btnPublier.className = 'btn btn-secondary';

                if (statusSelect) {
                    statusSelect.addEventListener('change', updatePublishButton);
                }

                function collectFormData() {
                    const formData = new FormData();
                    const tokenInput = document.querySelector('input[name="_token"]');
                    formData.append('_token', tokenInput ? tokenInput.value : '');
                    formData.append('titre', (document.getElementById('titre').value || '').trim());
                    const secteurEl = document.querySelector('select[name="secteur"]');
                    formData.append('secteur', secteurEl ? secteurEl.value : '');
                    formData.append('branche', (document.getElementById('branche').value || '').trim());
                    formData.append('localisation', (document.getElementById('localisation').value || '').trim());
                    formData.append('montant_demande', (document.getElementById('montant_demande').value || '').trim());
                    formData.append('duree', (document.getElementById('duree').value || '').trim());

                    const summernoteEl = document.querySelector('#summernote');
                    let description = '';
                    if (summernoteEl) {
                        if (typeof $ !== 'undefined' && $.fn && $.fn.summernote) {
                            try { description = $(summernoteEl).summernote('code'); } catch (e) { description = summernoteEl.innerHTML; }
                        } else {
                            description = summernoteEl.innerHTML;
                        }
                    }
                    formData.append('description', description);

                    return formData;
                }

                function setButtonsLoading(loading) {
                    [btnSauvegarder, btnPublier].forEach(function(btn) {
                        btn.disabled = loading;
                        if (loading) {
                            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> En cours...';
                        } else {
                            btn.innerHTML = btn.id === 'btn-sauvegarder' ? 'Sauvegarder' : 'Publier le projet';
                            if (btn.id === 'btn-publier') updatePublishButton();
                        }
                    });
                }

                async function submitForm(status) {
                    const titre = (document.getElementById('titre').value || '').trim();
                    if (!titre) {
                        showFeedback('danger', 'Le titre du projet est obligatoire.');
                        document.getElementById('titre').focus();
                        return;
                    }

                    const requiredDocs = documents.filter(function(doc) { return doc.required; });
                    const missingRequired = requiredDocs.filter(function(doc) { return !state.files[doc.id]; });
                    if (missingRequired.length > 0) {
                        showFeedback('danger', 'Veuillez ajouter tous les documents obligatoires. Manquants: ' + missingRequired.map(function(d) { return d.label; }).join(', '));
                        return;
                    }

                    clearFeedback();
                    setButtonsLoading(true);

                    const formData = collectFormData();
                    formData.append('statut', status);
                    const tokenInput = document.querySelector('input[name="_token"]');
                    const csrfToken = tokenInput ? tokenInput.value : '';

                    try {
                        const isEdit = editMode && editProjectId > 0;
                        const endpoint = isEdit ? `/dashboard/porteur/projects/${editProjectId}` : '/dashboard/porteur/projects/store';
                        const method = isEdit ? 'PUT' : 'POST';

                        if (isEdit) {
                            formData.append('_method', 'PUT');
                        }

                        const response = await fetch(endpoint, {
                            method: method,
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: formData
                        });

                        let data;
                        const contentType = response.headers.get('content-type') || '';
                        if (contentType.includes('application/json')) {
                            data = await response.json();
                        } else {
                            const text = await response.text();
                            throw new Error('Réponse inattendue du serveur. Veuillez réessayer.');
                        }

                        if (!data.success) {
                            throw new Error(data.message || 'Erreur lors de la création du projet');
                        }

                        const projectId = isEdit ? editProjectId : data.project.id;
                        const uploadPromises = [];

                        if (Object.keys(state.files).length > 0) {
                            for (const docId of Object.keys(state.files)) {
                                const file = state.files[docId];
                                // Ignorer les documents existants (non modifiés)
                                if (file.existing) continue;
                                const docFormData = new FormData();
                                docFormData.append('_token', csrfToken);
                                docFormData.append('project_id', projectId);
                                docFormData.append('type', docId);
                                docFormData.append('fichier', file);

                                uploadPromises.push(
                                    fetch('/dashboard/porteur/projects/upload-document', {
                                        method: 'POST',
                                        headers: {
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': csrfToken
                                        },
                                        body: docFormData
                                    }).then(async function(r) {
                                        if (!r.ok) {
                                            let errMsg = 'Erreur lors de l\'upload du document.';
                                            try {
                                                const errData = await r.json();
                                                if (errData.errors) {
                                                    const msgs = [];
                                                    for (const field in errData.errors) {
                                                        msgs.push(errData.errors[field].join(' '));
                                                    }
                                                    if (msgs.length) errMsg = msgs.join(' ');
                                                } else if (errData.message) {
                                                    errMsg = errData.message;
                                                }
                                            } catch (e) {}
                                            throw new Error(errMsg);
                                        }
                                        return r.json();
                                    })
                                );
                            }
                        }

                        if (uploadPromises.length > 0) {
                            await Promise.all(uploadPromises);
                        }

                        if (typeof ALOGOTO !== 'undefined') {
                            ALOGOTO.success(status === 'published' ? 'Projet publié avec succès !' : 'Projet enregistré comme brouillon.');
                        }
                        showFeedback('success', status === 'published' ? 'Projet publié avec succès ! Redirection...' : 'Projet enregistré comme brouillon.');
                        setButtonsLoading(false);
                        btnSauvegarder.style.display = 'none';
                        btnPublier.style.display = 'none';
                        setTimeout(function() { window.location.href = 'mes_projets.php'; }, 1500);
                    } catch (error) {
                        console.error('Submit error:', error);
                        showFeedback('danger', error.message || 'Une erreur est survenue lors de la soumission.');
                        setButtonsLoading(false);
                    }
                }

                btnAnnuler.addEventListener('click', async function() {
                    const titre = (document.getElementById('titre').value || '').trim();
                    if (titre || Object.keys(state.files).length > 0) {
                        var confirmed = await ModalHelper.confirm('<i class="bi bi-arrow-left-circle me-2 text-warning"></i> Annuler', 'Voulez-vous vraiment annuler ? Les données non sauvegardées seront perdues.', 'Oui, annuler', 'Continuer', 'btn-warning');
                        if (!confirmed) return;
                    }
                    window.location.href = 'mes_projets.php';
                });

                btnSauvegarder.addEventListener('click', function() { submitForm('draft'); });

                btnPublier.addEventListener('click', function() {
                    if (!statusSelect || statusSelect.value !== 'published') {
                        showFeedback('danger', 'Veuillez sélectionner le statut "Publié" pour publier le projet.');
                        return;
                    }
                    submitForm('published');
                });
            }

            state.activeIndex = getFirstPendingIndex();
            render();

            <?php if ($editMode && $editProject): ?>
            (function() {
                const summernoteEl = document.querySelector('#summernote');
                if (summernoteEl && typeof $ !== 'undefined' && $.fn && $.fn.summernote) {
                    try { $(summernoteEl).summernote('code', <?= json_encode($editProject->description ?? '', JSON_UNESCAPED_UNICODE) ?>); } catch(e) { summernoteEl.innerHTML = <?= json_encode($editProject->description ?? '', JSON_UNESCAPED_UNICODE) ?>; }
                } else if (summernoteEl) {
                    summernoteEl.innerHTML = <?= json_encode($editProject->description ?? '', JSON_UNESCAPED_UNICODE) ?>;
                }

                const editLabel = document.querySelector('.page-title');
                if (editLabel) editLabel.textContent = 'MODIFIER LE PROJET';
                const editStatusOpt = document.getElementById('status-org');
                if (editStatusOpt) editStatusOpt.value = 'published';

                <?php if (!empty($editProject->documents)): foreach ($editProject->documents as $doc): ?>
                if (!state.files['<?= $doc->type ?>']) {
                    state.files['<?= $doc->type ?>'] = { name: '<?= addslashes(basename($doc->fichier)) ?>', size: 0, existing: true };
                }
                <?php endforeach; ?>
                render();
                <?php endif; ?>
            })();
            <?php endif; ?>
        });
    </script>

    <?php require_once __DIR__ . '/footer.php'; ?>
