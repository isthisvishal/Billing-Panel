# Billing-Panel

This repository implements a billing and hosting automation platform skeleton.

## Features added
- Queue jobs for provisioning automation (Create, Suspend, Unsuspend, Terminate)
- Invoice events and listeners (paid, overdue, grace warning, cancelled, expired)
- Daily invoice scan command (`php artisan invoices:scan`) and scheduler
- Plugin discovery skeleton (`plugins/` folder + `plugin.json`), plugin DB table & configs
- Admin-configurable settings via `AdminSetting` model
- Notifications skeleton (email + Discord service)
- Docker + Caddy-based SSL and installer script `scripts/install.sh`

## Deployment

### One-Command VM Deployment (Recommended)
For a fresh **Debian/Ubuntu** VM, run:

```bash
curl -fsSL https://github.com/isthisvishal/Billing-Panel/raw/main/scripts/install.sh | bash
```

The script will:
- Install Docker & Docker Compose if needed
- Ask for your domain (e.g., billing.example.com)
- Set up environment variables
- Start all services with SSL via Caddy
- Run database migrations automatically

### Manual Docker Deployment

#### Prerequisites
- Docker & Docker Compose installed
- Git
- A domain name for HTTPS

#### Steps

1. **Clone the repository:**
```bash
git clone https://github.com/isthisvishal/Billing-Panel.git
cd Billing-Panel
```

2. **Create `.env` file:**
```bash
cat > .env <<EOF
APP_NAME=Billing-Panel
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_KEY=base64:your-app-key-here

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=billing
DB_USERNAME=root
DB_PASSWORD=your-secure-password

QUEUE_CONNECTION=database
REDIS_HOST=redis
REDIS_PORT=6379

DISCORD_WEBHOOK_URL=your-discord-webhook-url
EOF
```

3. **Create `Caddyfile` for HTTPS:**
```bash
cat > Caddyfile <<EOF
your-domain.com {
  reverse_proxy app:9000
}
EOF
```

4. **Start services:**
```bash
docker compose up -d --build
```

5. **Run migrations and install:**
```bash
docker compose exec app php artisan migrate --force
docker compose exec app php artisan app:install
```

6. **Start the queue worker:**
```bash
docker compose up -d worker
docker compose up -d scheduler
```

### Verify Deployment

Check all services are running:
```bash
docker compose ps
```

View logs:
```bash
docker compose logs -f app
```

### Environment Variables

| Variable | Default | Purpose |
|----------|---------|---------|
| `APP_ENV` | production | Environment mode |
| `APP_DEBUG` | false | Debug mode (disable in production) |
| `DB_HOST` | db | MySQL host |
| `QUEUE_CONNECTION` | database | Queue driver (database/redis) |
| `DISCORD_WEBHOOK_URL` | - | Discord notifications webhook |

### Running Queue Jobs Manually

```bash
# Run queue worker
docker compose exec worker php artisan queue:work --tries=3

# Run daily invoice scan
docker compose exec app php artisan invoices:scan
```

### Scaling & Management

```bash
# View logs
docker compose logs -f [service_name]

# Scale worker processes
docker compose up -d --scale worker=3

# Stop all services
docker compose down

# Remove volumes (WARNING: data loss)
docker compose down -v
```

## Architecture

- **App (PHP 8.2-FPM)**: Main application container
- **Web (Caddy)**: Reverse proxy with automatic HTTPS
- **Database (MySQL 8)**: Application data persistence
- **Redis**: Queue and cache backend
- **Worker**: Background job processor
- **Scheduler**: Cron job runner

## Notes
Everything is configurable from the admin panel: plugins, API keys, and grace rules are stored in the database via `AdminSetting` and `plugin_configs`.

For full production hardening:
- Add real plugin implementations for Proxmox, Pterodactyl, Stripe, PayPal, Cashfree, and Razorpay
- Configure database backups
- Set strong passwords in environment variables
- Enable HTTPS with Caddy (automatic with install script)
- Monitor logs and health checks
- Set up log aggregation for production
