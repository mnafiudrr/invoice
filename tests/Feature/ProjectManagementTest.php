<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();
    }

    public function test_guest_is_redirected_from_projects(): void
    {
        $this->get(route('admin.projects.index'))
            ->assertRedirect(route('login'));
    }

    public function test_owner_can_create_project(): void
    {
        $this->actingAs($this->owner);

        $this->post(route('admin.projects.store'), [
            'name' => 'Website Development',
            'client_name' => 'PT ABC',
            'client_email' => 'client@abc.com',
            'client_company' => 'PT ABC',
            'description' => 'Company website project',
            'password' => 'X8mP2kL9Qv7R',
        ])->assertRedirect();

        $this->assertDatabaseHas('projects', [
            'name' => 'Website Development',
            'slug' => 'website-development',
            'client_name' => 'PT ABC',
            'client_email' => 'client@abc.com',
        ]);

        $project = Project::where('slug', 'website-development')->first();
        $this->assertNotEquals('X8mP2kL9Qv7R', $project->access_password_hash);
        $this->assertTrue(password_verify('X8mP2kL9Qv7R', $project->access_password_hash));
    }

    public function test_slug_is_unique_with_suffix_on_collision(): void
    {
        $this->actingAs($this->owner);

        Project::factory()->create(['slug' => 'website-development']);

        $this->post(route('admin.projects.store'), [
            'name' => 'Website Development',
            'client_name' => 'PT XYZ',
            'client_email' => 'client@xyz.com',
            'password' => 'another-password-1',
        ])->assertRedirect();

        $this->assertDatabaseHas('projects', ['slug' => 'website-development-2']);
    }

    public function test_owner_can_update_project(): void
    {
        $this->actingAs($this->owner);

        $project = Project::factory()->create(['user_id' => $this->owner->id]);

        $this->put(route('admin.projects.update', $project), [
            'name' => 'Renamed Project',
            'slug' => 'renamed-project',
            'client_name' => 'PT ABC',
            'client_email' => 'client@abc.com',
            'password' => '',
        ])->assertRedirect(route('admin.projects.show', $project));

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Renamed Project',
            'slug' => 'renamed-project',
        ]);
    }

    public function test_update_with_new_password_rehashes_it(): void
    {
        $this->actingAs($this->owner);

        $project = Project::factory()->create(['user_id' => $this->owner->id]);

        $this->put(route('admin.projects.update', $project), [
            'name' => $project->name,
            'slug' => $project->slug,
            'client_name' => $project->client_name,
            'client_email' => $project->client_email,
            'password' => 'new-password-123',
        ])->assertRedirect();

        $project->refresh();
        $this->assertTrue(password_verify('new-password-123', $project->access_password_hash));
    }

    public function test_owner_can_regenerate_password(): void
    {
        $this->actingAs($this->owner);

        $project = Project::factory()->create(['user_id' => $this->owner->id]);
        $oldHash = $project->access_password_hash;

        $this->post(route('admin.projects.regenerate-password', $project))
            ->assertRedirect(route('admin.projects.show', $project))
            ->assertSessionHas('new_password');

        $project->refresh();
        $this->assertNotEquals($oldHash, $project->access_password_hash);
    }

    public function test_project_delete_is_blocked_when_it_has_invoices(): void
    {
        $this->actingAs($this->owner);

        $project = Project::factory()->create(['user_id' => $this->owner->id]);
        Invoice::factory()->for($project)->create();

        $this->delete(route('admin.projects.destroy', $project))
            ->assertRedirect(route('admin.projects.show', $project))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('projects', ['id' => $project->id]);
    }

    public function test_project_can_be_deleted_without_invoices(): void
    {
        $this->actingAs($this->owner);

        $project = Project::factory()->create(['user_id' => $this->owner->id]);

        $this->delete(route('admin.projects.destroy', $project))
            ->assertRedirect(route('admin.projects.index'));

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_validation_requires_minimum_8_char_password(): void
    {
        $this->actingAs($this->owner);

        $this->post(route('admin.projects.store'), [
            'name' => 'Website Development',
            'client_name' => 'PT ABC',
            'client_email' => 'client@abc.com',
            'password' => 'short',
        ])->assertSessionHasErrors('password');
    }
}
