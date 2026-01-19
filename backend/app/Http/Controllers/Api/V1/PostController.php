<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $categorySlug = $request->query('category');
        $perPageInput = $request->query('per_page', '15');
        $perPage = min(max((int) (is_string($perPageInput) ? $perPageInput : $perPageInput), 1), 100); // Limit between 1-100

        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $posts = $this->postService->getPostsByCategory($category->id, $perPage);
            } else {
                $posts = $this->postService->getPublishedPosts($perPage);
            }
        } else {
            $posts = $this->postService->getPublishedPosts($perPage);
        }

        return PostResource::collection($posts);
    }

    public function adminIndex(): AnonymousResourceCollection
    {
        $posts = $this->postService->getAllPosts();

        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $this->postService->createPost($request->user(), $request->validated());

        return (new PostResource($post))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Request $request, string $slug): PostResource
    {
        $post = $this->postService->getPostBySlug($slug);

        if (!$post) {
            abort(Response::HTTP_NOT_FOUND, 'Post not found');
        }

        if ($post->status !== PostStatus::PUBLISHED) {
            if (!$request->user() || !$request->user()->can('view', $post)) {
                abort(Response::HTTP_FORBIDDEN, 'Unauthorized');
            }
        }

        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, int $id): PostResource
    {
        $post = $this->postService->getPostById($id);

        if (!$post) {
            abort(Response::HTTP_NOT_FOUND, 'Post not found');
        }

        $this->authorize('update', $post);

        $post = $this->postService->updatePost($post, $request->validated());

        return new PostResource($post);
    }

    public function destroy(int $id): JsonResponse
    {
        $post = $this->postService->getPostById($id);

        if (!$post) {
            abort(Response::HTTP_NOT_FOUND, 'Post not found');
        }

        $this->authorize('delete', $post);

        $this->postService->deletePost($post);

        return response()->json(['message' => 'Post deleted successfully'], Response::HTTP_OK);
    }
}
