# Task 008 — Single-Invoice Sharing (share_links)

Follow-up to Phase 6. Source: `mvp.md` §11, `.docs/plans/features/008-sharing.md`, `.docs/plans/scopes/data-model.md`.

- [x] Create `share_links` migration via `php artisan make:migration create_share_links_table` (per data-model.md).
- [x] Create `ShareLink` model (`belongsTo Project`, `belongsTo Invoice`, `isExpired()`).
- [x] Create `ShareLinkService`: unique token, hashed password, verify, revoke.
- [x] Admin invoice page: "Share Invoice" button; share URL + password shown once with copy buttons.
- [x] Active share links listed with revoke action.
- [x] Public routes `/s/{token}`: password form → invoice view with real status (paid/unpaid).
- [x] `share.access` middleware + session flag; expired links 404.
- [x] Rate-limited share password attempts.
- [x] Shared PDF + attached file streaming via private disk.
- [x] Feature tests (InvoiceShareTest, 61 total passing).