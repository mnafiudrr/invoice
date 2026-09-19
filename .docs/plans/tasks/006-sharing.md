# Task 006 — Sharing & Client Access (public pages, session auth)

Phase 6. Source: `mvp.md` §24 Phase 6, `.docs/plans/features/007-client-access.md`, `008-sharing.md`.

- [x] Public project page route `GET /p/{project:slug}`.
- [x] Password form view for unauthorized clients.
- [x] POST password action → `Hash::check` → session flag `project_access.{project_id}`.
- [x] Rate-limit project password attempts.
- [x] Authorized project page: invoice list (number, date, total, status).
- [x] Client-safe UI: no internal IDs, no paths, no admin controls.
- [x] Client invoice route `GET /p/{project:slug}/f/{invoice:invoice_number}`.
- [x] Route-model binding on `invoice_number` for client routes.
- [x] Authorized inline PDF streaming (browser PDF viewer).
- [x] Stream attached receipt/proof files to authorized clients.
- [x] Unauthorized file access → password page (never expose file).
- [x] Share URL built via named routes on project detail.
- [x] Copy Link + Copy Password on admin project detail (with reveal-on-demand).
- [x] End-to-end test: create → generate → copy → open → view.