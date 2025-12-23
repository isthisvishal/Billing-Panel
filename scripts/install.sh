#!/usr/bin/env bash
set -euo pipefail

# Simple installer for Billing-Panel
# Usage: curl -fsSL https://repo/.../scripts/install.sh | bash

echo "Billing-Panel one-click installer"
read -p "Enter FQDN (e.g. billing.example.com): " DOMAIN

# Install docker if missing (Debian/Ubuntu)
if ! command -v docker >/dev/null 2>&1; then
  echo "Docker not found; installing..."
  apt-get update && apt-get install -y ca-certificates curl gnupg lsb-release
  mkdir -p /etc/apt/keyrings
  curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg
  echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list >/dev/null
  apt-get update
  apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
fi

# Create .env
cat > .env <<EOF
APP_ENV=production
APP_DEBUG=false
APP_URL=https://$DOMAIN
QUEUE_CONNECTION=database
DB_CONNECTION=mysql
DB_HOST=db
DB_DATABASE=billing
DB_USERNAME=root
DB_PASSWORD=secret
EOF

# Create Caddyfile
cat > Caddyfile <<EOF
$DOMAIN {
  reverse_proxy app:9000
}
EOF

# Start containers
docker compose up -d --build

# Wait for migrations to be available
echo "Waiting for app to be ready..."
sleep 10

# Run migrations and installer
docker compose exec app php artisan app:install

echo "Installer finished. Visit https://$DOMAIN to continue setup."
