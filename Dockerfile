FROM php:8.2-fpm-alpine AS php-base

RUN apk add --no-cache bash nginx curl gettext su-exec tini ca-certificates \
    libpng libzip oniguruma \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    libpng-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring zip gd bcmath opcache \
    && apk del .build-deps

WORKDIR /var/www

FROM php-base AS build
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts --no-autoloader
COPY . .
RUN mkdir -p bootstrap/cache storage/framework/cache/data \
    storage/framework/sessions storage/framework/views storage/logs storage/app/public \
    && composer dump-autoload --no-dev --optimize --no-interaction \
    && composer check-platform-reqs --no-dev

FROM php-base AS production
ENV APP_ENV=production APP_DEBUG=false LOG_CHANNEL=stderr LOG_LEVEL=info \
    DB_CONNECTION=mysql SESSION_DRIVER=database SESSION_SECURE_COOKIE=true \
    CACHE_STORE=database QUEUE_CONNECTION=sync PORT=10000 RUN_MIGRATIONS=true

COPY --from=build --chown=www-data:www-data /var/www /var/www
COPY docker/nginx.conf /etc/nginx/templates/default.conf.template
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-app.conf
COPY --chmod=755 docker/entrypoint.sh /usr/local/bin/app-entrypoint
RUN sed -i 's/\r$//' /usr/local/bin/app-entrypoint \
    && sed -i '1s/^\xEF\xBB\xBF//' /usr/local/bin/app-entrypoint \
    && chmod +x /usr/local/bin/app-entrypoint \
    && mkdir -p /run/nginx \
    && chmod -R ug+rwX /var/www/storage /var/www/bootstrap/cache

EXPOSE 10000
HEALTHCHECK --interval=30s --timeout=5s --start-period=60s --retries=3 \
    CMD curl --fail --silent "http://127.0.0.1:${PORT}/up" > /dev/null || exit 1
ENTRYPOINT ["/sbin/tini", "--", "/usr/local/bin/app-entrypoint"]
