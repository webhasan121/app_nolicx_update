<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    // use SoftDeletes;
    protected $fillable = ['product_id', 'user_id', 'user_type', 'belongs_to', 'belongs_to_type', 'name', 'image', 'price', 'qty', 'size'];
    // protected $fillable = ['user_id', 'total_price', 'status', 'qty', 'status', ''];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withDefault([
            'id' => 0,
            'slug' => 'deleted-product',
            'name' => 'Deleted Product',
            'image' => 'default.png',
            'price' => 0,
        ]);
    }
}
