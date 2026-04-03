<?php

namespace App\Livewire\System\Vendors\Vendor;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Reactive;

class ProductSingle extends Component
{
    /**
     * url dara
     * @param Product
     */
    public $id;

    /**
     * component data
     */
    public $product;


    /**
     * mount
     */
    public function mount()
    {
        // 
        $this->getData();
    }


    public function getData() {}



    public function render()
    {
        return view('livewire.system.vendors.vendor.product-single')->layout('layouts.app');
    }
}
