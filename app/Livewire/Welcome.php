<?php

namespace App\Livewire;

use App\Jobs\UpdateProductSalesIndex;
use App\Models\CartOrder;
use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Slider;
use App\Models\Slider_has_slide;
use App\Models\Static_slider;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;


#[layout('layouts.user.app')]
class Welcome extends Component
{
    public $products = [], $categories = [], $topSellingProducts = [], $displayAtHome = [];

    public  function mount()
    {
        $this->products =  Product::query()->reseller()->active()->orderBy('id', 'desc')->limit(20)->get();
        // dd($this->products);
        $this->categories = Category::getAll();
    }
    public function getProducts()
    {
        // $this->displayAtHome = Product::query()->reseller()->active()->home()->orderBy('id', 'desc')->limit(20)->get();
    }

    public function render()
    {

        return view(
            'livewire.welcome',
            [
                'ss' => Static_slider::query()->home()->active()->with('slides')->get()
            ]
        );
    }
}
