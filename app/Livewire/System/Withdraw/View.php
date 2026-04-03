<?php

namespace App\Livewire\System\Withdraw;

use App\Http\Controllers\UserWalletController;
use App\Models\Withdraw;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;

#[layout('layouts.app')]
class View extends Component
{
    #[URL]
    public $id, $tab = 'Withdraw';

    #[validate('required')]
    public $paid_from, $trx;

    public $rMessage = null;

    public $withdraw;


    public function mount()
    {
        $this->getDeta();
        // $this->paid_from = Auth::user()->name . "-" . Auth::user()->email;
    }

    public function getDeta()
    {
        $this->withdraw = Withdraw::findOrFail($this->id);
        if (!$this->withdraw?->seen_by_admin) {
            $this->withdraw->seen_by_admin = now();
            $this->withdraw->save();
        }
    }


    public function confirmPayment()
    {
        if ($this->withdraw->user?->abailCoin() > $this->withdraw->amount) {

            if (!$this->withdraw->is_rejected) {

                $this->validate();
                // $sf = round(($this->withdraw * 0.2) / 100, 2);
                // $mf = round(($this->withdraw * 0.3) / 100, 2);
                // $tf = $sf + $mf;


                $deta = [
                    'confirmed_by' => auth()->user()->name . "-" . auth()->user()->email,
                    'status' => true,
                    'paid_from' => $this->paid_from,
                    'transaction_id' => $this->trx,
                ];
                $this->withdraw->forcefill($deta);
                $this->withdraw->save();

                // reduce amount from user
                // $this->withdraw->user->coin -= $this->withdraw->amount;
                UserWalletController::remove($this->withdraw->user_id, $this->withdraw->amount);

                $this->dispatch('refresh');
                $this->dispatch('success', 'Withdraw Confirmed !');
            } else {
                $this->dispatch('refresh');
                $this->dispatch('error', 'Payment already Rejected !');
            }
        } else {
            $this->dispatch('warning', 'User too low balance !');
        }
        // dd('payment confirm method');
        $this->dispatch('success', 'Withdraw Confirmed !');
    }

    public function rejectPayment()
    {
        return false;
        if ($this->withdraw->status == false) {



            $this->withdraw->status = false;
            $this->withdraw->is_rejected = true;
            $this->withdraw->reject_for = $this->rMessage;
            $this->withdraw->save();



            $this->dispatch('refresh');
            $this->dispatch('success', 'Withdraw Rejected !');
        } else {
            $this->dispatch('refresh');
            $this->dispatch('error', 'Withdraw Request Already Accept !');
        }
        // dd('rmethod');
    }


    public function render()
    {

        return view('livewire.system.withdraw.view');
    }
}
