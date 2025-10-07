<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Http\ApiMessages;

use Tests\TestCase;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_access_register_form()
    {
        $response = $this->get('/register');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['csrf_token'],
            ]);
    }

    #[Test]
    public function authenticated_user_cannot_access_register_form()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/register');

        $response->assertStatus(302);
    }

    #[Test]
    public function guest_can_register_successfully()
    {
        Notification::fake();

        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $payload);

        $response->assertStatus(201)
            ->assertJson(['message' => ApiMessages::USER_REGISTERED]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    #[Test]
    public function cannot_register_with_existing_email()
    {
        $user = User::factory()->create(['email' => 'existing@example.com']);

        $payload = [
            'name' => 'Another User',
            'email' => 'existing@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $payload);
        $response->assertStatus(422)
            ->assertJson(['message' => "Поле email уже занято"]);
    }

    #[Test]
    public function authenticated_user_cannot_register_again()
    {
        $user = User::factory()->create();

        $payload = [
            'name' => 'Name User',
            'email' => 'example@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $this->actingAs($user);

        $response = $this->post('/register', $payload);
        $response->assertStatus(302);
    }

    #[Test]
    public function cannot_register_with_password_mismatch()
    {
        $payload = [
            'name' => 'Name User',
            'email' => 'example@example.com',
            'password' => 'password1',
            'password_confirmation' => 'password2',
        ];

        $response = $this->postJson('/register', $payload);
        $response->assertStatus(422)->assertJson([
            'message' => ApiMessages::VALIDATION_FAILED,
            'errors' => [
                'password' => ['The password field confirmation does not match.']
            ]
        ]);
    }

    #[Test]
    public function successfully_confirm_email()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(200)
            ->assertJson(['message' => ApiMessages::EMAIL_VERIFIED]);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    #[Test]
    public function guest_user_cannot_verify_email()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(401);

        $this->assertNull($user->fresh()->email_verified_at);
    }

    #[Test]
    public function verification_fails_with_expired_link()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user);

        $expiredUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($expiredUrl);

        $response->assertStatus(403);

        $this->assertNull($user->fresh()->email_verified_at);
    }
}
