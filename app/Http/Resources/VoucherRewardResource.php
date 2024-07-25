<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherRewardResource extends JsonResource
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
            'code' => $this->code,
            'status' => $this->status,
            'reward_id' => $this->reward_id,
            'campaign_managed_id' => $this->campaign_managed_id,
            'campaign_id' => $this->campaign_id,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'campaign' => new CampaignResource($this->whenLoaded('campaign')),
            'reward' => new RewardResource($this->whenLoaded('reward')),
            'campaign_managed' => new CampaignManagedResource($this->whenLoaded('campaign_managed')),
        ];
    }
}
