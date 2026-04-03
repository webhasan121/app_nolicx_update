<?php

namespace App\Livewire\Pages;

use App\Models\Product;
use App\Models\productSalesIndex;
use Livewire\Component;

class TopSales extends Component
{
    public $products;

    public function mount()
    {
        // $this->products = productSalesIndex::query()->orderBy('total_sales', 'desc')->limit(20)->pluck('product_id');
        // dd($this->products);
        $this->products = Product::query()->reseller()->whereIn('id', productSalesIndex::query()->orderBy('total_sales', 'desc')->limit(20)->pluck('product_id'))->get();
    }

    public function render()
    {
        return view('livewire.pages.top-sales');
    }
}
