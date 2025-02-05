# Utiliser l'image officielle de Composer comme première étape
FROM composer:2.6 as composer_stage

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers composer
COPY composer.json composer.lock ./

# Copier le reste du projet
COPY . .

# Installer les dépendances
RUN composer install --no-interaction --optimize-autoloader --no-dev

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
    libicu-dev

# Nettoyer le cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer les extensions PHP
RUN docker-php-ext-configure intl && \
    docker-php-ext-install \
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
    iconv

# Définir le répertoire de travail
WORKDIR /var/www

# Copier l'application depuis l'étape du composer
COPY --from=composer_stage /app /var/www

# Définir les permissions
RUN chown -R www-data:www-data /var/www

EXPOSE 80

CMD ["php-fpm"]