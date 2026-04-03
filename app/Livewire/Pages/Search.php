<?php

namespace App\Livewire\Pages;

use App\Models\Category;
use App\Models\Product;
use App\Models\reseller;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Illuminate\Support\Str;
use Livewire\WithPagination;

#[layout('layouts.user.app')]
class Search extends Component
{
    use WithPagination;
    #[URL]
    public $q;
    public function render()
    {
        // search category
        $category = Category::where('name', 'like', '%' . $this->q . '%')->get();
        if (count($category) == 0) {
            $category = Category::getAll();
        }
        // reseller
        $shop = reseller::where(function ($q) {
            $q->where('shop_name_en', 'like', '%' . $this->q . '%')
                ->where(['status' => 'Active']);
        })->get();

        // product - full text search with weighted relevance and pagination
        // $product = Product::selectRaw("*, 
        //     MATCH(name) AGAINST(? IN BOOLEAN MODE) * 3 +
        //     MATCH(title) AGAINST(? IN BOOLEAN MODE) * 2 +
        //     MATCH(description) AGAINST(? IN BOOLEAN MODE) AS relevance", [$this->q, $this->q, $this->q])
        //     ->whereRaw("MATCH(name, title, description) AGAINST(? IN BOOLEAN MODE)", [$this->q])
        //     ->orderByDesc('relevance')
        //     ->paginate(10);

        // $product = Product::where('belongs_to_type', 'reseller')
        //     ->whereRaw("MATCH(name) AGAINST(? IN BOOLEAN MODE)", [$this->q])
        //     ->get();

        $product = Product::where(['belongs_to_type' => 'reseller', 'status' => 'Active'])->where(function ($q) {
            $q->whereAny(['name', 'title'], 'like', '%' . $this->q . '%');
        })->paginate(30);
        return view('livewire.pages.search', compact('category', 'product', 'shop'));
    }
}
