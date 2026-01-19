<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * Category Controller (API V1)
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::withCount('posts')
            ->orderBy('name')
            ->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:7'],
        ]);

        $category = Category::create($validated);

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:7'],
        ]);

        $category->update($validated);

        return response()->json([
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}

