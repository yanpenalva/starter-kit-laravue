FROM composer:2.8.7 AS composer
FROM node:22.9.0-alpine AS node

FROM php:8.4.5-fpm-alpine AS base

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV ACCEPT_EULA=Y
ENV APP_ENV=development
ENV DEBIAN_FRONTEND=noninteractive

ARG APP_ENV=development
WORKDIR /var/www/html

RUN apk add --no-cache \
    postgresql-dev \
    libzip-dev \
    supervisor \
    git \
    vim \
    nano \
    htop \
    curl-dev \
    jpegoptim optipng pngquant gifsicle \
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
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
    pdo pdo_pgsql zip exif gd curl bcmath intl pcntl xml \
    && pecl install redis xdebug \
    && docker-php-ext-enable redis \
    && rm -f /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && apk del .build-deps

RUN echo "upload_max_filesize = 16M" > /usr/local/etc/php/conf.d/custom.ini && \
    echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "max_execution_time = 60" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "expose_php = Off" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "log_errors = On" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "error_log = /var/log/php_errors.log" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "disable_functions = exec,passthru,shell_exec,system,parse_ini_file,show_source" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "date.timezone = America/Bahia" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "opcache.jit=tracing" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "opcache.jit_buffer_size=100M" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "xdebug.start_with_request=trigger" >> /usr/local/etc/php/conf.d/custom.ini && \
    echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/custom.ini && \
    if [ "$APP_ENV" = "production" ] || [ "$APP_ENV" = "staging" ]; then echo "display_errors = Off"; else echo "display_errors = On"; fi >> /usr/local/etc/php/conf.d/custom.ini

RUN mkdir -p /var/log/php /var/log/supervisor && \
    touch /var/log/php_errors.log && chmod 666 /var/log/php_errors.log && \
    echo "0 0 * * 0 truncate -s 0 /var/log/php_errors.log" > /etc/crontabs/root

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=node /usr/local/bin /usr/local/bin
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules

COPY ./docker/SUPERVISOR/supervisord.conf /etc/supervisord.conf
COPY --chown=www-data:www-data . .

RUN chmod +x ./permissions.sh && ./permissions.sh

RUN composer install --no-interaction --no-progress --optimize-autoloader && \
    composer clear-cache && \
    npm install && \
    npm audit fix --force

RUN git config --global --add safe.directory /var/www/html

ENTRYPOINT ["/bin/sh", "-c"]
CMD ["[ \"$PHP_ENABLE_XDEBUG\" = \"1\" ] && echo 'zend_extension=xdebug.so' > /usr/local/etc/php/conf.d/zzz-xdebug.ini; exec supervisord -c /etc/supervisord.conf"]
