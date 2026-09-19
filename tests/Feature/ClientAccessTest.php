<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClientAccessTest extends TestCase
{
    use RefreshDatabase;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');

        $this->project = Project::factory()->create([
            'access_password_hash' => Hash::make('X8mP2kL9Qv7R'),
        ]);
    }

    public function test_unauthorized_visitor_sees_password_form(): void
    {
        $this->get(route('projects.show', $this->project))
            ->assertOk()
            ->assertSee('password protected')
            ->assertSee('Password');
    }

    public function test_unauthorized_visitor_does_not_see_invoice_data(): void
    {
        Invoice::factory()->for($this->project)->create(['invoice_number' => 'INV-2026-001']);

        $this->get(route('projects.show', $this->project))
            ->assertDontSee('INV-2026-001');
    }

    public function test_correct_password_unlocks_project_and_shows_invoices(): void
    {
        $invoice = Invoice::factory()->for($this->project)->create(['invoice_number' => 'INV-2026-001']);

        $this->post(route('projects.password.check', $this->project), [
            'password' => 'X8mP2kL9Qv7R',
        ])->assertRedirect(route('projects.show', $this->project));

        $this->get(route('projects.show', $this->project))
            ->assertOk()
            ->assertSee('INV-2026-001');
    }

    public function test_wrong_password_redirects_back_with_error(): void
    {
        $this->post(route('projects.password.check', $this->project), [
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('password');

        $this->get(route('projects.show', $this->project))
            ->assertSee('password protected');
    }

    public function test_authorized_client_can_stream_invoice_pdf(): void
    {
        $invoice = Invoice::factory()->for($this->project)->create(['invoice_number' => 'INV-2026-001']);
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

        // Unlock first
        $this->post(route('projects.password.check', $this->project), ['password' => 'X8mP2kL9Qv7R']);

        $this->get(route('invoices.show', [$this->project, $invoice]))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_unauthorized_client_cannot_stream_pdf(): void
    {
        $invoice = Invoice::factory()->for($this->project)->create(['invoice_number' => 'INV-2026-001']);
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

        $this->get(route('invoices.show', [$this->project, $invoice]))
            ->assertRedirect(route('projects.password', $this->project));
    }

    public function test_client_cannot_access_another_projects_invoice(): void
    {
        $otherProject = Project::factory()->create([
            'access_password_hash' => Hash::make('other-password'),
        ]);
        $invoice = Invoice::factory()->for($otherProject)->create(['invoice_number' => 'INV-2026-999']);

        // Unlock first project only
        $this->post(route('projects.password.check', $this->project), ['password' => 'X8mP2kL9Qv7R']);

        $this->get(route('invoices.show', [$this->project, $invoice]))
            ->assertNotFound();
    }

    public function test_client_can_stream_attached_receipt(): void
    {
        $invoice = Invoice::factory()->for($this->project)->create(['invoice_number' => 'INV-2026-001']);
        $file = File::create([
            'project_id' => $this->project->id,
            'invoice_id' => $invoice->id,
            'type' => File::TYPE_PAYMENT_RECEIPT,
            'path' => 'receipts/2026/abc.pdf',
            'original_filename' => 'receipt.pdf',
            'mime_type' => 'application/pdf',
            'size' => 100,
        ]);
        Storage::disk('private')->put($file->path, '%PDF-1.7 fake');

        $this->post(route('projects.password.check', $this->project), ['password' => 'X8mP2kL9Qv7R']);

        $this->get(route('invoices.file', [$this->project, $invoice, $file]))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_client_page_hides_internal_ids_and_paths(): void
    {
        $invoice = Invoice::factory()->for($this->project)->create(['invoice_number' => 'INV-2026-001']);
        File::create([
            'project_id' => $this->project->id,
            'invoice_id' => $invoice->id,
            'type' => File::TYPE_INVOICE,
            'path' => 'invoices/2026/secret-uuid.pdf',
            'original_filename' => 'INV-2026-001.pdf',
            'mime_type' => 'application/pdf',
            'size' => 100,
        ]);

        $this->post(route('projects.password.check', $this->project), ['password' => 'X8mP2kL9Qv7R']);

        $this->get(route('projects.show', $this->project))
            ->assertOk()
            ->assertDontSee('secret-uuid.pdf')
            ->assertDontSee('storage/app/private')
            ->assertDontSee('invoices/2026');
    }
}
