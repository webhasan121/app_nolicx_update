<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTask extends Model
{
    protected $table = 'user_tasks';

    protected $fillable =
    [
        'user_id',
        'vip_id',
        'package_id',
        'earn_by',
        'coin',
        'time',
    ];
}
