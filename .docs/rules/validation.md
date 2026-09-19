# Rule: Validation (Form Requests)

Validate every input server-side. Use Form Requests to keep controllers thin and rules reusable.

## Conventions

- One Form Request per mutation: `StoreProjectRequest`, `UpdateProjectRequest`, `StoreInvoiceRequest`, `MarkPaidRequest`, `StoreFileRequest`.
- Form Requests live in `app/Http/Requests/`.
- Use Laravel validation rules with explicit messages where the default is unclear.
- Inline `$request->validate(...)` is acceptable only for trivial cases; prefer Form Requests for anything beyond a few fields.

## Example

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route middleware enforces auth
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'invoice_number' => ['required', 'string', 'max:50', 'unique:invoices,invoice_number'],
            'language' => ['required', Rule::in(['id', 'en'])],
            'currency' => ['required', 'string', 'size:3'],
            'issued_at' => ['required', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'status' => ['required', Rule::in(Invoice::$statuses)],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
```

## Rules

- Money fields: `numeric` + `min:0`, never `integer`.
- Enum-ish fields validated with `Rule::in(...)` against model constants.
- Client password form: validate `password` presence; do the `Hash::check` in the service (not the request).
- Always set `authorize()` appropriately (owner-only forms return true; rely on route auth middleware).

## Anti-patterns

- Trusting client-side validation alone.
- Duplicating the same rules inline across controllers.
- Putting business rules (e.g. "cannot mark paid twice") inside validation rules — that's service logic.