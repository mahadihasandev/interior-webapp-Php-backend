#!/bin/bash
set -e

# Support dynamic port for Render ($PORT is provided by Render, default 10000 or 80)
PORT="${PORT:-80}"
sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

# Ensure storage & cache directories exist with correct web server ownership
mkdir -p /var/www/html/storage/framework/cache /var/www/html/storage/framework/sessions /var/www/html/storage/framework/views /var/www/html/storage/app/public /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ensure SQLite database directory & file exist if sqlite is used
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    mkdir -p /var/www/html/database
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        touch /var/www/html/database/database.sqlite
    fi
    chown -R www-data:www-data /var/www/html/database
    chmod -R 775 /var/www/html/database
fi

# Ensure an application key exists
if [ -z "$APP_KEY" ]; then
    echo "Notice: APP_KEY not provided in environment. Generating temporary application key..."
    export APP_KEY=$(php artisan key:generate --show)
fi

# Clean up any stale or host-copied public/storage link and link properly
rm -rf /var/www/html/public/storage
php artisan storage:link || true

# Run database migrations
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force || true
fi

# Run database seeding if RUN_SEEDS is true
if [ "${RUN_SEEDS:-false}" = "true" ]; then
    echo "Running database seeders..."
    php artisan db:seed --force || true
fi

# Clear any cached configs first then cache for production
php artisan config:clear || true
if [ "$APP_ENV" = "production" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "============================================="
echo " L'Atelier Architectural Studio Backend Ready"
echo " Listening on port: ${PORT}"
echo "============================================="

exec apache2-foreground
