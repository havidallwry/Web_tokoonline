# syntax=docker/dockerfile:1

#####################################################
# Stage 1 - Build frontend assets (Vite + Tailwind)
#####################################################
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build


#####################################################
# Stage 2 - Runtime (PHP-FPM 8.2 + Nginx, port 8080)
#####################################################
FROM serversideup/php:8.2-fpm-nginx AS runtime

ENV APP_ENV=production \
    APP_DEBUG=false \
    SSL_MODE=off \
    PHP_OPCACHE_ENABLE=1 \
    AUTORUN_ENABLED=true \
    AUTORUN_LARAVEL_MIGRATION=true \
    AUTORUN_LARAVEL_STORAGE_LINK=true

USER root

# pdo_mysql, zip, pcntl, redis sudah tersedia di image ini
WORKDIR /var/www/html

# Aplikasi + dependensi PHP
COPY --chown=www-data:www-data . .
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build

RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rw storage bootstrap/cache

USER www-data

EXPOSE 8080
