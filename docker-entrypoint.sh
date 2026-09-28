#!/bin/bash
set -e

# Support dynamic port for Render ($PORT is provided by Render, typically 10000)
PORT="${PORT:-80}"
sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

# Ensure SQLite database file exists if sqlite is used
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    mkdir -p /var/www/html/database
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        touch /var/www/html/database/database.sqlite
    fi
    chown -R www-data:www-data /var/www/html/database
    chmod -R 775 /var/www/html/database
fi

# Ensure storage directories exist and are writable
mkdir -p /var/www/html/storage/framework/cache /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/app/public
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create storage symlink
php artisan storage:link || true

# Run database migrations if RUN_MIGRATIONS is true or default
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
fi

# Run database seeding if RUN_SEEDS is true
if [ "${RUN_SEEDS:-false}" = "true" ]; then
    echo "Running database seeders..."
    php artisan db:seed --force || true
fi

# Optimize Laravel caching for production
if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "Starting Apache on port ${PORT}..."
exec apache2-foreground
