# Invoice Management Web App — Implementation Plan

## 1. Project Overview

Build a small private web application at:

`invoice.fiu.my.id`

The application is a personal invoice/document management system.

The main purpose is to:

1. Store all invoices created for clients.
2. Store payment receipts and other payment-related documents.
3. Organize invoices by project.
4. Generate professional invoices in Indonesian and English.
5. Allow the owner to manage everything from an authenticated admin dashboard.
6. Allow clients to access shared invoices without creating an account.
7. Protect all client-facing project and invoice pages with passwords.
8. Keep all PDF files private and never expose their actual storage URLs.
9. Make sharing an invoice extremely simple: create invoice → generate/share link → copy password → send to client.

The application is intentionally simple and should not become a full accounting system.

---

# 2. Recommended Tech Stack

## Backend

* PHP
* Laravel
* Laravel Blade
* Laravel Authentication
* Laravel Filesystem

## Frontend

* Blade
* Tailwind CSS
* Alpine.js only where lightweight interactivity is needed

Do not use React, Vue, Next.js, or a SPA architecture.

## Database

* PostgreSQL

## PDF Generation

Use a server-side PDF generator such as:

* Dompdf for simple HTML/CSS invoices

The invoice should first be rendered as an HTML Blade template and then converted to PDF.

If more advanced CSS/layout support becomes necessary later, the PDF implementation can be replaced with Browsershot/Chromium.

## Infrastructure

* Docker
* Docker Compose
* Nginx
* PHP-FPM
* PostgreSQL

## Deployment

* VPS or existing Docker-capable server
* Cloudflare for DNS and HTTPS

---

# 3. High-Level Architecture

```text
                    invoice.fiu.my.id
                           |
                         Nginx
                           |
                       Laravel
                    /            \
                   /              \
             PostgreSQL       Private Storage
                                |
                              PDFs
```

There is no need for Redis, Elasticsearch, queues, object storage, or other infrastructure in the initial version.

If storage requirements grow significantly, private object storage such as S3 or MinIO can be introduced later.

---

# 4. User Types

There are only two access types.

## Owner

Only the application owner can log in.

The owner can:

* Create projects
* Edit projects
* Create invoices
* Edit invoices
* Generate invoice PDFs
* Upload payment receipts
* Upload payment proofs
* Mark invoices as paid
* Manage invoice documents
* Generate/copy client share links
* Manage project passwords
* View all invoice history

## Client

Clients do not have accounts.

Clients only receive:

* A URL
* A password

They can use the password to access the project or a specific invoice.

---

# 5. URL Structure

## Project page

```text
/p/{project-slug}
```

Example:

```text
https://invoice.fiu.my.id/p/website-development-pt-abc
```

This page shows all invoices belonging to the project.

It requires the project password.

---

## Individual invoice page

```text
/p/{project-slug}/f/{invoice-identifier}
```

Example:

```text
https://invoice.fiu.my.id/p/website-development-pt-abc/f/INV-2026-001
```

This page requires authorization before displaying the invoice/document.

The actual PDF storage URL must never be exposed publicly.

---

# 6. Important Security Requirement

PDF files must be stored privately.

Do NOT put invoice files in a publicly accessible directory such as:

```text
/public/storage/invoices
```

Instead use something such as:

```text
storage/app/private/invoices/
```

or another private storage disk.

A client request should work like this:

```text
Client
  |
  | GET /p/project/f/invoice
  v
Laravel
  |
  | Check project/invoice authorization
  |
  +---- Not authorized ---> Password page
  |
  +---- Authorized -------> Read private PDF
                                  |
                                  v
                             Stream PDF
```

The client must never be able to directly access:

```text
/storage/invoices/2026/invoice.pdf
```

The PDF must only be served through an authorized Laravel endpoint.

Use Laravel's filesystem APIs to read and stream the file.

---

# 7. Authentication

Use Laravel's standard authentication system for the owner.

There should be no public registration.

Routes such as:

```text
/login
/admin
/admin/projects
/admin/invoices
```

must require authentication.

The initial application only needs one owner account, but the database should still use a standard `users` table.

---

# 8. Database Structure

## users

```text
users
-----
id
name
email
password
created_at
updated_at
```

Use Laravel's normal authentication structure.

---

## projects

```text
projects
--------
id
user_id
name
slug
client_name
client_email
client_company
description
access_password_hash
created_at
updated_at
```

Relationships:

```text
User
 └── hasMany Projects

Project
 └── belongsTo User
```

The project password must never be stored in plaintext.

Use Laravel's password hashing:

```php
Hash::make($password)
```

and:

```php
Hash::check($password, $hash)
```

Do not use SHA-256/SHA-512 as a password hashing mechanism.

Use Laravel's default secure password hashing mechanism.

---

## invoices

```text
invoices
--------
id
project_id
invoice_number
language
currency
subtotal
tax
total
issued_at
due_at
status
notes
created_at
updated_at
```

Possible statuses:

```text
draft
sent
paid
cancelled
```

Relationships:

```text
Project
 └── hasMany Invoices

Invoice
 └── belongsTo Project
```

---

## invoice_items

```text
invoice_items
-------------
id
invoice_id
description
quantity
unit_price
amount
created_at
updated_at
```

Relationships:

```text
Invoice
 └── hasMany InvoiceItems
```

This allows an invoice to contain multiple billable items.

Example:

```text
Website Development      1 × Rp 5,000,000
Hosting Setup             1 × Rp   500,000
Maintenance               2 × Rp   750,000
```

---

## payments

```text
payments
--------
id
invoice_id
amount
paid_at
method
reference
notes
created_at
updated_at
```

Relationships:

```text
Invoice
 └── hasMany Payments
```

This allows partial payments or multiple payments in the future.

For the initial UI, a simple one-payment workflow is sufficient.

---

## files

```text
files
-----
id
project_id
invoice_id
type
path
original_filename
mime_type
size
created_at
updated_at
```

Possible file types:

```text
invoice
payment_receipt
payment_proof
other
```

Example:

```text
Invoice #INV-2026-001
 |
 +-- Invoice PDF
 |
 +-- Payment Receipt PDF
 |
 +-- Client Transfer Proof
```

Files should be stored outside PostgreSQL.

PostgreSQL stores only metadata and the private storage path.

---

# 9. File Storage

Use private storage such as:

```text
storage/app/private/
```

Suggested structure:

```text
storage/app/private/
    invoices/
        2026/
        2025/

    receipts/
        2026/
        2025/

    proofs/
        2026/
        2025/
```

Physical filenames should not rely on client-provided filenames.

Prefer generated unique IDs such as ULIDs/UUIDs:

```text
01K5ABCXYZ123456.pdf
```

The original filename can still be stored in the database for display/download purposes.

---

# 10. Password Protection

## Project password

A project has a relatively strong password.

Example:

```text
X8mP2kL9Qv7R
```

When the client visits:

```text
/p/website-development-pt-abc
```

they see:

```text
This project is password protected.

Password:
[____________]

[Continue]
```

After successful authentication, store authorization in the Laravel session.

For example:

```text
project_access.{project_id} = true
```

The client can then access all invoices belonging to that project without entering the password again during that session.

---

# 11. Individual Invoice Access

Individual invoices may also be shared independently.

Example:

```text
/p/website-development-pt-abc/f/INV-2026-001
```

The application must verify that the client is authorized to access the corresponding project/invoice before streaming the PDF.

For MVP, the project password can be sufficient for invoice access.

If individual invoice passwords are needed later, introduce a dedicated `share_links` table rather than complicating the invoice model.

Possible future structure:

```text
share_links
-----------
id
project_id
invoice_id nullable
token
password_hash
expires_at nullable
created_at
updated_at
```

This would support:

```text
Project share
/p/{project-slug}

Individual invoice share
/s/{random-token}
```

However, this is optional and should not be implemented unless the workflow actually requires it.

---

# 12. Password Session Flow

Example:

```text
Client opens:

/p/website-development-pt-abc
```

Laravel checks:

```text
project_access.{project_id}
```

If unauthorized:

```text
Show password form
```

If authorized:

```text
Show project
```

The client can then open:

```text
/p/website-development-pt-abc/f/INV-2026-001
/p/website-development-pt-abc/f/INV-2026-002
/p/website-development-pt-abc/f/INV-2026-003
```

without repeatedly entering the password.

---

# 13. Invoice Generation

The owner should be able to create an invoice from the admin dashboard.

Example form:

```text
New Invoice

Project
[ Website Development - PT ABC ]

Invoice Number
[ INV-2026-001 ]

Language
[ English ]

Invoice Date
[ 19 September 2026 ]

Due Date
[ 26 September 2026 ]

Items

Description                 Qty     Unit Price
Website Development          1      Rp 5,000,000

Tax
[ 0 ]

Notes
[ Payment via BCA ... ]

[ Preview ]
[ Generate Invoice ]
```

---

# 14. Invoice Design

Invoice PDFs should look professional and clean.

Example structure:

```text
                         INVOICE

                         INV-2026-001
                         19 September 2026


FROM
Nafiu Rosyid
...


BILL TO
PT ABC
John Doe
...


DESCRIPTION                         AMOUNT
------------------------------------------------
Website Development                 Rp 5.000.000


                                   SUBTOTAL
                                   Rp 5.000.000

                                   TAX
                                   Rp 0

                                   TOTAL
                                   Rp 5.000.000


PAYMENT INFORMATION
Bank: BCA
Account Name: ...
Account Number: ...


Thank you for your business.
```

The invoice should also have a clear visual status when applicable:

```text
PAID
```

However, the original invoice document should remain the invoice. Payment information should be represented through the invoice status and separate payment/receipt documents.

---

# 15. Multi-language Support

Invoices must support:

```text
id
en
```

Use Laravel localization.

Example:

```text
lang/en/invoice.php
lang/id/invoice.php
```

Example translations:

```text
invoice
issued_date
due_date
bill_to
description
quantity
unit_price
subtotal
tax
total
payment_information
paid
```

Each invoice should store its selected language:

```text
language = id
```

or:

```text
language = en
```

Do not maintain completely separate invoice templates for each language.

Use the same Blade template with translated labels.

---

# 16. Admin Dashboard

The dashboard should be intentionally simple.

Example:

```text
Dashboard

Projects              12
Invoices               47
Unpaid                 3
Paid                  44


Recent Invoices

INV-2026-009   PT ABC       Rp 5,000,000   PAID
INV-2026-008   PT XYZ       Rp 3,000,000   UNPAID
INV-2026-007   John Doe     Rp 2,000,000   PAID
```

---

# 17. Project Management

Project list:

```text
Projects

Website Development - PT ABC
Client: PT ABC
Invoices: 5
Last Invoice: INV-2026-009

[View]
```

Project detail:

```text
Website Development - PT ABC

Client:
PT ABC
John Doe

Project Share

URL:
https://invoice.fiu.my.id/p/website-development-pt-abc

Password:
X8mP2kL9Qv7R

[Copy Link]
[Copy Password]


Invoices

INV-2026-001
Rp 5,000,000
PAID

INV-2026-002
Rp 3,000,000
PAID

INV-2026-003
Rp 2,000,000
UNPAID
```

---

# 18. Sharing Workflow

The primary workflow should be extremely fast.

```text
Create Project
      |
      v
Create Invoice
      |
      v
Generate Invoice PDF
      |
      v
Send to Client
```

The admin should be able to copy:

```text
Link
Password
```

independently.

For example:

```text
Client Link

https://invoice.fiu.my.id/p/website-development-pt-abc

Password

X8mP2kL9Qv7R

[Copy Link]
[Copy Password]
```

The owner can then paste these into WhatsApp/email:

```text
Invoice-nya bisa dicek di:
https://invoice.fiu.my.id/p/website-development-pt-abc

Password:
X8mP2kL9Qv7R
```

---

# 19. Payment Workflow

When a client pays:

```text
Invoice
    |
    v
Mark as Paid
    |
    +--> Payment record
    |
    +--> Upload payment receipt/proof
```

Invoice status changes:

```text
UNPAID → PAID
```

The project page should clearly display payment status.

Example:

```text
INV-2026-001
Rp 5,000,000

PAID
Paid on 10 September 2026

Documents:
[View Invoice]
[View Payment Receipt]
[View Payment Proof]
```

---

# 20. Public Client Page

A project page should not look like an admin dashboard.

Example:

```text
Website Development
PT ABC

Documents

Invoice #INV-2026-001
19 September 2026
Rp 5,000,000
PAID

[View Invoice]


Invoice #INV-2026-002
25 September 2026
Rp 3,000,000
UNPAID

[View Invoice]
```

The client should not see internal database IDs, storage paths, admin controls, or other projects.

---

# 21. PDF Viewer

When a client clicks:

```text
View Invoice
```

Laravel verifies authorization and streams the PDF.

The response should allow the browser to display the PDF inline.

Do not redirect the user to a public storage URL.

Conceptually:

```text
GET /p/{project}/f/{invoice}

    ↓

Authorize

    ↓

Find private file

    ↓

Stream PDF

    ↓

Browser PDF viewer
```

---

# 22. Security Requirements

Implement at minimum:

* HTTPS
* Owner authentication
* No public registration
* Password hashing
* Rate limiting for login/password attempts
* Private file storage
* Authorization before serving files
* No direct public storage URLs
* CSRF protection
* Laravel validation
* Server-side authorization checks
* Unique file identifiers
* No client access to admin routes
* No exposure of filesystem paths

Do not rely on obscurity of URLs as a security mechanism.

A random URL/token is useful, but authorization and password protection must still be enforced.

---

# 23. Docker Structure

Suggested project structure:

```text
invoice-app/
├── app/
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   ├── public/
│   │   └── pdf/
│   └── lang/
├── routes/
├── storage/
├── docker/
│   └── nginx/
│       └── default.conf
├── Dockerfile
├── compose.yaml
├── composer.json
└── .env
```

Initial Docker services:

```text
app
nginx
postgres
```

No additional services are required for MVP.

---

# 24. Suggested Development Phases

## Phase 1 — Foundation

* Laravel project
* Docker setup
* PostgreSQL
* Nginx
* Authentication
* Admin middleware
* Basic Tailwind layout

## Phase 2 — Projects

* Project CRUD
* Project slug generation
* Project password
* Project dashboard
* Project public page
* Project password authentication

## Phase 3 — Invoices

* Invoice CRUD
* Invoice items
* Invoice status
* Invoice numbering
* Invoice calculations
* Invoice preview

## Phase 4 — PDF

* HTML invoice template
* Indonesian translation
* English translation
* PDF generation
* Private PDF storage
* PDF streaming endpoint

## Phase 5 — Payments

* Payment records
* Mark invoice as paid
* Payment receipt upload
* Payment proof upload
* Document management

## Phase 6 — Sharing

* Share URL display
* Copy URL
* Copy password
* Public invoice access
* Public project invoice list
* Session-based client authorization

## Phase 7 — Polish

* Dashboard statistics
* Better invoice design
* Responsive UI
* Error pages
* Empty states
* Rate limiting
* Backup strategy
* Production Docker configuration

---

# 25. MVP Scope

The MVP should NOT include:

* Client accounts
* Client registration
* Online payments
* Accounting
* Tax reporting
* Recurring invoices
* Subscription billing
* Multi-user teams
* Complex roles/permissions
* CRM
* Email automation
* WhatsApp API
* Redis
* Elasticsearch
* Microservices
* SPA frontend

The goal is:

```text
Create project
      ↓
Create invoice
      ↓
Generate PDF
      ↓
Store PDF privately
      ↓
Share URL + password
      ↓
Client opens it
      ↓
Client views invoice
      ↓
Mark as paid
      ↓
Store payment receipt/proof
```

Keep the implementation boring, maintainable, and easy to deploy.

---

# 26. Future Extensions

Only implement these if they become necessary:

* S3/MinIO private object storage
* Individual invoice share links
* Share-link expiration
* Multiple owners/admins
* Email delivery
* Automatic payment reminders
* Invoice templates
* Recurring invoices
* Client portal
* Digital signatures
* QR code on invoices
* Invoice download audit logs
* 2FA
* Automated backups

These should not complicate the initial implementation.

---

# 27. Core Design Principle

This application is a **private invoice/document vault with a simple invoice generator**, not an accounting platform.

The most important requirements are:

1. Easy invoice creation.
2. Professional invoice PDFs.
3. Organized project-based storage.
4. Very fast client sharing.
5. Strong separation between owner/admin access and client access.
6. Private PDF storage.
7. Password-protected public pages.
8. No client accounts.
9. Simple Docker deployment.
10. Minimal infrastructure.

The application should optimize for:

> "I need to send an old invoice to a client in 10 seconds."

rather than trying to become a complete financial management platform.
