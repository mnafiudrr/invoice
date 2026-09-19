# Feature: PDF Generation

## Purpose
Generate professional bilingual (ID/EN) invoice PDFs from a single Blade template and store them privately.

## User stories
- US-3: As the owner, I can generate an ID or EN invoice PDF and store it privately.
- US-6: As a client, I can view the PDF in-browser without ever seeing the storage URL.

## Scope
- Single Blade invoice template (`resources/views/pdf/invoice.blade.php`).
- Localization via `lang/{en,id}/invoice.php`.
- HTML → PDF via Dompdf, wrapped behind a `PdfGenerator` service interface (swappable for Browsershot later).
- Private storage under `storage/app/private/invoices/{year}/`.
- Generated filename = UUID/ULID + `.pdf`; original name kept in DB.
- Authorized streaming endpoint.

## Acceptance criteria
- [ ] Same template renders both languages (labels swapped, layout identical).
- [ ] PDF includes: header, FROM/BILL TO, line items, subtotal/tax/total, payment info, notes, thank-you line.
- [ ] PDF visually marks PAID when invoice status is paid.
- [ ] PDF written to private disk; never to `public/`.
- [ ] Filename is a generated UUID/ULID, not client-provided.
- [ ] `GET /p/{slug}/f/{invoice}` streams the PDF inline after authorization.
- [ ] Direct access to the storage path returns 404 / is impossible.
- [ ] Existing generated PDF can be re-generated (replaces file row).

## Data
- `files` rows with `type = invoice`.
- Reads `invoices`, `invoice_items`, `projects`.

## Notes
- PdfGenerator abstraction: `generate(Invoice $invoice): StoredFile`. Keeps Dompdf behind an interface so it can be swapped without touching controllers/services.