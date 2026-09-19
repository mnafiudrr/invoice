# Feature: Sharing Workflow

## Purpose
Make sharing an invoice to a client take ~10 seconds: copy link, copy password, paste into WhatsApp/email.

## User stories
- US-4: As the owner, I can copy a share link and password to send to a client.

## Scope
- Share URL + password displayed prominently on project detail.
- Copy Link / Copy Password buttons (Alpine.js + clipboard).
- Consistent shareable URL built via named routes.

## Acceptance criteria
- [ ] Project detail shows share URL: `https://invoice.fiu.my.id/p/{slug}`.
- [ ] Copy Link button copies the URL.
- [ ] Copy Password button copies the plaintext password (shown once / reveal on demand).
- [ ] Share payload is exactly: link + password.
- [ ] Works regardless of environment by using `route()` + `APP_URL`.

## Data
- `projects.slug`, `projects.access_password_hash` (decrypted only at reveal-time; consider a one-time reveal for security).

## Notes
- Store the password such that the owner can copy it: either display once at creation, or provide a "Reveal password" toggle. Prefer reveal-on-demand over storing plaintext.
- Future (out of scope): per-invoice share links + `share_links` table + token URLs `/s/{token}`.