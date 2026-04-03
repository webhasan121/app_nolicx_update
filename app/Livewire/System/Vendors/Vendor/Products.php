<?php

namespace App\Livewire\System\Vendors\Vendor;

use App\Models\vendor;
use Livewire\Component;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Url;


class Products extends Component
{
    /**
     * url data
     */
    #[URL]
    public $id, $filter = 'Active';

    /**
     * component data
     */
    public $vendor;


    /**
     * mount
     */
    public function mount()
    {
        $this->getData();
    }


    /**getting data */
    public function getData()
    {
        $this->vendor = vendor::find($this->id);
    }


    public function render()
    {
        return view('livewire.system.vendors.vendor.products')->layout('layouts.app');
    }
}
