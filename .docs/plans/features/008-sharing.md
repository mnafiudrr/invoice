# Feature: Sharing Workflow

## Purpose
Make sharing an invoice to a client take ~10 seconds: copy link, copy password, paste into WhatsApp/email.

## User stories
- US-4: As the owner, I can copy a share link and password to send to a client.

## Scope
- Share URL + password displayed prominently on project detail.
- Copy Link / Copy Password buttons (Alpine.js + clipboard).
- Consistent shareable URL built via named routes.
- Individual invoice sharing via `share_links`: `/s/{token}` + password.

## Acceptance criteria
- [ ] Project detail shows share URL: `https://invoice.fiu.my.id/p/{slug}`.
- [ ] Copy Link button copies the URL.
- [ ] Copy Password button copies the plaintext password (shown once / reveal on demand).
- [ ] Share payload is exactly: link + password.
- [ ] Works regardless of environment by using `route()` + `APP_URL`.
- [ ] Invoice detail has a "Share Invoice" button that creates a token + password.
- [ ] New share link + password shown once, with copy buttons.
- [ ] Active share links listed on the invoice; can be revoked.
- [ ] `/s/{token}` shows a password form, then the invoice with its real status.
- [ ] Client can stream the shared invoice PDF and attached files.
- [ ] Expired share links return 404.

## Data
- `projects.slug`, `projects.access_password_hash`.
- `share_links` (project_id, invoice_id, token, password_hash, expires_at).

## Notes
- Store the password such that the owner can copy it: display once at creation.
- Passwords are bcrypt-hashed; the plaintext is flashed once and never persisted.
- Share-link expiration is supported by the schema; setting it via UI is future work.