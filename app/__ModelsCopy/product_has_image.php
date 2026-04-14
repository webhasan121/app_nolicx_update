<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class product_has_image extends Model
{
    //
    protected $fillable =
    [
        'product_id',
        'image',
    ];





    /**
     * give user default 'user' role 
     * when model is created
     */
    protected static function boot(): void
    {
        parent::boot();
        // static::creating(function (Product $product) {
        //     $product->user_id = Auth::id();
        //     $product->status = 1;
        // });


        static::deleted(function (product_has_image $pi) {
            Storage::disk('public')->delete($pi->image);
        });
    }
}
