# Rule: Routes

Define routes deliberately; keep admin and client concerns clearly separated.

## Conventions

- Prefer **named routes** everywhere; group admin routes under the `admin.` prefix, client routes under domain names.
- Admin routes are wrapped in `auth` + `web` middleware.
- Use route-model binding with explicit keys for client-facing URLs:
  - Client project: `Route::get('/p/{project:slug}', ...)`
  - Client invoice: `Route::get('/p/{project:slug}/f/{invoice:invoice_number}', ...)`
- Keep URLs matching the scope document (`.docs/plans/scopes/url-structure.md`).

## Example

```php
// routes/web.php

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('projects', Admin\ProjectController::class);
        Route::post('projects/{project}/password', [Admin\ProjectController::class, 'regeneratePassword'])
            ->name('projects.regenerate-password');

        Route::resource('invoices', Admin\InvoiceController::class);
        Route::post('invoices/{invoice}/pdf', [Admin\InvoiceController::class, 'generatePdf'])->name('invoices.generate-pdf');
        Route::post('invoices/{invoice}/paid', [Admin\InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
        Route::post('invoices/{invoice}/files', [Admin\InvoiceController::class, 'storeFile'])->name('invoices.files.store');
    });
});

// Client routes (public, password-gated)
Route::get('/p/{project:slug}', [Client\ProjectController::class, 'show'])->name('projects.show');
Route::post('/p/{project:slug}/password', [Client\ProjectController::class, 'checkPassword'])->name('projects.password');
Route::get('/p/{project:slug}/f/{invoice:invoice_number}', [Client\InvoiceController::class, 'show'])->name('invoices.show');
```

## Rules

- One concern per file. If routes grow, split into `routes/admin.php` and `routes/client.php` loaded from a provider or `web.php`.
- No closures with logic in route files — always point at controllers.
- Client routes must never expose admin capabilities.
- Route names are `domain.action` (e.g. `admin.invoices.generate-pdf`, `projects.show`).

## Anti-patterns

- Resource routes for everything including actions that deserve explicit names.
- Hardcoded URLs in Blade instead of `route()`.
- Client routes leaking admin behavior.