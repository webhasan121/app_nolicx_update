<?php

namespace App\Livewire\User\Wallet;

use App\Models\TakeComissions;
use Livewire\Component;
use App\Helpers\paginate;
use Illuminate\Support\Facades\Auth;

class SystemTakeComission extends Component
{

    public function render()
    {
        $comissions = TakeComissions::where(['user_id' => Auth::id()])->paginate(20);
        return view('livewire.user.wallet.system-take-comission', compact('comissions'));
    }
}
