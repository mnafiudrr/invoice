# Feature: Payment Management

## Purpose
Record when a client pays, transition the invoice to `paid`, and attach receipts/proofs.

## User stories
- US-7: As the owner, I can mark an invoice paid and attach a receipt/proof.

## Scope
- Mark invoice as paid from `draft|sent`.
- Create a `payments` record (amount, paid_at, method, reference, notes).
- Upload payment receipt (`payment_receipt`) and payment proof (`payment_proof`) files.
- Reflect PAID + paid-on date on client pages.

## Acceptance criteria
- [ ] "Mark as Paid" form captures amount, paid_at, method, reference, notes.
- [ ] On submit: payment row created; invoice status → `paid`.
- [ ] Receipt and/or proof upload stored privately with `type = payment_receipt | payment_proof`.
- [ ] Invoice with status `paid` shows PAID badge and paid-on date on project page.
- [ ] Client can view attached receipt/proof through the authorized streaming endpoint.
- [ ] Existing paid invoice can have additional documents uploaded (multiple `files` rows).
- [ ] Un-marking (back to `sent`) is allowed only if no payment rows exist (or confirm-delete payment).

## Data
- `payments`, `files` tables; mutates `invoices.status`.

## Notes
- MVP: one payment per invoice UI. Schema allows many; do not build multi-payment UI yet.