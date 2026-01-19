<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User Resource
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $isOwnProfile = $user?->id === $this->id;
        $isAdmin = $user?->hasRole('admin') ?? false;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when($isOwnProfile || $isAdmin, $this->email),
            'avatar' => $this->when(isset($this->avatar), $this->avatar),
            'bio' => $this->bio,
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->map(function ($role) {
                    return ['id' => $role->id, 'name' => $role->name];
                });
            }),
            'created_at' => $this->created_at ? $this->created_at->toISOString() : null,
        ];
    }
}

