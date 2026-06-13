<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products_has_comments extends Model
{
    //
    protected $casts = [
        'rating' => 'integer',
        'approved' => 'boolean',
        'images' => 'array',
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cartOrder()
    {
        return $this->belongsTo(CartOrder::class);
    }

    public function likes()
    {
        return $this->hasMany(ProductCommentLike::class, 'products_has_comment_id');
    }
}
