<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorHasDocument extends Model
{
    protected $table = 'vendor_has_documents';

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

        // payments
        'payment_type',
        'payment_by',
        'holder_name',
        'payment_to',
        'swift_code',
    ];

    public function vendorRequest()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
