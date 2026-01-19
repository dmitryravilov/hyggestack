<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $route = $request->route();
        $actionName = $route ? $route->getActionName() : '';
        $isShowRoute = $route !== null && str_contains($actionName, 'PostController@show');
        $isAdminRoute = $route !== null &&
            (str_contains($actionName, 'PostController@adminIndex') ||
             str_contains($actionName, 'PostController@update'));
        $includeContent = $isShowRoute || $isAdminRoute;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->when($includeContent, $this->content),
            'status' => $this->status,
            'featured_image' => $this->featured_image,
            'category_id' => $this->category_id,
            'views_count' => $this->views_count,
            'published_at' => $this->published_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'author' => new UserResource($this->whenLoaded('author')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
