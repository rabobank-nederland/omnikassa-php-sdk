FROM php:8.4-fpm

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

# Copy parent directory for local composer repository (sdk)
COPY ../../ /var/www/html/

# Copy composer files for dependency installation
COPY composer.json composer.lock ./

# Copy entrypoint script from repo path `php/container-entrypoint`
COPY docker/php/container-entrypoint.sh /usr/local/bin/container-entrypoint
RUN chmod +x /usr/local/bin/container-entrypoint

ENTRYPOINT ["container-entrypoint"]
