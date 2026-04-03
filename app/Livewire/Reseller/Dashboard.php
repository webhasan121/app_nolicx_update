<?php

namespace App\Livewire\Reseller;

use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use App\Models\vendor;

class Dashboard extends Component
{
    public $products = [], $tp,  $category, $vendor, $trands;

    public function mount()
    {

        $this->tp = Product::where(['belongs_to_type' => 'vendor'])->count();
        $this->vendor = vendor::count();
        $this->products = Product::where(['belongs_to_type' => 'vendor', 'status' => 'Active'])->limit('50')->get();
    }

    public function getData()
    {
        // dd($this->products);
    }


    public function render()
    {
        return view('livewire.reseller.dashboard');
    }
}
