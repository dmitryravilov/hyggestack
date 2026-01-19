<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $postId = $this->route('id');

        if (!$postId) {
            return false;
        }

        /** @var Post|null $post */
        $post = Post::query()->find($postId);

        if (!$post) {
            return false;
        }

        return $this->user()?->can('update', $post) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('title') && !$this->has('slug')) {
            $this->merge([
                'slug' => Str::slug($this->input('title')),
            ]);
        }
    }

    public function rules(): array
    {
        $postId = $this->route('id');

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', "unique:posts,slug,{$postId}"],
            'excerpt' => ['sometimes', 'required', 'string', 'max:500'],
            'content' => ['sometimes', 'required', 'string'],
            'status' => ['sometimes', 'required', 'string', 'in:'.implode(',', PostStatus::values())],
            'featured_image' => ['sometimes', 'nullable', 'string', 'max:255'],
            'category_id' => ['sometimes', 'nullable', 'exists:categories,id'],
            'tags' => ['sometimes', 'nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'published_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
