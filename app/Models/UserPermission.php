<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;


    protected $fillable = [
        'id',
        'user_id',
        'permission_id',
        'campaign_managed_id',
        'created_at',
        'updated_at',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function permission(){
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }

    public function campaign_managed(){
        return $this->belongsTo(CampaignManaged::class, 'campaign_managed_id', 'id');
    }


}