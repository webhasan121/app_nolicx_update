<?php

namespace App\Livewire\System\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[URL]
    public $filter = 'Active', $from = 'all', $find, $isIncludeResel = true;
    public $leftSide = false;

    public function updated($property)
    {
        if ($property == $this->from && $this->from != 'reseller') {
            $this->isIncludeResel = false;
        }
    }

    public function render()
    {
        $products = [];
        $query = Product::query();

        if ($this->from && $this->from != 'all' && $this->from != 'id') {
            $query->where(['belongs_to_type' => $this->from]);
        }

        if ($this->find && $this->from != 'id') {
            $query->where(['user_id' => $this->find]);
        }

        // if ($this->isIncludeResel) {
        //     $query->where(function ($qy) {
        //         $qy->isResel()->count();
        //     });
        // }

        if ($this->filter != 'both') {
            $query->where(['status' => $this->filter]);
        }

        if ($this->find && (empty($this->from) || $this->from == 'all')) {
            $products = Product::where(['id' => $this->find])->paginate(2);
        } else {

            $products = $query->orderBy('id', 'desc')->paginate(50);
        }
        return view('livewire.system.products.index', compact('products'));
    }
}
