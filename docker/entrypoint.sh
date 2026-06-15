#!/bin/sh
set -e

# Create storage directories if missing
mkdir -p /var/www/storage/framework/cache
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/logs
mkdir -p /var/www/bootstrap/cache

# Set permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Cache Laravel config/routes/views for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (optional — disable if you manage DB separately)
# php artisan migrate --force

# Start supervisor (nginx + php-fpm)
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
