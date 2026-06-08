#!/bin/sh
set -e

composer install --no-interaction --prefer-dist --optimize-autoloader

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

exec php-fpm
