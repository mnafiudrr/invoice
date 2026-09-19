# Task 011 — UI/UX Phase C: Client, Auth, Errors Rework

Source: `.docs/plans/scopes/ui-ux-refactor.md` §4.4, §5 Phase C.

- [ ] Rebuild `layouts/app.blade.php` as the branded public shell (brand header, footer, max-width container).
- [ ] Add `layouts/client.blade.php` for client-facing pages (project + share) with brand header.
- [ ] Login page redesign: brand block, `x-card`, `x-input`, `x-button`, inline field errors.
- [ ] Build `components/password-gate.blade.php` (`x-password-gate`): unified password form for project + share; uses `x-card`, `x-input`, `x-alert` for errors.
- [ ] Rebuild `public/password.blade.php` and `public/share-password.blade.php` using `x-password-gate` (remove duplication).
- [ ] Rebuild `public/project.blade.php`: brand header, project title + company, invoice summary (count, total), invoice cards with status, `View Invoice` + document links.
- [ ] Rebuild `public/share-invoice.blade.php`: client product look, invoice number/status header, `x-table` for items, totals, document buttons, print button.
- [ ] Add print stylesheet so the share-invoice page prints cleanly (only invoice content).
- [ ] Rebuild error pages (403/404/419/500) from one shared `errors/error-layout` template with `x-*` components; add a 429 page if missing.
- [ ] Client pages: ensure zero admin vocabulary; verify no internal IDs/paths leak (existing tests cover this).
- [ ] Keep all tests green (including `ClientAccessTest`, `InvoiceShareTest`); update assertions only if markup/strings change deliberately.
- [ ] Run `pint`; manual review on desktop + mobile.