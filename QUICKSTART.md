# Quick Start Guide

## TL;DR - One Line Deploy

```bash
curl -fsSL https://github.com/isthisvishal/Billing-Panel/raw/main/scripts/install.sh | sudo bash
```

That's it! The script will:
- Install Docker automatically
- Ask for your domain name
- Ask for a database password
- Set everything up
- Start the application
- Configure SSL automatically

## What You Need

- **Server**: Ubuntu/Debian Linux (cloud VM, VPS, dedicated server, etc.)
- **Domain**: A domain name pointing to your server
- **Root Access**: SSH access as root (or with sudo)

## After Installation

1. **Wait 2-3 minutes** for services to fully start
2. **Visit** `https://your-domain.com` in your browser
3. **Log in** with default admin credentials
4. **Configure** payment gateway and notifications

## Manual Alternative (If automated script fails)

```bash
# 1. Clone repo
git clone https://github.com/isthisvishal/Billing-Panel.git
cd Billing-Panel

# 2. Setup environment
cp .env.example .env
nano .env  # Edit: APP_URL, DB_PASSWORD

# 3. Update domain
nano Caddyfile  # Change domain to yours

# 4. Start
docker compose up -d --build
sleep 20
docker compose exec app php artisan migrate --force
docker compose exec app php artisan app:install

# 5. Done! Visit https://your-domain.com
```

## Common Commands

```bash
# View status
docker compose ps

# View logs
docker compose logs -f app

# Restart
docker compose restart

# Stop
docker compose down

# Run artisan command
docker compose exec app php artisan <command>
```

## Troubleshooting

**Site not loading after 5 minutes?**
```bash
docker compose logs web  # Check Caddy
docker compose logs app  # Check PHP
```

**Database error?**
```bash
docker compose exec app php artisan tinker
>>> DB::connection()->getPdo()
```

**Need to change domain?**
```bash
nano .env  # Update APP_URL
nano Caddyfile  # Update domain
docker compose exec web caddy reload -c /etc/caddy/Caddyfile
```

## Full Documentation

- [DEPLOYMENT.md](DEPLOYMENT.md) - Complete deployment guide
- [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md) - Step-by-step checklist
- [README.md](README.md) - Feature overview

## Need Help?

1. Check [DEPLOYMENT.md](DEPLOYMENT.md) Troubleshooting section
2. Review container logs: `docker compose logs`
3. Check GitHub Issues
4. Ensure port 80 and 443 are open in firewall

## Security Notes

- Change all default passwords
- Keep `APP_DEBUG=false` in production
- Use strong DB_PASSWORD
- Configure firewall to only allow 80/443
- Enable automatic backups
- Keep system packages updated

---

**Estimated Setup Time**: 10-15 minutes  
**Difficulty**: Easy (automated)
