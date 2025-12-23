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

## Running
- Use `scripts/install.sh` to run a one-line install on a fresh server (Debian/Ubuntu)
- The system uses the database queue connection; run `php artisan queue:work --tries=3` in the `worker` container or via Docker Compose

## Notes
Everything configurable from admin panel: plugins, API keys and grace rules are stored in DB via `AdminSetting` and `plugin_configs`.

For full production deployment and hardening, add real plugin implementations for Proxmox, Pterodactyl, Stripe, PayPal, Cashfree and Razorpay, and enable HTTPS with Caddy or your reverse proxy of choice.
