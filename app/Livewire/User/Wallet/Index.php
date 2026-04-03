<?php

namespace App\Livewire\User\Wallet;

use App\Models\DistributeComissions;
use App\Models\Package_pays;
use App\Models\Packages;
use App\Models\TakeComissions;
use App\Models\User;
use App\Models\user_task;
use App\Models\vip;
use App\Models\Withdraw;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[layout('layouts.user.dash.userDash')]
class Index extends Component
{
    public $task, $comission, $reffer, $withdraw, $cut;

    public function mount()
    {
        $this->withdraw = Withdraw::where(['user_id' => Auth::id(), 'status' => 'Pending'])->latest('id')->get();
        $this->comission = DistributeComissions::where(['user_id' => Auth::id(), 'confirmed' => true])->whereDate('updated_at', today())->sum('amount');
        $this->cut = TakeComissions::where(['user_id' => Auth::id(), 'confirmed' => true])->whereDate('updated_at', today())->sum('take_comission');
        $this->reffer = auth()->user()->getMyvipRef()->whereDate('updated_at', today())->sum('comission');
    }

    public function render()
    {
        $this->task = user_task::where(['user_id' => Auth::id()])->whereDate('updated_at', '=', today())->first();
        // $this->comission = ;
        return view('livewire.user.wallet.index');
    }
}
