# Task 009 — UI/UX Phase A: Design System Foundation

Source: `.docs/plans/scopes/ui-ux-refactor.md` §4, §5 Phase A.

- [x] Extend `tailwind.config.js` with semantic tokens (colors: accent/success/warning/danger; radius; shadows; font family).
- [x] Add base CSS: focus-visible rings, selection color, base form styles, print media base.
- [x] Build `components/button.blade.php` (`x-button`): variants (primary/secondary/danger/ghost), sizes, `as` (href), `loading` prop.
- [x] Build `components/card.blade.php` (`x-card`): container with header/title/subtitle/footer slots.
- [x] Build `components/field.blade.php` (`x-field`): label + hint + error wrapper.
- [x] Build `components/input.blade.php`, `components/select.blade.php`, `components/textarea.blade.php` using `x-field`.
- [x] Build `components/alert.blade.php` (`x-alert`): success/error/warning/info; auto-dismiss option (Alpine).
- [x] Rebuild `components/status-badge.blade.php` from standardized tone map (draft/sent/paid/cancelled).
- [x] Build `components/empty-state.blade.php` (`x-empty-state`): icon + title + body + optional action.
- [x] Build `components/table.blade.php` (`x-table`): desktop table with responsive card fallback on mobile; slot-based.
- [x] Build `components/modal.blade.php` (`x-modal`): accessible confirm dialog (Alpine).
- [x] Build `components/breadcrumbs.blade.php` (`x-breadcrumbs`).
- [x] Build `components/page-header.blade.php` (`x-page-header`): title + subtitle + actions slot.
- [x] Build `components/copy-field.blade.php` (`x-copy-field`): read-only value + copy button (used for share URLs/passwords).
- [x] Build `components/pagination.blade.php` (`x-pagination`): styled wrapper around `{{ $items->links() }}`.
- [x] Move `invoiceForm()` out of Blade into `resources/js/invoice-form.js` as an Alpine data component; remove inline scripts from create/edit views.
- [x] Replace all existing `{{ $x->links() }}` with `x-pagination`.
- [x] Delete duplicate `status-badge` styling; ensure single component source.
- [x] Run `pint`; keep full test suite (61) green.