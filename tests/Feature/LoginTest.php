<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Http\ApiMessages;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_access_login_form()
    {
        $response = $this->get('/login');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['csrf_token'],
            ]);
    }

    #[Test]
    public function authenticated_user_cannot_login_form()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/login');

        $response->assertStatus(302);
    }

    #[Test]
    public function authenticated_user_cannot_login_again()
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($user);

        $payload = [
            'email' => 'existing@example.com',
            'password' => 'password',
        ];

        $response = $this->post('login', $payload);

        $response->assertStatus(302);
    }

    #[Test]
    public function guest_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'password' => 'password',
        ]);

        $payload = [
            'email' => 'existing@example.com',
            'password' => 'password',
        ];

        $response = $this->post('login', $payload);

        $response->assertStatus(200)->assertJsonStructure([
            'data' => ['user', 'csrf_token'],
        ]);
    }

    #[Test]
    public function authenticated_user_can_logout_successfully()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => ApiMessages::LOGOUT]);

        $this->assertGuest();
    }

    #[Test]
    public function guest_cannot_logout()
    {
        $response = $this->post('/logout');

        $response->assertStatus(401);
    }
}
