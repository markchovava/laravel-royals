<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherGenerated extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'campaign_managed_id',
        'code',
        'status',
        'receipt_no',
        'phone',
        'amount',
        'points',
        'updated_at',
        'created_at',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function campaign_managed(){
        return $this->belongsTo(CampaignManaged::class, 'campaign_managed_id', 'id');
    }
}
