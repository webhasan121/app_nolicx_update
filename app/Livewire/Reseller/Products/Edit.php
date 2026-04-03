<?php

namespace App\Livewire\Reseller\Products;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[layout('layouts.app')]
class Edit extends Component
{

    #[URL]
    public $id;
    public function render()
    {
        return view('livewire.reseller.products.edit');
    }
}
