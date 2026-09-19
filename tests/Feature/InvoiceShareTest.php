<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ShareLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InvoiceShareTest extends TestCase
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

    public function test_owner_can_create_share_link_for_invoice(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice(['invoice_number' => 'INV-2026-001']);

        $this->post(route('admin.invoices.share', $invoice))
            ->assertRedirect(route('admin.invoices.show', $invoice))
            ->assertSessionHas('share_link')
            ->assertSessionHas('share_password');

        $this->assertDatabaseHas('share_links', [
            'invoice_id' => $invoice->id,
        ]);

        $link = ShareLink::where('invoice_id', $invoice->id)->first();
        $this->assertNotEquals(session('share_password'), $link->password_hash);
    }

    public function test_share_link_token_is_unique(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice();

        $this->post(route('admin.invoices.share', $invoice));
        $this->post(route('admin.invoices.share', $invoice));

        $tokens = ShareLink::where('invoice_id', $invoice->id)->pluck('token');
        $this->assertSame($tokens->count(), $tokens->unique()->count());
    }

    public function test_unauthorized_visitor_sees_password_form_for_share(): void
    {
        $invoice = $this->createInvoice(['invoice_number' => 'INV-2026-001']);
        $link = ShareLink::create([
            'project_id' => $this->project->id,
            'invoice_id' => $invoice->id,
            'token' => 'uniquetoken123456789012345678901234',
            'password_hash' => Hash::make('share-pass'),
        ]);

        $this->get(route('shares.show', $link))
            ->assertOk()
            ->assertSee('password protected')
            ->assertDontSee('INV-2026-001');
    }

    public function test_correct_password_shows_invoice_with_status(): void
    {
        $invoice = $this->createInvoice(['invoice_number' => 'INV-2026-001', 'status' => Invoice::STATUS_PAID]);
        $link = ShareLink::create([
            'project_id' => $this->project->id,
            'invoice_id' => $invoice->id,
            'token' => 'uniquetoken123456789012345678901234',
            'password_hash' => Hash::make('share-pass'),
        ]);

        $this->post(route('shares.password.check', $link), ['password' => 'share-pass'])
            ->assertRedirect(route('shares.show', $link));

        $this->get(route('shares.show', $link))
            ->assertOk()
            ->assertSee('INV-2026-001')
            ->assertSee('PAID');
    }

    public function test_shared_invoice_shows_unpaid_status(): void
    {
        $invoice = $this->createInvoice(['invoice_number' => 'INV-2026-002', 'status' => Invoice::STATUS_SENT]);
        $link = ShareLink::create([
            'project_id' => $this->project->id,
            'invoice_id' => $invoice->id,
            'token' => 'uniquetoken123456789012345678901234',
            'password_hash' => Hash::make('share-pass'),
        ]);

        $this->post(route('shares.password.check', $link), ['password' => 'share-pass']);

        $this->get(route('shares.show', $link))
            ->assertOk()
            ->assertSee('UNPAID')
            ->assertDontSee('>PAID<');
    }

    public function test_wrong_password_redirects_back_with_error(): void
    {
        $invoice = $this->createInvoice();
        $link = ShareLink::create([
            'project_id' => $this->project->id,
            'invoice_id' => $invoice->id,
            'token' => 'uniquetoken123456789012345678901234',
            'password_hash' => Hash::make('share-pass'),
        ]);

        $this->post(route('shares.password.check', $link), ['password' => 'wrong'])
            ->assertSessionHasErrors('password');

        $this->get(route('shares.show', $link))->assertSee('password protected');
    }

    public function test_authorized_client_can_stream_shared_invoice_pdf(): void
    {
        $invoice = $this->createInvoice(['invoice_number' => 'INV-2026-001']);
        $file = File::create([
            'project_id' => $this->project->id,
            'invoice_id' => $invoice->id,
            'type' => File::TYPE_INVOICE,
            'path' => 'invoices/2026/abc.pdf',
            'original_filename' => 'INV-2026-001.pdf',
            'mime_type' => 'application/pdf',
            'size' => 100,
        ]);
        Storage::disk('private')->put($file->path, '%PDF-1.7 fake');

        $link = ShareLink::create([
            'project_id' => $this->project->id,
            'invoice_id' => $invoice->id,
            'token' => 'uniquetoken123456789012345678901234',
            'password_hash' => Hash::make('share-pass'),
        ]);

        $this->post(route('shares.password.check', $link), ['password' => 'share-pass']);

        $this->get(route('shares.pdf', $link))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_unauthorized_client_cannot_stream_shared_pdf(): void
    {
        $invoice = $this->createInvoice();
        $file = File::create([
            'project_id' => $this->project->id,
            'invoice_id' => $invoice->id,
            'type' => File::TYPE_INVOICE,
            'path' => 'invoices/2026/abc.pdf',
            'original_filename' => 'INV-2026-001.pdf',
            'mime_type' => 'application/pdf',
            'size' => 100,
        ]);
        Storage::disk('private')->put($file->path, '%PDF-1.7 fake');

        $link = ShareLink::create([
            'project_id' => $this->project->id,
            'invoice_id' => $invoice->id,
            'token' => 'uniquetoken123456789012345678901234',
            'password_hash' => Hash::make('share-pass'),
        ]);

        $this->get(route('shares.pdf', $link))
            ->assertRedirect(route('shares.password', $link));
    }

    public function test_expired_share_link_is_not_found(): void
    {
        $invoice = $this->createInvoice();
        $link = ShareLink::create([
            'project_id' => $this->project->id,
            'invoice_id' => $invoice->id,
            'token' => 'expiredtoken12345678901234567890123',
            'password_hash' => Hash::make('share-pass'),
            'expires_at' => now()->subDay(),
        ]);

        $this->get(route('shares.show', $link))->assertNotFound();
    }

    public function test_owner_can_revoke_share_link(): void
    {
        $this->actingAs($this->owner);

        $invoice = $this->createInvoice();
        $link = ShareLink::create([
            'project_id' => $this->project->id,
            'invoice_id' => $invoice->id,
            'token' => 'revoketoken12345678901234567890123',
            'password_hash' => Hash::make('share-pass'),
        ]);

        $this->delete(route('admin.invoices.share.destroy', [$invoice, $link]))
            ->assertRedirect(route('admin.invoices.show', $invoice));

        $this->assertDatabaseMissing('share_links', ['id' => $link->id]);

        $this->get(route('shares.show', $link))->assertNotFound();
    }
}
