<?php

namespace App\Livewire\Pages;

use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use App\Models\Product;
use Livewire\WithPagination;



#[layout('layouts.user.app')]
class Cproducts extends Component
{
    use WithPagination;

    #[URL]
    public $cat, $ids = [];


    public function render()
    {
        $catId = Category::where(['slug' => $this->cat])->first();
        array_push($this->ids, $catId->id);

        $this->pslug($catId);
        // dd($this->ids);  

        $products = Product::whereIn('category_id', $this->ids)->where(['belongs_to_type' => 'reseller', 'status' => 'Active'])->paginate(20);
        $categories = Category::getAll();
        return view('livewire.pages.cproducts', compact('products', 'categories'));
    }


    private function pslug($elem)
    {
        // array_push($this->ids, $elem->id);
        // $em = $elem->children;
        if ($elem->children) {
            foreach ($elem->children as $child) {
                array_push($this->ids, $child->id);
                $this->pslug($child);
            }
        } else {
            array_push($this->ids, $elem->id);
        }
    }
}
