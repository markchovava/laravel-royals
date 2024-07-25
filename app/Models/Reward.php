<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'campaign_managed_id',
        'name',
        'target_points',
        'points_per_voucher',
        'price_per_voucher',
        'created_at',
        'updated_at',
    ];
    
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function campaign_managed(){
        return $this->belongsTo(Campaign::class, 'campaign_managed_id', 'id');
    }
    
}
