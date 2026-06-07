FROM php:8.3-fpm AS app_base
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libpng-dev libonig-dev libxml2-dev libicu-dev supervisor \
    && docker-php-ext-install pdo_mysql mbstring zip gd xml bcmath intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
COPY --chown=www-data:www-data . .
RUN mkdir -p bootstrap/cache && composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

FROM app_base AS app
RUN mkdir -p /var/log/supervisor
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
COPY supervisord.conf /etc/supervisor/supervisord.conf
RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
EXPOSE 9000
ENTRYPOINT ["docker-entrypoint.sh"]

FROM nginx:alpine AS nginx
COPY --from=app /var/www/public /var/www/public
COPY nginx/default.conf /etc/nginx/conf.d/default.conf
