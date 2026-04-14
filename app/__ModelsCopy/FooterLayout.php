<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterLayout extends Model
{
    protected $fillable =
    [
        'name',
        'layout',
        'is_active'
    ];
}
