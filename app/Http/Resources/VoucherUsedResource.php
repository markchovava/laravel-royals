<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherUsedResource extends JsonResource
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
            'campaign_managed_id' => $this->campaign_managed_id,
            'code' => $this->code,
            'points' => $this->points,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'campaign_managed' => new CampaignManagedResource($this->whenLoaded('campaign_managed')),
        ];
    }
}
