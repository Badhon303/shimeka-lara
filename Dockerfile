# Production Dockerfile for Glow & Glam E-commerce
# Laravel + Vue SPA with PostgreSQL

# Stage 1: Build frontend assets
FROM node:20-alpine AS frontend

WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci --include=dev

COPY . .
RUN npm run build

# Stage 2: PHP runtime
FROM php:8.4-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    openssl \
    linux-headers \
    && docker-php-ext-install pdo pdo_pgsql pgsql gd zip opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files (excluding build artifacts via .dockerignore)
COPY . .

# Run composer install (artisan file must exist for post-autoload scripts)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Copy built frontend assets from node stage
COPY --from=frontend /app/public/build ./public/build

# Create storage directories (ignored by .gitignore)
RUN mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Copy supervisord config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Expose port
EXPOSE 8000

# Start services via supervisor
CMD ["/entrypoint.sh"]
