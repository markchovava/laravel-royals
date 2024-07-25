<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
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
            'campaign_managed_id' => $this->campaign_managed_id,
            'reward_id' => $this->reward_id,
            'current_quantity' => $this->current_quantity,
            'current_points' => $this->current_points,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'campaign_managed' => new CampaignManagedResource($this->whenLoaded('campaign_managed')),
            'reward' => new RewardResource($this->whenLoaded('reward')),
        ];
    }
}
