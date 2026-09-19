<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PdfGenerationTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');

        $this->owner = User::factory()->create();
        $this->project = Project::factory()->create(['user_id' => $this->owner->id]);
    }

    private function createInvoice(array $attributes = []): Invoice
    {
        $invoice = Invoice::factory()->for($this->project)->create($attributes);
        $invoice->items()->createMany([
            ['description' => 'Website Development', 'quantity' => 1, 'unit_price' => 5000000, 'amount' => 5000000],
            ['description' => 'Hosting Setup', 'quantity' => 1, 'unit_price' => 500000, 'amount' => 500000],
        ]);

        return $invoice;
    }

    public function test_generating_pdf_stores_private_file_with_uuid_filename(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice(['invoice_number' => 'INV-2026-001']);

        $this->post(route('admin.invoices.generate-pdf', $invoice))
            ->assertRedirect(route('admin.invoices.show', $invoice));

        $file = File::where('invoice_id', $invoice->id)->where('type', File::TYPE_INVOICE)->first();

        $this->assertNotNull($file);
        $this->assertMatchesRegularExpression('/^invoices\/\d{4}\/[0-9a-f-]+\.pdf$/', $file->path);
        $this->assertSame('INV-2026-001.pdf', $file->original_filename);
        $this->assertSame('application/pdf', $file->mime_type);
        $this->assertGreaterThan(0, $file->size);

        Storage::disk('private')->assertExists($file->path);
    }

    public function test_generating_pdf_regenerates_and_replaces_file(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice();

        $this->post(route('admin.invoices.generate-pdf', $invoice));
        $first = File::where('invoice_id', $invoice->id)->where('type', File::TYPE_INVOICE)->first();

        $this->post(route('admin.invoices.generate-pdf', $invoice));

        $files = File::where('invoice_id', $invoice->id)->where('type', File::TYPE_INVOICE)->get();
        $this->assertCount(1, $files);
        $this->assertNotEquals($first->path, $files->first()->path);
    }

    public function test_owner_can_stream_generated_pdf(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice(['invoice_number' => 'INV-2026-001']);
        $this->post(route('admin.invoices.generate-pdf', $invoice));

        $this->get(route('admin.invoices.download-pdf', $invoice))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_stream_404_when_pdf_not_generated(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice();

        $this->get(route('admin.invoices.download-pdf', $invoice))->assertNotFound();
    }

    public function test_pdf_respects_invoice_language(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice(['language' => 'id']);

        $this->post(route('admin.invoices.generate-pdf', $invoice))->assertRedirect();

        $file = File::where('invoice_id', $invoice->id)->where('type', File::TYPE_INVOICE)->first();
        $pdfContent = Storage::disk('private')->get($file->path);

        $this->assertNotEmpty($pdfContent);
        $this->assertStringStartsWith('%PDF', $pdfContent);
    }
}
