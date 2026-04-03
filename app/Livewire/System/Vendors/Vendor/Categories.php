<?php

namespace App\Livewire\System\Vendors\Vendor;

use App\Models\vendor;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Reactive;

class Categories extends Component
{
    #[URL]
    public $id;

    public $categories, $vendor;


    public function mount()
    {
        $this->vendor = vendor::find($this->id);
    }


    public function render()
    {
        return view('livewire.system.vendors.vendor.categories')->layout('layouts.app');
    }
}
