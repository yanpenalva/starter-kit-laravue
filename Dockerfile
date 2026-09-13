FROM composer:2 AS composer
FROM node:26.8.2-alpine AS node

RUN npm install --global npm@12.0.2

FROM php:8.5.9-fpm-alpine AS base

ARG APP_ENV=local
ENV APP_ENV=$APP_ENV \
    COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

RUN apk add --no-cache \
    postgresql16-dev postgresql16-client \
    clang16 llvm16 lz4-dev openssl-dev \
    libzip-dev \
    git \
    curl-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    htop \
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

RUN apk add --no-cache python3 py3-pip \
    && pip install --no-cache-dir --break-system-packages 'setuptools<81' supervisor

COPY ./docker/PHP/php-dev.ini /usr/local/etc/php/conf.d/custom.ini
COPY ./docker/SUPERVISOR/supervisord.conf /etc/supervisord.conf

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=node /usr/local/bin /usr/local/bin
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules

COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-progress --prefer-dist --no-scripts \
    && composer clear-cache

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund \
    && if [ "$APP_ENV" = "production" ] || [ "$APP_ENV" = "staging" ]; then npm prune --omit=dev; fi

COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
RUN git config --global --add safe.directory /var/www/html

ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]
