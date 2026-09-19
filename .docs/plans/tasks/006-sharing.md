# Task 006 — Sharing & Client Access (public pages, session auth)

Phase 6. Source: `mvp.md` §24 Phase 6, `.docs/plans/features/007-client-access.md`, `008-sharing.md`.

- [ ] Public project page route `GET /p/{project:slug}`.
- [ ] Password form view for unauthorized clients.
- [ ] POST password action → `Hash::check` → session flag `project_access.{project_id}`.
- [ ] Rate-limit project password attempts.
- [ ] Authorized project page: invoice list (number, date, total, status).
- [ ] Client-safe UI: no internal IDs, no paths, no admin controls.
- [ ] Client invoice route `GET /p/{project:slug}/f/{invoice:invoice_number}`.
- [ ] Route-model binding on `invoice_number` for client routes.
- [ ] Authorized inline PDF streaming (browser PDF viewer).
- [ ] Stream attached receipt/proof files to authorized clients.
- [ ] Unauthorized file access → password page (never expose file).
- [ ] Share URL built via named routes on project detail.
- [ ] Copy Link + Copy Password on admin project detail (with reveal-on-demand).
- [ ] End-to-end test: create → generate → copy → open → view.