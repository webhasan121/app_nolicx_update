<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use App\Models\Product
class Reseller_order_details extends Model
{
    protected $fillable =
    [
        'order_id',
        'belongs_to',
        'product_id',
        'reseller_price',
        'original_price',
        'quantity',
        'total',
        'attr', // sieze or other attributes
    ];

    public function order()
    {
        return $this->belongsTo(Reseller_has_order::class, 'id', 'order_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function reseller()
    {
        return $this->belongsTo(User::class);
    }
}
