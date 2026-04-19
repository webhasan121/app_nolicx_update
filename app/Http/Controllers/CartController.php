<?php

namespace App\Http\Controllers;

use App\Models\cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function store(Request $request)
    {
        $product = \App\Models\Product::findOrFail($request->product_id);

        $exists = auth()->user()
            ->myCarts()
            ->where('product_id', $product->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'type' => 'info',
                'message' => 'Product already in cart',
            ]);
        }

        cart::create([
            'product_id' => $product->id,
            'name' => $product->title,
            'image' => $product->thumbnail,
            'price' => $product->offer_type ? $product->discount : $product->price,
            'user_id' => auth()->id(),
            'user_type' => 'user',
            'belongs_to' => $product->user_id,
            'belongs_to_type' => 'reseller',
            'qty' => 1,
        ]);

        $count = auth()->user()->myCarts()->count();

        return response()->json([
            'type' => 'success',
            'message' => 'Product added to cart',
            'cartCount' => $count,
        ]);
    }
}
