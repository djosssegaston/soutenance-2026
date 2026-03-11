# Soutenance 2026

Projet complet combinant un backend Laravel (API + vues Blade) et un frontend PHP statique pour la partie vitrine et tableaux de bord.

## Arborescence
- `backend/` : application Laravel (API, authentification, migrations, assets Vite).
- `frontend/` : pages PHP/HTML publiques et tableaux de bord (assets statiques).
- `documentation/` : site statique de documentation/présentation.
- `index.php` : routeur d’accueil à la racine.

## Prérequis
- PHP 8.1+ avec `ext-fileinfo`, `ext-pdo` et `ext-openssl`.
- Composer.
- Node.js 18+ et npm (pour Vite côté Laravel).
- MySQL/MariaDB (ou autre SGBD supporté par Laravel) pour les migrations.

## Mise en route backend (Laravel)
1. Copier l’exemple d’environnement : `cp backend/.env.example backend/.env` puis ajuster la base de données et les clés mail/queue.
2. Installer les dépendances PHP : `cd backend && composer install`.
3. Générer la clé d’application : `php artisan key:generate`.
4. Exécuter les migrations : `php artisan migrate` (adapter le `.env` si nécessaire).
5. Installer les dépendances front Laravel : `npm install` puis `npm run dev` pour le mode dev ou `npm run build` pour la prod.
6. Lancer le serveur : `php artisan serve` (par défaut sur `http://127.0.0.1:8000`).

## Frontend statique
- Les pages publiques et dashboards sont dans `frontend/` et peuvent être servies par Apache/Nginx ou via `php -S localhost:8080 -t frontend/public` pour un test rapide.
- Les assets sont déjà construits dans `frontend/public/assets`.

## Documentation
- Contenu statique dans `documentation/`; peut être servi tel quel (ex. `php -S localhost:9000 -t documentation`).

## Déploiement
- Backend : construire les assets (`npm run build`), configurer le vhost pour pointer sur `backend/public`, et s’assurer que `storage/` et `bootstrap/cache` sont inscriptibles.
- Frontend : déployer le dossier `frontend/` derrière le serveur web (ou intégrer la partie vitrine dans Laravel selon vos besoins).

## Commandes utiles
- `git status` : vérifier l’état du dépôt.
- `php artisan migrate:fresh --seed` : réinitialiser la base (en dev).
- `npm run dev -- --host` : servir les assets Vite accessibles depuis le LAN.

## Licence
Non spécifiée dans le dépôt. Ajoutez une licence si nécessaire avant publication.
