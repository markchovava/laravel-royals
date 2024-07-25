<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardResource extends JsonResource
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
            'name' => $this->name,
            'target_points' => $this->target_points,
            'points_per_voucher' => $this->points_per_voucher,
            'price_per_voucher' => $this->price_per_voucher,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'campaign_managed' => new CampaignManagedResource($this->whenLoaded('campaign_managed')),
        ];
    }
}
