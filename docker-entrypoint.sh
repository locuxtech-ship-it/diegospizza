#!/bin/bash
set -e

if [ ! -f /var/www/.env ]; then
    cp /var/www/.env.example /var/www/.env
fi

if [ -n "$APP_KEY" ] && [ "$APP_KEY" != "base64:" ]; then
    sed -i "s|APP_KEY=.*|APP_KEY=$APP_KEY|" /var/www/.env
fi

if ! grep -q "APP_KEY=base64" /var/www/.env; then
    php artisan key:generate --force
fi

if [ -n "$DB_PASSWORD" ]; then
    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$DB_PASSWORD|" /var/www/.env
fi

if [ -n "$APP_URL" ]; then
    sed -i "s|APP_URL=.*|APP_URL=$APP_URL|" /var/www/.env
fi

if [ -n "$APP_DEBUG" ]; then
    sed -i "s|APP_DEBUG=.*|APP_DEBUG=$APP_DEBUG|" /var/www/.env
fi

if [ ! -L /var/www/public/storage ]; then
    php artisan storage:link --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
