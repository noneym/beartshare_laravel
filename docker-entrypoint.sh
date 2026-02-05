#!/bin/sh
set -e

echo "=== BeArtShare Docker Entrypoint ==="

# Storage dizinlerini oluştur
mkdir -p /app/storage/logs \
         /app/storage/framework/sessions \
         /app/storage/framework/views \
         /app/storage/framework/cache

# Log dosyasını oluştur
touch /app/storage/logs/laravel.log

# Dosya izinlerini ayarla
chown -R www-data:www-data /app/storage /app/bootstrap/cache
chmod -R 775 /app/storage /app/bootstrap/cache

# Laravel cache optimize
echo "Laravel optimize ediliyor..."
php /app/artisan config:cache
php /app/artisan route:cache
php /app/artisan view:cache

echo "=== Uygulama başlatılıyor ==="

exec "$@"
