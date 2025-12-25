#!/usr/bin/env bash
set -euo pipefail

# a) cd to Laravel root
cd /var/www

# b) Create required directories
mkdir -p storage/logs storage/framework/{views,cache} bootstrap/cache

# c) Set correct permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# d) Ensure .env exists
if [ ! -f .env ]; then
  echo "No .env found, copying from .env.example..."
  cp .env.example .env
fi

# e) Generate APP_KEY if missing
if ! grep -q "^APP_KEY=" .env || grep -q "^APP_KEY=$" .env; then
  echo "Generating APP_KEY..."
  php artisan key:generate --ansi
fi

# f) Run composer install with --no-scripts
if [ ! -f vendor/autoload.php ]; then
  echo "Running composer install..."
  composer install --no-interaction --no-scripts --optimize-autoloader
fi

# g) Run artisan commands
php artisan package:discover --ansi
if [ "${APP_ENV:-}" != "testing" ]; then
  echo "Running migrations..."
  php artisan migrate --force || echo "Migrations failed, continuing startup."
fi

exec "$@"
