# Documentation Index

Planning and conventions for the Invoice Management Web App (`invoice.fiu.my.id`).

## Plans

| Area | Files |
| --- | --- |
| Scope / MVP | `plans/scopes/mvp.md` (source of truth), `prd.md`, `data-model.md`, `security.md`, `url-structure.md`, `workflows.md` |
| Features | `plans/features/001-authentication.md` … `009-admin-dashboard.md` |
| Tasks | `plans/tasks/001-foundation.md` … `007-polish.md` |

## Diagrams (PlantUML)

- `diagrams/erd.puml` — database ERD
- `diagrams/flowchart.puml` — main end-to-end workflow
- `diagrams/flowchart-client-access.puml` — password/session + PDF streaming
- `diagrams/flowchart-payment.puml` — payment workflow

## Rules (coding conventions)

- `rules/directory-structure.md`
- `rules/migration.md`
- `rules/minimal-model.md`
- `rules/service-logic.md`
- `rules/controller.md`
- `rules/view.md`
- `rules/route.md`
- `rules/validation.md`
- `rules/naming.md`
- `rules/security.md`
- `rules/seed.md`
- `rules/commit.md` — commit conventions; `.docs` changes must be a separate `docs:` commit
- `rules/pull-request.md` — PR conventions; `.docs` commits may share the same PR as code
- `rules/testing.md` — unit-test conventions for services/models

## Reading order

1. `plans/scopes/mvp.md` → 2. `plans/scopes/prd.md` → 3. `plans/features/*` → 4. `plans/tasks/*` → 5. `rules/*`.

Render `.puml` files with PlantUML (e.g. `plantuml diagrams/erd.puml` or a VS Code extension).