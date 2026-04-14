<?php

namespace App\Livewire\System\Store;

use Livewire\Component;
use App\Models\Store;
use App\Models\Withdraw;
use Illuminate\Support\Facades\Auth;

class DonationStore extends Component {
    public $store, $method, $amount, $phone, $bankAccount, $accountholder, $bankBranch, $swiftCode, $accountNumber, $remarks;

    protected $listeners = ['refresh' => '$refresh'];

    public function getDeta() {
        $credit = Withdraw::where(['type' => 'credit', 'status' => true])->sum('server_fee');
        $debit = Withdraw::where(['type' => 'debit', 'status' => true])->sum('server_fee');
        // $this->store = Withdraw::where(['type' => 'credit', 'status' => true])->sum('server_fee');
        $this->store = $credit - $debit;
        // $this->store = Withdraw::where(['status' => true])->sum('server_fee');
    }

    public function withdraw3() {
        $this->dispatch('open-modal', 'withdrawModal3');
    }

    public function submit() {
        $this->getDeta();

        // if ($this->store < 1000) {
        //     $this->dispatch('error', 'At least 1000 balance required to withdraw');
        //     return;
        // }

        $this->validate([
            // 'amount' => 'required|numeric|min:500',
            'amount' => 'required|numeric',
            'method' => 'required',
            'phone'  => 'nullable',
            'bankAccount'  => 'nullable|string',
            'accountholder'  => 'nullable|string',
            'bankBranch'  => 'nullable|string',
            'swiftCode'  => 'nullable|string',
            'accountNumber'  => 'nullable|string',
            'remarks'  => 'nullable|string',
        ]);

        $wt = new Withdraw();
        $wt->user_id = Auth::user()->id;
        $wt->phone = $this->phone ?? null;
        $wt->pay_by = $this->method;
        $wt->pay_to = $this->accountNumber ? $this->accountNumber : ($this->phone ? $this->phone : null);
        $wt->amount = $this->amount;
        $wt->type = 'debit';
        $wt->status = 0;
        $wt->payable_amount = $this->amount;
        $wt->total_fee = $this->amount;
        $wt->fee_range = 100;
        $wt->store_req = 0;
        $wt->server_fee = $this->amount;
        $wt->maintenance_fee = 0;
        $wt->payment_method = $this->method;
        $wt->bank_account = $this->bankAccount ?? null;
        $wt->account_holder_name = $this->accountholder ?? null;
        $wt->bank_branch = $this->bankBranch ?? null;
        $wt->swift_code = $this->swiftCode ?? null;
        $wt->account_number = $this->accountNumber ?? null;
        $wt->remarks = $this->remarks ?? null;
        $wt->save();

        $this->dispatch('refresh');
        $this->dispatch('success', 'Withdraw resquest successfull');
        $this->dispatch('close-modal', 'withdrawModal3');
    }

    public function render() {
        // $store = Store::query()->donation()->first();
        $paymentMethod = [ 'Bkash', 'Nogod', 'Rocket', 'Bank' ];
        return view('livewire.system.store.donation-store', get_defined_vars());
    }
}
