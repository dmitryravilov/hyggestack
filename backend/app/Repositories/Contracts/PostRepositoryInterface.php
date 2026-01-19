<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PostRepositoryInterface
{
    public function getPublishedPosts(int $perPage = 15): LengthAwarePaginator;

    public function findBySlug(string $slug): ?Post;

    public function findById(int $id): ?Post;

    public function getByCategory(int $categoryId, int $perPage = 15): LengthAwarePaginator;

    public function getByTag(int $tagId, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Post;

    public function update(Post $post, array $data): bool;

    public function delete(Post $post): bool;

    public function getAll(int $perPage = 15): LengthAwarePaginator;
}

