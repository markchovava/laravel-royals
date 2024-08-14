<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'id',
        'user_id',
        'priority',
        'name',
        'slug',
        'created_at',
        'updated_at',
    ];


    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function users(){
        return $this->belongsToMany(User::class, 'user_permissions', 'permission_id', 'user_id')
            ->withTimestamps();
    }


}
