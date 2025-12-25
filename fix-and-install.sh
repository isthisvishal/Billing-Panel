#!/bin/bash
set -e

# 1. Fix permissions if needed
if [ -d vendor ]; then
  sudo chown -R $(whoami):$(whoami) vendor || true
  sudo chmod -R u+rwX vendor || true
fi

# 2. Remove old vendor and lock file
rm -rf vendor composer.lock

# 3. Install dependencies
composer install

# 4. Ensure required directories exist and are writable
mkdir -p storage/logs storage/framework/{views,cache,sessions} bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 5. Copy .env.example to .env if not present
if [ ! -f .env ]; then
  cp .env.example .env
fi

# 6. Generate app key
php artisan key:generate

# 7. Run migrations
php artisan migrate --force

# 8. Build and start Docker
if [ -f docker-compose.yml ]; then
  docker compose build --no-cache
  docker compose up -d
fi

echo "\nAll done! Your Laravel app is ready."
