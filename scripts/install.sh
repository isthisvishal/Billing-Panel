#!/usr/bin/env bash
set -euo pipefail

# Simple installer for Billing-Panel
# Usage: curl -fsSL https://repo/.../scripts/install.sh | bash

echo "================================"
echo "Billing-Panel one-click installer"
echo "================================"

# Check if running as root
if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root" 
   exit 1
fi

read -p "Enter FQDN (e.g. billing.example.com): " DOMAIN
read -p "Enter MySQL root password [default: secret]: " DB_PASSWORD
DB_PASSWORD=${DB_PASSWORD:-secret}

# Install docker if missing (Debian/Ubuntu)
if ! command -v docker >/dev/null 2>&1; then
  echo "Docker not found; installing..."
  apt-get update && apt-get install -y ca-certificates curl gnupg lsb-release
  mkdir -p /etc/apt/keyrings
  curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg
  echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list >/dev/null
  apt-get update
  apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
  systemctl start docker
  systemctl enable docker
fi

# Check if docker compose is installed
if ! command -v docker compose >/dev/null 2>&1; then
  echo "Docker Compose not found; installing..."
  apt-get install -y docker-compose-plugin
fi

# Clone repo if not already in it
if [ ! -f "docker-compose.yml" ]; then
  echo "Cloning Billing-Panel repository..."
  git clone https://github.com/isthisvishal/Billing-Panel.git /opt/billing-panel
  cd /opt/billing-panel
fi

# Create .env
echo "Creating .env file..."
cat > .env <<EOF
APP_NAME=Billing-Panel
APP_ENV=production
APP_DEBUG=false
APP_URL=https://$DOMAIN
APP_KEY=base64:
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=billing
DB_USERNAME=root
DB_PASSWORD=$DB_PASSWORD
QUEUE_CONNECTION=database
REDIS_HOST=redis
REDIS_PORT=6379
EOF

# Create Caddyfile
echo "Creating Caddyfile for SSL..."
cat > Caddyfile <<EOF
$DOMAIN {
  reverse_proxy app:9000 {
    transport http {
      read_timeout 300s
      write_timeout 300s
    }
  }
}
EOF

# Start containers
echo "Starting containers..."
docker compose up -d --build

# Wait for services to be ready
echo "Waiting for services to be ready..."
sleep 15

# Check if app container is healthy
max_attempts=30
attempt=0
while [ $attempt -lt $max_attempts ]; do
  if docker compose exec -T app test -f /var/www/artisan; then
    echo "App container is ready!"
    break
  fi
  echo "Waiting for app... ($((attempt+1))/$max_attempts)"
  sleep 2
  attempt=$((attempt+1))
done

# Run migrations
echo "Running database migrations..."
docker compose exec -T app php artisan migrate --force || true

# Run installer command if it exists
echo "Running app installer..."
docker compose exec -T app php artisan app:install 2>/dev/null || echo "Install command not yet available"

# Display summary
echo ""
echo "================================"
echo "Installation Complete!"
echo "================================"
echo "URL: https://$DOMAIN"
echo "Database: MySQL (host: db, user: root, password: $DB_PASSWORD)"
echo ""
echo "To view logs:"
echo "  docker compose logs -f app"
echo ""
echo "To stop services:"
echo "  docker compose down"
echo ""echo "Installer finished. Visit https://$DOMAIN to continue setup."
