# 🚀 Billing-Panel Deployment Guide

## ⚡ FASTEST WAY TO DEPLOY (Copy & Paste This)

### For Your Linux VM/Server:

```bash
curl -fsSL https://github.com/isthisvishal/Billing-Panel/raw/main/scripts/install.sh | sudo bash
```

**That's it!** The script will ask for:
1. Your domain name (e.g., `billing.example.com`)
2. MySQL password (make it strong!)

Then it automatically:
- ✅ Installs Docker if needed
- ✅ Sets up all services
- ✅ Configures HTTPS with Caddy
- ✅ Runs database migrations
- ✅ Starts the application

**Time needed:** 10-15 minutes  
**Cost:** FREE (uses Docker)

---

## 📋 What Gets Deployed

| Component | What It Does |
|-----------|-------------|
| **PHP 8.2 App** | Your Billing Panel application |
| **MySQL 8** | Database for all data |
| **Redis** | Cache and queue system |
| **Caddy** | Web server with automatic SSL/HTTPS |
| **Worker** | Background job processing |
| **Scheduler** | Automated daily tasks |

---

## 🎯 Step-by-Step Manual Deployment (If Script Fails)

### Step 1: Prepare Server
```bash
# SSH into your server
ssh root@your-server-ip

# Update system
apt-get update && apt-get upgrade -y
apt-get install -y curl git

# Install Docker
curl -fsSL https://get.docker.com | bash
usermod -aG docker root
```

### Step 2: Get Code
```bash
git clone https://github.com/isthisvishal/Billing-Panel.git
cd Billing-Panel
```

### Step 3: Configure
```bash
# Copy template
cp .env.example .env

# Edit with your settings
nano .env
# Change these:
# - APP_URL=https://your-domain.com
# - DB_PASSWORD=your-secure-password
```

### Step 4: Web Server Setup
```bash
# Edit Caddyfile
nano Caddyfile
# Change "billing.example.com" to your domain
```

### Step 5: Deploy
```bash
# Start everything
docker compose up -d --build

# Wait and run migrations
sleep 20
docker compose exec app php artisan migrate --force

# All done! Visit https://your-domain.com
```

---

## ✅ Verify It's Working

```bash
# Check all containers running
docker compose ps

# View application logs
docker compose logs -f app

# Test database connection
docker compose exec app php artisan tinker
# In tinker: DB::connection()->getPdo()
# Should show connection info
```

---

## 🛠 Useful Commands

### Daily Operations
```bash
# Check status
docker compose ps

# View real-time logs
docker compose logs -f app

# Restart everything
docker compose restart

# Stop all services
docker compose down

# Start again
docker compose up -d
```

### Run Artisan Commands
```bash
# Run any Laravel command
docker compose exec app php artisan <command>

# Examples:
docker compose exec app php artisan migrate
docker compose exec app php artisan cache:clear
docker compose exec app php artisan invoices:scan
```

### Database
```bash
# Access MySQL directly
docker compose exec db mysql -u root -p billing

# Backup database
docker compose exec db mysqldump -u root -p billing > backup.sql

# Restore from backup
docker compose exec -T db mysql -u root -p billing < backup.sql
```

---

## 🐛 Troubleshooting

### "Website won't load"
```bash
# Check web server (Caddy)
docker compose logs web

# Check if containers are running
docker compose ps

# Restart all
docker compose down
docker compose up -d --build
```

### "Can't connect to database"
```bash
# Check MySQL logs
docker compose logs db

# Check if MySQL is healthy
docker compose exec app php artisan tinker
# Then: DB::connection()->getPdo()
```

### "Jobs not processing"
```bash
# Check worker container
docker compose logs worker

# Restart worker
docker compose restart worker
```

### "Want to change domain after deploy"
```bash
# Update .env
nano .env  # Change APP_URL

# Update Caddyfile
nano Caddyfile  # Change domain

# Reload Caddy
docker compose exec web caddy reload -c /etc/caddy/Caddyfile
```

---

## 🔒 Security Checklist (Before Going Live)

- [ ] Change `DB_PASSWORD` in `.env` to strong password
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate proper `APP_KEY`: `docker compose exec app php artisan key:generate`
- [ ] Configure firewall: Only allow ports 80 and 443
- [ ] Set strong admin passwords
- [ ] Configure payment gateway (Stripe, PayPal, etc.)
- [ ] Enable Discord notifications for errors
- [ ] Set up automated database backups
- [ ] Keep Docker images updated: `docker pull` all images monthly

---

## 💾 Backup & Restore

### Automatic Daily Backup (Recommended)
```bash
# Create backups directory
mkdir -p backups

# Add to crontab (run daily at 2 AM)
crontab -e

# Add this line:
0 2 * * * cd /path/to/Billing-Panel && docker compose exec -T db mysqldump -uroot -p$DB_PASSWORD billing | gzip > backups/backup-$(date +\%Y\%m\%d).sql.gz
```

### Manual Backup Now
```bash
docker compose exec db mysqldump -u root -p billing | gzip > backup-now.sql.gz
```

### Restore From Backup
```bash
zcat backup-now.sql.gz | docker compose exec -T db mysql -u root -p billing
```

---

## 📈 Scaling for More Traffic

```bash
# Scale to 3 worker processes
docker compose up -d --scale worker=3

# Scale to 5
docker compose up -d --scale worker=5

# View current workers
docker compose ps | grep worker
```

---

## 📞 Getting Help

1. **Quick questions?** → See [QUICKSTART.md](QUICKSTART.md)
2. **Complete guide?** → See [DEPLOYMENT.md](DEPLOYMENT.md)
3. **Step-by-step checklist?** → See [DEPLOYMENT_CHECKLIST.md](DEPLOYMENT_CHECKLIST.md)
4. **Issues?** → Check container logs: `docker compose logs`

---

## 🎓 Learning Resources

- **Laravel**: https://laravel.com/docs
- **Docker**: https://docs.docker.com
- **Caddy**: https://caddyserver.com/docs
- **MySQL**: https://dev.mysql.com/doc

---

## 📝 Configuration Files Reference

| File | Purpose |
|------|---------|
| `.env` | Environment variables (create from `.env.example`) |
| `Caddyfile` | Web server config (change domain here) |
| `docker-compose.yml` | Docker services configuration |
| `scripts/install.sh` | Automated installation script |

---

## ❓ FAQ

**Q: Can I deploy on Windows/Mac?**  
A: Use Windows Subsystem for Linux (WSL2) or Docker Desktop for Mac

**Q: What if Docker isn't installed?**  
A: The install script installs it automatically!

**Q: Is HTTPS included?**  
A: Yes! Caddy provides automatic SSL certificates

**Q: Can I use my own domain?**  
A: Yes! Just point your DNS to your server's IP

**Q: Is the database secure?**  
A: It's on a private Docker network. Change the password in `.env`

**Q: What if something breaks?**  
A: Run `docker compose down` and start fresh, or restore from backup

---

## 🚀 You're Ready!

Your Billing Panel is production-ready with:
- ✅ Automated deployment
- ✅ SSL/HTTPS included
- ✅ Database backups
- ✅ Job queue processing
- ✅ Scheduled tasks
- ✅ Automatic restarts
- ✅ Full documentation

**Deploy it now:**
```bash
curl -fsSL https://github.com/isthisvishal/Billing-Panel/raw/main/scripts/install.sh | sudo bash
```

Good luck! 🎉
