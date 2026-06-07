FROM php:8.4-apache-bookworm

RUN set -eux; \
    apt-get update -o Acquire::Retries=3; \
    apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libpq-dev \
        libsqlite3-dev \
        libzip-dev \
        pkg-config \
        zlib1g-dev; \
    rm -rf /var/lib/apt/lists/*

RUN set -eux; \
    docker-php-ext-install intl opcache pdo_pgsql pdo_sqlite zip; \
    a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN set -eux; \
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts; \
    mkdir -p var/cache var/log var/share; \
    php bin/console sass:build; \
    chown -R www-data:www-data var; \
    sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80
