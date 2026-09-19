# Task 005 — Payments (records, mark paid, uploads)

Phase 5. Source: `mvp.md` §24 Phase 5, `.docs/plans/features/005-payment-management.md`.

- [x] Create `payments` migration via `php artisan make:migration create_payments_table` (per data-model.md).
- [x] Create `Payment` model (`belongsTo Invoice`).
- [x] Create `files` migration via `php artisan make:migration create_files_table` (project_id, invoice_id, type, path, original_filename, mime_type, size).
- [x] Create `File` model (`belongsTo Project`, `belongsTo Invoice`).
- [x] "Mark as Paid" form: amount, paid_at, method, reference, notes.
- [x] Service: on mark-paid, create payment + set invoice status → `paid`.
- [x] Guard: cannot mark paid twice without confirmation; un-mark only when no payments.
- [x] Receipt upload (`payment_receipt`) stored privately.
- [x] Proof upload (`payment_proof`) stored privately.
- [x] Generic file upload action for `other` types.
- [x] Validate mime + size limits on uploads.
- [x] File delete action removes DB row + physical file.
- [x] Show paid-on date + documents on invoice detail.
- [x] Client project page shows PAID + paid date + document links.