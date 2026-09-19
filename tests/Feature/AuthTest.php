<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function createOwner(): User
    {
        return User::factory()->create([
            'email' => 'owner@example.com',
            'password' => bcrypt('secret'),
        ]);
    }

    public function test_login_page_is_accessible(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_guest_is_redirected_from_admin_to_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_owner_can_login_and_reach_dashboard(): void
    {
        $this->createOwner();

        $this->post(route('login.attempt'), [
            'email' => 'owner@example.com',
            'password' => 'secret',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_login_with_wrong_password_redirects_back_with_error(): void
    {
        $this->createOwner();

        $this->post(route('login.attempt'), [
            'email' => 'owner@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        $this->createOwner();

        for ($i = 1; $i <= 5; $i++) {
            $this->post(route('login.attempt'), [
                'email' => 'owner@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $this->post(route('login.attempt'), [
            'email' => 'owner@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(429);
    }

    public function test_logged_in_user_is_redirected_away_from_login(): void
    {
        $this->actingAs($this->createOwner());

        $this->get(route('login'))->assertRedirect(route('admin.dashboard'));
    }

    public function test_logout_redirects_to_login(): void
    {
        $this->actingAs($this->createOwner());

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
