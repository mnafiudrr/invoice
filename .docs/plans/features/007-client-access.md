# Feature: Client Access

## Purpose
Let clients view project invoices with only a link + password, without accounts. Keep all storage private.

## User stories
- US-5: As a client, I can open a shared link, enter the password, and view my invoices.
- US-6: As a client, I can open an invoice PDF in the browser without seeing the storage URL.

## Scope
- `GET /p/{project:slug}` — public project page.
- Password form when not authorized.
- Session flag `project_access.{project_id} = true` on success.
- `GET /p/{project:slug}/f/{invoice:invoice_number}` — authorized inline PDF stream.
- File streaming for receipts/proofs.
- Client UI must differ from admin UI: no admin controls, no internal IDs, no paths.

## Acceptance criteria
- [ ] Unauthorized visit to `/p/{slug}` shows password form (not invoice data).
- [ ] Correct password stores session flag and shows the invoice list.
- [ ] Wrong password shows error; attempts rate-limited.
- [ ] All invoices of the project visible after one password entry.
- [ ] Invoice page streams PDF inline (browser PDF viewer) — never a redirect to storage.
- [ ] Accessing another project's slug/invoice without its password is denied.
- [ ] Page shows zero internal identifiers (no ids, no file paths, no DB values).
- [ ] Session flag per project; logging in as owner does not unlock client pages and vice versa.

## Data
- Reads `projects`, `invoices`, `invoice_items`, `payments`, `files`.
- Uses `session` only; no `share_links` table in MVP.

## Notes
- Project password regenerated → new sessions need the new password; old sessions stay valid until they expire (documented in workflows).