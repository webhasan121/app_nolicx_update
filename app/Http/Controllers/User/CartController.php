<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    //
    public function index(Request $request)
    {
        $user = $request->user();

        $carts = $user->myCarts()
            ->with('product.owner')
            ->get()
            ->map(function ($cart) {
                return [
                    'id' => $cart->id,
                    'price' => $cart->price,
                    'created_at_human' => $cart->created_at?->diffForHumans(),
                    'product' => [
                        'id' => $cart->product?->id,
                        'name' => $cart->product?->name,
                        'slug' => $cart->product?->slug,
                        'thumbnail' => $cart->product?->thumbnail,
                        'shop_name' => $cart->product?->owner?->resellerShop()?->shop_name_en,
                    ]
                ];
            });

        return Inertia::render('User/Carts/Index', [
            'carts' => $carts,
        ]);
    }
    public function destroy($id)
    {
        auth()->user()->myCarts()->where('id', $id)->delete();

        return back()->with('success', 'Cart Item Deleted!');
    }
}
