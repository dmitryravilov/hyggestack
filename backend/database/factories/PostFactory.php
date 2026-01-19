<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titleTemplates = [
            'Why '.fake()->words(2, true).' matters',
            'The simple '.fake()->word(),
            'How to '.fake()->words(2, true),
            fake()->words(3, true).' in small ways',
            'Finding '.fake()->word().' at home',
        ];
        $title = fake()->randomElement($titleTemplates);

        $contentTemplates = [
            "I've been thinking about this lately. ".fake()->sentence(8, false)." It's simpler than it seems.\n\n".fake()->sentence(12, false).' '.fake()->sentence(10, false)."\n\n".fake()->sentence(9, false)." That's what I've learned.",
            'Last week I tried something different. '.fake()->sentence(10, false)."\n\nThe result surprised me. ".fake()->sentence(11, false).' '.fake()->sentence(9, false)."\n\nMaybe it's worth trying.",
            "There's something about ".fake()->word().' that I keep coming back to. '.fake()->sentence(9, false)."\n\n".fake()->sentence(10, false)." It doesn't have to be complicated.\n\n".fake()->sentence(8, false)." That's enough.",
        ];
        $content = fake()->randomElement($contentTemplates);

        $excerptTemplates = [
            fake()->sentence(8, false),
            fake()->sentence(7, false)." Here's what I learned.",
            fake()->sentence(9, false),
        ];
        $excerpt = fake()->randomElement($excerptTemplates);

        return [
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'excerpt' => $excerpt,
            'content' => $content,
            'status' => PostStatus::DRAFT,
            'author_id' => User::factory(),
            'published_at' => null,
            'views_count' => 0,
        ];
    }

    /**
     * Indicate that the post is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::PUBLISHED,
            'published_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }
}
