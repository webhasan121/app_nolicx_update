<?php

namespace App\Livewire\Vendor\Products\Pages;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[layout('layouts.app')]
class Orders extends Component
{

    #[URL]
    public $product;

    public $products;


    public function mount()
    {
        $this->products = Product::find(decrypt($this->product));
    }


    public function render()
    {
        return view('livewire.vendor.products.pages.orders');
    }
}
