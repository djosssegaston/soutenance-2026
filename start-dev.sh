#!/bin/bash
echo "Starting Alogoto dev server with upload_max_filesize=10M..."
cd /var/www/html/alogoto2/alogoto2/backend/public && /usr/bin/php8.4 \
  -d upload_max_filesize=10M \
  -d post_max_size=12M \
  -S 127.0.0.1:8000 \
  /var/www/html/alogoto2/alogoto2/backend/vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
