<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Category;


#[layout('layouts.user.app')]
class Categories extends Component
{
    public $categories;

    public function mount()
    {
        $this->categories = Category::getAll();;
    }

    public function render()
    {
        return view('livewire.pages.categories');
    }
}
