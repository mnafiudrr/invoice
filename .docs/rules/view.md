# Rule: Views (Blade + Tailwind + Alpine)

Keep Blade views readable, structured, and consistent.

## Conventions

- Place views under `resources/views/` by area: `admin/`, `public/`, `pdf/`, `layouts/`.
- Use named routes in every `href`/`action` — never hardcoded paths:
  - `route('admin.invoices.show', $invoice)`
  - `route('projects.show', $project)`
- Use a base layout per area (`layouts/admin.blade.php`, `layouts/client.blade.php`) with `@yield('content')` or component slots.
- Keep templates declarative: extract repeated snippets into partials (`admin/invoices/_form.blade.php`, `public/_invoice-row.blade.php`).
- Format money/currency and dates in Blade via small helper functions (registered in `app/Support/helpers.php`) or `@php` blocks — never inside models.
- Use Tailwind utility classes. Use Alpine.js only for lightweight interactivity (copy-to-clipboard, dynamic item rows, mobile nav).
- Translate labels via `__('invoice.subtotal')` for the PDF template.

## Component system (see `ui-ux-refactor.md`)

- Prefer the shared components in `resources/views/components/` over raw utility soup:
  `x-button`, `x-card`, `x-input`, `x-select`, `x-textarea`, `x-field`, `x-alert`, `x-status-badge`, `x-empty-state`, `x-table`, `x-modal`, `x-breadcrumbs`, `x-page-header`, `x-copy-field`, `x-pagination`.
- Buttons: always `x-button` (or a link styled as a button). Never hand-roll primary/secondary/danger buttons.
- Forms: use `x-input` / `x-select` / `x-textarea` (they wire label + error). No bare `<input class="...">` in page templates.
- Confirmations: destructive actions use `x-modal` confirm — **never** `onsubmit="return confirm(...)"`.
- Tables: use `x-table` so mobile gets the responsive card fallback.
- Empty states: use `x-empty-state`, not bare "No X yet." text.
- Pagination: wrap with `x-pagination`.
- Flash: pages render through the layout flash partial (`x-alert`); success auto-dismisses.
- No `<script>` blocks in Blade. JavaScript lives in `resources/js/` (Alpine data components / modules) and is attached via `x-data` + named components.

## Example snippet

```blade
{{-- public/project.blade.php --}}
@extends('layouts.client')

@section('content')
  <x-page-header title="{{ $project->name }}" subtitle="{{ $project->client_company }}" />

  <x-table :rows="$invoices" responsive>
    @foreach ($invoices as $invoice)
      <x-table-row>
        <x-slot:left>
          <p class="font-medium">{{ $invoice->invoice_number }}</p>
          <p class="text-sm text-gray-500">{{ $invoice->issued_at->format('d M Y') }}</p>
        </x-slot:left>
        <x-slot:right>
          <p>{{ money($invoice->total, $invoice->currency) }}</p>
          <x-status-badge :status="$invoice->status" />
        </x-slot:right>
      </x-table-row>
    @endforeach
  </x-table>
@endsection
```

## Rules

- No business logic in templates (no `Hash::`, no file writes, no service calls).
- Client views must render only whitelisted fields (see `007-client-access.md`).
- Always escape output (default Blade behavior) — no `{!! !!}` unless absolutely required and sanitized.
- No inline `<script>` in views; no `onsubmit="return confirm(...)"`.

## Anti-patterns

- Duplicated invoice tables across admin + pdf + public.
- Hardcoded URLs.
- Business logic inside `@php` blocks.
- Hand-rolled buttons/inputs/cards instead of the component system.