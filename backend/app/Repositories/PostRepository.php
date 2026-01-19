<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostRepository implements PostRepositoryInterface
{
    public function getPublishedPosts(int $perPage = 15): LengthAwarePaginator
    {
        return Post::with(['author', 'category', 'tags'])
            ->published()
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function findBySlug(string $slug): ?Post
    {
        return Post::with(['author', 'category', 'tags'])
            ->where('slug', $slug)
            ->first();
    }

    public function findById(int $id): ?Post
    {
        return Post::with(['author', 'category', 'tags'])
            ->find($id);
    }

    public function getByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return Post::with(['author', 'category', 'tags'])
            ->where('category_id', $categoryId)
            ->published()
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function getByTag(int $tagId, int $perPage = 15): LengthAwarePaginator
    {
        return Post::with(['author', 'category', 'tags'])
            ->whereHas('tags', function ($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            })
            ->published()
            ->latest('published_at')
            ->paginate($perPage);
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function update(Post $post, array $data): bool
    {
        return $post->update($data);
    }

    public function delete(Post $post): bool
    {
        return $post->delete();
    }

    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return Post::with(['author', 'category', 'tags'])
            ->latest()
            ->paginate($perPage);
    }
}
