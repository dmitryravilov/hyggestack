<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Post API Tests
 */
class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that published posts can be listed.
     */
    public function test_can_list_published_posts(): void
    {
        Post::factory()->published()->count(3)->create();

        $response = $this->getJson('/api/v1/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'slug', 'excerpt'],
                ],
            ]);
    }

    /**
     * Test that a post can be viewed by slug.
     */
    public function test_can_view_post_by_slug(): void
    {
        $post = Post::factory()->published()->create([
            'slug' => 'test-post',
            'title' => 'Test Post',
        ]);

        $response = $this->getJson("/api/v1/posts/{$post->slug}");

        $response->assertStatus(200)
            ->assertJsonPath('data.slug', 'test-post');
    }

    /**
     * Test that authors can create posts.
     */
    public function test_author_can_create_post(): void
    {
        $user = User::factory()->create();
        $user->assignRole('author');

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/posts', [
                'title' => 'New Post',
                'excerpt' => 'This is an excerpt',
                'content' => 'This is the content',
                'status' => 'draft',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'New Post');

        $this->assertDatabaseHas('posts', [
            'title' => 'New Post',
            'author_id' => $user->id,
        ]);
    }
}

