#!/bin/bash
# Billing-Panel Local Development Setup
# Run this to set up local development environment

set -euo pipefail

echo "================================"
echo "Billing-Panel Local Development Setup"
echo "================================"

# Create .env.local from example
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
    echo "✓ .env created"
    echo "  Edit .env with your local settings if needed"
else
    echo "✓ .env already exists"
fi

# Create local Caddyfile
if [ ! -f Caddyfile ]; then
    echo "Creating Caddyfile..."
    cat > Caddyfile <<EOF
localhost {
  reverse_proxy app:9000 {
    transport http {
      read_timeout 300s
      write_timeout 300s
    }
  }
}
EOF
    echo "✓ Caddyfile created for localhost"
fi

# Start services
echo ""
echo "Starting Docker containers..."
docker compose up -d --build

echo ""
echo "Waiting for services to initialize..."
sleep 15

# Run migrations
echo "Running database migrations..."
docker compose exec -T app php artisan migrate --force || echo "⚠ Migrations may already exist"

echo ""
echo "================================"
echo "Setup Complete!"
echo "================================"
echo ""
echo "Services running:"
docker compose ps

echo ""
echo "Next steps:"
echo "1. View logs: docker compose logs -f app"
echo "2. Access app: http://localhost (via local browser if port-forwarded)"
echo "3. Database: host=db, user=root, password=secret"
echo "4. Run commands: docker compose exec app php artisan <command>"
echo ""
echo "To stop: docker compose down"
echo "To remove data: docker compose down -v"
echo ""
