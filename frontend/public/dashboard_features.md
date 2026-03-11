# Fonctionnalités des dashboards ALOGOTO

## Vue d'ensemble

Les dashboards ALOGOTO couvrent trois rôles métier:

- Porteur de projet
- Institution financière
- Administrateur

Chaque page conserve la structure visuelle existante du projet:

- sidebar métier
- topbar avec recherche globale
- cartes KPI
- blocs graphiques
- tableaux responsives
- composants Bootstrap et classes CSS déjà présents dans le dashboard

## Couche UX commune

Les améliorations front-end communes sont portées par:

- `dashboard/assets/css/dashboard-enhancements.css`
- `dashboard/assets/js/dashboard-enhancements.js`

### Responsivité

- Toutes les grilles restent basées sur Bootstrap avec réorganisation naturelle en `col-12`, `col-md-*`, `col-lg-*` et `col-xl-*`.
- Les colonnes, cartes, wrappers de graphiques et tableaux reçoivent un `min-width: 0` pour éviter les débordements.
- Les lignes utilisant `dashboard-gap-16` et `dashboard-gap-20` obtiennent un espacement vertical uniforme.
- Sur mobile, les tableaux des dashboards passent en mode carte:
  chaque cellule affiche automatiquement son libellé via `data-label`.
- La couche d'amélioration évite le scroll horizontal inutile sur la zone de contenu.

### Recherche dynamique intelligente

- La recherche de la topbar fonctionne en temps réel sans rechargement.
- Les recherches de tableaux restent locales à leur tableau.
- La recherche est insensible à la casse.
- La recherche est insensible aux accents grâce à une normalisation du texte.
- Les suggestions apparaissent au fur et à mesure de la saisie.
- Chaque suggestion est cliquable.
- Un clic sur une suggestion déclenche:
  - un scroll doux vers l'élément concerné
  - un surlignement temporaire
  - la réouverture de la ligne si le résultat appartient à un tableau filtré

### Interactions jQuery

- Les filtres et suggestions sont pilotés via jQuery.
- Les tableaux sont filtrés en direct caractère par caractère.
- Les suggestions sont générées dynamiquement à partir du contenu présent dans la page.
- Les interactions sont sans rechargement de page.

### Skeleton loading

- Les cartes KPI, cartes de contenu, tableaux et blocs graphiques affichent un squelette de chargement à l'ouverture.
- Les squelettes reproduisent la structure visuelle du contenu final.
- Le retrait du squelette est progressif pour renforcer la perception de fluidité.

### Boutons d'action des tableaux

- Les actions dans les colonnes `Actions` sont converties en boutons compacts à icône uniquement.
- Les textes restent présents pour l'accessibilité via `aria-label` et `title`.
- Les tooltips Bootstrap s'affichent au survol, au-dessus du bouton.
- Les principales variantes reconnues:
  - voir / détail
  - modifier
  - valider
  - refuser
  - suspendre
  - supprimer

### Animations légères

- Hover discret sur les lignes de tableaux
- Hover discret sur les cartes
- Focus renforcé sur les champs de recherche
- Transition douce lors de l'apparition des suggestions
- Surbrillance temporaire sur le résultat ciblé

## Dashboard Porteur

### `dashboard/dashboard_porteur.php`

Rôle:
tableau de bord de synthèse du porteur.

Composants présents:

- cartes KPI sur les projets, financements, progression et remboursements
- tableau résumé des projets
- bloc `Prochain remboursement`
- graphique d'historique des remboursements
- timeline d'activité récente

Actions principales:

- consultation rapide des projets
- accès au détail du prochain remboursement
- lecture de l'activité récente

### `dashboard/mes_projets.php`

Rôle:
piloter le portefeuille de projets du porteur.

Composants présents:

- KPI portefeuille
- tableau des projets avec montants, statuts et dates
- cartes de projet détaillées avec progression

Actions principales:

- voir un projet
- modifier un projet

### `dashboard/creer_projet.php`

Rôle:
soumettre un nouveau projet au financement.

Composants présents:

- formulaire de création dans une card
- champs métier pour nom, secteur, description, montant, durée, localisation, image et documents
- validation visuelle Bootstrap

Actions principales:

- soumettre le projet
- compléter les pièces et informations clés

### `dashboard/documents.php`

Rôle:
gérer les documents liés aux projets.

Composants présents:

- tableau des documents
- statuts de validation
- zone d'upload

Actions principales:

- voir un document
- supprimer un document
- uploader un document

### `dashboard/financement.php`

Rôle:
suivre les financements reçus.

Composants présents:

- cartes résumé
- tableau des financements
- indicateurs sur total financé, investisseurs et progression

Actions principales:

- consulter les financements confirmés
- vérifier l'avancement global

### `dashboard/remboursements.php`

Rôle:
suivre l'historique des remboursements.

Composants présents:

- tableau d'historique
- badges `Payé`, `En attente`, `En retard`
- graphique d'évolution des remboursements

Actions principales:

- voir les échéances traitées
- identifier les remboursements en retard

### `dashboard/messages.php`

Rôle:
messagerie du porteur.

Composants présents:

- liste des conversations
- zone de discussion
- composition d'un message

Interlocuteurs:

- institutions financières
- administrateur

### `dashboard/notifications.php`

Rôle:
suivi des alertes du porteur.

Composants présents:

- liste ou timeline de notifications
- événements de paiement
- validation documentaire
- nouveau financement
- message reçu

### `dashboard/statistiques.php`

Rôle:
analyse de performance du porteur.

Composants présents:

- cartes KPI
- graphique de financement mensuel
- graphique de remboursements mensuels
- graphique d'évolution du projet

Actions principales:

- comparer les tendances
- suivre la progression globale

### `dashboard/profil.php`

Rôle:
consulter le profil du porteur.

Composants présents:

- informations d'identité
- email, téléphone, organisation
- photo ou avatar

Actions principales:

- modifier le profil

### `dashboard/securite.php`

Rôle:
gérer la sécurité du compte porteur.

Composants présents:

- changement de mot de passe
- activation 2FA en placeholder
- historique de connexions

Actions principales:

- mettre à jour le mot de passe
- consulter les dernières connexions

## Dashboard Institution

### `dashboard/dashboard_institution.php`

Rôle:
vue globale de l'institution financière.

Composants présents:

- KPI portefeuille
- graphique de performance
- tableau des projets suivis
- cartes de suivi des remboursements

### `dashboard/parcourir_projets.php`

Rôle:
explorer les projets ouverts au financement.

Composants présents:

- KPI de pipeline
- tableau des projets disponibles
- score de risque et statut
- cartes d'insight métier

Actions principales:

- voir le projet

### `dashboard/analyse_risque.php`

Rôle:
comparer les projets par risque et rendement.

Composants présents:

- tableau d'analyse de risque
- recommandations
- graphique de comparaison risque / rendement

Actions principales:

- analyser un dossier
- préparer une décision d'investissement

### `dashboard/validation.php`

Rôle:
arbitrer les projets soumis à validation.

Composants présents:

- tableau des projets à valider
- montant demandé
- score de risque
- statut de validation

Actions principales:

- valider
- refuser

### `dashboard/portfolio.php`

Rôle:
mesurer la qualité du portefeuille institutionnel.

Composants présents:

- KPI sur investissement total, ROI, nombre de projets financés et score portefeuille
- graphique de performance portefeuille

### `dashboard/investissements.php`

Rôle:
suivre les investissements réalisés.

Composants présents:

- tableau des investissements
- montant investi
- date
- ROI estimé
- statut

### `dashboard/rapports.php`

Rôle:
pilotage financier et reporting.

Composants présents:

- graphiques des investissements mensuels
- suivi ROI
- performance des projets
- bouton d'export placeholder

Actions principales:

- exporter un rapport

### `dashboard/remboursements_institution.php`

Rôle:
suivre les remboursements reçus par l'institution.

Composants présents:

- tableau des remboursements
- projet
- porteur
- montant
- date
- statut

### `dashboard/messages_institution.php`

Rôle:
messagerie institutionnelle.

Composants présents:

- conversations avec porteurs
- conversations avec administrateur
- zone de discussion

### `dashboard/notifications_institution.php`

Rôle:
centraliser les alertes institutionnelles.

Composants présents:

- notification de nouveau projet
- notification de remboursement reçu
- notification de message reçu

### `dashboard/profil_institution.php`

Rôle:
consulter les informations de l'institution.

Composants présents:

- nom de l'institution
- email
- téléphone
- adresse
- responsable

Actions principales:

- modifier le profil institution

## Dashboard Administrateur

### `dashboard/dashboard_admin.php`

Rôle:
supervision transverse de la plateforme.

Composants présents:

- KPI globaux
- graphique des financements et remboursements
- tableau de synthèse projets
- graphique de répartition sectorielle
- cartes de classement institutions

### `dashboard/utilisateurs.php`

Rôle:
administrer les comptes utilisateurs.

Composants présents:

- tableau des utilisateurs
- rôle, statut, date d'inscription

Actions principales:

- voir
- suspendre
- supprimer

### `dashboard/projets_admin.php`

Rôle:
gouverner les projets soumis à la plateforme.

Composants présents:

- tableau des projets
- porteur
- secteur
- montant
- statut

Actions principales:

- valider
- refuser
- voir détail

### `dashboard/finance.php`

Rôle:
vision financière globale de la plateforme.

Composants présents:

- KPI volume financé, remboursements, défauts et revenus plateforme
- graphique financier

### `dashboard/litiges.php`

Rôle:
suivre et traiter les litiges.

Composants présents:

- tableau des litiges
- type
- statut
- date

Actions principales:

- résoudre
- fermer

### `dashboard/audit_logs.php`

Rôle:
tracer l'activité du système.

Composants présents:

- historique des actions
- utilisateur
- date
- adresse IP

### `dashboard/securite_admin.php`

Rôle:
gérer les paramètres de sécurité de l'administration.

Composants présents:

- gestion des accès
- authentification
- sessions actives

### `dashboard/notifications_admin.php`

Rôle:
pilotage des notifications globales.

Composants présents:

- journal des notifications plateforme
- alertes globales
- états de diffusion

### `dashboard/parametres.php`

Rôle:
configurer la plateforme.

Composants présents:

- paramètres de frais
- seuils de financement
- gestion des secteurs

Actions principales:

- ajuster les paramètres métier de la plateforme

## Notes techniques

- Les sidebars métier restent inchangées dans leur structure.
- La topbar conserve son design mais embarque maintenant une recherche plus riche.
- Les tableaux héritent d'une logique responsive et d'une couche d'interaction commune.
- Les scripts du site vitrine ne sont pas touchés.
- Les améliorations front-end sont limitées à l'univers dashboard.
