<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'campaign_managed_id',
        'reward_id',
        'current_quantity',
        'current_points',
        'created_at',
        'updated_at',
    ];

    public function campaign_managed(){
        return $this->belongsTo(CampaignManaged::class, 'campaign_managed_id', 'id');
    }

    public function reward(){
        return $this->belongsTo(Reward::class, 'reward_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
