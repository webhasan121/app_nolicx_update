<?php

namespace App\Livewire\User\Vip\Package;

use App\Models\Packages;
use App\Models\vip;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[layout('layouts.user.dash.userDash')]
class Index extends Component
{
    public $vips;

    public function mount()
    {
        $this->vips = Packages::all();
        // dd($this->vips[0]);
    }

    public function render()
    {
        return view('livewire.user.vip.package.index');
    }
}
