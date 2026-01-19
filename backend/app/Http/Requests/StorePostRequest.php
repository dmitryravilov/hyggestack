<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Post::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('slug') && $this->has('title')) {
            $this->merge([
                'slug' => Str::slug($this->input('title')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug'],
            'excerpt' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['required', 'string', 'in:'.implode(',', PostStatus::values())],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
