<?php

namespace App\Livewire\User\Wallet;

use App\Models\vip;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[layout('layouts.user.dash.userDash')]
class Reffer extends Component
{

    use WithPagination;
    public function render()
    {
        $refs = auth()->user()->getMyvipRef;
        // dd(auth()->user()->getMyvipRef);
        return view('livewire.user.wallet.reffer', compact('refs'));
    }
}
