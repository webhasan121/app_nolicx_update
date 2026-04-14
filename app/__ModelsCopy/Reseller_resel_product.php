<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reseller_resel_product extends Model
{
    protected $guarded = [];

    public function reselProduct()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }


    public function mainProduct()
    {
        return $this->belongsTo(Product::class, 'parent_id', 'id');
    }


    public function reSeller()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function owner()
    {
        return $this->belongsTo(User::class, 'belongs_to', 'id');
    }
}
