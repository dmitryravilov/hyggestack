<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('v1')->group(function () {
    // API info
    Route::get('/', function () {
        return response()->json([
            'name' => 'HyggeStack API',
            'version' => '1.0.0',
            'status' => 'ok',
        ]);
    });

    // Authentication (admin/writer only)
    Route::post('/login', [AuthController::class, 'login']);

    // Public content
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{slug}', [PostController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tags/{tag}', [TagController::class, 'show']);
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // User management (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });

    // Posts (writer/author/admin only)
    Route::middleware('role:writer|author|admin')->group(function () {
        Route::get('/admin/posts', [PostController::class, 'adminIndex']);
        Route::post('/posts', [PostController::class, 'store']);
        Route::put('/posts/{id}', [PostController::class, 'update']);
        Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    });

    // Categories (writer/admin only)
    Route::middleware('role:writer|admin')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    });
});
