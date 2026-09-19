# Feature: Invoice Management

## Purpose
Create professional invoices with line items, auto-calculated totals, status tracking, and unique numbering.

## User stories
- US-2: As the owner, I can create an invoice with line items so the PDF is professional.
- US-7: As the owner, I can manage invoice status (draft → sent → paid/cancelled).

## Scope
- Invoice CRUD in admin.
- Line items (description, quantity, unit_price → amount).
- Computed subtotal, tax, total.
- Language (`id`/`en`) + currency per invoice.
- Unique `invoice_number` (e.g. `INV-2026-001`).
- Status transitions: `draft | sent | paid | cancelled`.
- Preview before PDF generation.

## Acceptance criteria
- [ ] Create invoice bound to a project.
- [ ] Dynamic line items (add/remove rows) with live amount = quantity × unit_price.
- [ ] Subtotal = Σ amounts; total = subtotal + tax; values persisted as decimals.
- [ ] `invoice_number` unique per app; generation helper proposed: `INV-{YEAR}-{seq}`.
- [ ] Language + currency persisted; affects PDF template and formatting.
- [ ] Status changeable; marking paid goes through the payment feature (see `005-payment-management`).
- [ ] Preview route renders the HTML template without saving a PDF.
- [ ] Edit recalculates and re-persists totals.

## Data
- `invoices`, `invoice_items` tables.

## Notes
- Deletion should also remove generated PDF file rows (or keep history — decide: keep invoices, allow hard-delete only when no files).
- Money formatting lives in a presentation helper, not in models.