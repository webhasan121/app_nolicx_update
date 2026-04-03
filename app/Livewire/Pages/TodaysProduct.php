<?php

namespace App\Livewire\Pages;

use App\Models\Product;
use Illuminate\Support\Carbon;
use Livewire\Component;

class TodaysProduct extends Component
{
    public function render()
    {
        return view(
            'livewire.pages.todays-product',
            [
                'todays_products' => Product::whereDate('created_at', now())->where(['belongs_to_type' => 'reseller'])->orderBy('vc')->limit(20)->get(),
            ]
        );
    }
}
