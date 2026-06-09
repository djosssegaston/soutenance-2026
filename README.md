# ALOGOTO — Plateforme de Crowdfunding / Microfinance

Projet de soutenance 2026. Plateforme connectant des **porteurs de projets** à des **institutions financières (IMF)** au Bénin et dans l'espace OHADA.

---

## Architecture

```
alogoto2/
├── backend/          → Laravel 12 (API + Vues Blade + Reverb)
├── frontend/         → PHP statique (pages publiques + dashboards legacy)
│   ├── dashboard/    → Dashboards legacy (35+ pages PHP)
│   ├── dashboard02/  → Dashboards redesign (HTML templates par rôle)
│   ├── public/       → Site vitrine public
│   └── includes/     → Helpers partagés
├── documentation/    → Site de documentation statique
├── storage/          → Sauvegardes SQL
├── ACCES_DASHBOARDS.md
├── FRONTEND_RESTRUCTURING.md
└── README.md
```

---

## Stack Technique

### Backend (Laravel 12)
| Technologie | Version |
|-------------|---------|
| PHP | ^8.2 (runtime 8.4.20) |
| Laravel Framework | ^12.0 |
| Base de données | MySQL / MariaDB |
| Cache / Queue / Session | driver `database` |
| Broadcasting temps réel | Laravel Reverb ^1.10 |
| Auth API | Laravel Sanctum |
| Auth Sociale | Laravel Socialite ^5.27 |
| Paiement | FedaPay ^0.4.8 |
| PDF | barryvdh/laravel-dompdf ^3.1 |
| Build front | Vite 7 + Tailwind CSS 4 + Axios |
| Tests | PHPUnit 11, Laravel Dusk/Pail/Boost |

### Frontend (PHP Statique)
| Technologie | Usage |
|-------------|-------|
| Bootstrap 5 (CDN) | Framework CSS |
| Chart.js, ApexCharts, NVD3 | Graphiques |
| jQuery, SweetAlert2, Summernote | Interactions |
| Font Awesome, Bootstrap Icons | Icônes |

---

## Utilisateurs & Rôles

| Rôle | Identifiant | Mot de passe | Description |
|------|-------------|--------------|-------------|
| **Admin** | `admin@alogoto.bj` | `Admin2026` | Super-utilisateur : valide les projets, gère utilisateurs, paramètres, audit |
| **Porteur** | `porteur1@alogoto.bj` | `Porteur2026` | Crée et soumet des projets, suit financements, rembourse |
| **Institution** | `institution1@alogoto.bj` | `Institution2026` | Parcourt les projets, analyse les risques, finance, suit le portfolio |

---

## Structure Backend

```
backend/
├── app/
│   ├── Enums/               → 3 enums (ProjectStatus, FundingStatus, EcheanceStatus)
│   ├── Events/              → 4 events (FundingReceived, NewNotification, etc.)
│   ├── Listeners/           → 1 listener (SendRepaymentNotifications)
│   ├── Notifications/       → 1 notification (RepaymentRecordedNotification)
│   ├── Policies/            → 1 policy (ProjectPolicy)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/         → 45 contrôleurs API (Admin*, Institution*, Porteur*)
│   │   │   └── ...          → 15 contrôleurs Web (Auth, Dashboard, Payment, etc.)
│   │   └── Middleware/
│   │       ├── RoleMiddleware.php
│   │       └── VerifyCsrfToken.php
│   ├── Models/              → 40 modèles Eloquent
│   └── Services/            → 14 services métier
├── database/
│   ├── migrations/          → 80+ migrations
│   ├── seeders/             → 15 seeders
│   └── factories/           → 15 factories
├── routes/
│   ├── api.php              → Routes API versionnées (v1)
│   ├── web.php              → Routes Web (auth, dashboards, documents)
│   ├── channels.php         → Canaux broadcasting (3 channels)
│   └── console.php          → Commandes console
├── config/                  → 15 fichiers de configuration
├── resources/views/         → 25+ vues Blade (auth, dashboard, payment, documents)
└── tests/                   → Tests Feature + Unit
```

---

## Modèle de Données (40 modèles)

### Entités Principales
```
User (porteur/admin)
  ├── Institution (profil institutionnel lié à un user)
  ├── Project (projets créés par le porteur)
  │   ├── ProjectDocument (documents du projet)
  │   ├── ProjectValidation (validations admin)
  │   ├── ProjectStatusHistory (historique des statuts)
  │   ├── ProjectComment (commentaires)
  │   ├── InstitutionAnalysis (analyses par les institutions)
  │   ├── Funding (financements)
  │   │   ├── Echeance (échéancier de remboursement)
  │   │   ├── FinancementHistory (historique)
  │   │   └── FinancementDocument (documents)
  │   ├── Repayment (remboursements)
  │   │   ├── RepaymentConfirmation
  │   │   ├── RepaymentDispute
  │   │   └── RepaymentEvent
  │   └── Echeance (échéances)
  ├── Conversation (messagerie)
  │   ├── ConversationParticipant
  │   └── Message
  │       ├── MessageAttachment
  │       └── MessageAudit
  ├── Interview (entretiens)
  │   ├── InterviewHistory
  │   └── RendezVous
  └── UserNotification
```

### Entités Support
```
KycDocument          → Documents KYC du porteur
AuditLog             → Journalisation des actions
Dispute              → Litiges sur remboursements
Setting              → Paramètres système (clé → valeur)
Secteur              → Secteurs d'activité
DocumentRule         → Règles documentaires par rôle
Transaction          → Transactions FedaPay
RealtimeNotification → Notifications temps réel
```

### Géographie (relations en hiérarchie)
```
Pay → Departement → Commune → Arrondissement → Quartier
```

### Entités de Messagerie
```
RealtimeNotification → notifications push Laravel Reverb
MessageAudit         → audit des messages modifiés/supprimés
```

---

## Routes API (v1) — ~150 endpoints

Toutes les routes sont préfixées par `/api/v1`.

### Authentification
| Méthode | Path | Description |
|---------|------|-------------|
| POST | `/auth/register` | Inscription porteur |
| POST | `/auth/login` | Connexion |
| POST | `/auth/logout` | Déconnexion |
| GET | `/auth/me` | Utilisateur courant |
| POST | `/auth/resend-verification` | Renvoyer email vérification |
| GET | `/auth/verify-email/{id}/{hash}` | Vérifier email (signed) |

### Profil
| Méthode | Path | Description |
|---------|------|-------------|
| GET | `/profile` | Voir profil |
| PUT | `/profile/update` | Modifier profil |
| PUT | `/profile/password` | Changer mot de passe |
| POST | `/profile/avatar` | Upload avatar |

### Projets
| Méthode | Path | Description |
|---------|------|-------------|
| GET/POST | `/projects` | Lister / Créer projets |
| GET/PUT/DELETE | `/projects/{id}` | Voir / Modifier / Supprimer |
| POST | `/projects/{id}/submit` | Soumettre projet |
| POST | `/projects/{id}/admin-validate` | Valider par admin (admin) |
| POST | `/projects/{id}/admin-validate` | Valider par admin (admin) |
| POST | `/projects/{id}/institution-analyze` | Analyser par institution |
| POST | `/projects/{id}/institution-accept` | Accepter par institution |

### Dashboard (tous rôles)
| Méthode | Path | Rôle |
|---------|------|------|
| GET | `/dashboard/admin/stats` | admin |
| GET | `/dashboard/admin/recent-projects` | admin |
| GET | `/dashboard/institution/stats` | institution |
| GET | `/dashboard/institution/projects` | institution |
| GET | `/dashboard/institution/portfolio` | institution |
| GET | `/dashboard/porteur/kpis` | porteur |
| GET | `/dashboard/porteur/projects` | porteur |
| GET | `/dashboard/porteur/next-repayment` | porteur |
| GET | `/dashboard/counts` | tous |

### Admin (préfixé `/admin/`)
| Groupe | Endpoints |
|--------|-----------|
| Dashboard | `GET /`, `/charts`, `/alerts`, `/activity`, `/system`, `/recent-projects` |
| Projects | `GET /`, `/statistics`, `/alerts`, `/{id}`, `/validate`, `/reject`, `/suspend` |
| Echeances | `GET /`, `/projects`, `/project/{id}` |
| Remboursements | `GET /` |
| Financements | `GET /`, `/statistics`, `/{id}` |
| Audit Logs | `GET /` |
| Users | `GET /`, `/statistics`, `/{id}`, `/{id}/{action}` |
| Messages | `/conversations`, `/start`, `/{id}`, `/{id}/reply`, etc. |
| Security | `GET /`, `POST /` |
| Settings | `GET /`, `POST /` |
| Secteurs | CRUD complet `GET/POST/PUT/DELETE /{secteur}` |
| Document Rules | CRUD complet |

### Institution (préfixé `/institution/`)
| Groupe | Endpoints |
|--------|-----------|
| Projets | `GET /projets`, `/statistiques`, `/filtres`, `/{id}`, `/{id}/analyse` |
| Analyses | `GET /analyses`, `/stats`, `/{id}`, `/approve`, `/reject`, etc. |
| Entretiens | `GET /entretiens`, `/stats`, `/{id}`, `/store`, `/confirm`, `/cancel`, `/report` |
| Remboursements | `GET /remboursements`, `/statistiques`, `/project/{id}`, etc. |
| Financements | `GET /financements`, `/statistiques`, `/{id}/echeances`, `/store`, `/decaisser`, etc. |
| Notifications | `GET /notifications`, `/read`, `/read-all`, `/archive`, `/delete` |
| Profil | `GET /profile`, `/update`, `/photo`, `/history` |
| Sécurité | `GET /security`, `/password`, `/kill-session` |
| Messages | `GET /messages`, `/search`, `/start`, `/send`, `/upload`, etc. |
| Dashboard | `GET /dashboard`, `/charts`, `/alerts`, `/activity` |

### Porteur
| Méthode | Path |
|---------|------|
| GET | `/porteur/remboursements`, `/{repayment}`, `/historique` |
| POST | `/porteur/remboursements/{repayment}/payer` |
| GET | `/porteur/financements/`, `/propositions`, `/{funding}`, `/{funding}/echeances` |
| POST | `/porteur/financements/{funding}/soumettre-plan` |
| POST | `/porteur/echeances/{echeance}/payer` |

### Messagerie
| Méthode | Path |
|---------|------|
| GET | `/conversations` |
| GET | `/conversations/{id}/messages` |
| POST | `/messages` |
| PUT/DELETE | `/messages/{message}` |
| POST | `/messages/{message}/read` |

### Notifications
| Méthode | Path |
|---------|------|
| GET | `/notifications` |
| POST | `/notifications/{id}/read`, `/read-all` |
| POST | `/notifications/{id}/archive`, `/restore` |
| DELETE | `/notifications/{id}` |
| GET | `/notifications/unread-count` |

### Géographie
| Méthode | Path |
|---------|------|
| GET | `/locations/pays` |
| GET | `/locations/pays/{id}/departements` |
| GET | `/locations/departements/{id}/communes` |
| GET | `/locations/communes/{id}/arrondissements` |
| GET | `/locations/arrondissements/{id}/quartiers` |

### Rendez-vous
`GET/POST/PUT/DELETE /rendez-vous`, `/{id}/accept`, `/{id}/reject`

---

## State Machines

### ProjectStatus (20 états)
```
DRAFT → SUBMITTED → UNDER_ADMIN_REVIEW → ADMIN_VALIDATED → AVAILABLE_FOR_IMF
         ↓ rejeté                      →                      ↓
      ADMIN_REJECTED              CANCELLED/SUSPENDED   UNDER_INSTITUTION_REVIEW
                                                          ↓
                                              INTERVIEW_SCHEDULED
                                              INSTITUTION_ACCEPTED
                                                         ↓
                                              FINANCING_PENDING → FUNDED → ACTIVE → REPAID → CLOSED
                                                                                   → COMPLETED
```

### FundingStatus (10 états)
```
PENDING → PROPOSED → AWAITING_BORROWER_PLAN → AWAITING_IMF_VALIDATION → APPROVED → DISBURSED → ACTIVE → COMPLETED
                                                                                                   → DEFAULTED
```

### EcheanceStatus (5 états)
```
PENDING → UPCOMING → PAID
                   → OVERDUE
                   → PARTIAL
```

---

## Services Métier (14 services)

| Service | Rôle |
|---------|------|
| `ProjectWorkflowService` | Machine à états des projets, transitions validées |
| `FinancingWorkflowService` | Workflow de financement (proposition → décaissement) |
| `FundingService` | Gestion des financements, génération d'échéances |
| `FundingManagementService` | Gestion des plans de financement |
| `EcheanceService` | CRUD échéances, calculs d'intérêts |
| `RepaymentService` | Enregistrement des remboursements |
| `RepaymentMonitoringService` | Monitoring des remboursements, alertes retard |
| `RiskAnalysisService` | Scoring risque des projets |
| `InterviewService` | Gestion des entretiens institution/porteur |
| `ConversationService` | Logique de messagerie, permissions |
| `DashboardAnalyticsService` | KPIs et statistiques dashboards |
| `FedaPayService` | Intégration paiement FedaPay |
| `MonitoringService` | Supervision système |
| `ProjectMonitoringService` | Suivi de projets |

---

## Frontend Statique

### Structure des dashboards legacy (`frontend/dashboard/`)
```
dashboard/
├── components/          → Composants réutilisables PHP
│   ├── admin_data.php, institution_data.php, porteur_data.php
│   ├── admin_page_start.php, admin_page_end.php (+ institution, porteur)
│   ├── sidebar_admin.php, sidebar_institution.php, sidebar_porteur.php
│   ├── header.php
│   ├── dashboard_helpers.php, porteur_helpers.php
│   ├── stat_card.html, data_table.html, mobile_bottom_nav.html
│   └── messages_shared.php
├── assets/
│   ├── css/             → Styles dashboards
│   ├── js/              → Scripts (api.js, auth-guard.js, chart-manager.js, etc.)
│   └── data/            → Données statiques
├── dashboard_admin.php  → Dashboard admin
├── dashboard_porteur.php → Dashboard porteur
├── dashboard_institution.php → Dashboard institution
├── *.php                → 35 pages de fonctionnalités (projets, finance, etc.)
└── logout.php
```

### Pages par rôle (~12 pages chacun)

**Admin** : `projets_admin`, `utilisateurs`, `finance`, `litiges`, `audit_logs`, `messages_admin`, `notifications_admin`, `profil_admin`, `securite_admin`, `parametres`

**Porteur** : `mes_projets`, `creer_projet`, `remboursements`, `documents`, `financement`, `echeancier_remboursement`, `messages`, `notifications`, `profil`, `securite`

**Institution** : `parcourir_projets`, `analyse_risque`, `entretiens_planifies`, `portfolio`, `remboursements_institution`, `investissements`, `validation`, `messages_institution`, `notifications_institution`, `profil_institution`, `securite_institution`, `rapports`

### Dashboards redesign (`frontend/dashboard02/`)
Version redesign des dashboards avec templates HTML par rôle.

```
dashboard02/
├── admin/       → ~125 templates (index, pages, layouts)
├── institution/ → ~123 templates
├── porteur/     → ~125 templates
├── shared/      → Dashboard page generator PHP + CSS/JS communs
│   ├── css/
│   ├── js/
│   │   ├── alogoto-helpers.js  (toasts, confirmations, loader)
│   │   └── realtime-init.js
│   ├── dashboard_page.php
│   ├── header_meta.php
│   ├── modal-helper.php
│   └── switcher.php
├── auth_helpers.php
└── path_helpers.php
```

### Site public (`frontend/public/`)
Pages vitrine : `index`, `login`, `signup`, `about`, `contact`, `services`, `faq`, `team`, `privacy`, `404`.

### Helpers et URLs
- `frontend/includes/path_helpers.php` → Helpers de base (URLs projet)
- `dashboard_helpers.php` → `dashboard_escape()`, `dashboard_format_fcfa()`
- `porteur_helpers.php` → `porteur_escape()`, `porteur_format_fcfa()`
- `frontend/dashboard/assets/js/api.js` → Client API JS complet avec retry, refresh token, queue

---

## Événements & Notifications

### Events (4)
| Event | Déclencheur |
|-------|-------------|
| `FundingReceived` | Paiement FedaPay reçu |
| `NewNotification` | Nouvelle notification créée |
| `ProjectSubmitted` | Projet soumis par porteur |
| `RepaymentRecorded` | Remboursement enregistré |

### Listener (1)
- `SendRepaymentNotifications` → écoute `RepaymentRecorded`

### Notification (1)
- `RepaymentRecordedNotification` → email au porteur

### Broadcasting (Reverb)
| Channel | Description |
|---------|-------------|
| `App.Models.User.{id}` | Notifications privées utilisateur |
| `conversations.{conversationId}` | Messages temps réel |
| `notifications.{userId}` | Notifications temps réel |

---

## Middleware

| Middleware | Routes | Effet |
|------------|--------|-------|
| `auth:sanctum` | API protégées | Authentification par token Sanctum |
| `role:admin` | Routes admin | Vérifie rôle=`admin` |
| `role:institution` | Routes institution | Vérifie rôle=`institution` |
| `role:porteur` | Routes porteur | Vérifie rôle=`porteur` |
| `verified` | Routes web | Vérifie email verified |
| `signed` | Routes de vérification | URL signée |

Le middleware `role` est défini dans `app/Http/Middleware/RoleMiddleware.php` :
```php
// Vérifie que l'utilisateur authentifié a le bon rôle
if (auth()->user()->role !== $role) abort(403);
```

---

## Installation

### Prérequis
- PHP 8.2+ avec extensions `fileinfo`, `pdo`, `openssl`
- Composer
- Node.js 18+ et npm
- MySQL/MariaDB

### Setup rapide
```bash
# 1. Cloner le dépôt
git clone <url> alogoto2 && cd alogoto2/alogoto2

# 2. Configurer l'environnement
cp backend/.env.example backend/.env
# → Éditer backend/.env (BDD, mail, FedaPay, Reverb)

# 3. Installer dépendances PHP
cd backend && composer install

# 4. Générer la clé d'application
php artisan key:generate

# 5. Migrations + seeders
php artisan migrate --seed

# 6. Installer dépendances JS + build
npm install && npm run build

# 7. Lancer en dev
composer run dev
# ou séparément :
php artisan serve          # Laravel sur :8000
php artisan queue:listen   # Files d'attente
php artisan reverb:start   # WebSocket temps réel
```

### Frontend statique (optionnel)
```bash
# Servir les pages statiques
php -S localhost:8080 -t frontend/public
```

### Reset base de données
```bash
php artisan migrate:fresh --seed
```

---

## Broadcasting (Laravel Reverb)

Canaux configurés dans `routes/channels.php` :
```php
Broadcast::channel('App.Models.User.{id}', fn ($user, $id) => (int) $user->id === (int) $id);
Broadcast::channel('conversations.{id}', fn ($user, $id) => Conversation::find($id)?->canAccess($user->id));
Broadcast::channel('notifications.{userId}', fn ($user, $userId) => (int) $user->id === (int) $userId);
```

Lancer Reverb : `php artisan reverb:start` (port 8080 par défaut).

---

## Paiement FedaPay

- Mode sandbox (clés dans `.env` : `FEDAPAY_API_KEY`, `FEDAPAY_API_SECRET`)
- Webhook : `POST /fedapay/webhook` (route publique)
- Callbacks : `/payment/success`, `/payment/failed`
- Paiements initiés via `POST /payment/{project}`

---

## Tests

```bash
# Lancer tous les tests
php artisan test

# Tester un fichier spécifique
php artisan test --filter=WorkflowE2E

# Avec rapports compacts
php artisan test --compact
```

Tests existants (Feature) :
- `AdminSecteurDocumentTest`
- `ApiStabilizationTest`
- `FinancingWorkflowE2ETest`
- `WorkflowE2ETest`
- `ExampleTest`

---

## Seeders & Données de Test

### Ordre d'exécution
```php
LocationSeeder      → Géographie (pays, dépts, communes, arrondts, quartiers)
UsersSeeder         → 3 utilisateurs (admin, porteur, institution)
InstitutionsSeeder   → Profil institution
ProjectsSeeder       → Projets de test
DocumentsSeeder      → Documents attachés
FinancementsSeeder   → Financements + échéances
RemboursementsSeeder → Remboursements
NotificationsSeeder  → Notifications
MessagesSeeder       → Messages + conversations
AuditLogsSeeder      → Logs d'audit
DisputesSeeder       → Litiges
```

### Seeders additionnels
- `DefaultSecteursSeeder` → Secteurs d'activité par défaut
- `TestDataSeeder` → Données de test complètes
- `TestConversationsSeeder` → Conversations de test
- `LocationSeeder` + `EnsureAllCommunesHaveArrondissementsSeeder`

---

## Sécurité

- **Authentification** : Sanctum (API tokens) + sessions (Web)
- **CSRF** : Protection via VerifyCsrfToken
- **Rôles** : Middleware `role:admin|porteur|institution`
- **Vérification email** : Obligatoire pour accès aux dashboards
- **Rate limiting** : Login (5/min), Register (3/min), OTP (3/min)
- **Sessions** : Driver `database`, isolation par rôle
- **Documents sécurisés** : Routes protégées (`documents/secure/`)
- **Audit logs** : Journalisation de toutes les actions critiques
- **Password history** : Historique des mots de passe
- **Session management** : Kill session, liste des sessions actives
- **Reset password** : Liens signés, tokens

---

## Commandes Utiles

```bash
php artisan serve                    # Serveur Laravel
php artisan queue:listen             # Files d'attente
php artisan reverb:start             # WebSocket
php artisan migrate:fresh --seed     # Reset BDD + seed
npm run build                        # Build assets Vite
npm run dev                          # Dev Vite (HMR)
composer run dev                     # Tout-en-un (serveur + queue + vite)
vendor/bin/pint                      # Formatage code (Laravel Pint)
```

---

## Licence

Non spécifiée. Projet académique — Soutenance 2026.
