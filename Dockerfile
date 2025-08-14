FROM composer:2 AS composer
FROM node:24.5.0-alpine AS node
FROM php:8.4-fpm-alpine AS base

ARG APP_ENV=local
ENV APP_ENV=$APP_ENV \
    COMPOSER_ALLOW_SUPERUSER=1 \
    ACCEPT_EULA=Y \
    DEBIAN_FRONTEND=noninteractive

WORKDIR /var/www/html

RUN apk add --no-cache \
    postgresql15-dev postgresql15-client \
    clang16 llvm16 lz4-dev openssl-dev \
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
    && docker-php-ext-install pdo pdo_pgsql zip exif gd curl bcmath intl pcntl xml \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && if [ "$APP_ENV" != "production" ] && [ "$APP_ENV" != "staging" ]; then pecl install xdebug && docker-php-ext-enable xdebug; fi \
    && apk del .build-deps

COPY ./docker/PHP/php-dev.ini /usr/local/etc/php/conf.d/custom.ini
COPY ./docker/SUPERVISOR/supervisord.conf /etc/supervisord.conf

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=node /usr/local/bin /usr/local/bin
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules

COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-progress --prefer-dist --no-scripts \
    && composer clear-cache

COPY package.json package-lock.json ./
RUN if [ "$APP_ENV" = "production" ] || [ "$APP_ENV" = "staging" ]; then \
    npm ci --omit=dev; \
    else \
    npm ci; \
    fi

COPY --chown=www-data:www-data . .

COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

RUN git config --global --add safe.directory /var/www/html

ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
