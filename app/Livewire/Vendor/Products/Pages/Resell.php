<?php

namespace App\Livewire\Vendor\Products\Pages;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[layout('layouts.app')]
class Resell extends Component
{
    #[URL]
    public $product;

    public $products, $act;

    public function mount()
    {
        $this->products = Product::find(decrypt($this->product));
        $this->act = auth()->user()->account_type();

        // if ($this->act == 'reseller') {

        //     $this->vendors = $this->products->isResel?->owner?->vendorShop();
        // }
        // dd($this->vendors);
    }


    public function render()
    {
        return view('livewire.vendor.products.pages.resell');
    }
}
