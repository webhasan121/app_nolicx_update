<?php

namespace App\Livewire\Reseller\Products;

use Livewire\Component;
use Livewire\Attributes\Layout;


#[layout('layouts.app')]
class Create extends Component
{
    public function render()
    {
        return view('livewire.reseller.products.create');
    }
}
