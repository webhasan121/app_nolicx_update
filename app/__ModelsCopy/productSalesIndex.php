<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class productSalesIndex extends Model
{
    protected $fillable = [
        'product_id',
        'total_sales',
        'total_order',
        'user_type'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
