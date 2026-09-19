# UI/UX Refactor Plan

> Status: **Plan — not yet implemented.** Source of the current build: tasks `001–008`. This document defines the audit, target experience, design system, and phased work to overhaul the UI/UX.

## 1. Why we are doing this

The current UI works but is **functional-first**: raw Tailwind utility classes scattered in every view, inconsistent spacing/colors, tables that break on mobile, no component system, and an admin layout that is a bare top bar rather than a real dashboard shell. The result reads as "internal tool", not the professional product the scope promises (`mvp.md` §20, `prd.md` §6).

**Goal:** a cohesive, responsive, accessible, professional UI for both the owner (admin) and the client (public pages), built once as reusable Blade components and applied consistently.

## 2. Current-state audit (as of task 008)

### Admin
| Area | Current problem |
| --- | --- |
| Layout | Bare top nav; no sidebar, no breadcrumbs, no page-title pattern, no active-state styling |
| Tables | Plain `<table>`; horizontal scroll not handled; no responsive card fallback on mobile |
| Forms | create/edit are near-duplicates; raw `confirm()` dialogs; no disable-on-submit; inconsistent field styling |
| Buttons | Ad-hoc color classes per page (`bg-green-600`, `bg-indigo-600`, `bg-amber-600`…); many stacked inline buttons overflow on small screens |
| Flash | Inline success/error boxes in the layout; error box duplicates the global one per page; no auto-dismiss |
| Status badge | One component exists (`status-badge`) but labels/colors are ad-hoc |
| Share UI | Share panel only in invoice show; project share has a separate panel; inconsistent copy-button pattern |
| Inline JS | `invoiceForm()` script defined inside create/edit Blade (duplication, violates `view.md`) |
| Empty states | Basic "No X yet." text; inconsistent |

### Client / public
| Area | Current problem |
| --- | --- |
| Project page | Functional card list; no header/branding; no invoice count / total summary |
| Share invoice page | Functional; no branding; raw tables; no due-date emphasis; no "print" affordance |
| Password gates | Two near-identical views (`password`, `share-password`) |
| Error pages | Four identical templates (403/404/419/500) with duplicated markup |
| Login | Minimal card; no brand, no error inline handling beyond global flash |

### Cross-cutting
- No design tokens (no single place for color/type/spacing decisions).
- No reusable `Button`, `Card`, `Input`, `Table`, `EmptyState`, `Alert` components.
- No focus/disabled/hover consistency; accessibility gaps (labels, aria, contrast).
- No responsive pass for small screens.
- No pagination styling (default Laravel links).
- `<script>` inline in Blade; Alpine usage ad-hoc.

## 3. Design principles

1. **One source of truth** — components, tokens, and partials; no per-page re-implementation.
2. **Mobile-first responsive** — admin and client pages usable from a phone.
3. **Clear hierarchy** — every page: title, breadcrumb/subtext, primary action in a consistent place.
4. **Calm visual language** — neutral surfaces, one accent (indigo), semantic colors only for status/feedback (green=success, red=danger, amber=warning).
5. **Accessible by default** — real `<label>`, `aria-*`, keyboard focus rings, sufficient contrast, semantic HTML.
6. **Fast to build** — server-rendered Blade components (no SPA); Alpine only for small interactions (copy, toggles, dynamic rows).
7. **Client pages look like a product, not a dashboard** — zero admin vocabulary, strong branding, "print-friendly" invoice view.

## 4. Design system (to build in Phase A)

### 4.1 Tokens (Tailwind config / CSS variables)
- **Accent:** indigo-600 (existing) as the single primary.
- **Semantic:** success=green, warning=amber, danger=red, neutral=slate/gray.
- **Type scale:** display (30px/2xl), title (20px/xl), heading (18px), body (14px), caption (12px).
- **Radius:** sm=4, md=6 (buttons/inputs), lg=8 (cards).
- **Shadow:** sm for cards, ring for focus.
- **Spacing:** consistent 4/8/12/16/24/32 rhythm; sections `space-y-6`.

### 4.2 Component inventory (`resources/views/components/`)
| Component | Purpose |
| --- | --- |
| `x-button` | Button/link-as-button; variants (primary, secondary, danger, ghost); size; `loading` state; as `href` option |
| `x-card` | Standard card container with header/title/footer slots |
| `x-input` | Text/email/number/date/password inputs with label, hint, error |
| `x-select` | Select with label, options, error |
| `x-textarea` | Textarea with label, hint, error |
| `x-field` | Shared label + error wrapper used by input/select/textarea |
| `x-alert` | Success/error/warning/info banner (auto-dismiss for success) |
| `x-status-badge` | Replace existing; standardized map of status→tone |
| `x-empty-state` | Icon + title + body + optional action (consistent empty lists) |
| `x-table` | Responsive table: desktop table, mobile card rows; `responsive` prop |
| `x-modal` | Confirm dialog replacing `confirm()` (for destructive actions) |
| `x-breadcrumbs` | Breadcrumb trail (admin) |
| `x-page-header` | Title + subtitle + actions slot (consistent page tops) |
| `x-copy-field` | Read-only value + copy button (share links/passwords) |
| `x-pagination` | Styled pagination wrapper |

### 4.3 Admin shell (Phase B)
```
┌────────────────────────────────────────────┐
│ topbar: brand · search(optional) · user   │
├────────┬───────────────────────────────────┤
│ sidebar│  breadcrumbs                     │
│ Dashboard│  page header (title+actions)    │
│ Projects│  flash area (auto-dismiss)       │
│ Invoices│  content                         │
│        │                                  │
│ active │  responsive: sidebar→drawer       │
│ state  │  (mobile hamburger)              │
└────────┴───────────────────────────────────┘
```

### 4.4 Client shell (Phase C)
```
┌────────────────────────────────────────────┐
│ brand header (invoice.fiu.my.id)          │
│  project name · company · access badge    │
├────────────────────────────────────────────┤
│ invoice list / invoice detail cards       │
│  print stylesheet for invoice detail      │
└────────────────────────────────────────────┘
```

## 5. Phased work

### Phase A — Design system foundation (task `009`)
- Tailwind tokens: extend theme (colors semantic, radius, font).
- Build all `x-*` components listed in §4.2.
- Global CSS: base styles, focus rings, print media base.
- Delete inline `invoiceForm()` scripts; move to `resources/js/invoice-form.js` Alpine component.
- Styled pagination component; swap `{{ $x->links() }}` usage.

### Phase B — Admin rework (task `010`)
- New `layouts/admin.blade.php`: sidebar + topbar + breadcrumbs + page-header slot.
- Rebuild pages with components: dashboard, projects (index/create/edit/show), invoices (index/create/edit/show).
- Responsive tables → card fallback on mobile.
- Modal-based confirm for deletes; disable-on-submit for forms.
- Consistent flash (auto-dismiss success via Alpine).

### Phase C — Client & auth rework (task `011`)
- New `layouts/client.blade.php` (branded) and rebuilt `layouts/app.blade.php`.
- Login page redesign.
- Unify password gates into one component (`x-password-gate`).
- Rebuild project page + share-invoice page with components; add print stylesheet for the invoice detail.
- Rebuild error pages from one shared template.

### Phase D — QA, polish, a11y (task `012`)
- Responsive pass (breakpoints 640/768/1024).
- Keyboard/focus audit; aria labels; contrast check.
- Empty states everywhere.
- Consistent loading/disabled states.
- End-to-end visual review of every route (admin + client + errors).
- Tests: add assertions that key pages render without error; keep the existing 61 tests green.

## 6. Acceptance criteria

- [ ] All admin and client pages use the new component system (no raw ad-hoc classes for buttons/cards/inputs).
- [ ] Admin shell has sidebar + breadcrumbs + page header; works as a drawer on mobile.
- [ ] Tables collapse to readable cards on small screens.
- [ ] Destructive actions use a styled confirm modal, not `confirm()`.
- [ ] Flash messages auto-dismiss; forms disable while submitting.
- [ ] Login, project page, share page, and error pages share the same branded look.
- [ ] Invoice detail is print-friendly.
- [ ] No `<script>` blocks inside Blade views.
- [ ] All existing feature tests still pass (61) plus new UI smoke tests.
- [ ] `pint` clean.

## 7. Out of scope

- SPA / JS framework.
- Dark mode (future).
- Re-skinning the PDF layout (separate concern; may follow this plan).
- Real-time notifications.

## 8. Risks

- **Scope creep:** component library can balloon. Mitigate: build only the components the 20+ existing views actually need.
- **Regression:** large view rewrites. Mitigate: land per-page in task 010/011 with tests after each page; run full suite before every commit.
- **Design inconsistency creep:** guard via `rules/view.md` + component-only mandate in code review.