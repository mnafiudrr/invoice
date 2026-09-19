# Workflows

> Detailed step-by-step product workflows. Source: `mvp.md` §6, §12, §18, §19, §21.

## W1 — Share an invoice to a client (the "10-second" flow)

1. Owner opens project detail.
2. Click **Copy Link** → `https://invoice.fiu.my.id/p/website-development-pt-abc`.
3. Click **Copy Password** → e.g. `X8mP2kL9Qv7R`.
4. Paste both into WhatsApp/email.
5. Client opens the link, enters the password once.
6. Client browses all invoices of that project in the session.

## W2 — Create + generate an invoice

1. Create project (name, slug, client info, project password) if needed.
2. Create invoice: project, number, language, currency, issued/due dates, line items, tax, notes.
3. Preview the HTML invoice.
4. Generate PDF (single Blade template, localized labels).
5. PDF is saved to private storage; a `files` row of type `invoice` is created.
6. Send share link + password to client.

## W3 — Client access (authorization gate)

1. Client GETs `/p/{slug}`.
2. If `project_access.{id}` is in session → show invoice list.
3. Else → password form.
4. POST password → `Hash::check` → store session flag → redirect back.
5. On invoice link: verify session flag → find private PDF → stream inline.
6. Failure to authorize → password page (never expose file or path).

## W4 — Mark invoice paid + attach documents

1. Owner opens invoice detail.
2. Click **Mark as Paid**.
3. Record payment: amount, paid_at, method, reference, notes.
4. Upload receipt → `payment_receipt` file.
5. Upload client transfer proof → `payment_proof` file.
6. Invoice status becomes `paid`.
7. Client project page now shows PAID + paid-on date + document links.

## W5 — Payments detail

- MVP: one payment record per invoice is sufficient; the schema allows many.
- Total paid across payments is the source of truth for "paid" state, but the MVP keeps a simple boolean status transition on the invoice.

## Edge cases

- **Wrong project password:** show error, rate-limit attempts, stay on password form.
- **Accessing another project's invoice:** blocked by session flag scoped per project.
- **Deleted project:** client pages 404; share links stop working.
- **Regenerated project password:** previous client sessions remain valid until session ends (accepted for MVP; note in docs).