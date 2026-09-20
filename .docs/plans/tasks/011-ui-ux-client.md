# Task 011 — UI/UX Phase C: Client, Auth, Errors Rework

Source: `.docs/plans/scopes/ui-ux-refactor.md` §4.4, §5 Phase C.

- [x] Rebuild `layouts/app.blade.php` as the branded public shell (brand header, footer, max-width container).
- [x] Add `layouts/client.blade.php` for client-facing pages (project + share) with brand header.
- [x] Login page redesign: brand block, `x-card`, `x-input`, `x-button`, inline field errors.
- [x] Build `components/password-gate.blade.php` (`x-password-gate`): unified password form for project + share; uses `x-card`, `x-input`, `x-alert` for errors.
- [x] Rebuild `public/password.blade.php` and `public/share-password.blade.php` using `x-password-gate` (remove duplication).
- [x] Rebuild `public/project.blade.php`: brand header, project title + company, invoice summary (count, total), invoice cards with status, `View Invoice` + document links.
- [x] Rebuild `public/share-invoice.blade.php`: client product look, invoice number/status header, `x-table` for items, totals, document buttons, print button.
- [x] Add print stylesheet so the share-invoice page prints cleanly (only invoice content).
- [x] Rebuild error pages (403/404/419/500) from one shared `errors/error-layout` template with `x-*` components; add a 429 page if missing.
- [x] Client pages: ensure zero admin vocabulary; verify no internal IDs/paths leak (existing tests cover this).
- [x] Keep all tests green (including `ClientAccessTest`, `InvoiceShareTest`); update assertions only if markup/strings change deliberately.
- [x] Run `pint`; manual review on desktop + mobile.