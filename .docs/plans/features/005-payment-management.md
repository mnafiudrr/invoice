# Feature: Payment Management

## Purpose
Record client payments — including multiple/partial payments — and derive the invoice's payment status, plus attach receipts/proofs.

## User stories
- US-7: As the owner, I can record payments against an invoice and attach a receipt/proof.
- As the owner, I can record several partial payments until the invoice is fully paid.

## Scope
- Record multiple payments per invoice (amount, paid_at, method, reference, notes).
- Status is **derived** from the sum of payments:
  - `draft` / `sent` — no payments yet (manual)
  - `partially_paid` — 0 < paid < total
  - `paid` — paid >= total
  - `cancelled` — manual; blocks new payments
- Overpayment is blocked (amount must not exceed the remaining balance).
- Payments can be deleted; deleting recomputes the status.
- Upload payment receipt (`payment_receipt`) and payment proof (`payment_proof`) files.
- Reflect PAID / PARTIALLY PAID / UNPAID + amounts on client pages.

## Acceptance criteria
- [ ] "Add Payment" form captures amount (required, > 0), paid_at, method, reference, notes; amount defaults to remaining balance.
- [ ] On submit: payment row created; status recomputed (`partially_paid` or `paid`).
- [ ] Overpayment rejected with a validation error.
- [ ] Payments list shows Total / Paid / Remaining summary and per-payment delete.
- [ ] Deleting a payment recomputes the status.
- [ ] Receipt/proof uploads stored privately with `type = payment_receipt | payment_proof`.
- [ ] Invoice with status `paid` shows PAID badge + last paid-on date; `partially_paid` shows PARTIALLY PAID + "Paid X of Y".
- [ ] Client can view attached receipt/proof through the authorized streaming endpoint.
- [ ] Manual status dropdown only offers `draft` / `sent` / `cancelled` (paid/partial are derived).
- [ ] Cancelled invoices cannot accept payments.

## Data
- `payments`, `files` tables; mutates `invoices.status` via `PaymentService::recomputeStatus`.

## Notes
- Status is auto-derived, not stored manually — keeps accounting consistent.
- Payment history is the source of truth; the `paid`/`partially_paid` statuses are computed from it.