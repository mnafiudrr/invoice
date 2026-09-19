# Task 004 — PDF Generation (templates, i18n, storage, streaming)

Phase 4. Source: `mvp.md` §24 Phase 4, `.docs/plans/features/004-pdf-generation.md`.

- [ ] Create single invoice Blade template `resources/views/pdf/invoice.blade.php`.
- [ ] Add localization files `lang/en/invoice.php` + `lang/id/invoice.php`.
- [ ] Template uses translated labels; same layout for both languages.
- [ ] Build `PdfGenerator` interface + `DompdfPdfGenerator` implementation.
- [ ] Register PdfGenerator in a service provider (bound as singleton).
- [ ] Configure private storage disk (e.g. `private` → `storage/app/private`).
- [ ] Generate PDF → store under `invoices/{year}/` with UUID/ULID filename.
- [ ] Create `files` row with `type = invoice` + metadata.
- [ ] Preview route (HTML) without PDF write.
- [ ] "Generate Invoice" action (regenerate replaces file).
- [ ] Authorized streaming endpoint for owner (`/admin/invoices/{invoice}/pdf`).
- [ ] Client PDF endpoint (`/p/{slug}/f/{invoice}`) streams inline after auth (see task 006).
- [ ] PAID status visual on PDF when invoice is paid.
- [ ] Verify no PDF ever written to `public/`.