<?php

namespace App\Livewire\Reseller\Products\Page;

use Livewire\Component;
use Livewire\Attributes\Layout;


#[layout('layouts.app')]
class Porder extends Component
{
    public function render()
    {
        return view('livewire.reseller.products.page.porder');
    }
}
