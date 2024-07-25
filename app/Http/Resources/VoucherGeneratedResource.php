<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherGeneratedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * 
     *
     **/
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'campaign_managed_id' => $this->campaign_managed_id,
            'user_id' => $this->user_id,
            'code' => $this->code,
            'receipt_no' => $this->receipt_no,
            'phone' => $this->phone,
            'points' => $this->points,
            'amount' => $this->amount,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'campaign_managed' => new CampaignManagedResource($this->whenLoaded('campaign_managed')),
        ];
    }
}
