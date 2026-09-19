# Rule: Directory Structure

Keep the Laravel layout boring and conventional. Follow this map; don't invent new top-level folders without updating this file.

## Top level

```
invoice-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   ├── Services/
│   └── Providers/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── docker/
│   └── nginx/
│       └── default.conf
├── lang/
│   ├── en/invoice.php
│   └── id/invoice.php
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── admin/
│       ├── public/
│       ├── pdf/
│       └── layouts/
├── routes/
├── storage/
│   └── app/private/
│       ├── invoices/{year}/
│       ├── receipts/{year}/
│       └── proofs/{year}/
├── tests/
├── .env.example
├── compose.yaml
├── Dockerfile
└── composer.json
```

## Rules

- **Controllers** → `app/Http/Controllers`. Group by domain: `Admin/`, `Client/`.
- **Form Requests** → `app/Http/Requests/`, named after action (`StoreProjectRequest`, `UpdateInvoiceRequest`).
- **Models** → `app/Models/`, plain Eloquent (see `minimal-model.md`).
- **Services** → `app/Services/`, one folder per domain: `InvoiceService`, `PdfService`, `PaymentService`, `ProjectService`.
- **Blade views** → `resources/views/`:
  - `admin/` — owner-facing pages (dashboard, projects, invoices).
  - `public/` — client-facing pages (project page, password form).
  - `pdf/` — PDF templates.
  - `layouts/` — shared layouts (admin layout, client layout, pdf layout).
- **Language files** → `lang/{en,id}/invoice.php` for the invoice template; general UI strings also go in `lang/{en,id}/`.
- **Migrations** → `database/migrations/`, one table per file.
- **Routes** → `routes/web.php` (or split: `routes/admin.php`, `routes/client.php`, both loaded from `web.php`/`RouteServiceProvider`).
- **Private files** → always under `storage/app/private/`; never `public/`.

## Anti-patterns

- No `app/Repositories`, `app/DTOs`, `app/ValueObjects` for MVP — premature.
- No business logic in `routes/` or `config/` files.
- No Blade views outside `resources/views/`.