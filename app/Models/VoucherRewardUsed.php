<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherRewardUsed extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'code',
        'status',
        'user_id',
        'campaign_id',
        'campaign_managed_id',
        'reward_id',
        'created_at',
        'updated_at',
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
