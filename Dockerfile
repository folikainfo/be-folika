FROM php:8.2-fpm

# ── package & ekstensi ─────────────────────────────────────
RUN apt-get update && apt-get install -y \
    zip unzip git curl nginx supervisor libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring bcmath gd

# ── composer ───────────────────────────────────────────────
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ── aplikasi ───────────────────────────────────────────────
COPY . /var/www
WORKDIR /var/www

RUN composer install --no-dev --optimize-autoloader \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache   # ← izin tulis

# ── nginx & supervisor ─────────────────────────────────────
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8080
CMD ["/usr/bin/supervisord"]
