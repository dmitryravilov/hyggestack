<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * User Management API Tests (Admin Only)
 */
class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that admin can list users.
     */
    public function test_admin_can_list_users(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $writer = User::factory()->create();
        $writer->assignRole('writer');

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'email', 'roles'],
                ],
            ]);

        $this->assertCount(2, $response->json('data'));
    }

    /**
     * Test that non-admin cannot list users.
     */
    public function test_writer_cannot_list_users(): void
    {
        $writer = User::factory()->create();
        $writer->assignRole('writer');

        $response = $this->actingAs($writer, 'sanctum')
            ->getJson('/api/v1/users');

        $response->assertStatus(403);
    }

    /**
     * Test that admin can create a user.
     */
    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/users', [
                'name' => 'New User',
                'email' => 'newuser@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'writer',
                'bio' => 'Test bio',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('user.data.name', 'New User')
            ->assertJsonPath('user.data.email', 'newuser@example.com');

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
        ]);

        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertTrue($user->hasRole('writer'));
    }

    /**
     * Test that admin can update a user.
     */
    public function test_admin_can_update_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('writer');

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/users/{$user->id}", [
                'name' => 'Updated Name',
                'bio' => 'Updated bio',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.data.name', 'Updated Name')
            ->assertJsonPath('user.data.bio', 'Updated bio');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Test that admin can change user role.
     */
    public function test_admin_can_change_user_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('writer');

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/users/{$user->id}", [
                'role' => 'admin',
            ]);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('writer'));
    }

    /**
     * Test that admin can delete a user.
     */
    public function test_admin_can_delete_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('writer');

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'User deleted successfully']);

        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * Test that admin cannot delete their own account.
     */
    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/v1/users/{$admin->id}");

        $response->assertStatus(403)
            ->assertJson(['message' => 'You cannot delete your own account.']);

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
        ]);
    }

    /**
     * Test user creation validation.
     */
    public function test_user_creation_requires_valid_data(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/users', [
                'name' => '',
                'email' => 'invalid-email',
                'password' => 'short',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
