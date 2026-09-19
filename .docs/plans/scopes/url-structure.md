# URL Structure

> All routes for the MVP. Admin routes require auth; public client routes use the project password session.

## Public (client-facing)

| Method | Path | Purpose | Auth |
| --- | --- | --- | --- |
| GET | `/` | Landing / redirect to `/admin` or `/login` | public |
| GET | `/login` | Owner login | public |
| POST | `/login` | Owner login submit | public |
| POST | `/logout` | Owner logout | auth |
| GET | `/p/{project:slug}` | Project page (password form or invoice list) | project password |
| POST | `/p/{project:slug}/password` | Submit project password | public → sets session |
| GET | `/p/{project:slug}/f/{invoice:invoice_number}` | Stream invoice PDF inline | project password session |
| GET | `/p/{project:slug}/f/{invoice:invoice_number}/file/{file}` | Stream other attached file | project password session |
| GET | `/s/{shareLink:token}` | Shared invoice (password form or invoice) | share password |
| GET | `/s/{shareLink:token}/password` | Share password form | public |
| POST | `/s/{shareLink:token}/password` | Submit share password | public → sets session |
| GET | `/s/{shareLink:token}/pdf` | Stream shared invoice PDF | share password session |
| GET | `/s/{shareLink:token}/file/{file}` | Stream shared attached file | share password session |

## Admin (owner only)

| Method | Path | Purpose |
| --- | --- | --- |
| GET | `/admin` | Dashboard (stats + recent invoices) |
| GET | `/admin/projects` | Project list |
| GET | `/admin/projects/create` | Create project form |
| POST | `/admin/projects` | Store project |
| GET | `/admin/projects/{project}` | Project detail (share, invoices, files) |
| GET | `/admin/projects/{project}/edit` | Edit project form |
| PUT/PATCH | `/admin/projects/{project}` | Update project |
| DELETE | `/admin/projects/{project}` | Delete project |
| GET | `/admin/invoices` | Invoice list |
| GET | `/admin/invoices/create` | Create invoice form |
| POST | `/admin/invoices` | Store invoice |
| GET | `/admin/invoices/{invoice}` | Invoice detail |
| GET | `/admin/invoices/{invoice}/edit` | Edit invoice form |
| PUT/PATCH | `/admin/invoices/{invoice}` | Update invoice |
| DELETE | `/admin/invoices/{invoice}` | Delete invoice |
| POST | `/admin/invoices/{invoice}/preview` | Render HTML preview |
| POST | `/admin/invoices/{invoice}/pdf` | Generate + store PDF |
| GET | `/admin/invoices/{invoice}/pdf` | Stream generated PDF (owner) |
| POST | `/admin/invoices/{invoice}/paid` | Mark paid + record payment |
| POST | `/admin/invoices/{invoice}/files` | Upload file (receipt/proof/other) |
| POST | `/admin/invoices/{invoice}/share` | Create invoice share link |
| DELETE | `/admin/invoices/{invoice}/share/{shareLink}` | Revoke invoice share link |
| POST | `/admin/projects/{project}/password` | Regenerate project password |

> Note: invoice-number in the client path uses route-model binding on `invoice_number`; the admin uses `id` binding via explicit binding rules.

## URL building

Use named routes throughout Blade (`route('admin.projects.show', ...)`, `route('projects.show', $project)`), never hardcoded paths, so the share URL always reflects the configured app domain.