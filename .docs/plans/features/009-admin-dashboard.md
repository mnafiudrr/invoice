# Feature: Admin Dashboard

## Purpose
Give the owner a fast overview: how many projects/invoices, paid vs unpaid, and recent invoices.

## User stories
- As the owner, I can see key counts and recent invoices at a glance when I log in.

## Scope
- Summary cards: Projects, Invoices, Unpaid, Paid.
- Recent invoices list (number, client, amount, status).
- Links into project/invoice management.

## Acceptance criteria
- [ ] Dashboard renders counts for projects, total invoices, paid, unpaid.
- [ ] Recent invoices (latest 10) show number, client/project, total, status badge.
- [ ] Rows link to invoice detail / project detail.
- [ ] Responsive layout (Tailwind).
- [ ] Requires auth.

## Data
- Aggregates over `projects`, `invoices`.

## Notes
- Queries should be simple Eloquent `count()` / latest pagination. No caching needed at MVP scale.