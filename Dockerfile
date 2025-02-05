# Utiliser l'image officielle de Composer comme première étape
FROM composer:2.6 as composer_stage

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers composer
COPY composer.json composer.lock ./

# Installer les dépendances
RUN composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-reqs

# Copier le reste du projet
COPY . .

# Deuxième étape : l'image PHP finale
FROM php:8.2-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    librabbitmq-dev \
    libssh-dev \
    libpq-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
        ctype \
        iconv \
        xml \
        dom \
        session \
        tokenizer \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && rm -rf /var/lib/apt/lists/*

# Configurer PHP pour la production
COPY docker/php/php.ini-production /usr/local/etc/php/php.ini
RUN echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini \
    && echo "upload_max_filesize=20M" >> /usr/local/etc/php/conf.d/docker-php-upload.ini \
    && echo "post_max_size=20M" >> /usr/local/etc/php/conf.d/docker-php-upload.ini

# Définir le répertoire de travail
WORKDIR /var/www

# Copier l'application depuis l'étape du composer
COPY --from=composer_stage /app /var/www

# Créer le répertoire var et définir les permissions
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var/cache var/log \
    && chmod -R 777 var/cache var/log

# Définir les permissions
RUN chown -R www-data:www-data /var/www

EXPOSE 80

CMD ["php-fpm"]