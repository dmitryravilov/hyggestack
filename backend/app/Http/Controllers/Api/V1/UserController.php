<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $users = User::with('roles')->get();

        return UserResource::collection($users);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'bio' => ['nullable', 'string'],
            'role' => ['required', 'string', 'in:admin,writer'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'bio' => $validated['bio'] ?? null,
        ]);

        $user->assignRole($validated['role']);

        return response()->json([
            'user' => [
                'data' => new UserResource($user->load('roles')),
            ],
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'bio' => ['nullable', 'string'],
            'role' => ['sometimes', 'required', 'string', 'in:admin,writer'],
        ];

        if ($request->has('password') && $request->input('password')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } else {
            $rules['password'] = ['sometimes', 'nullable', 'string', 'min:8'];
        }

        $validated = $request->validate($rules);

        if (isset($validated['password']) && $validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
            unset($validated['role']);
        }

        $user->update($validated);

        return response()->json([
            'user' => [
                'data' => new UserResource($user->fresh('roles')),
            ],
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $currentUser = $request->user();

        if ($user->id === $currentUser->id) {
            return response()->json(
                ['message' => 'You cannot delete your own account.'],
                Response::HTTP_FORBIDDEN
            );
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}

