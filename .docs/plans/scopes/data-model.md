# Data Model

> Source of truth for schema: `mvp.md` §8. Visual: `.docs/diagrams/erd.puml`.

## Conventions

- All tables have `id` (bigint, PK), `created_at`, `updated_at`.
- Money columns use `decimal(15, 2)`.
- Foreign keys are bigint, indexed, `constrained()` in migrations.
- Passwords are never stored in plaintext.
- Files live on disk; the DB stores metadata + relative private path.

---

## users

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint PK | |
| name | varchar | |
| email | varchar | unique, indexed |
| password | varchar | Laravel bcrypt hash |
| remember_token | varchar nullable | Laravel auth |
| timestamps | | |

Relations: `hasMany projects`.

---

## projects

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint PK | |
| user_id | bigint FK | owner |
| name | varchar | |
| slug | varchar | unique, indexed |
| client_name | varchar | |
| client_email | varchar | |
| client_company | varchar nullable | |
| description | text nullable | |
| access_password_hash | varchar | bcrypt of project password |
| timestamps | | |

Relations: `belongsTo users`, `hasMany invoices`, `hasMany files`.

---

## invoices

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint PK | |
| project_id | bigint FK | |
| invoice_number | varchar | unique, e.g. `INV-2026-001` |
| language | varchar(2) | `id` or `en` |
| currency | varchar(3) | e.g. `IDR` |
| subtotal | decimal(15,2) | computed |
| tax | decimal(15,2) | computed |
| total | decimal(15,2) | computed |
| issued_at | date | |
| due_at | date nullable | |
| status | varchar | `draft/sent/paid/cancelled` |
| notes | text nullable | |
| timestamps | | |

Relations: `belongsTo projects`, `hasMany invoice_items`, `hasMany payments`, `hasMany files`.

---

## invoice_items

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint PK | |
| invoice_id | bigint FK | |
| description | varchar | |
| quantity | decimal(15,2) | default 1 |
| unit_price | decimal(15,2) | |
| amount | decimal(15,2) | qty × unit_price |
| timestamps | | |

Relations: `belongsTo invoices`.

---

## payments

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint PK | |
| invoice_id | bigint FK | |
| amount | decimal(15,2) | |
| paid_at | date | |
| method | varchar | e.g. bank transfer |
| reference | varchar nullable | |
| notes | text nullable | |
| timestamps | | |

Relations: `belongsTo invoices`. MVP UI uses a single payment per invoice; schema supports many.

---

## files

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint PK | |
| project_id | bigint FK | |
| invoice_id | bigint FK nullable | |
| type | varchar | `invoice/payment_receipt/payment_proof/other` |
| path | varchar | relative path in private disk |
| original_filename | varchar | for display/download |
| mime_type | varchar | |
| size | bigint | bytes |
| timestamps | | |

Relations: `belongsTo projects`, `belongsTo invoices`.

---

## share_links (OPTIONAL / future)

Do **not** implement for MVP unless individual invoice sharing is truly required.

| Column | Type | Notes |
| --- | --- | --- |
| id | bigint PK | |
| project_id | bigint FK | |
| invoice_id | bigint FK nullable | null = project-level share |
| token | varchar | unique |
| password_hash | varchar | |
| expires_at | timestamp nullable | |
| timestamps | | |

---

## Statuses

| Domain | Values |
| --- | --- |
| Invoice | `draft`, `sent`, `paid`, `cancelled` |
| File type | `invoice`, `payment_receipt`, `payment_proof`, `other` |

---

## Indexes to add

- `projects.slug` — unique
- `invoices.invoice_number` — unique
- `invoices.project_id`
- `invoice_items.invoice_id`
- `payments.invoice_id`
- `files.project_id`, `files.invoice_id`