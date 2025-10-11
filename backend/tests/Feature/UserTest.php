<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Http\ApiMessages;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_cannot_get_profile()
    {
        $response = $this->get('/users/me');

        $response->assertStatus(401);
    }

    #[Test]
    public function authenticated_user_can_get_profile()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/users/me');

        $response->assertStatus(200)->assertJson(['data' => $user->toArray()]);
    }

    #[Test]
    public function authenticated_user_can_update_profile()
    {
        $user = User::factory()->create(['name' => 'Name1', 'password' => 'password1']);

        $this->actingAs($user);

        $data = [
            'name' => 'Name2',
            'password' => 'password2',
        ];

        $response = $this->patch('/users/me', $data);

        $response->assertStatus(200)->assertJson(['message' => ApiMessages::ENTITY_UPDATED]);

        $user = User::find($user->id)->first();

        $this->assertEquals('Name2', $user->name);
        $this->assertTrue(Hash::check('password2', $user->password));
    }

    #[Test]
    public function authenticated_user_can_delete_profile()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->delete('/users/me');

        $response->assertStatus(200)->assertJson(['message' => ApiMessages::ENTITY_DELETED]);
    }

    #[Test]
    public function admin_can_update_role_another_user()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $data = [
            'role' => 'operator',
        ];

        $this->actingAs($admin);

        $response = $this->patch("/users/{$user->id}/role", $data);

        $response->assertStatus(200)->assertJson(['message' => ApiMessages::ENTITY_UPDATED]);

        $userUpdated = User::find($user->id);
        $this->assertEquals('operator', $userUpdated->role);
    }

    #[Test]
    public function user_cannot_update_role_another_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $data = [
            'role' => 'operator',
        ];

        $this->actingAs($user2);

        $response = $this->patch("/users/{$user1->id}/role", $data);

        $response->assertStatus(403);

        $userUpdated = User::find($user1->id);
        $this->assertEquals('user', $userUpdated->role);
    }
}
