# Deployment & Backup

## Deployment

- Target: any Docker-capable VPS, behind Cloudflare for DNS + HTTPS.
- `docker compose up -d --build` on the server.
- Set real secrets in `.env` (`APP_KEY`, `OWNER_*`, `DB_*`, `APP_URL=https://invoice.fiu.my.id`).
- Run once: `docker compose exec app php artisan migrate --seed --force`.
- The app runs as a non-root PHP-FPM user (`www-data`); nginx serves `public/` only.
- Assets: build once on deploy (`npm ci && npm run build`) so `public/build` is committed/present; or mount a built `public/build`.

## Production hardening checklist

- [ ] `APP_ENV=production`, `APP_DEBUG=false`
- [ ] HTTPS terminated at Cloudflare (and optionally nginx TLS)
- [ ] Strong `OWNER_PASSWORD`
- [ ] `storage/app/private` never symlinked into `public/`
- [ ] Login + project password attempts rate-limited (already configured)
- [ ] Regular `config:cache` / `route:cache` on deploy

## Backup strategy

Two parts must be backed up: **PostgreSQL data** and **private files**.

```bash
# Database dump (run on host or via docker)
docker compose exec postgres pg_dump -U invoice invoice > backup/$(date +%F).sql

# Private storage (invoice PDFs, receipts, proofs)
rsync -a storage/app/private/ backup/private/
```

Restore:

```bash
docker compose exec -T postgres psql -U invoice invoice < backup/2026-09-19.sql
rsync -a backup/private/ storage/app/private/
```

- Schedule both with cron; store off-server (e.g. private S3, another host).
- The `files` table stores only metadata; the physical files must be restored to `storage/app/private/` for the URLs to keep working.