<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Navigations_has_link extends Model
{
    //  mass assignable fillable
    protected $fillable = [
        'name',
        'navigations_id',
        'url',
    ];
}
