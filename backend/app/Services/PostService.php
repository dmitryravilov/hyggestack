<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PostService
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository
    ) {
    }

    public function getPublishedPosts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->postRepository->getPublishedPosts($perPage);
    }

    public function getPostBySlug(string $slug): ?Post
    {
        $post = $this->postRepository->findBySlug($slug);

        if ($post && $post->status === PostStatus::PUBLISHED->value) {
            $post->incrementViews();
        }

        return $post;
    }

    public function getPostById(int $id): ?Post
    {
        return $this->postRepository->findById($id);
    }

    public function createPost(User $user, array $data): Post
    {
        $data['author_id'] = $user->id;

        if (isset($data['status']) && $data['status'] === PostStatus::PUBLISHED->value && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $tagIds = null;
        if (isset($data['tags']) && is_array($data['tags'])) {
            $tagIds = $data['tags'];
            unset($data['tags']);
        }

        $post = $this->postRepository->create($data);

        if ($tagIds) {
            $post->tags()->sync($tagIds);
        }

        return $post->load(['author', 'category', 'tags']);
    }

    public function updatePost(Post $post, array $data): Post
    {
        if (isset($data['status']) && $data['status'] === PostStatus::PUBLISHED->value) {
            if ((!isset($data['published_at']) || empty($data['published_at'])) && !$post->published_at) {
                $data['published_at'] = now();
            }
        }

        if (isset($data['tags']) && is_array($data['tags'])) {
            $tagIds = $data['tags'];
            unset($data['tags']);
            $post->tags()->sync($tagIds);
        }

        $this->postRepository->update($post, $data);

        return $post->fresh(['author', 'category', 'tags']);
    }

    public function deletePost(Post $post): bool
    {
        return $this->postRepository->delete($post);
    }

    public function getPostsByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->postRepository->getByCategory($categoryId, $perPage);
    }

    public function getPostsByTag(int $tagId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->postRepository->getByTag($tagId, $perPage);
    }

    public function getAllPosts(int $perPage = 15): LengthAwarePaginator
    {
        return $this->postRepository->getAll($perPage);
    }
}
