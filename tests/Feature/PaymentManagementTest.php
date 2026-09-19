<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentManagementTest extends TestCase
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
        return Invoice::factory()->for($this->project)->create($attributes);
    }

    public function test_marking_invoice_as_paid_creates_payment_and_updates_status(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice(['total' => 5000000, 'status' => Invoice::STATUS_SENT]);

        $this->post(route('admin.invoices.mark-paid', $invoice), [
            'amount' => 5000000,
            'paid_at' => now()->format('Y-m-d'),
            'method' => 'Bank transfer',
            'reference' => 'TRX-001',
        ])->assertRedirect(route('admin.invoices.show', $invoice));

        $invoice->refresh();
        $this->assertSame(Invoice::STATUS_PAID, $invoice->status);
        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'method' => 'Bank transfer',
            'reference' => 'TRX-001',
        ]);
    }

    public function test_mark_paid_defaults_amount_to_invoice_total(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice(['total' => 1000000]);

        $this->post(route('admin.invoices.mark-paid', $invoice), [
            'paid_at' => now()->format('Y-m-d'),
            'method' => 'Cash',
        ]);

        $payment = Payment::where('invoice_id', $invoice->id)->first();
        $this->assertEquals(1000000, (float) $payment->amount);
    }

    public function test_cannot_mark_invoice_paid_twice(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice(['status' => Invoice::STATUS_PAID]);

        $this->post(route('admin.invoices.mark-paid', $invoice), [
            'paid_at' => now()->format('Y-m-d'),
            'method' => 'Bank transfer',
        ])->assertStatus(422);

        $this->assertSame(0, Payment::where('invoice_id', $invoice->id)->count());
    }

    public function test_owner_can_upload_payment_receipt_to_private_storage(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice();

        $this->post(route('admin.invoices.files.store', $invoice), [
            'type' => File::TYPE_PAYMENT_RECEIPT,
            'file' => UploadedFile::fake()->create('receipt.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('admin.invoices.show', $invoice));

        $file = File::where('invoice_id', $invoice->id)->first();

        $this->assertNotNull($file);
        $this->assertSame(File::TYPE_PAYMENT_RECEIPT, $file->type);
        $this->assertMatchesRegularExpression('/^receipts\/\d{4}\/[0-9a-f-]+\.pdf$/', $file->path);
        $this->assertSame('receipt.pdf', $file->original_filename);
        Storage::disk('private')->assertExists($file->path);
    }

    public function test_owner_can_upload_payment_proof(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice();

        $this->post(route('admin.invoices.files.store', $invoice), [
            'type' => File::TYPE_PAYMENT_PROOF,
            'file' => UploadedFile::fake()->image('proof.png'),
        ])->assertRedirect();

        $this->assertDatabaseHas('files', [
            'invoice_id' => $invoice->id,
            'type' => File::TYPE_PAYMENT_PROOF,
        ]);
    }

    public function test_upload_rejects_oversized_or_wrong_mime(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice();

        $this->post(route('admin.invoices.files.store', $invoice), [
            'type' => File::TYPE_OTHER,
            'file' => UploadedFile::fake()->create('malware.exe', 50, 'application/x-msdownload'),
        ])->assertSessionHasErrors('file');
    }

    public function test_deleting_file_removes_db_row_and_physical_file(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice();
        $file = File::create([
            'project_id' => $invoice->project_id,
            'invoice_id' => $invoice->id,
            'type' => File::TYPE_OTHER,
            'path' => 'other/2026/test.pdf',
            'original_filename' => 'test.pdf',
            'mime_type' => 'application/pdf',
            'size' => 100,
        ]);
        Storage::disk('private')->put($file->path, 'fake-content');

        $this->delete(route('admin.invoices.files.destroy', [$invoice, $file]))
            ->assertRedirect(route('admin.invoices.show', $invoice));

        $this->assertDatabaseMissing('files', ['id' => $file->id]);
        Storage::disk('private')->assertMissing($file->path);
    }
}
