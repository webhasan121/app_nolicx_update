<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCommentLike extends Model
{
    protected $fillable = [
        'products_has_comment_id',
        'user_id',
    ];

    public function comment()
    {
        return $this->belongsTo(Products_has_comments::class, 'products_has_comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
