<?php

namespace App\Livewire\Rider;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[layout('layouts.app')]
class RiderInfo extends Component
{
    public function render()
    {
        return view('livewire.rider.rider-info');
    }
}
