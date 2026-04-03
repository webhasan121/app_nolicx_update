<?php

namespace App\Livewire\Page;

use App\Models\page_settings;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[layout('layouts.user.app')]
class Page extends Component
{
    #[URL]
    public $slug;

    public function render()
    {
        $pages = page_settings::where('slug', '=', $this->slug)->first();
        $otherPages = page_settings::all();
        return view('livewire.page.page', compact('pages', 'otherPages'));
    }
}
