<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[layout('layouts.user.dash.userDash')]
class Carts extends Component
{
    public $totalAmount;
    public function remove($id)
    {
        auth()->user()->myCarts()->find($id)->delete();
        $this->dispatch('cart', auth()->user()->myCarts()->count());
        $this->dispatch('success', "Cart Item Deletd !");
    }

    public function render()
    {
        return view('livewire.user.carts');
    }
}
