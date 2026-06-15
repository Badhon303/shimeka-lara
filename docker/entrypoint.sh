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

# Generate .env from env vars if not exists (for Laravel to read)
if [ ! -f /var/www/.env ]; then
    cat > /var/www/.env <<EOF
APP_NAME="${APP_NAME:-Glow & Glam}"
APP_ENV=${APP_ENV:-production}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}
APP_KEY=

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-glowglam}
DB_USERNAME=${DB_USERNAME:-glowglam}
DB_PASSWORD=${DB_PASSWORD:-glowglam123}

CACHE_DRIVER=${CACHE_DRIVER:-file}
SESSION_DRIVER=${SESSION_DRIVER:-file}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}
EOF
fi

# Generate APP_KEY if not set
php artisan key:generate --force

# Cache Laravel config/routes/views for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (optional — disable if you manage DB separately)
# php artisan migrate --force

# Start supervisor (nginx + php-fpm)
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
