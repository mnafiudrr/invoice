# Task 001 — Foundation (Laravel + Docker + Auth)

Phase 1. Source: `mvp.md` §24 Phase 1, `.docs/plans/features/001-authentication.md`.

- [ ] Scaffold Laravel project (PHP, composer).
- [ ] Create `compose.yaml` with services: `app`, `nginx`, `postgres`.
- [ ] Add `Dockerfile` (php-fpm) with PHP extensions (pgsql, gd/intl if needed).
- [ ] Add `docker/nginx/default.conf` (Laravel-friendly nginx site).
- [ ] Configure PostgreSQL connection in `.env` + `config/database.php`.
- [ ] Run migrations against PostgreSQL.
- [ ] Enable Laravel auth scaffolding (web).
- [ ] Add login + logout routes; ensure no `/register` route.
- [ ] Add `auth` middleware group for `/admin*`.
- [ ] Create owner seeder (email + hashed password via `Hash::make`).
- [ ] Rate-limit login attempts (throttle middleware).
- [ ] Install Tailwind CSS + Alpine.js.
- [ ] Build base admin layout (sidebar/topbar) with Tailwind.
- [ ] Add `/admin` placeholder dashboard route behind auth.
- [ ] Verify `docker compose up` boots app + nginx + postgres together.