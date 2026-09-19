# Rule: Pull Requests

Keep PRs focused, small, and reviewable.

## Conventions

- One PR per feature/bugfix. Keep it small (< ~400 changed lines where possible).
- Title uses the conventional-commit style of the primary change: `feat: add invoice preview`.
- Add a short description: what changed, why, and any manual test steps.
- Reference the task doc it implements (`.docs/plans/tasks/00X-*.md`) and/or feature doc in the description.
- Attach acceptance-criteria status from the corresponding feature file.
- Never force-push after review; add new commits instead.

## Documentation rule (`.docs`)

> `.docs/` changes MAY be included in the same PR as application code, but MUST be in a separate commit within that PR.

- Allowed: one PR containing `feat:` + `docs:` commits.
- The `.docs/` updates should describe/provision what the code PR introduces (e.g. update `data-model.md` when adding a column, add tests/rule docs).
- A PR that is *only* documentation is a plain `docs:` PR.

### Example PR structure

```text
PR: "feat: add invoice PDF streaming endpoint"
├─ commit 1: feat: add invoice PDF streaming endpoint   (app code)
└─ commit 2: docs: update url-structure and security    (.docs/)
```

## Checklist before opening

- [ ] Lint + tests pass locally.
- [ ] `.docs/` changes in their own `docs:` commit.
- [ ] No secrets in the diff.
- [ ] Description links to the task/feature doc.

## Anti-patterns

- Mixing `.docs/` edits inside the `feat:` commit.
- One PR covering multiple unrelated tasks.
- Opening PRs without tests when the change is testable.