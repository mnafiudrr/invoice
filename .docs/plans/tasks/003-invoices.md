# Task 003 — Invoices (CRUD, items, numbering, calculations)

Phase 3. Source: `mvp.md` §24 Phase 3, `.docs/plans/features/003-invoice-management.md`.

- [ ] Create `invoices` + `invoice_items` migrations (per data-model.md).
- [ ] Create `Invoice` model (`belongsTo Project`, `hasMany InvoiceItems`, `hasMany Payments`, `hasMany Files`).
- [ ] Create `InvoiceItem` model (`belongsTo Invoice`).
- [ ] Invoice number generator: `INV-{YEAR}-{seq}` with unique enforcement.
- [ ] Invoice FormRequest (project, number, language, currency, dates, items, tax, notes).
- [ ] Totals calculation service: subtotal, tax, total (decimal rounding).
- [ ] Admin: invoice list page (filter by project/status).
- [ ] Admin: invoice create form (dynamic line-item rows via Alpine.js).
- [ ] Admin: invoice edit form.
- [ ] Admin: invoice detail page (items, totals, status, payments, files).
- [ ] Status transitions (draft → sent → paid/cancelled) with guards.
- [ ] Invoice preview route rendering HTML template.