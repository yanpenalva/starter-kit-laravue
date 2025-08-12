#!/usr/bin/env bash
set -e

echo ">> Executando ajustes de permissões..."
chmod +x /var/www/html/permissions.sh
/var/www/html/permissions.sh

echo ">> Criando diretórios necessários..."
mkdir -p /var/www/html/storage/framework/{cache,sessions,views} \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache \
         /var/log/supervisor

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/log/supervisor
chmod -R g+w /var/www/html/storage /var/www/html/bootstrap/cache /var/log/supervisor
find /var/www/html/storage -type d -exec chmod g+s {} +
find /var/www/html/bootstrap/cache -type d -exec chmod g+s {} +

echo ">> Rodando package:discover..."
php artisan package:discover --ansi || true

echo ">> Iniciando container..."
exec "$@"
