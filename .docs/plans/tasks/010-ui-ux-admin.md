# Task 010 — UI/UX Phase B: Admin Rework

Source: `.docs/plans/scopes/ui-ux-refactor.md` §4.3, §5 Phase B.

- [x] Rebuild `layouts/admin.blade.php`: sidebar (Dashboard/Projects/Invoices) + topbar (brand, user, logout) + content area.
- [x] Sidebar: active-state styling per current route; responsive drawer on mobile (Alpine hamburger).
- [x] Add `x-breadcrumbs` + `x-page-header` to the admin layout content flow.
- [x] Move flash rendering into a shared partial using `x-alert`; auto-dismiss success via Alpine.
- [x] Dashboard: use `x-card`, `x-page-header`, `x-table`; keep stats cards but as components.
- [x] Projects index: `x-table` with responsive fallback; `x-empty-state`; `x-button` actions.
- [x] Projects create/edit: rebuild with `x-card` + `x-field` + `x-input`; disable submit while loading.
- [x] Projects show: share panel uses `x-copy-field`; delete uses `x-modal` confirm.
- [x] Invoices index: `x-table` + filters row; `x-empty-state`; styled pagination.
- [x] Invoices create/edit: rebuild forms with `x-*` components; reuse shared `_form` partial for create/edit (remove duplication); `invoiceForm` from `009`.
- [x] Invoices show: rebuild header with `x-page-header`; documents/payments sections as `x-card`; file delete + invoice delete via `x-modal`; share panel via `x-copy-field`.
- [x] Replace every raw `onsubmit="return confirm(...)"` with `x-modal` confirm component.
- [x] Ensure all admin pages responsive (sidebar→drawer, tables→cards).
- [x] Update `tests` if any view assertion relies on old markup; keep all tests green.
- [x] Run `pint`; manual visual review of every admin route.