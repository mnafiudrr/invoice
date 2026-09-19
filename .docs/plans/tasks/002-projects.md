# Task 002 — Projects (CRUD, slug, password)

Phase 2. Source: `mvp.md` §24 Phase 2, `.docs/plans/features/002-project-management.md`.

- [ ] Create `projects` migration (per data-model.md).
- [ ] Create `Project` model + relationships (`belongsTo User`, `hasMany Invoices`, `hasMany Files`).
- [ ] Add unique index on `projects.slug`.
- [ ] Create project FormRequest with validation rules.
- [ ] Project slug generator (str-slug + unique suffix on collision).
- [ ] Store `access_password_hash` with `Hash::make`.
- [ ] Admin: project list page.
- [ ] Admin: project create form + store.
- [ ] Admin: project edit form + update.
- [ ] Admin: project delete (block when invoices exist).
- [ ] Admin: project detail page (client info, share URL, invoices, files).
- [ ] "Regenerate project password" action.
- [ ] Copy Link / Copy Password buttons (Alpine.js clipboard).