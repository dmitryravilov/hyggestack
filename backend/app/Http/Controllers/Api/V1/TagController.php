<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Tag Controller (API V1)
 */
class TagController extends Controller
{
    /**
     * Display a listing of tags.
     */
    public function index(): AnonymousResourceCollection
    {
        $tags = Tag::withCount('posts')
            ->orderBy('name')
            ->get();

        return TagResource::collection($tags);
    }

    /**
     * Display the specified tag.
     */
    public function show(Tag $tag): TagResource
    {
        return new TagResource($tag);
    }
}

