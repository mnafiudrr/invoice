# Rule: Testing (Unit)

Write tests for business logic. This is a **unit-test-first** project — logic in services and models must be covered by tests, not only exercised through the UI.

## Test stack

- PHPUnit (Laravel's default), in `tests/Unit/` and `tests/Feature/`.
- Keep **unit tests** in `tests/Unit` (pure service/model logic, no HTTP).
- Use **feature tests** in `tests/Feature` for route/controller behavior (login, password gate, PDF streaming).
- SQLite in-memory for unit tests; use a dedicated test database config in `phpunit.xml` / `tests/CreatesApplication.php`.

## Naming

- File: `tests/Unit/Services/InvoiceServiceTest.php`.
- Method: `test_<behavior>` or PHPUnit attributes (`#[Test]`) with snake_case: `test_totals_are_recomputed_on_update`.
- Feature test: `tests/Feature/ClientAccessTest.php` → `test_unauthenticated_visitor_sees_password_form`.

## What to unit test

- `InvoiceService`: numbering (`INV-{YEAR}-{seq}` uniqueness), subtotal/tax/total calculation, mark-as-paid transition, guard against double-payment.
- `ProjectService`: slug generation + uniqueness collision, password hashing (`Hash::make`), regeneration.
- `PdfService`: generates a file into the private disk, returns path + size, uses UUID/ULID filename, honors language.
- `PaymentService`: creates `payments` row, flips invoice status to `paid`.
- `FileService`: private-disk upload, metadata row, delete removes physical file.
- `Project`/`Invoice` models: relationships, casts, `isPaid()` etc. — only what is logic, not framework.

## Example

```php
namespace Tests\Unit\Services;

use App\Models\Invoice;
use App\Models\Project;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_subtotal_is_sum_of_item_amounts(): void
    {
        $project = Project::factory()->create();
        $invoice = Invoice::factory()->for($project)->create();

        $invoice->items()->createMany([
            ['description' => 'A', 'quantity' => 2, 'unit_price' => 100, 'amount' => 200],
            ['description' => 'B', 'quantity' => 1, 'unit_price' => 50, 'amount' => 50],
        ]);

        $this->assertEquals(250, app(InvoiceService::class)->recalculate($invoice)->subtotal);
    }
}
```

## Rules

- Every service method with branch logic gets a test.
- Cover the happy path **and** the guard rails (wrong password, double paid, unauthorized file access).
- Never rely on external state (no real Postgres, no real PDF engine in unit tests) — mock/fake `PdfGenerator` via the service interface.
- Run the suite before any PR: `php artisan test`.

## Anti-patterns

- Testing only through the browser/UI.
- Testing framework internals (`assertEquals(..., $user->id)` after `factory()->create()` only).
- Assertions without behavior (no-op tests).
- No tests for a new service — treat as a merge-blocker.