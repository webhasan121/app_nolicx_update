<?php

namespace App\Livewire\System\Settings\Pages;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[layout('layouts.app')]
class Edit extends Component
{
    public function render()
    {
        return view('livewire.system.settings.pages.edit');
    }
}
