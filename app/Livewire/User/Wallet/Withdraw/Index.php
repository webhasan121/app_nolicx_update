<?php

namespace App\Livewire\User\Wallet\Withdraw;

use App\Http\Middleware\EnsureResellerIsActive;
use App\Models\Withdraw;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;


#[layout('layouts.user.dash.userDash')]
class Index extends Component
{

    #[validate('required')]
    public $pay_to, $pay_by, $amount, $phone, $withdrawId = '', $withdraw;

    public function mount()
    {
        $this->phone = auth()->user()->phone ?? "";
        $this->withdraw = auth()->user()->myWithdraw;
    }


    public function render()
    {
        $withdraw = [];
        return view('livewire.user.wallet.withdraw.index', compact('withdraw'));
    }
}
