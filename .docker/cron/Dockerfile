FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    cron \
    libicu-dev \
    pkg-config \
    libzip-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql intl zip opcache

# Установим Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

WORKDIR /var/www/crypto

COPY crontab /etc/cron.d/crypto-cron
RUN chmod 0644 /etc/cron.d/crypto-cron
RUN crontab /etc/cron.d/crypto-cron

CMD ["cron", "-f"]