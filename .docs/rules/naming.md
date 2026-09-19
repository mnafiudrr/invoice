# Rule: Naming

Consistent naming keeps the codebase scannable.

## Conventions

- **Tables**: snake_case plurals (`invoice_items`, `access_password_hash`).
- **Models**: singular StudlyCase (`InvoiceItem`).
- **Migrations**: `create_<table>_table`, `add_<column>_to_<table>_table`.
- **Controllers**: resource plural (`InvoiceController`) in domain subfolders (`Admin\`, `Client\`).
- **Form Requests**: `Store<Resource>Request`, `Update<Resource>Request`, or `<Action>Request` (`MarkPaidRequest`, `CheckProjectPasswordRequest`).
- **Services**: `<Domain>Service` (`InvoiceService`).
- **Routes**: named `domain.action` (`admin.invoices.generate-pdf`, `projects.show`).
- **Blade views**: snake_case matching controller (`admin/invoices/show.blade.php`).
- **Columns**: snake_case, descriptive (`client_name`, `access_password_hash`, `issued_at`).
- **Constants**: `UPPER_SNAKE` (`Invoice::STATUS_PAID`), arrays `$statuses` (lowerCamel).

## Exact terms to standardize on

| Concept | Always call it |
| --- | --- |
| Project access password | `access_password_hash` (column), `accessPassword` (model) |
| Invoice status | `status` + `Invoice::STATUS_*` constants |
| File/document type | `type` + `File::TYPE_*` constants |
| Money columns | `subtotal`, `tax`, `total`, `amount`, `unit_price` |
| Client-facing slug | `slug` on projects |

## Anti-patterns

- Inventing synonyms (`doc` vs `file`, `paid_at` vs `pay_date`).
- Abbreviations in DB columns (`pwd`, `inv_no`).
- Inconsistent casing between model properties and columns.