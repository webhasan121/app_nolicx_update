<?php

namespace App\Livewire\User\Upgrade\Rider;

use App\Models\rider;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[layout('layouts.user.dash.userDash')]
class Index extends Component
{
    public $rider = [], $nav = 'Active';

    public function mount()
    {
        $this->rider = auth()->user()->requestsToBeRider()->get();
    }

    public function render()
    {
        return view('livewire.user.upgrade.rider.index');
    }
}
