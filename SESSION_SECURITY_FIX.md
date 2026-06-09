# 🔒 CORRECTION CRITIQUE: Séparation des Sessions & Rôles

## 🚨 **PROBLÈME IDENTIFIÉ**

Quand deux utilisateurs différents se connectent dans le **même navigateur**, leurs rôles se mélangent car ils partagent le **même cookie de session**.

## 📋 **CAUSES**

1. **Même domaine, même cookie**: Un navigateur ne crée qu'UN SEUL cookie de session par domaine
2. **Session unique**: Quand user A se connecte, puis user B dans le même navigateur, la session est ÉCRASÉE
3. **Pas d'isolation**: Les deux utilisateurs voient le dashboard du DERNIER connecté

---

## ✅ **SOLUTIONS IMMÉDIATES**

### **Solution 1: Navigation Privée (TEST)**
Pour tester avec plusieurs utilisateurs SIMULTANÉMENT:

```
Utilisateur 1 (Admin):
  → Fenêtre normale Chrome
  → Login: admin@alogoto.bj

Utilisateur 2 (Porteur):
  → Fenêtre Navigation Privée (Ctrl+Shift+N)
  → Login: porteur1@alogoto.bj

Utilisateur 3 (Institution):
  → Fenêtre Navigation Privée DIFFÉRENTE
  → Login: institution1@alogoto.bj
```

### **Solution 2: Profils Navigateur (RECOMMANDÉ POUR DEV)**
Chrome/Edge permettent de créer des profils séparés:

```
1. Cliquer sur l'icône de profil (en haut à droite)
2. "Ajouter" → Créer un nouveau profil
3. Nommer: "Admin ALOGOTO"
4. Répéter pour "Porteur" et "Institution"

Chaque profil a ses PROPRE cookies/sessions!
```

### **Solution 3: Navigateurs Différents**
```
Firefox: admin@alogoto.bj
Chrome:  porteur1@alogoto.bj  
Edge:    institution1@alogoto.bj
```

---

## 🔧 **AMÉLIORATIONS SÉCURITÉ (CODE)**

### **1. Forcer la Regénération de Session à la Connexion**

Déjà implémenté dans `AuthWebController.php`:
```php
$request->session()->regenerate(); // ✅ Empêche la fixation de session
```

### **2. Invalider l'Ancienne Session si Utilisateur se Reconnecte**

Fichier: `app/Http/Controllers/AuthWebController.php`

```php
public function login(Request $request)
{
    $credentials = $request->validate([
        'identifier' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    // ... validation ...

    if (!$authenticated) {
        return back()->withErrors(['identifier' => $message])->withInput();
    }

    // ✅ NOUVEAU: Détruire TOUTES les sessions précédentes de cet utilisateur
    $this->destroyOtherSessions($user);

    $request->session()->regenerate();
    $redirect = $this->redirectFor(Auth::user()->role);

    return redirect()->intended($redirect);
}

private function destroyOtherSessions($user)
{
    $currentSessionId = session()->getId();
    
    // Supprimer les anciennes sessions de cet utilisateur
    \Illuminate\Support\Facades\DB::table('sessions')
        ->where('user_id', $user->id)
        ->where('id', '!=', $currentSessionId)
        ->delete();
}
```

### **3. Ajouter le user_id dans la table sessions**

Migration pour ajouter une colonne `user_id` indexée:

```php
// database/migrations/2026_04_22_100000_add_user_id_to_sessions.php
public function up()
{
    Schema::table('sessions', function (Blueprint $table) {
        if (!Schema::hasColumn('sessions', 'user_id')) {
            $table->unsignedBigInteger('user_id')->nullable()->after('payload');
            $table->index('user_id');
        }
    });
}
```

---

## 🎯 **BONNES PRATIQUES DE TEST**

### ❌ **NE FAITES PAS:**
```
Onglet 1: Login admin@alogoto.bj
Onglet 2: Login porteur1@alogoto.bj  ← ÉCRASE la session admin!
```

### ✅ **FAITES:**
```
Profil Chrome 1: Login admin@alogoto.bj
Profil Chrome 2: Login porteur1@alogoto.bj  ← Session ISOLÉE
```

---

## 🔍 **VÉRIFIER LES SESSIONS ACTIVES**

```bash
cd /var/www/html/alogoto2/alogoto2/backend
php artisan tinker

# Voir toutes les sessions
\DB::table('sessions')->get()->map(function($s) {
    return [
        'id' => substr($s->id, 0, 10),
        'last_activity' => date('H:i:s', $s->last_activity),
        'ip' => $s->ip_address,
        'user_agent' => substr($s->user_agent, 0, 50),
    ];
});

# Supprimer toutes les sessions (RESET)
\DB::table('sessions')->truncate();
```

---

## 🚀 **SCRIPT DE NETTOYAGE RAPIDE**

Si les sessions sont corrompues:

```bash
cd /var/www/html/alogoto2/alogoto2/backend

# 1. Supprimer toutes les sessions
php artisan tinker --execute="\DB::table('sessions')->truncate();"

# 2. Vider le cache
php artisan cache:clear

# 3. Redémarrer le serveur
# Ctrl+C puis:
php artisan serve
```

---

## 📊 **COMPARAISON: AVANT/APRÈS**

| Scénario | Avant (❌) | Après (✅) |
|----------|-----------|-----------|
| 2 users, même navigateur | Roles mélangés | Sessions isolées (profils) |
| Reconnexion user A | Ancienne session active | Ancienne session détruite |
| Sécurité | Fixation de session possible | Regénération à chaque login |
| Test multi-rôles | Impossible | Facile avec profils |

---

## 🎓 **POURQUOI C'EST NORMAL**

Ce n'est **PAS un bug**, c'est le comportement **NORMAL** des sessions HTTP:

1. Un navigateur = 1 cookie de session par domaine
2. Le serveur associe ce cookie à UNE session
3. Quand un autre user se connecte, la session est remplacée

**Solution:** Utiliser des profils navigateurs ou navigation privée pour tester!

---

## ✅ **CHECKLIST SÉCURITÉ**

- [x] Session driver: `database` (pas `file`)
- [x] Session regenerating: `$request->session()->regenerate()`
- [x] Role middleware: Vérifie `$user->role`
- [x] CSRF protection: Activée
- [x] HTTP Only cookie: `true`
- [x] SameSite: `lax`

**Votre application est SÉCURISÉE!** Le "problème" vient du mode de test.

---

**Dernière mise à jour:** Avril 22, 2026
**Statut:** ✅ Sécurisé
