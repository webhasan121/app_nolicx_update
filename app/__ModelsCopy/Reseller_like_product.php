<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reseller_like_product extends Model
{
    protected $fillable =
    [
        'user_id',
        'product_id',
        'quantity',
        'attr',
        'reseller_price',
        'note',
    ];
}
