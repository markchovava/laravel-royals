<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignManaged extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'name',
        'description',
        'total',
        'quantity',
        'quantity_remaining',
        'status',
        'start_date',
        'num_of_days',
        'end_date',
        'company_name',
        'company_phone',
        'company_website',
        'company_address',
        'company_email',
        'created_at',
        'updated_at',
    ];
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function reward(){
        return $this->hasOne(Reward::class, 'campaign_managed_id', 'id');
    }
}
