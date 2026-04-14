<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_logins extends Model
{
    protected $fillable =
    [
        'user_id',
        'ip',
        'device',
        'country_code',
        'country',
    ];
}
