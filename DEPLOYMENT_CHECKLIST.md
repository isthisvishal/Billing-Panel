# Billing-Panel Deployment Checklist

## Pre-Deployment

- [ ] Domain registered and DNS configured
- [ ] Server provisioned (2GB+ RAM, 10GB+ storage)
- [ ] SSH access to server
- [ ] Server OS: Debian 11+, Ubuntu 20.04+, or equivalent

## Deployment Day

### Option A: One-Command Installation (Recommended)

```bash
# SSH into your server
ssh root@your-server-ip

# Run the installer
curl -fsSL https://github.com/isthisvishal/Billing-Panel/raw/main/scripts/install.sh | sudo bash
```

When prompted:
- Enter your domain (e.g., `billing.example.com`)
- Enter a strong MySQL root password

### Option B: Manual Deployment

```bash
# SSH into your server
ssh root@your-server-ip

# Clone and setup
git clone https://github.com/isthisvishal/Billing-Panel.git
cd Billing-Panel

# Configure
cp .env.example .env
nano .env  # Update with your domain and passwords

# Update Caddyfile
nano Caddyfile  # Change billing.example.com to your domain

# Deploy
docker compose up -d --build
sleep 15
docker compose exec app php artisan migrate --force
docker compose exec app php artisan app:install
```

## Post-Deployment Verification

- [ ] Visit `https://your-domain.com` in browser
- [ ] Verify SSL certificate is valid (green lock icon)
- [ ] Check all containers running: `docker compose ps`
- [ ] Review logs for errors: `docker compose logs`
- [ ] Database migrations completed successfully
- [ ] Admin panel accessible

## Initial Configuration

- [ ] Log in to admin panel
- [ ] Configure payment gateway (Stripe, PayPal, etc.)
- [ ] Set up Discord webhook for notifications
- [ ] Configure email settings
- [ ] Set up admin users
- [ ] Test invoice creation and processing

## Production Hardening

- [ ] Change MySQL root password to strong value
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Update `APP_KEY` if empty
- [ ] Enable HTTPS (automatic with Caddy)
- [ ] Configure firewall (allow 80, 443 only)
- [ ] Set up automated backups
- [ ] Monitor disk space and resources
- [ ] Configure log rotation

## Monitoring & Maintenance

- [ ] Set up health check monitoring
- [ ] Configure log aggregation
- [ ] Schedule regular backups
- [ ] Plan security patching schedule
- [ ] Monitor Docker container resources

## Rollback Plan

If deployment fails:

```bash
# Stop all services
docker compose down

# Restore from backup (if available)
zcat backup.sql.gz | docker compose exec -T db mysql -uroot -p billing

# Restart services
docker compose up -d
```

## Support

- Check `/workspaces/Billing-Panel/DEPLOYMENT.md` for detailed troubleshooting
- Review Docker logs: `docker compose logs`
- Check application logs inside container
- Consult GitHub Issues for known problems

## Completion Checklist

- [ ] Application is live and accessible
- [ ] SSL certificate working
- [ ] Database is running and migrations completed
- [ ] Queue worker is processing jobs
- [ ] Admin panel is functional
- [ ] Notifications are configured
- [ ] Backups are scheduled
- [ ] Team has access and documentation

---

**Deployment Date**: _______________  
**Deployed By**: _______________  
**Live URL**: _______________  
**Notes**: _____________________________________________________________________________
