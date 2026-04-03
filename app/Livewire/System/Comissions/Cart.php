<?php

namespace App\Livewire\System\Comissions;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[layout('layouts.app')]
class Cart extends Component
{
    
    public function render()
    {
        return view('livewire.system.comissions.cart');
    }
}
