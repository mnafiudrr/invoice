# Invoice Web

A small, private invoice/document management web application at `invoice.fiu.my.id`.

It lets the owner store client invoices and payment documents, organize them by project, generate professional bilingual (ID/EN) invoice PDFs, and share each project with a client using only a link + a password — no client accounts.

## Features

- **Owner authentication** — single admin login, no public registration.
- **Project management** — projects group invoices per client, each with a unique slug and a shared access password.
- **Invoice management** — line items, auto-calculated totals, unique numbering (`INV-{YEAR}-{seq}`), statuses (`draft / sent / paid / cancelled`), ID/EN localization.
- **PDF generation** — professional invoice PDFs rendered from a Blade template via Dompdf, stored privately.
- **Payments & documents** — mark invoices paid, record payments, upload receipts/proofs.
- **Sharing** — project share (`/p/{slug}`) with a password, and individual invoice shares (`/s/{token}`).
- **Private storage** — PDFs live in `storage/app/private`, served only through authorized endpoints.

## Tech stack

- Laravel 11 (PHP 8.4), Blade + Tailwind CSS + Alpine.js
- PostgreSQL
- Docker (app / nginx / postgres)
- Dompdf for PDF generation

## Getting started (Docker)

```bash
cp .env.example .env
# set APP_KEY (php artisan key:generate), DB_*, OWNER_* values

docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
```

The app is then served at `http://localhost:${APP_PORT:-8080}` (see `.env`).

The app container's **entrypoint** (`docker/php/entrypoint.sh`) self-provisions on startup: it fixes `storage/` + `bootstrap/cache` ownership for `www-data` (prevents the HTTP 500 "Permission denied" on compiled views), runs `composer install` when `vendor/` is missing, builds frontend assets when `public/build/manifest.json` is missing, and generates `APP_KEY` if unset. This makes `docker compose up -d --build` work on a fresh server without manual `composer install` / `npm run build`.

### Owner account

The single owner is seeded from env vars:

```dotenv
OWNER_NAME="..."
OWNER_EMAIL=admin@invoice.fiu.my.id
OWNER_PASSWORD=...
OWNER_COMPANY="Fiu Project"
OWNER_ADDRESS="..."
OWNER_PHONE="..."
```

## Local development (no Docker)

```bash
composer install
npm install
php artisan serve
npm run dev   # Vite
```

Tests use SQLite in-memory by default (`phpunit.xml`).

## Tests & lint

```bash
php artisan test        # PHPUnit (unit + feature)
vendor/bin/pint         # code style
```

## Project structure

- `app/Services/` — business logic (InvoiceService, ProjectService, PaymentService, FileService, ShareLinkService, PdfGenerator)
- `app/Http/Controllers/Admin/` and `app/Http/Controllers/Client/` — thin controllers
- `resources/views/components/` — reusable Blade UI components (`x-button`, `x-card`, `x-table`, …)
- `resources/views/pdf/` — PDF invoice template
- `.docs/` — planning docs, diagrams, task lists, and coding rules

## Documentation

- Planning: `.docs/plans/` (scope, PRD, data model, security, URL structure, workflows, tasks)
- Diagrams: `.docs/diagrams/` (PlantUML ERD + flowcharts)
- Coding rules: `.docs/rules/` (structure, migrations, models, services, views, security, testing, commit/PR)

## License

Private project. Not open-source.