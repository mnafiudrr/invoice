# Feature: Owner Authentication

## Purpose
Let the single application owner log in and protect all admin routes. No public registration.

## User stories
- US-A1: As the owner, I can log in with email + password so my invoices stay private.
- US-A2: As the owner, I am logged out when I log out or after session expiry.

## Scope
- Laravel `auth` scaffolding (web only).
- `/login` + `/logout`.
- Single seeded owner account.
- `auth` middleware on `/admin*`.

## Acceptance criteria
- [ ] `/login` renders a login form; credentials verified with `Auth::attempt`.
- [ ] No `/register` route exists.
- [ ] Unauthenticated access to `/admin*` redirects to `/login`.
- [ ] Owner account exists via seeder (email + hashed password).
- [ ] Login attempts rate-limited (e.g. `throttle`).
- [ ] Successful login redirects to `/admin`; logout returns to `/login`.

## Data
- `users` table (standard Laravel).

## Notes
- No roles/permissions system — single owner is implied by auth middleware.