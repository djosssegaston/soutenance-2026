#!/bin/bash
# =============================================================
# start-reverb.sh
# Démarre le serveur Laravel Reverb (WebSocket) pour ALOGOTO
# À lancer DANS un second terminal, parallèlement à
#   php artisan serve
# =============================================================
# Usage:
#   chmod +x start-reverb.sh
#   ./start-reverb.sh
#
# Pour arrêter : Ctrl+C
# Pour redémarrer : php artisan reverb:restart
# =============================================================

cd "$(dirname "$0")"

echo "=== Démarrage de Laravel Reverb (WebSocket) ==="
echo "Port     : ${REVERB_SERVER_PORT:-8080}"
echo "Host     : ${REVERB_SERVER_HOST:-0.0.0.0}"
echo "App ID   : ${REVERB_APP_ID:-$(grep ^REVERB_APP_ID .env | cut -d= -f2)}"
echo ""
echo "Le serveur WebSocket va écouter sur ws://127.0.0.1:${REVERB_SERVER_PORT:-8080}"
echo "Lancer dans un terminal séparé de php artisan serve"
echo "----------------------------------------------------"

php artisan reverb:start
