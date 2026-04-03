<?php 

use Livewire\Volt\Component;
use Livewire\Attributes\URL;
use App\Models\cart;

new class extends Component 
{
    public $product;

    // public function addToCart()
    // {   
    //     if (Auth::guest()) {
    //         $this->dispatch('warning', 'Login to add Cart');
    //     }else{

    //         $isAlreadyInCart = auth()->user()->myCarts()->where(['product_id' => $this->product->id])->exists();
    //         if ($isAlreadyInCart) {
    //             $this->dispatch('info', "Product already in cart");
    //         }else{
    //             cart::create(
    //                 [
    //                     'product_id' => $this->product->id,
    //                     'name' => $this->product?->title,
    //                     'image' => $this->product?->thumbnail,
    //                     'price' => $this->product?->offer_type ? $this->product?->discount : $this->product?->price,
    //                     'user_id' => auth()->user()->id,
    //                     'user_type' => 'user',
    //                     'belongs_to' => $this->product->user_id,
    //                     'belongs_to_type' => 'reseller',
    //                     'qty' => 1,
    //                 ]
    //             );
        
    //             $count = auth()->user()->myCarts()->count();
    //             // dd($isAlreadyInCart);
    //             $this->dispatch('cart', $count);
    //             $this->dispatch('success', 'Product Added to cart');
    //         }
    //     }

    // }
}

?>


{{-- @props(['product']) --}}
<livewire:pages.product-cart :$product>