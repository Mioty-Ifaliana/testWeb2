# Build stage
FROM composer:2 as builder

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./
COPY . .

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Production stage
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm \
    libzip-dev \
    libicu-dev

# Install PHP extensions
RUN docker-php-ext-configure intl
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# Configure PHP
COPY php.ini-production /usr/local/etc/php/php.ini
RUN sed -i 's/memory_limit = 128M/memory_limit = 256M/g' /usr/local/etc/php/php.ini

# Set working directory
WORKDIR /var/www

# Copy composer dependencies from builder stage
COPY --from=builder /app/vendor ./vendor
COPY --from=builder /app .

# Install and build frontend assets
RUN npm install
RUN npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
