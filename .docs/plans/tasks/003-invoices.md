# Task 003 — Invoices (CRUD, items, numbering, calculations)

Phase 3. Source: `mvp.md` §24 Phase 3, `.docs/plans/features/003-invoice-management.md`.

- [x] Create `invoices` + `invoice_items` migrations via `php artisan make:migration` (per data-model.md).
- [x] Create `Invoice` model (`belongsTo Project`, `hasMany InvoiceItems`, `hasMany Payments`, `hasMany Files`).
- [x] Create `InvoiceItem` model (`belongsTo Invoice`).
- [x] Invoice number generator: `INV-{YEAR}-{seq}` with unique enforcement.
- [x] Invoice FormRequest (project, number, language, currency, dates, items, tax, notes).
- [x] Totals calculation service: subtotal, tax, total (decimal rounding).
- [x] Admin: invoice list page (filter by project/status).
- [x] Admin: invoice create form (dynamic line-item rows via Alpine.js).
- [x] Admin: invoice edit form.
- [x] Admin: invoice detail page (items, totals, status, payments, files).
- [x] Status transitions (draft → sent → paid/cancelled) with guards.
- [x] Invoice preview route rendering HTML template.