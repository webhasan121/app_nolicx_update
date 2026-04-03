<?php

namespace App\Livewire\Pages;

use App\Models\Product;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;


#[layout('layouts.user.app')]
class Products extends Component
{


    use WithPagination;
    #[URL]
    public $sort = 'desc', $search;

    public $cart, $products = [], $offset = 0, $limit = 50, $load = false, $withDiscount = true;

    protected $listeners = ['$refresh'];

    public function updated()
    {
        $this->getData();
    }

    public function getData()
    {
        // dd(User::count());
        $this->products = Product::where(['belongs_to_type' => 'reseller', 'status' => 'Active'])->orderBy('id', $this->sort)->offset($this->offset)->limit($this->limit)->get();
        // array_push($this->products, $data);
        // dd($this->products);
     

        if (Product::count() > $this->limit) {
            $this->load = true;
        } else {
            $this->load = false;
        } 
    }

    public function loadMore()
    {
        $this->limit += 1;
        $this->getData();
    }


    public function render()
    {
        // dd($products);
        return view('livewire.pages.products');
    }
}
