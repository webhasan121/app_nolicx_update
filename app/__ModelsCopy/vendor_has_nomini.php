<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vendor_has_nomini extends Model
{
    //
    protected $fillable = [
        // security
        'user_id',
        'vendor_id',
        'nomini',
        'nomini_relation',
        'nomini_nid',
        'nomini_phone',
    ];
}
