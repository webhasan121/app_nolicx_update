<?php

namespace App\Livewire\Reseller\Categories;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;


#[layout('layouts.app')]
class Index extends Component
{
    public $categories;

    #[On('refresh')]
    public function mount()
    {
        $this->categories = auth()->user()->myCategoryAsReseller;
    }

    public function render()
    {
        return view('livewire.reseller.categories.index');
    }
}
