<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Post $post): bool
    {
        if ($post->status === 'published') {
            return true;
        }

        return $user->isWriter() && ($user->id === $post->author_id || $user->isAdmin());
    }

    public function create(User $user): bool
    {
        return $user->isWriter();
    }

    public function update(User $user, Post $post): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isWriter() && $user->id === $post->author_id;
    }

    public function delete(User $user, Post $post): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isWriter() && $user->id === $post->author_id;
    }
}
