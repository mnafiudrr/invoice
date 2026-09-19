# Rule: Commits

Keep the history readable and reviewable.

## Conventions

- Follow [Conventional Commits](https://www.conventionalcommits.org/):
  `feat:`, `fix:`, `refactor:`, `chore:`, `docs:`, `test:`, `style:`, `perf:`, `build:`, `ci:`.
- One logical change per commit. Do not bundle unrelated work.
- Use the imperative mood in the subject: `Add invoice preview route`, not `Added invoice preview`.
- Keep subjects under ~72 characters; add a body when context is needed.
- Do not commit secrets, `.env`, generated PDFs, or build artifacts.

## Documentation rule (`.docs`)

> **Any change under `.docs/` MUST be committed in a separate commit from application code.**

- A PR that touches application code **and** documentation must contain at least two commits:
  1. one commit for the app code,
  2. one `docs:` commit for the `.docs/` changes.
- The order does not matter, but the split is mandatory so `git log -- <app-code>` and `git log -- .docs` stay clean.
- Both commits may live in the **same PR** (see `pull-request.md`).

### Example

```text
docs: add ERD and workflow diagrams          <- .docs/ only
feat: add invoice PDF streaming endpoint      <- app code only
```

```bash
git add app/ routes/ resources/ tests/
git commit -m "feat: add invoice PDF streaming endpoint"

git add .docs/
git commit -m "docs: add ERD and workflow diagrams"
```

## Anti-patterns

- Mixing `.docs/` edits into a `feat:`/`fix:` commit.
- Giant "WIP" commits with many unrelated changes.
- Committing generated files (vendor, node_modules, PDFs, compiled assets).