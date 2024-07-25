<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignManagedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * 
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
            'total' => $this->total,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'company_name' => $this->company_name,
            'company_phone' => $this->company_phone,
            'company_website' => $this->company_website,
            'company_email' => $this->company_email,
            'company_address' => $this->company_address,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'reward' => new RewardResource($this->whenLoaded('reward')),
        ];
    }
}
