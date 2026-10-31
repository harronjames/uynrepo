# Production image (build from repo root: docker build -t umzugland-client:latest .)
# Multi-stage: Composer deps → app assemble → minimal PHP-FPM + nginx runner

# ================================
# Stage 1: Composer dependencies (production)
# ================================
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
# composer:2 imajında intl/gd yok; eklentiler runner aşamasında kurulur
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-req=ext-intl \
    --ignore-platform-req=ext-gd \
    --ignore-platform-req=ext-pcntl

# ================================
# Stage 2: Application files
# ================================
FROM php:8.5-fpm AS builder

WORKDIR /var/www

COPY --from=vendor /app/vendor ./vendor
COPY . .

RUN rm -rf \
    .git \
    .github \
    tests \
    node_modules \
    docker-compose/mysql \
    deploy.env \
    deploy.env.example \
    .env \
    .env.local \
    .env.ci \
    .env.backup \
    phpunit.xml \
    phpstan.neon.dist \
    .php-cs-fixer.dist.php \
    rector.php \
    translations \
    test.py \
    deploy.sh \
    README.md \
    && mkdir -p \
      storage/framework/cache \
      storage/framework/sessions \
      storage/framework/views \
      storage/logs \
      storage/app/public \
      bootstrap/cache \
    && php artisan package:discover --ansi || true

# ================================
# Stage 3: Runner (PHP-FPM + nginx)
# ================================
FROM php:8.5-fpm AS runner

WORKDIR /var/www

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    RUN_MIGRATIONS=1

RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx \
        supervisor \
        curl \
        libicu-dev \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        zip \
        unzip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif intl pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /etc/nginx/sites-enabled/default

COPY --from=builder /var/www /var/www

COPY docker-compose/nginx/production.conf /etc/nginx/conf.d/default.conf
COPY docker-compose/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker-compose/php/production.ini /usr/local/etc/php/conf.d/99-production.ini
COPY docker-compose/app/entrypoint.prod.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R ug+rwx /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
