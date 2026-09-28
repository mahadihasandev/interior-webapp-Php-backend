# Stage 1: Build front-end Vite assets using official Node 20
FROM node:20-alpine AS assets-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY resources ./resources
COPY vite.config.js ./
COPY public ./public
RUN npm run build

# Stage 2: Production PHP 8.4 Apache Environment
FROM php:8.4-apache

# Install system dependencies & PostgreSQL / SQLite / MySQL dev libraries
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    libpq-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install all required PHP extensions for Laravel 12 & multi-database support
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql pdo_sqlite mbstring exif pcntl bcmath gd zip opcache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache Document Root to Laravel public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configure Apache Directory permissions for .htaccess and client routing
RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/laravel.conf && a2enconf laravel

# Install Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Copy precompiled Vite assets from Stage 1
COPY --from=assets-builder /app/public/build /var/www/html/public/build

# Install PHP dependencies without dev packages
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-req=php+

# Ensure storage directories exist and have proper permissions
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/app/public \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy entrypoint script and make executable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose Render standard port
EXPOSE 80 10000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
