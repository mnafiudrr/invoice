# Rule: Controllers

Controllers are **thin**. They accept a request, delegate to a service, and return a response.

## Conventions

- Place in `app/Http/Controllers` grouped by domain:
  - `Admin/ProjectController`, `Admin/InvoiceController`, `Admin/DashboardController`
  - `Client/ProjectController`, `Client/InvoiceController`
- Resource controllers (`php artisan make:controller --resource`) for CRUD where natural.
- Always type-hint the request and return a response/redirect.
- Validate via **Form Requests**, not inline `$request->validate` for anything beyond a couple of fields.

## Example

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Services\InvoiceService;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $invoice = $this->invoiceService->create($request->validated());

        return redirect()
            ->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice created.');
    }

    public function show(Invoice $invoice)
    {
        return view('admin.invoices.show', ['invoice' => $invoice->load('items', 'payments', 'files')]);
    }
}
```

## Rules

- Max ~3-5 lines of logic per action (validate → call service → return).
- Load relationships in the controller/view, not in a model method that writes.
- Keep authorization checks explicit (`$this->authorize(...)` or middleware) rather than inline guessing.
- Do not reach into `request()` globally; use injected `$request`.

## Anti-patterns

- Business logic inside controllers.
- Building PDFs, hashing, or writing files in a controller.
- Long controller methods (>20 lines) that mix validation, computation, and redirects.