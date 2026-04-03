<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class vendor_has_document extends Model
{
    //
    // protected $table = 'vendor_has_document';
    protected $fillable = [
        'user_id',
        'vendor_id',
        'deatline',

        // verification 
        'nid',
        'nid_front',
        'nid_back',

        'shop_trade',
        'shop_trade_image',
        'shop_tin',
        'shop_tin_image',
        // 'shop_bin',
        // 'shop_bin_image',

        // payments 
        'payment_type',
        'payment_by',
        'holder_name',
        'payment_to',
        'swift_code',

        // certificate
        // 'iso',
        // 'minority',
        // 'women',
        // 'other',
    ];


    //////////////// 
    // RELATION //
    ///////////////
    public function vendorRequest()
    {
        return $this->belongsTo(vendor::class, 'vendor_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
