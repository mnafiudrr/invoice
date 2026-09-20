<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_auth(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_dashboard_shows_stats_and_recent_invoices(): void
    {
        $owner = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $owner->id]);
        Invoice::factory()->for($project)->create(['invoice_number' => 'INV-2026-001', 'status' => Invoice::STATUS_PAID]);
        Invoice::factory()->for($project)->create(['invoice_number' => 'INV-2026-002', 'status' => Invoice::STATUS_DRAFT]);

        $this->actingAs($owner)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('INV-2026-001')
            ->assertSee('INV-2026-002')
            ->assertSee('Paid')
            ->assertSee('Draft');
    }

    public function test_dashboard_empty_state(): void
    {
        $owner = User::factory()->create();

        $this->actingAs($owner)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('No invoices yet');
    }
}
