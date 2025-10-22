FROM php:8.4-fpm

# Install system dependencies and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libicu-dev \
        libzip-dev \
        zip \
        unzip \
        git \
    && docker-php-ext-install intl opcache zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html/example-integration

## Copy composer files first for dependency installation
#COPY composer.json composer.lock ./
#
## Install PHP dependencies
#RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Copy entrypoint script from repo path `php/container-entrypoint`
COPY docker/php/container-entrypoint.sh /usr/local/bin/container-entrypoint
RUN chmod +x /usr/local/bin/container-entrypoint

# Use the 'normal' entrypoint, we don't need the custom one in dev
ENTRYPOINT ["container-entrypoint"]
