<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorHasNomini extends Model
{
    protected $table = 'vendor_has_nominis';

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
