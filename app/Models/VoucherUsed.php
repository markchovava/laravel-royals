<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherUsed extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'code',
        'points',
        'voucher_generated_id',
        'campaign_managed_id',
        'updated_at',
        'created_at',
    ];


}
