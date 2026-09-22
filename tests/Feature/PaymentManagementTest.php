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

    private function paymentPayload(array $overrides = []): array
    {
        return array_merge([
            'amount' => 1000000,
            'paid_at' => now()->format('Y-m-d'),
            'method' => 'Bank transfer',
            'reference' => 'TRX-001',
            'notes' => null,
        ], $overrides);
    }

    public function test_full_payment_marks_invoice_as_paid(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice(['total' => 5000000, 'status' => Invoice::STATUS_SENT]);

        $this->post(route('admin.invoices.payments.store', $invoice), $this->paymentPayload([
            'amount' => 5000000,
        ]))->assertRedirect(route('admin.invoices.show', $invoice));

        $invoice->refresh();
        $this->assertSame(Invoice::STATUS_PAID, $invoice->status);
        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'method' => 'Bank transfer',
            'reference' => 'TRX-001',
        ]);
    }

    public function test_partial_payment_sets_partially_paid_status(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice(['total' => 800000, 'status' => Invoice::STATUS_SENT]);

        $this->post(route('admin.invoices.payments.store', $invoice), $this->paymentPayload([
            'amount' => 300000,
            'paid_at' => '2026-05-21',
        ]))->assertRedirect();

        $invoice->refresh();
        $this->assertSame(Invoice::STATUS_PARTIALLY_PAID, $invoice->status);
        $this->assertEquals(300000, $invoice->paidAmount());
        $this->assertEquals(500000, $invoice->remainingAmount());
    }

    public function test_multiple_partial_payments_accumulate(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice(['total' => 800000, 'status' => Invoice::STATUS_SENT]);

        $this->post(route('admin.invoices.payments.store', $invoice), $this->paymentPayload(['amount' => 300000, 'paid_at' => '2026-05-21']));
        $this->post(route('admin.invoices.payments.store', $invoice), $this->paymentPayload(['amount' => 300000, 'paid_at' => '2026-05-23']));
        $this->post(route('admin.invoices.payments.store', $invoice), $this->paymentPayload(['amount' => 200000, 'paid_at' => '2026-06-02']));

        $invoice->refresh();
        $this->assertSame(3, $invoice->payments()->count());
        $this->assertEquals(800000, $invoice->paidAmount());
        $this->assertSame(Invoice::STATUS_PAID, $invoice->status);
        $this->assertEquals(0, $invoice->remainingAmount());
    }

    public function test_overpayment_is_blocked(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice(['total' => 800000, 'status' => Invoice::STATUS_SENT]);

        $this->post(route('admin.invoices.payments.store', $invoice), $this->paymentPayload(['amount' => 900000]))
            ->assertSessionHasErrors('amount');

        $this->assertSame(0, $invoice->payments()->count());
    }

    public function test_zero_amount_payment_is_rejected(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice(['total' => 800000, 'status' => Invoice::STATUS_SENT]);

        $this->post(route('admin.invoices.payments.store', $invoice), $this->paymentPayload(['amount' => 0]))
            ->assertSessionHasErrors('amount');
    }

    public function test_deleting_payment_recomputes_status(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice(['total' => 800000, 'status' => Invoice::STATUS_SENT]);
        $payment = $this->postPayment($invoice, 300000);

        $invoice->refresh();
        $this->assertSame(Invoice::STATUS_PARTIALLY_PAID, $invoice->status);

        $this->delete(route('admin.invoices.payments.destroy', [$invoice, $payment]))
            ->assertRedirect(route('admin.invoices.show', $invoice));

        $invoice->refresh();
        $this->assertSame(Invoice::STATUS_SENT, $invoice->status);
        $this->assertEquals(0, $invoice->paidAmount());
    }

    public function test_deleting_payment_after_fully_paid_reverts_to_partial(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice(['total' => 800000, 'status' => Invoice::STATUS_SENT]);
        $this->postPayment($invoice, 800000);

        $invoice->refresh();
        $this->assertSame(Invoice::STATUS_PAID, $invoice->status);

        $payment = $invoice->payments()->first();
        $this->delete(route('admin.invoices.payments.destroy', [$invoice, $payment]));

        $invoice->refresh();
        $this->assertSame(Invoice::STATUS_SENT, $invoice->status);
    }

    public function test_cannot_record_payment_on_cancelled_invoice(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice(['total' => 800000, 'status' => Invoice::STATUS_CANCELLED]);

        $this->post(route('admin.invoices.payments.store', $invoice), $this->paymentPayload(['amount' => 300000]))
            ->assertStatus(422);

        $this->assertSame(0, $invoice->payments()->count());
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

    private function postPayment(Invoice $invoice, float $amount): Payment
    {
        $this->post(route('admin.invoices.payments.store', $invoice), $this->paymentPayload(['amount' => $amount]));

        return Payment::where('invoice_id', $invoice->id)->latest()->firstOrFail();
    }
}
