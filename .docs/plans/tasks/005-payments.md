# Task 005 — Payments (records, mark paid, uploads)

Phase 5. Source: `mvp.md` §24 Phase 5, `.docs/plans/features/005-payment-management.md`.

- [ ] Create `payments` migration via `php artisan make:migration create_payments_table` (per data-model.md).
- [ ] Create `Payment` model (`belongsTo Invoice`).
- [ ] Create `files` migration via `php artisan make:migration create_files_table` (project_id, invoice_id, type, path, original_filename, mime_type, size).
- [ ] Create `File` model (`belongsTo Project`, `belongsTo Invoice`).
- [ ] "Mark as Paid" form: amount, paid_at, method, reference, notes.
- [ ] Service: on mark-paid, create payment + set invoice status → `paid`.
- [ ] Guard: cannot mark paid twice without confirmation; un-mark only when no payments.
- [ ] Receipt upload (`payment_receipt`) stored privately.
- [ ] Proof upload (`payment_proof`) stored privately.
- [ ] Generic file upload action for `other` types.
- [ ] Validate mime + size limits on uploads.
- [ ] File delete action removes DB row + physical file.
- [ ] Show paid-on date + documents on invoice detail.
- [ ] Client project page shows PAID + paid date + document links.