FROM composer:2.8.7 AS composer
FROM node:22.9.0-alpine AS node

FROM php:8.4.8-fpm-alpine AS base

ARG APP_ENV=local
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV ACCEPT_EULA=Y
ENV DEBIAN_FRONTEND=noninteractive
ENV APP_ENV=$APP_ENV

WORKDIR /var/www/html

RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    supervisor \
    git \
    curl-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    icu-dev \
    unzip \
    libwebp-dev \
    zlib-dev \
    bash \
    tzdata \
    wget \
    busybox-suid \
    jpegoptim optipng pngquant gifsicle \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
    pdo pdo_pgsql zip exif gd curl bcmath intl pcntl xml \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && if [ "$APP_ENV" != "production" ] && [ "$APP_ENV" != "staging" ]; then \
    pecl install xdebug && docker-php-ext-enable xdebug; \
    fi \
    && apk del .build-deps

COPY ./docker/PHP/php-dev.ini /usr/local/etc/php/conf.d/custom.ini

RUN mkdir -p /var/log/php /var/log/supervisor && \
    touch /var/log/php_errors.log && chmod 666 /var/log/php_errors.log && \
    echo "0 0 * * 0 truncate -s 0 /var/log/php_errors.log" > /etc/crontabs/root

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=node /usr/local/bin /usr/local/bin
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules

COPY ./docker/SUPERVISOR/supervisord.conf /etc/supervisord.conf
COPY --chown=www-data:www-data . .

RUN chmod +x ./permissions.sh && ./permissions.sh

RUN composer install --no-interaction --no-progress --prefer-dist --optimize-autoloader && \
    composer clear-cache && \
    npm install && \
    if [ "$APP_ENV" != "production" ] && [ "$APP_ENV" != "staging" ]; then npm audit fix --force; fi

RUN git config --global --add safe.directory /var/www/html

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s CMD curl -f http://localhost || exit 1

ENTRYPOINT ["supervisord", "-c", "/etc/supervisord.conf"]
