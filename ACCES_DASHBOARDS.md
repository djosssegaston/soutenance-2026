# 🚀 ALOGOTO2 - Accès aux Dashboards

## 📋 Identifiants de Connexion

### 🔴 Administrateur
- **URL**: http://localhost/alogoto2/alogoto2/backend/public/dashboard/admin
- **Email**: `admin@alogoto.bj`
- **Mot de passe**: `Admin2026`
- **Rôle**: admin
- **Fonctionnalités**:
  - Validation/rejet des projets
  - Gestion des utilisateurs
  - Audit et sécurité
  - Rapports financiers
  - Paramètres système

### 🔵 Porteur de Projet
- **URL**: http://localhost/alogoto2/alogoto2/backend/public/dashboard/porteur
- **Email**: `porteur1@alogoto.bj`
- **Mot de passe**: `Porteur2026`
- **Rôle**: porteur
- **Fonctionnalités**:
  - Créer et gérer des projets
  - Suivre le statut des projets
  - Effectuer des paiements (FedaPay)
  - Messagerie avec institutions
  - Notifications

### 🟢 Institution Financière
- **URL**: http://localhost/alogoto2/alogoto2/backend/public/dashboard/institution
- **Email**: `institution1@alogoto.bj`
- **Mot de passe**: `Institution2026`
- **Rôle**: institution
- **Fonctionnalités**:
  - Parcourir les projets disponibles
  - Analyser les projets (risk scoring)
  - Planifier des entretiens
  - Financer des projets
  - Suivre les remboursements
  - Portfolio d'investissements

---

## 🔗 Accès Rapide

### Page d'Accès Dashboards
Ouvrez ce fichier dans votre navigateur:
```
/var/www/html/alogoto2/alogoto2/access-dashboards.html
```

Ou via URL:
```
http://localhost/alogoto2/alogoto2/access-dashboards.html
```

---

## 📊 URLs des Dashboards

| Dashboard | URL |
|-----------|-----|
| **Admin** | http://localhost/alogoto2/alogoto2/backend/public/dashboard/admin |
| **Admin - Projets** | http://localhost/alogoto2/alogoto2/backend/public/dashboard/admin/projets |
| **Admin - Utilisateurs** | http://localhost/alogoto2/alogoto2/backend/public/dashboard/admin/utilisateurs |
| **Admin - Finance** | http://localhost/alogoto2/alogoto2/backend/public/dashboard/admin/finance |
| **Admin - Audit** | http://localhost/alogoto2/alogoto2/backend/public/dashboard/admin/audit |
| **Porteur** | http://localhost/alogoto2/alogoto2/backend/public/dashboard/porteur |
| **Porteur - Mes Projets** | http://localhost/alogoto2/alogoto2/backend/public/dashboard/porteur/mes-projets |
| **Porteur - Créer Projet** | http://localhost/alogoto2/alogoto2/backend/public/dashboard/porteur/creer-projet |
| **Institution** | http://localhost/alogoto2/alogoto2/backend/public/dashboard/institution |
| **Institution - Projets** | http://localhost/alogoto2/alogoto2/backend/public/dashboard/institution/projets |
| **Institution - Portfolio** | http://localhost/alogoto2/alogoto2/backend/public/dashboard/institution/portfolio |
| **Institution - Remboursements** | http://localhost/alogoto2/alogoto2/backend/public/dashboard/institution/remboursements |

---

## 🔐 Page de Login

URL de connexion:
```
http://localhost/alogoto2/alogoto2/backend/public/login
```

Avec pré-remplissage auto:
```
http://localhost/alogoto2/alogoto2/backend/public/login?email=admin@alogoto.bj
http://localhost/alogoto2/alogoto2/backend/public/login?email=porteur1@alogoto.bj
http://localhost/alogoto2/alogoto2/backend/public/login?email=institution1@alogoto.bj
```

---

## 👥 Autres Comptes Disponibles

### Porteurs (5 comptes)
1. `gildas.agossa590@alogoto.bj` / `Porteur2026`
2. `germain.houssou909@alogoto.bj` / `Porteur2026`
3. `wilfried.sossoukpe625@alogoto.bj` / `Porteur2026`
4. `gloria.tchibozo227@alogoto.bj` / `Porteur2026`
5. `eulalie.kponton644@alogoto.bj` / `Porteur2026`

### Institutions (3 comptes)
1. `rosine.ahouansou199@alogoto.bj` / `Institution2026`
2. `armand.houssou451@alogoto.bj` / `Institution2026`
3. `wilfried.sossou943@alogoto.bj` / `Institution2026`

---

## ⚠️ Notes Importantes

1. **State Machine**: Les statuts des projets sont maintenant gérés par la state machine (enum `ProjectStatus`)
2. **Paiements**: FedaPay est intégré mais nécessite une configuration sandbox
3. **Base de données**: Les montants sont en DECIMAL(15,2) conforme OHADA
4. **Sécurité**: Foreign keys avec CASCADE activées

---

## 🛠️ Dépannage

### Problème: "403 Forbidden"
- Vérifiez que vous êtes connecté avec le bon rôle
- Clear cache: `php artisan cache:clear`

### Problème: "404 Not Found"
- Vérifiez que le serveur est démarré
- Routes: `php artisan route:list`

### Problème: "500 Error"
- Logs: `storage/logs/laravel.log`
- Config: `php artisan config:clear`

---

**Dernière mise à jour**: 22 Avril 2026
**Version**: Refactoring complet (5 phases complétées)
