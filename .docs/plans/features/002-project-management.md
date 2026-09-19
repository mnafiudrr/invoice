# Feature: Project Management

## Purpose
Group invoices per client. Each project carries client info, a unique slug, and a shared access password.

## User stories
- US-1: As the owner, I can create a project so invoices are grouped per client.
- US-2: As the owner, I can edit project details and regenerate the project password.
- US-3: As the owner, I can delete a project (and its invoices/files).

## Scope
- Project CRUD in admin.
- Auto-generated unique slug.
- Project password stored as bcrypt hash.
- Project detail page: client info, share URL, copy link / copy password, invoice list, file list.

## Acceptance criteria
- [ ] Admin can create a project with name, client name/email/company, description.
- [ ] Slug auto-generated and unique; editable/regenerable.
- [ ] Access password is hashed (`Hash::make`) on create/update; never returned in full by APIs.
- [ ] Share URL shown as `https://invoice.fiu.my.id/p/{slug}`.
- [ ] Copy Link and Copy Password buttons work.
- [ ] Project deletion cascades or is blocked when invoices exist (decide: restrict delete if invoices exist).
- [ ] Project detail lists all invoices with status + total.

## Data
- `projects` table. Relations: `belongsTo users`, `hasMany invoices`, `hasMany files`.

## Notes
- Deleting a project with invoices: recommend **restricting** (redirect with error) to avoid data loss; add a confirm step if allowed.