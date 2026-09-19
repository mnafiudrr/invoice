# Task 009 — UI/UX Phase A: Design System Foundation

Source: `.docs/plans/scopes/ui-ux-refactor.md` §4, §5 Phase A.

- [ ] Extend `tailwind.config.js` with semantic tokens (colors: accent/success/warning/danger; radius; shadows; font family).
- [ ] Add base CSS: focus-visible rings, selection color, base form styles, print media base.
- [ ] Build `components/button.blade.php` (`x-button`): variants (primary/secondary/danger/ghost), sizes, `as` (href), `loading` prop.
- [ ] Build `components/card.blade.php` (`x-card`): container with header/title/subtitle/footer slots.
- [ ] Build `components/field.blade.php` (`x-field`): label + hint + error wrapper.
- [ ] Build `components/input.blade.php`, `components/select.blade.php`, `components/textarea.blade.php` using `x-field`.
- [ ] Build `components/alert.blade.php` (`x-alert`): success/error/warning/info; auto-dismiss option (Alpine).
- [ ] Rebuild `components/status-badge.blade.php` from standardized tone map (draft/sent/paid/cancelled).
- [ ] Build `components/empty-state.blade.php` (`x-empty-state`): icon + title + body + optional action.
- [ ] Build `components/table.blade.php` (`x-table`): desktop table with responsive card fallback on mobile; slot-based.
- [ ] Build `components/modal.blade.php` (`x-modal`): accessible confirm dialog (Alpine).
- [ ] Build `components/breadcrumbs.blade.php` (`x-breadcrumbs`).
- [ ] Build `components/page-header.blade.php` (`x-page-header`): title + subtitle + actions slot.
- [ ] Build `components/copy-field.blade.php` (`x-copy-field`): read-only value + copy button (used for share URLs/passwords).
- [ ] Build `components/pagination.blade.php` (`x-pagination`): styled wrapper around `{{ $items->links() }}`.
- [ ] Move `invoiceForm()` out of Blade into `resources/js/invoice-form.js` as an Alpine data component; remove inline scripts from create/edit views.
- [ ] Replace all existing `{{ $x->links() }}` with `x-pagination`.
- [ ] Delete duplicate `status-badge` styling; ensure single component source.
- [ ] Run `pint`; keep full test suite (61) green.