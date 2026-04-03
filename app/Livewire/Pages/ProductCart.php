<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class ProductCart extends Component
{
    #[URL]
    public $product;

    public function addToCart()
    {
        if (Auth::guest()) {
            $this->dispatch('warning', 'Login to add Cart');
        } else {

            $isAlreadyInCart = auth()->user()->myCarts()->where(['product_id' => $this->product->id])->exists();
            if ($isAlreadyInCart) {
                $this->dispatch('info', "Product already in cart");
            } else {
                cart::create(
                    [
                        'product_id' => $this->product->id,
                        'name' => $this->product?->title,
                        'image' => $this->product?->thumbnail,
                        'price' => $this->product?->offer_type ? $this->product?->discount : $this->product?->price,
                        'user_id' => auth()->user()->id,
                        'user_type' => 'user',
                        'belongs_to' => $this->product->user_id,
                        'belongs_to_type' => 'reseller',
                        'qty' => 1,
                    ]
                );

                $count = auth()->user()->myCarts()->count();
                // dd($isAlreadyInCart);
                $this->dispatch('cart', $count);
                $this->dispatch('success', 'Product Added to cart');
            }
        }
    }

    public function render()
    {
        return view('livewire.pages.product-cart');
    }
}
