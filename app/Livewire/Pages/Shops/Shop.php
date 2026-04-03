<?php

namespace App\Livewire\Pages\Shops;

use App\Models\Category;
use App\Models\Product;
use App\Models\reseller;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[layout('layouts.user.app')]
class Shop extends Component
{
    #[URL]
    public $id, $name;

    public $products, $categories, $shops;

    public function mount()
    {

        $this->shops = reseller::findOrFail($this->id);
    }


    public function getDeta()
    {
        $this->products = Product::query()->active()->reseller()->where(['user_id' => $this->shops?->user?->id])->get();
        $this->categories = Category::where(['belongs_to' => 'reseller', 'user_id' => $this->shops?->user?->id])->get();
        // dd($this->categories);
    }


    public function render()
    {
        return view('livewire.pages.shops.shop');
    }
}
