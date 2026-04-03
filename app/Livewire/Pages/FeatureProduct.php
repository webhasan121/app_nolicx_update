<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Product;

class FeatureProduct extends Component
{
    public function render()
    {
        return view('livewire.pages.feature-product', [
            'products' => Product::whereDate(['created_at' => now()->endOfDay()])->orderBy('vc')->limit(20),
        ]);
    }
}