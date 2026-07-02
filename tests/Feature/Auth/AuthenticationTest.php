<?php

namespace Tests\Feature\Auth;

use App\Models\Leader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_leaders_can_authenticate_using_the_login_screen(): void
    {
        $leader = Leader::factory()->create();

        $response = $this->post('/login', [
            'email' => $leader->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_leaders_can_not_authenticate_with_invalid_password(): void
    {
        $leader = Leader::factory()->create();

        $this->post('/login', [
            'email' => $leader->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_leaders_can_logout(): void
    {
        $leader = Leader::factory()->create();

        $response = $this->actingAs($leader)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_unauthenticated_access_to_dashboard_redirects_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_login_validates_email_max_length(): void
    {
        $response = $this->post('/login', [
            'email' => str_repeat('a', 246) . '@test.com', // 255+ chars
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_login_validates_password_max_length(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => str_repeat('a', 129), // 129 chars, exceeds 128
        ]);

        $response->assertSessionHasErrors('password');
    }
}
