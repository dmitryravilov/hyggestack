<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Post Authorization Tests
 */
class PostAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that writer can update their own post.
     */
    public function test_writer_can_update_own_post(): void
    {
        $writer = User::factory()->create();
        $writer->assignRole('writer');

        $post = Post::factory()->create([
            'author_id' => $writer->id,
            'status' => PostStatus::DRAFT,
        ]);

        $response = $this->actingAs($writer, 'sanctum')
            ->putJson("/api/v1/posts/{$post->id}", [
                'title' => 'Updated Title',
                'excerpt' => 'Updated excerpt',
                'content' => 'Updated content',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Updated Title');
    }

    /**
     * Test that writer cannot update another writer's post.
     */
    public function test_writer_cannot_update_other_writers_post(): void
    {
        $writer1 = User::factory()->create();
        $writer1->assignRole('writer');

        $writer2 = User::factory()->create();
        $writer2->assignRole('writer');

        $post = Post::factory()->create([
            'author_id' => $writer1->id,
        ]);

        $response = $this->actingAs($writer2, 'sanctum')
            ->putJson("/api/v1/posts/{$post->id}", [
                'title' => 'Unauthorized Update',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Test that admin can update any post.
     */
    public function test_admin_can_update_any_post(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $writer = User::factory()->create();
        $writer->assignRole('writer');

        $post = Post::factory()->create([
            'author_id' => $writer->id,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/posts/{$post->id}", [
                'title' => 'Admin Updated Title',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Admin Updated Title');
    }

    /**
     * Test that writer can delete their own post.
     */
    public function test_writer_can_delete_own_post(): void
    {
        $writer = User::factory()->create();
        $writer->assignRole('writer');

        $post = Post::factory()->create([
            'author_id' => $writer->id,
        ]);

        $response = $this->actingAs($writer, 'sanctum')
            ->deleteJson("/api/v1/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Post deleted successfully']);
    }

    /**
     * Test that writer cannot delete another writer's post.
     */
    public function test_writer_cannot_delete_other_writers_post(): void
    {
        $writer1 = User::factory()->create();
        $writer1->assignRole('writer');

        $writer2 = User::factory()->create();
        $writer2->assignRole('writer');

        $post = Post::factory()->create([
            'author_id' => $writer1->id,
        ]);

        $response = $this->actingAs($writer2, 'sanctum')
            ->deleteJson("/api/v1/posts/{$post->id}");

        $response->assertStatus(403);
    }

    /**
     * Test that admin can delete any post.
     */
    public function test_admin_can_delete_any_post(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $writer = User::factory()->create();
        $writer->assignRole('writer');

        $post = Post::factory()->create([
            'author_id' => $writer->id,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/v1/posts/{$post->id}");

        $response->assertStatus(200);
    }

    /**
     * Test that draft posts are only visible to writer and admin.
     */
    public function test_draft_post_only_visible_to_writer_and_admin(): void
    {
        $writer1 = User::factory()->create();
        $writer1->assignRole('writer');

        $writer2 = User::factory()->create();
        $writer2->assignRole('writer');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $post = Post::factory()->create([
            'author_id' => $writer1->id,
            'status' => PostStatus::DRAFT,
            'slug' => 'draft-post',
        ]);

        // Writer can view
        $response = $this->actingAs($writer1, 'sanctum')
            ->getJson("/api/v1/posts/{$post->slug}");
        $response->assertStatus(200);

        // Admin can view
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/v1/posts/{$post->slug}");
        $response->assertStatus(200);

        // Other writer cannot view
        $response = $this->actingAs($writer2, 'sanctum')
            ->getJson("/api/v1/posts/{$post->slug}");
        $response->assertStatus(403);

        // Unauthenticated cannot view
        $response = $this->getJson("/api/v1/posts/{$post->slug}");
        $response->assertStatus(403);
    }

    /**
     * Test that published posts are visible to everyone.
     */
    public function test_published_post_visible_to_everyone(): void
    {
        $post = Post::factory()->published()->create([
            'slug' => 'published-post',
        ]);

        // Unauthenticated can view
        $response = $this->getJson("/api/v1/posts/{$post->slug}");
        $response->assertStatus(200);
    }

    /**
     * Test that only writers and admins can create posts.
     */
    public function test_only_writers_and_admins_can_create_posts(): void
    {
        $regularUser = User::factory()->create();
        // No role assigned

        $response = $this->actingAs($regularUser, 'sanctum')
            ->postJson('/api/v1/posts', [
                'title' => 'Test Post',
                'excerpt' => 'Excerpt',
                'content' => 'Content',
            ]);

        $response->assertStatus(403);
    }
}
