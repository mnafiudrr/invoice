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

## Example snippet

```blade
{{-- public/project.blade.php --}}
@extends('layouts.client')

@section('content')
  <h1 class="text-2xl font-semibold">{{ $project->name }}</h1>
  <p class="text-gray-600">{{ $project->client_company }}</p>

  <ul class="mt-6 space-y-4">
    @foreach ($invoices as $invoice)
      <li class="flex items-center justify-between rounded border p-4">
        <div>
          <p class="font-medium">{{ $invoice->invoice_number }}</p>
          <p class="text-sm text-gray-500">{{ $invoice->issued_at->format('d M Y') }}</p>
        </div>
        <div class="text-right">
          <p>{{ money($invoice->total, $invoice->currency) }}</p>
          <x-status-badge :status="$invoice->status" />
        </div>
      </li>
    @endforeach
  </ul>
@endsection
```

## Rules

- No business logic in templates (no `Hash::`, no file writes, no service calls).
- Client views must render only whitelisted fields (see `007-client-access.md`).
- Always escape output (default Blade behavior) — no `{!! !!}` unless absolutely required and sanitized.

## Anti-patterns

- Duplicated invoice tables across admin + pdf + public.
- Hardcoded URLs.
- Business logic inside `@php` blocks.