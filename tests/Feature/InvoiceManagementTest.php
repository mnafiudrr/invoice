<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();
        $this->project = Project::factory()->create(['user_id' => $this->owner->id]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'project_id' => $this->project->id,
            'language' => 'en',
            'currency' => 'IDR',
            'issued_at' => now()->format('Y-m-d'),
            'due_at' => now()->addDays(7)->format('Y-m-d'),
            'tax' => 100000,
            'notes' => 'Payment via BCA.',
            'items' => [
                ['description' => 'Website Development', 'quantity' => 2, 'unit_price' => 5000000],
                ['description' => 'Hosting Setup', 'quantity' => 1, 'unit_price' => 500000],
            ],
        ], $overrides);
    }

    public function test_owner_can_create_invoice_with_computed_totals(): void
    {
        $this->actingAs($this->owner);

        $this->post(route('admin.invoices.store'), $this->validPayload([
            'invoice_number' => 'INV-2026-001',
        ]))->assertRedirect();

        $this->assertDatabaseHas('invoices', [
            'invoice_number' => 'INV-2026-001',
            'subtotal' => 10500000,
            'tax' => 100000,
            'total' => 10600000,
            'status' => Invoice::STATUS_DRAFT,
        ]);

        $invoice = Invoice::where('invoice_number', 'INV-2026-001')->first();
        $this->assertSame(2, $invoice->items()->count());
        $this->assertEquals(10000000, (float) $invoice->items()->first()->amount);
    }

    public function test_invoice_number_auto_generates(): void
    {
        $this->actingAs($this->owner);

        $this->post(route('admin.invoices.store'), $this->validPayload())->assertRedirect();

        $invoice = Invoice::first();
        $this->assertMatchesRegularExpression('/^INV-\d{4}-\d{3}$/', $invoice->invoice_number);
    }

    public function test_invoice_number_is_unique(): void
    {
        $this->actingAs($this->owner);

        $this->post(route('admin.invoices.store'), $this->validPayload(['invoice_number' => 'INV-2026-001']));

        $this->post(route('admin.invoices.store'), $this->validPayload(['invoice_number' => 'INV-2026-001']))
            ->assertSessionHasErrors('invoice_number');
    }

    public function test_owner_can_update_invoice_and_totals_recalculate(): void
    {
        $this->actingAs($this->owner);

        $invoice = Invoice::factory()->for($this->project)->create();
        $invoice->items()->create([
            'description' => 'Old item',
            'quantity' => 1,
            'unit_price' => 100,
            'amount' => 100,
        ]);

        $this->put(route('admin.invoices.update', $invoice), $this->validPayload([
            'invoice_number' => $invoice->invoice_number,
        ]))->assertRedirect(route('admin.invoices.show', $invoice));

        $invoice->refresh();
        $this->assertEquals(10500000, (float) $invoice->subtotal);
        $this->assertEquals(10600000, (float) $invoice->total);
        $this->assertSame(2, $invoice->items()->count());
    }

    public function test_owner_can_delete_invoice(): void
    {
        $this->actingAs($this->owner);

        $invoice = Invoice::factory()->for($this->project)->create();

        $this->delete(route('admin.invoices.destroy', $invoice))
            ->assertRedirect(route('admin.invoices.index'));

        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

    public function test_invoice_requires_at_least_one_item(): void
    {
        $this->actingAs($this->owner);

        $this->post(route('admin.invoices.store'), $this->validPayload(['items' => []]))
            ->assertSessionHasErrors('items');
    }

    public function test_invoice_language_must_be_valid(): void
    {
        $this->actingAs($this->owner);

        $this->post(route('admin.invoices.store'), $this->validPayload(['language' => 'fr']))
            ->assertSessionHasErrors('language');
    }

    public function test_preview_route_renders_html(): void
    {
        $this->actingAs($this->owner);

        $invoice = Invoice::factory()->for($this->project)->create();

        $this->get(route('admin.invoices.preview', $invoice))
            ->assertOk()
            ->assertSee($invoice->invoice_number);
    }

    public function test_owner_can_change_invoice_status(): void
    {
        $this->actingAs($this->owner);

        $invoice = Invoice::factory()->for($this->project)->create(['status' => Invoice::STATUS_DRAFT]);

        $this->post(route('admin.invoices.status', $invoice), ['status' => Invoice::STATUS_SENT])
            ->assertRedirect(route('admin.invoices.show', $invoice));

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => Invoice::STATUS_SENT,
        ]);
    }

    public function test_edit_page_project_select_renders_numeric_values(): void
    {
        $this->actingAs($this->owner);

        $invoice = Invoice::factory()->for($this->project)->create(['invoice_number' => 'INV-2026-001']);

        $this->get(route('admin.invoices.edit', $invoice))
            ->assertOk()
            ->assertSee('value="'.$this->project->id.'" selected', false)
            ->assertDontSee('value="'.$this->project->name.'"', false);
    }
}
