<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reseller_has_document extends Model
{
    //
    // protected $table = 'vendor_has_document';
    protected $fillable = [
        'user_id',
        'reseller_id',
        'deatline',

        // verification 
        'nid',
        'nid_front',
        'nid_back',

        'shop_trade',
        'shop_trade_image',
        'shop_tin',
        'shop_tin_image',

        // payments 
        'payment_type',
        'payment_by',
        'holder_name',
        'payment_to',
        'swift_code',
    ];




    //////////////// 
    // RELATION //
    ///////////////
    public function resellerRequest()
    {
        return $this->belongsTo(reseller::class, 'reseller_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
