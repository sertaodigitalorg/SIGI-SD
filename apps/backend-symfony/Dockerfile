FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libicu-dev libpq-dev libzip-dev \
    && docker-php-ext-install intl opcache pdo_pgsql pdo_sqlite zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80
