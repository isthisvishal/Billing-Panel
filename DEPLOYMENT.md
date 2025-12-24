# Billing-Panel Deployment Guide

## Quick Start (One Command)

```bash
curl -fsSL https://github.com/isthisvishal/Billing-Panel/raw/main/scripts/install.sh | sudo bash
```

This will:
- Install Docker if not present
- Ask for your domain and database password
- Set up all services
- Configure SSL with Caddy automatically
- Run migrations
- Start all services

## System Requirements

- **OS**: Debian 11+, Ubuntu 20.04+, or similar Linux
- **Memory**: 2GB minimum (4GB recommended)
- **Storage**: 10GB minimum
- **Network**: Public IP with domain pointing to server
- **Ports**: 80, 443 (for Caddy)

## Manual Deployment Steps

### 1. Prepare Your Server

```bash
# Update system
sudo apt-get update && sudo apt-get upgrade -y

# Install prerequisites
sudo apt-get install -y curl git

# Install Docker (if not using install script)
curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker $USER
newgrp docker
```

### 2. Clone Repository

```bash
git clone https://github.com/isthisvishal/Billing-Panel.git
cd Billing-Panel
```

### 3. Configure Environment

```bash
# Copy example env file
cp .env.example .env

# Edit with your settings
nano .env
```

**Required variables:**
- `APP_URL`: Your HTTPS domain
- `DB_PASSWORD`: Strong password for MySQL root
- `APP_KEY`: Generate with `php artisan key:generate` (can set to empty initially)

### 4. Configure Web Server

Edit `Caddyfile`:

```bash
nano Caddyfile
```

Change `billing.example.com` to your actual domain.

### 5. Start Services

```bash
# Build and start all services
docker compose up -d --build

# Wait for services to initialize
sleep 15

# Run migrations
docker compose exec app php artisan migrate --force

# Run installer
docker compose exec app php artisan app:install
```

### 6. Verify Installation

```bash
# Check all services are running
docker compose ps

# View app logs
docker compose logs app

# Visit https://your-domain.com in browser
```

## Docker Service Architecture

| Service | Image | Purpose |
|---------|-------|---------|
| **app** | php:8.2-fpm | Laravel application |
| **web** | caddy:2 | Reverse proxy + SSL |
| **db** | mysql:8 | Database server |
| **redis** | redis:7 | Cache & queue backend |
| **worker** | php:8.2-fpm | Background job processor |
| **scheduler** | php:8.2-fpm | Cron job runner |

## Common Commands

### View Logs

```bash
# All services
docker compose logs -f

# Specific service
docker compose logs -f worker

# Follow app logs
docker compose logs -f app
```

### Run Commands Inside App

```bash
# Run artisan command
docker compose exec app php artisan <command>

# Run migration
docker compose exec app php artisan migrate

# Scan invoices
docker compose exec app php artisan invoices:scan

# Clear cache
docker compose exec app php artisan cache:clear
```

### Database Management

```bash
# Access MySQL shell
docker compose exec db mysql -uroot -p billing

# Create backup
docker compose exec db mysqldump -uroot -p billing > backup.sql

# Restore from backup
docker compose exec -T db mysql -uroot -p billing < backup.sql
```

### Queue Management

```bash
# View queue jobs
docker compose logs worker

# Process queue once
docker compose exec app php artisan queue:work --once

# Clear failed jobs
docker compose exec app php artisan queue:failed-table
docker compose exec app php artisan queue:retry all
```

## Production Hardening Checklist

- [ ] Change all default passwords in `.env`
- [ ] Set `APP_DEBUG=false` in production
- [ ] Configure real Discord webhook URL if using notifications
- [ ] Set up regular database backups
- [ ] Enable log rotation for Docker containers
- [ ] Configure email settings for notifications
- [ ] Install real payment gateway plugins (Stripe, PayPal, etc.)
- [ ] Set up monitoring and alerting
- [ ] Configure firewall rules (only allow 80, 443)
- [ ] Use managed TLS certificates with Caddy (automatic)
- [ ] Set resource limits in docker-compose.yml
- [ ] Regular security updates: `sudo apt update && apt upgrade`

## Troubleshooting

### Services won't start

```bash
# Check compose syntax
docker compose config

# Rebuild images
docker compose down
docker compose up -d --build

# Check logs
docker compose logs
```

### Database connection error

```bash
# Verify MySQL is running
docker compose logs db

# Check connection from app
docker compose exec app php artisan tinker
>>> DB::connection()->getPdo()
```

### SSL certificate issues

```bash
# Check Caddyfile syntax
docker compose logs web

# Reload Caddy config
docker compose exec web caddy reload -c /etc/caddy/Caddyfile
```

### Queue jobs not processing

```bash
# Check worker container
docker compose ps worker

# View worker logs
docker compose logs worker

# Restart worker
docker compose restart worker
```

### High memory usage

```bash
# Check container stats
docker stats

# Limit resources in docker-compose.yml
# Add to services:
#   deploy:
#     resources:
#       limits:
#         memory: 512M
```

## Backup & Recovery

### Automated Daily Backups

Create a cron job to backup the database:

```bash
# Edit crontab
crontab -e

# Add this line (runs daily at 2 AM)
0 2 * * * cd /path/to/billing-panel && docker compose exec -T db mysqldump -uroot -p$MYSQL_ROOT_PASSWORD billing | gzip > backups/backup-$(date +\%Y\%m\%d).sql.gz
```

### Manual Backup

```bash
# Backup database
docker compose exec db mysqldump -uroot -p billing | gzip > backup.sql.gz

# Backup volumes
docker run --rm -v billing-panel_db_data:/data -v $(pwd):/backup alpine tar czf /backup/db_volume_backup.tar.gz -C / data

# Backup .env and configuration
tar -czf config_backup.tar.gz .env Caddyfile
```

### Restore from Backup

```bash
# Restore database
zcat backup.sql.gz | docker compose exec -T db mysql -uroot -p billing

# Restore volumes
docker run --rm -v billing-panel_db_data:/data -v $(pwd):/backup alpine tar xzf /backup/db_volume_backup.tar.gz -C /
```

## Scaling for High Traffic

```bash
# Scale worker processes
docker compose up -d --scale worker=5

# Update docker-compose.yml to persist scaling
# Then: docker compose up -d
```

## Support & Updates

- **GitHub Issues**: Report bugs and request features
- **Documentation**: Check README.md for features overview
- **Updates**: Pull latest changes with `git pull && docker compose up -d --build`

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com)
- [Caddy Documentation](https://caddyserver.com/docs)
- [MySQL Documentation](https://dev.mysql.com/doc)
