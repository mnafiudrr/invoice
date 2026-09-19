# Task 012 — UI/UX Phase D: QA, Polish, Accessibility

Source: `.docs/plans/scopes/ui-ux-refactor.md` §5 Phase D, §6.

- [ ] Responsive pass across breakpoints (640/768/1024) for all admin + client pages.
- [ ] Keyboard navigation + visible focus rings on all interactive elements.
- [ ] `aria-*` labels on icon-only/ambiguous controls (copy buttons, close buttons, hamburger).
- [ ] Form error messages linked to inputs via `aria-describedby`.
- [ ] Contrast check for body text, captions, badges; fix any low-contrast tones.
- [ ] Consistent disabled/loading states on all submit buttons (prevent double submits).
- [ ] Empty states present on: dashboard, projects index, invoices index, project show, invoice documents, payments.
- [ ] Consolidate money/date formatting helpers usage so every page formats identically.
- [ ] Add UI smoke tests: key admin + client routes render 200 and contain expected components; keep 61 existing tests green.
- [ ] Verify print stylesheet for invoice detail; test print view (e.g. via browser or grep of print CSS class).
- [ ] Full route-by-route visual review (admin + client + errors + login).
- [ ] Run `pint`; final full test suite green.