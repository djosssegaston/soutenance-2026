#!/bin/bash

# Script de nettoyage des sessions ALOGOTO
# Utilisation: ./reset-sessions.sh

echo "🔒 Nettoyage des sessions ALOGOTO..."
echo ""

cd /var/www/html/alogoto2/alogoto2/backend

echo "1️⃣  Suppression de toutes les sessions..."
php artisan tinker --execute="\DB::table('sessions')->truncate();" 2>/dev/null

echo "2️⃣  Vidage du cache..."
php artisan cache:clear 2>/dev/null

echo "3️⃣  Vidage du cache de configuration..."
php artisan config:clear 2>/dev/null

echo "4️⃣  Vidage du cache de routes..."
php artisan route:clear 2>/dev/null

echo ""
echo "✅ Toutes les sessions ont été supprimées!"
echo ""
echo "📋 Pour tester avec plusieurs utilisateurs:"
echo "   - Utilisez la navigation privée (Ctrl+Shift+N)"
echo "   - OU créez des profils Chrome séparés"
echo "   - OU utilisez différents navigateurs"
echo ""
echo "🔑 Comptes de test:"
echo "   Admin:       admin@alogoto.bj / Admin2026"
echo "   Porteur:     porteur1@alogoto.bj / Porteur2026"
echo "   Institution: institution1@alogoto.bj / Institution2026"
echo ""
