<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Category API Tests
 */
class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that anyone can list categories.
     */
    public function test_can_list_categories(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'description'],
                ],
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    /**
     * Test that anyone can view a category.
     */
    public function test_can_view_category(): void
    {
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $response = $this->getJson("/api/v1/categories/{$category->slug}");

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Test Category')
            ->assertJsonPath('data.slug', 'test-category');
    }

    /**
     * Test that writer can create a category.
     */
    public function test_writer_can_create_category(): void
    {
        $writer = User::factory()->create();
        $writer->assignRole('writer');

        $response = $this->actingAs($writer, 'sanctum')
            ->postJson('/api/v1/categories', [
                'name' => 'New Category',
                'description' => 'Category description',
                'color' => '#FF5733',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'New Category')
            ->assertJsonPath('data.description', 'Category description');

        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
            'slug' => 'new-category',
        ]);
    }

    /**
     * Test that admin can create a category.
     */
    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/categories', [
                'name' => 'Admin Category',
            ]);

        $response->assertStatus(201);
    }

    /**
     * Test that unauthenticated user cannot create a category.
     */
    public function test_unauthenticated_user_cannot_create_category(): void
    {
        $response = $this->postJson('/api/v1/categories', [
            'name' => 'New Category',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test that writer can update a category.
     */
    public function test_writer_can_update_category(): void
    {
        $writer = User::factory()->create();
        $writer->assignRole('writer');

        $category = Category::factory()->create();

        $response = $this->actingAs($writer, 'sanctum')
            ->putJson("/api/v1/categories/{$category->slug}", [
                'name' => 'Updated Category',
                'description' => 'Updated description',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Category')
            ->assertJsonPath('data.description', 'Updated description');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
        ]);
    }

    /**
     * Test that writer can delete a category.
     */
    public function test_writer_can_delete_category(): void
    {
        $writer = User::factory()->create();
        $writer->assignRole('writer');

        $category = Category::factory()->create();

        $response = $this->actingAs($writer, 'sanctum')
            ->deleteJson("/api/v1/categories/{$category->slug}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Category deleted successfully']);

        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
        ]);
    }

    /**
     * Test category creation validation.
     */
    public function test_category_creation_requires_name(): void
    {
        $writer = User::factory()->create();
        $writer->assignRole('writer');

        $response = $this->actingAs($writer, 'sanctum')
            ->postJson('/api/v1/categories', [
                'name' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}
