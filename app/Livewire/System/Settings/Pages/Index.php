<?php

namespace App\Livewire\System\Settings\Pages;

use App\Models\page_settings;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[layout('layouts.app')]
class Index extends Component
{
    public function deletePage($id)
    {
        if ($pages = page_settings::findOrFail($id)) {
            $pages->delete();
            $this->dispatch('success', "Page Deleted !");
        }
    }
    public function render()
    {
        $pages = page_settings::all();
        return view('livewire.system.settings.pages.index', compact('pages'));
    }
}
