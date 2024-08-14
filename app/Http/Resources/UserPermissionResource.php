<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPermissionResource extends JsonResource
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
            'user_id' => $this->user_id,
            'permission_id' => $this->permission_id,
            'campaign_managed_id' => $this->campaign_managed_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'permission' => new PermissionResource($this->whenLoaded('permission')),
            'campaign_managed' => new CampaignManagedResource($this->whenLoaded('campaign_managed')),
        ];
    }
}
