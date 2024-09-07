<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'role_level' => $this->role_level,
            'password' => $this->password,
            /* 'code' => $this->code, */
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'role' => new RoleResource($this->whenLoaded('role')),
            'user_author' => new UserAuthorResource($this->whenLoaded('user_author')),
        ];
    }
}
