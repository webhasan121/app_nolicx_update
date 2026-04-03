<?php

namespace App\Livewire\User\Vip;

use App\Models\vip;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Reactive;
use function Livewire\Volt\state;

#[layout('layouts.user.dash.userDash')]
class Index extends Component
{
    public $vip;

    public function mount()
    {
        $this->vip = vip::where(['user_id' => auth()->user()->id])->get();
    }

    public function render()
    {
        return view('livewire.user.vip.index');
    }
}
