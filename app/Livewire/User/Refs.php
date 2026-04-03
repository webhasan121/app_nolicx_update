<?php

namespace App\Livewire\User;

use Livewire\Component;
use App\Models\User;
use Livewire\Attributes\Layout;


#[layout('layouts.user.dash.userDash')]
class Refs extends Component
{
    public function mount() {}

    public function render()
    {
        $refUser = User::where(['reference' => auth()->user()->myRef->ref])->latest('id')->paginate(100);
        return view('livewire.user.refs', compact('refUser'));
    }
}
