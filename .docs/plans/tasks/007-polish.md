# Task 007 — Polish & Hardening

Phase 7. Source: `mvp.md` §24 Phase 7, `.docs/plans/scopes/security.md`, `.docs/plans/features/009-admin-dashboard.md`.

- [x] Dashboard with summary cards (projects, invoices, paid, unpaid).
- [x] Recent invoices list on dashboard.
- [x] Improve invoice PDF design (typography, spacing, status badge).
- [x] Responsive UI pass on all admin + client pages.
- [x] Error pages (403/404/419/500) styled.
- [x] Empty states for lists/dashboards.
- [x] Rate limiting configured globally (login + password attempts).
- [x] Money/date formatting helper consistent across pages.
- [x] Production Docker config (non-root user, nginx, optimized).
- [x] Backup strategy note (Postgres dump + private storage copy).
- [x] `.env.example` documenting all config.
- [x] Security checklist review from security.md.
- [x] Final end-to-end smoke test in Docker.