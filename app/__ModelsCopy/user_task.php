<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class user_task extends Model
{
    //
    protected $fillable =
    [
        'user_id',
        'vip_id',
        'package_id',
        'earn_by', // task, vip-purchase, reffered,
        'coin',
        'time',
    ];
}
