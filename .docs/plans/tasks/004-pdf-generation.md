# Task 004 — PDF Generation (templates, i18n, storage, streaming)

Phase 4. Source: `mvp.md` §24 Phase 4, `.docs/plans/features/004-pdf-generation.md`.

- [x] Create single invoice Blade template `resources/views/pdf/invoice.blade.php`.
- [x] Add localization files `lang/en/invoice.php` + `lang/id/invoice.php`.
- [x] Template uses translated labels; same layout for both languages.
- [x] Build `PdfGenerator` interface + `DompdfPdfGenerator` implementation.
- [x] Register PdfGenerator in a service provider (bound as singleton).
- [x] Configure private storage disk (e.g. `private` → `storage/app/private`).
- [x] Generate PDF → store under `invoices/{year}/` with UUID/ULID filename.
- [x] Create `files` row with `type = invoice` + metadata.
- [x] Preview route (HTML) without PDF write.
- [x] "Generate Invoice" action (regenerate replaces file).
- [x] Authorized streaming endpoint for owner (`/admin/invoices/{invoice}/pdf`).
- [x] Client PDF endpoint (`/p/{slug}/f/{invoice}`) streams inline after auth (see task 006).
- [x] PAID status visual on PDF when invoice is paid.
- [x] Verify no PDF ever written to `public/`.