FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    curl \
    libicu-dev \
    libpq-dev \
    libxml2-dev \
    libzip-dev \
    build-essential \
    autoconf \
    && docker-php-ext-install intl pdo pdo_mysql opcache zip

RUN pecl install redis && docker-php-ext-enable redis

RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/crypto