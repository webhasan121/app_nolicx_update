<?php

namespace App\Livewire\System\Products;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[layout('layouts.app')]
class Filter extends Component
{
    public function render()
    {
        return view('livewire.system.products.filter');
    }
}
