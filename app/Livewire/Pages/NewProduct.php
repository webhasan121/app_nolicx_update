<?php

namespace App\Livewire\Pages;

use App\Models\Product;
use Livewire\Component;

class NewProduct extends Component
{
    public function render()
    {
        return view(
            'livewire.pages.new-product',
            [
                'products' => [],
            ]
        );
    }
}
