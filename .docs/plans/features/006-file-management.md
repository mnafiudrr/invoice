# Feature: File Management

## Purpose
Attach arbitrary documents (invoice PDF, receipts, proofs, other) to projects/invoices while keeping storage private.

## User stories
- US-7: As the owner, I can upload payment documents and see them next to the invoice.

## Scope
- Upload endpoint accepting files for a project/invoice.
- Types: `invoice | payment_receipt | payment_proof | other`.
- Metadata row in `files`; physical file in private disk with UUID/ULID name.
- Authorized download/stream for both owner and clients.
- List documents on invoice detail and client project page.

## Acceptance criteria
- [ ] Upload validates mime type + size limits.
- [ ] Stored under private disk, organized by type/year (e.g. `receipts/2026/`).
- [ ] Physical filename = generated UUID/ULID; `original_filename` preserved in DB.
- [ ] Owner can download from admin.
- [ ] Client can stream any file attached to an authorized invoice.
- [ ] Deleting a file removes both DB row and physical file.
- [ ] No file is ever served from a public URL.

## Data
- `files` table.

## Notes
- Gate `invoice`-type file creation to the PDF-generation service; other types via the generic upload flow.
- Size limits: e.g. 10 MB per file; PDF/mime whitelist.