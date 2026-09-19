# PRD — Invoice Management Web App

> Companion to `mvp.md`. This document is the product-level source of truth for the MVP.

## 1. Product Summary

A small, private, single-owner web application at `invoice.fiu.my.id` that acts as a **personal invoice/document vault with a simple invoice generator**.

The owner stores invoices and payment documents, organizes them by project, generates professional bilingual (ID/EN) invoice PDFs, and shares each project with a client using **only a link + a password** — no client accounts.

## 2. Goals

1. Store all client invoices in one organized place.
2. Store payment receipts and payment proofs next to their invoices.
3. Group invoices by project.
4. Generate professional ID/EN invoice PDFs server-side.
5. Admin dashboard for the single owner.
6. Password-protected, account-free client access.
7. Private PDF storage — actual storage URLs are never exposed.
8. "Send an old invoice to a client in 10 seconds" as the core UX benchmark.

## 3. Non-Goals (Out of Scope for MVP)

Explicitly excluded:

- Client accounts / registration
- Online payments / payment gateways
- Accounting, tax reporting
- Recurring invoices, subscription billing
- Multi-user teams, complex roles/permissions
- CRM, email automation, WhatsApp API
- Redis, Elasticsearch, microservices, SPA frontend
- Individual invoice share links / share-link expiration (deferred — see Future)

## 4. Personas

| Persona | Access | Account? |
| --- | --- | --- |
| **Owner** | Full admin dashboard + all data | Yes — single Laravel auth user |
| **Client** | Public project/invoice pages only | No — link + password |

## 5. Functional Requirements

### FR-1 Owner Authentication
- FR-1.1 `/login` with email + password.
- FR-1.2 No public registration; seed the single owner account.
- FR-1.3 All `/admin*` routes require authentication.

### FR-2 Project Management
- FR-2.1 Create, edit, view, delete projects.
- FR-2.2 Unique slug auto-generated per project.
- FR-2.3 Store client name / email / company / description.
- FR-2.4 Project password stored as a bcrypt hash (`Hash::make`).
- FR-2.5 Display share URL + copy-link/copy-password actions.

### FR-3 Invoice Management
- FR-3.1 Create/edit invoices bound to a project.
- FR-3.2 Unique `invoice_number` (e.g. `INV-2026-001`).
- FR-3.3 Language (`id` / `en`) and currency per invoice.
- FR-3.4 Line items: description, quantity, unit_price, computed amount.
- FR-3.5 Computed subtotal, tax, total.
- FR-3.6 Issued / due dates.
- FR-3.7 Status: `draft | sent | paid | cancelled`.
- FR-3.8 Notes (e.g. payment instructions).
- FR-3.9 Preview before generating the PDF.

### FR-4 PDF Generation
- FR-4.1 Single Blade template, labels swapped via Laravel localization.
- FR-4.2 Render HTML → PDF with Dompdf.
- FR-4.3 Store PDF privately under `storage/app/private/invoices/`.
- FR-4.4 Generated filenames are UUIDs/ULIDs, not client names.

### FR-5 Payments
- FR-5.1 Record a payment (amount, paid_at, method, reference, notes).
- FR-5.2 Mark invoice `paid` from `draft|sent`.
- FR-5.3 Upload payment receipt (`payment_receipt`) and proof (`payment_proof`).
- FR-5.4 Reflect PAID status + paid-on date on client pages.

### FR-6 File Management
- FR-6.1 Attach files to a project and/or invoice.
- FR-6.2 Types: `invoice | payment_receipt | payment_proof | other`.
- FR-6.3 Store files privately; keep metadata (path, original name, mime, size) in DB.

### FR-7 Client Access
- FR-7.1 `GET /p/{project-slug}` shows password form when unauthorized.
- FR-7.2 Authorized clients get `project_access.{project_id} = true` in session.
- FR-7.3 `GET /p/{project-slug}/f/{invoice-identifier}` streams the private PDF inline after authorization.
- FR-7.4 Clients never see internal IDs, storage paths, admin controls, or other projects.

### FR-8 Sharing Workflow
- FR-8.1 One-click copy of share URL and project password.
- FR-8.2 Share payload is simply: link + password.

### FR-9 Admin Dashboard
- FR-9.1 Summary cards: projects, invoices, unpaid, paid.
- FR-9.2 Recent invoices list.

## 6. Non-Functional Requirements

- **Security:** HTTPS, hashed passwords, rate-limited login/password attempts, private file storage, authorization-before-serve, CSRF, Laravel validation, no exposure of filesystem paths.
- **Performance:** trivial scale; no cache/queue infrastructure needed.
- **Deployability:** Docker Compose (`app`, `nginx`, `postgres`), deployable to a VPS.
- **Maintainability:** boring, conventional Laravel; business logic in service classes; thin controllers; minimal models.
- **Accessibility/Responsiveness:** Tailwind-based UI, responsive.

## 7. User Stories

| ID | Story |
| --- | --- |
| US-1 | As the owner, I can create a project so invoices are grouped per client. |
| US-2 | As the owner, I can create an invoice with line items so the PDF is professional. |
| US-3 | As the owner, I can generate an ID or EN invoice PDF and store it privately. |
| US-4 | As the owner, I can copy a share link and password to send to a client. |
| US-5 | As a client, I can open a shared link, enter the password, and view my invoices. |
| US-6 | As a client, I can open an invoice PDF in the browser without seeing the storage URL. |
| US-7 | As the owner, I can mark an invoice paid and attach a receipt/proof. |

## 8. Acceptance Criteria (MVP Definition of Done)

- [ ] Owner can log in; no registration route exists.
- [ ] Project CRUD works; password is hashed.
- [ ] Invoice CRUD works; totals are correct; numbering is unique.
- [ ] ID + EN PDFs generate from one template.
- [ ] PDFs are stored privately and streamed through an authorized endpoint.
- [ ] A client can access `/p/{slug}` with the password and view invoices + PDFs.
- [ ] Share URL + password can be copied from the admin dashboard.
- [ ] Invoice can be marked paid with receipt/proof uploads.
- [ ] Client pages contain zero admin controls and zero internal identifiers.
- [ ] `docker compose up` runs app + nginx + postgres together.

## 9. Risks & Mitigations

| Risk | Mitigation |
| --- | --- |
| Dompdf layout limitations | Abstract PDF renderer behind a service; swap to Browsershot if needed. |
| Shared password across clients in a project | Documented in scope; per-invoice share links deferred to `share_links`. |
| Forgetting passwords | Owner-only app; reset handled manually via DB/artisan tinker. |