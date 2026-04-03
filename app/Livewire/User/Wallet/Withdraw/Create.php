<?php

namespace App\Livewire\User\Wallet\Withdraw;

use Livewire\Component;
use Livewire\Attributes\Layout;


#[layout('layouts.user.dash.userDash')]
class Create extends Component
{
    public function render()
    {
        return view('livewire.user.wallet.withdraw.create');
    }
}
