<?php
require_once __DIR__ . '/components/porteur_helpers.php';
$page_title = 'Creer un projet';
$page_subtitle = 'Constituez un dossier complet avant soumission aux institutions.';
$page_active_nav = 'create-project';
$page_document_title = 'Creer un projet - ALOGOTO';
$page_inline_styles = <<<'CSS'
    .porteur-checklist {
      list-style: none;
      padding: 0;
      margin: 0;
      display: grid;
      gap: 12px;
    }

    .porteur-checklist li {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      color: var(--text-heading-color);
    }

    .porteur-checklist i {
      color: var(--primary-color-1);
      margin-top: 2px;
    }

    .porteur-form-helper {
      font-size: 12px;
      color: var(--p-color);
    }
CSS;

include __DIR__ . '/components/porteur_page_start.php';
?>
        <div class="row dashboard-gap-20 mt-2">
          <div class="col-12 col-xl-8">
            <section class="dashboard-card content-card porteur-form-card">
              <div class="dashboard-card__head mb-3">
                <h6>Nouveau projet</h6>
              </div>

              <form class="row g-3" action="#" method="post" enctype="multipart/form-data">
                <div class="col-12 col-md-6">
                  <label class="form-label" for="projectName">Nom du projet</label>
                  <input type="text" class="form-control is-valid" id="projectName" value="Cooperative Cacao Premium" required />
                  <div class="valid-feedback">Nom de projet pret.</div>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label" for="projectSector">Secteur</label>
                  <select class="form-select is-valid" id="projectSector" required>
                    <option>Agriculture</option>
                    <option>Commerce</option>
                    <option>Technologie</option>
                    <option>Sante</option>
                  </select>
                  <div class="valid-feedback">Secteur selectionne.</div>
                </div>

                <div class="col-12">
                  <label class="form-label" for="projectDescription">Description</label>
                  <textarea class="form-control is-valid" id="projectDescription" required>Projet d extension de la capacite de transformation du cacao avec acquisition d equipements et structuration logistique.</textarea>
                  <div class="valid-feedback">Description bien detaillee.</div>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label" for="projectAmount">Montant recherche</label>
                  <input type="number" class="form-control is-valid" id="projectAmount" value="18000000" required />
                  <div class="form-text porteur-form-helper">Saisir le montant en FCFA.</div>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label" for="projectDuration">Duree du financement</label>
                  <select class="form-select is-valid" id="projectDuration" required>
                    <option>12 mois</option>
                    <option selected>18 mois</option>
                    <option>24 mois</option>
                    <option>36 mois</option>
                  </select>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label" for="projectLocation">Localisation</label>
                  <input type="text" class="form-control is-valid" id="projectLocation" value="Abidjan, CI" required />
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label" for="projectImage">Image du projet</label>
                  <input type="file" class="form-control is-valid" id="projectImage" />
                  <div class="valid-feedback">Visuel charge.</div>
                </div>

                <div class="col-12">
                  <label class="form-label" for="projectDocs">Documents associes</label>
                  <input type="file" class="form-control is-valid" id="projectDocs" multiple />
                  <div class="form-text porteur-form-helper">Business plan, documents legaux, previsionnels, photos terrain.</div>
                </div>

                <div class="col-12 pt-2">
                  <div class="porteur-action-group">
                    <button type="submit" class="dashboard-btn-primary btn-dashboard primary">
                      <i class="bi bi-send"></i>
                      <span>Soumettre le projet</span>
                    </button>
                    <button type="button" class="btn-dashboard outline">
                      <i class="bi bi-save"></i>
                      <span>Enregistrer en brouillon</span>
                    </button>
                  </div>
                </div>
              </form>
            </section>
          </div>

          <div class="col-12 col-xl-4">
            <section class="dashboard-card mb-4">
              <div class="dashboard-card__head mb-3">
                <h6>Checklist de soumission</h6>
              </div>
              <ul class="porteur-checklist">
                <li><i class="bi bi-check-circle-fill"></i><span>Resume executif complete</span></li>
                <li><i class="bi bi-check-circle-fill"></i><span>Projection de tresorerie sur 18 mois</span></li>
                <li><i class="bi bi-check-circle-fill"></i><span>Garanties et justificatifs juridiques</span></li>
                <li><i class="bi bi-check-circle-fill"></i><span>Plan d execution et calendrier de decaissement</span></li>
              </ul>
            </section>

            <section class="dashboard-card">
              <div class="dashboard-card__head mb-3">
                <h6>Apercu du dossier</h6>
              </div>
              <div class="porteur-soft-card">
                <p class="porteur-page-subtle mb-2">Score de completion</p>
                <h4 class="mb-2">82%</h4>
                <div class="dashboard-progress mb-3">
                  <div class="dashboard-progress__bar bg-warning-token" style="width: 82%;"></div>
                </div>
                <p class="porteur-page-subtle mb-0">Ajoutez une lettre d engagement fournisseur pour atteindre un dossier premium.</p>
              </div>
            </section>
          </div>
        </div>
<?php include __DIR__ . '/components/porteur_page_end.php'; ?>
