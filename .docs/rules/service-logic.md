# Rule: Service Logic

Business logic goes in **service classes** in `app/Services/`. Controllers stay thin; models stay minimal.

## Conventions

- One class per domain concern: `ProjectService`, `InvoiceService`, `PdfService`, `PaymentService`, `FileService`.
- Public methods are action verbs: `create(array $data): Project`, `markAsPaid(Invoice $invoice, array $data): Payment`, `generatePdf(Invoice $invoice): File`.
- Services depend on Eloquent + other services; they are resolved via constructor injection.
- Services do the **full transaction** including file writes where applicable.
- Keep methods small and readable; extract private helpers when a method exceeds ~20 lines.

## Example

```php
namespace App\Services;

use App\Models\Invoice;
use App\Models\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    public function __construct(private PdfService $pdf) {}

    public function generatePdf(Invoice $invoice): File
    {
        return DB::transaction(function () use ($invoice) {
            $filename = Str::uuid()->toString() . '.pdf';
            $path = $this->pdf->render($invoice, $filename);

            return File::updateOrCreate(
                ['invoice_id' => $invoice->id, 'type' => File::TYPE_INVOICE],
                [
                    'project_id' => $invoice->project_id,
                    'path' => $path,
                    'original_filename' => $invoice->invoice_number . '.pdf',
                    'mime_type' => 'application/pdf',
                    'size' => $this->pdf->size($path),
                ]
            );
        });
    }
}
```

## Rules

- Controllers only: validate (FormRequest), call one service method, redirect/respond.
- All state-changing work goes through services; controllers never run `->save()` or file writes directly (except trivial CRUD where a service is overkill — but prefer consistency: use services).
- Cross-cutting logic (number generation, total calculation) belongs in a service, reused by controllers and console/tests.

## Anti-patterns

- Business logic in controllers.
- Business logic in Blade views.
- Duplicated calculation logic across pages.
- Long, 100-line service methods with mixed responsibilities.