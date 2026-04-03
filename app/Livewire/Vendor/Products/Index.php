<?php

namespace App\Livewire\Vendor\Products;

use App\Models\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

#[layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[URL]
    public $nav = 'Active', $take, $relatedImage = [];


    public $selectedModel = [];
    public $ap, $dp, $tp, $search;

    public function moveToTrash()
    {
        // 
        if (count($this->selectedModel) > 0) {
            auth()->user()->myProducts()->whereIn('id', $this->selectedModel)->delete();
            $this->reset('selectedModel');

            $this->dispatch('success', 'Product Move to Trash');
        }
    }
    public function restoreFromTrash()
    {
        // 
        if (count($this->selectedModel) > 0) {
            auth()->user()->myProducts()->onlyTrashed()->whereIn('id', $this->selectedModel)->restore();
            $this->reset('selectedModel');

            $this->dispatch('success', 'Product restore from Trash');
        }
    }


    public function render()
    {

        //     

        if ($this->take == 'trash') {
            $products = auth()->user()->myProducts()->onlyTrashed()->latest('id')->paginate(20);
        } else {
            $products = auth()->user()->myProducts()->where(['status' => $this->nav])->latest('id')->paginate(200);
        }

        // if ($this->nav == 'Draft') {
        //     $products = auth()->user()->myProducts()->onlyTrashed()->paginate(200);
        // }


        if ($this->search) {
            $products = auth()->user()->myProducts()->where('title', 'like', '%' . $this->search . "%")->orwhere('name', 'like', '%' . $this->search . "%")->latest('id')->paginate(20);
        }
        return view('livewire.vendor.products.index', compact('products'));
    }
}
