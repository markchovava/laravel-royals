<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'code',
        'status',
        'reward_id',
        'campaign_managed_id',
        'campaign_id',
        'updated_at',
        'created_at',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function campaign_managed(){
        return $this->belongsTo(CampaignManaged::class, 'campaign_managed_id', 'id');
    }
    public function campaign(){
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }
    public function reward(){
        return $this->belongsTo(Reward::class, 'reward_id', 'id');
    }

    
}
