# syntax=docker/dockerfile:1.7
# Image unique de l'API SUNOOGO : PHP-FPM (web), file d'attente, planificateur et Reverb
# partagent le même code ; seul la commande change (voir docker-compose.yml à la racine).

# --- Dépendances PHP ---------------------------------------------------------
FROM composer:2.8 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --no-scripts --prefer-dist --no-autoloader
COPY . .
RUN composer dump-autoload --no-dev --classmap-authoritative --no-scripts

# --- Assets du site et du back-office ------------------------------------------
FROM node:22.12-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

# --- Exécution ---------------------------------------------------------------
FROM php:8.4-fpm-alpine AS runtime

# Extensions : bases de données, Redis, images (photos), intl (dates en français), pcntl (Reverb, files).
# postgresql16-client / mariadb-client : dumps pour les sauvegardes quotidiennes.
RUN apk add --no-cache icu-libs libzip libpng libjpeg-turbo freetype postgresql16-client mariadb-client fcgi \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev postgresql16-dev linux-headers \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath exif gd intl opcache pcntl pdo_mysql pdo_pgsql zip \
    && pecl install redis-6.1.0 \
    && docker-php-ext-enable redis \
    && apk del .build-deps

COPY docker/php.ini /usr/local/etc/php/conf.d/sunoogo.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-sunoogo.conf

WORKDIR /var/www/html
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /app/vendor ./vendor
COPY --chown=www-data:www-data --from=assets /app/public/build ./public/build

RUN rm -rf tests node_modules .env \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY --chmod=0755 docker/entrypoint.sh /usr/local/bin/sunoogo-entrypoint

USER www-data
EXPOSE 9000
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s CMD SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1
ENTRYPOINT ["sunoogo-entrypoint"]
CMD ["php-fpm"]

# --- Serveur web (nginx) -------------------------------------------------------
# Même dossier public que l'application ; les fichiers déposés (storage/app/public) arrivent par un volume.
FROM nginx:1.27-alpine AS web
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=runtime /var/www/html/public /var/www/html/public
RUN ln -s ../storage/app/public /var/www/html/public/storage
