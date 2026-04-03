<?php

namespace App\Livewire\User\Wallet\Diposit;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Package_pays;
use App\Models\Packages;
use App\Models\userDeposit;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;

#[layout('layouts.app')]
class History extends Component
{

    #[validate('required')]
    public $amount, $paymentMethod, $receiverAccountNumber, $senderName, $senderAccountNumber, $transactionId;

    public $history;

    public function mount()
    {
        // if the user dosn't have reseller or vendor or rider or system admin role, redirect to home
        if (!Auth::user()->hasRole(['reseller', 'vendor', 'rider', 'system'])) {
            $this->redirectIntended('dashboard');
        }
        $this->history = auth()->user()->myDeposit;
    }


    public function confirmDeposit()
    {
        $validate = $this->validate();
        // dd($validate);
        $depo = new userDeposit();
        $depo->forceFill(
            [
                'amount' => $this->amount,
                'paymentMethod' => $this->paymentMethod,
                'receiverAccountNumber' => $this->receiverAccountNumber,
                'senderName' => $this->senderName,
                'senderAccountNumber' => $this->senderAccountNumber,
                'transactionId' => $this->transactionId,
                'user_id' => Auth::user()->id,
                'confirmed' => false,

            ]
        );
        $depo->save();

        // dispatch the success message
        $this->dispatch('success', 'Deposit has been requested !');
        $this->reset('amount', 'paymentMethod', 'receiverAccountNumber', 'senderName', 'senderAccountNumber', 'transactionId');
        $this->history = auth()->user()->myDeposit; // Refresh the history after deposit
        $this->dispatch('close-modal', 'depositModal'); // Close the modal after submission

    }


    public function render()
    {
        $payNumber = Package_pays::where(['package_id' => Packages::first()->get('id')->value('id')])->pluck('pay_to', 'pay_type');
        return view('livewire.user.wallet.diposit.history', [
            'payNumber' => $payNumber
        ]);
    }
}
