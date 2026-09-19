# Security Requirements

> Minimum security bar for the MVP. Derived from `mvp.md` §6 and §22.

## 1. Transport & Authentication

- HTTPS everywhere (Cloudflare / Nginx TLS).
- Owner login only at `/login`. **No public registration route.**
- Single seeded owner account in the standard `users` table.
- All `/admin*` routes behind `auth` middleware.

## 2. Password Handling

- Owner password: Laravel default hashing (`Hash::make` / `Hash::check`).
- Project passwords: also stored as bcrypt hash, never plaintext.
- **Never** use SHA-256/SHA-512 for password hashing.
- Never log or display hashes.

## 3. Client Authorization

- Project access gated by password form.
- On success: `session(['project_access.'.$project->id => true])`.
- Session flag grants access to all invoices in that project for the session.
- Individual invoice routes (`/p/{slug}/f/{invoice}`) must re-check the same session flag before serving content.
- Rate-limit password attempts (ThrottleRequests / dedicated rate limiter).

## 4. Private File Storage

- Files stored under `storage/app/private/` (a dedicated private disk), **never** in `public/`.
- No symlink `php artisan storage:link` exposure for invoices.
- Physical filenames are generated UUIDs/ULIDs; original names only in DB metadata.

## 5. Serving Files

- PDFs are served only through an authorized Laravel endpoint that:
  1. verifies project/invoice authorization;
  2. resolves the file via the `files` table;
  3. streams the file (e.g. `Storage::disk('private')->response(...)`) with `Content-Disposition: inline`.
- Clients must never obtain a direct public URL to the file.
- Do not rely on URL obscurity as a security mechanism.

## 6. Request Hardening

- CSRF protection enabled (Laravel default) on all POST forms.
- Server-side validation on all inputs (Form Requests).
- Server-side authorization checks, never rely on UI hiding alone.
- No exposure of filesystem paths, internal IDs, or DB values to clients.
- Client pages render only whitelisted fields.

## 7. Checklist

- [ ] HTTPS enabled
- [ ] No registration route
- [ ] Owner auth middleware on `/admin*`
- [ ] Project passwords bcrypt-hashed
- [ ] Rate limiting on login + project password attempts
- [ ] Private disk configured; no public symlink
- [ ] UUID/ULID physical filenames
- [ ] Authorized streaming endpoint only for PDFs
- [ ] CSRF active
- [ ] Validation + authorization on every mutation
- [ ] No paths/internal IDs leaked to clients