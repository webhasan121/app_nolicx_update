<?php

namespace App\Livewire\System\Store;

use App\Models\DistributeComissions;
use App\Models\Store;
use App\Models\Balance;
use App\Models\TakeComissions;
use App\Models\Withdraw;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CoinStore extends Component {
    public $ammount, $store, $take, $give, $method, $amount, $phone, $bankAccount, $accountholder, $bankBranch, $swiftCode, $accountNumber, $remarks;

    public function addAmmountToStore() {
        try {
            //code...
            if ($this->ammount > 1) {
                DB::transaction(function () {
                    $user = Store::query()->store()->first();
                    $user->coin += $this->ammount;
                    $user->save();
                });
                $this->dispatch('refresh');
                $this->dispatch('open-modal', 'add-store-coin');
                // reset(['ammount']);
            }
        } catch (\Throwable $th) {
            //throw $th;
            abort(500, $th->getMessage());
        }
    }

    public function getDeta() {
        // $this->store = TakeComissions::where(['confirmed' => true])->sum('store');
        $this->store = Balance::sum('current');
        $this->take = TakeComissions::where(['confirmed' => true])->sum('take_comission');
        $this->give = TakeComissions::where(['confirmed' => true])->sum('distribute_comission');
    }

    public function withdraw1() {
        $this->dispatch('open-modal', 'withdrawModal1');
    }

    public function submit() {
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

        DB::transaction(function () {

            // 🔴 Lock the single balance row
            $balance = Balance::lockForUpdate()->first();

            if (!$balance) {
                throw new \Exception('System balance not found');
            }

            // ✅ Prevent overdraft
            if ($balance->current < $this->amount) {
                throw new \Exception('Insufficient system balance');
            }

            // ✅ Create withdraw request
            $wt = new Withdraw();
            $wt->user_id = Auth::user()->id;
            $wt->phone = $this->phone ?? null;
            $wt->pay_by = $this->method;
            $wt->pay_to = $this->accountNumber ?? $this->phone ?? null;
            $wt->amount = $this->amount;
            $wt->type = 'debit';
            $wt->status = 0;
            $wt->payable_amount = $this->amount;
            $wt->total_fee = $this->amount;
            $wt->fee_range = 100;
            $wt->store_req = $this->amount;
            $wt->server_fee = 0;
            $wt->maintenance_fee = 0;
            $wt->payment_method = $this->method;
            $wt->bank_account = $this->bankAccount ?? null;
            $wt->account_holder_name = $this->accountholder ?? null;
            $wt->bank_branch = $this->bankBranch ?? null;
            $wt->swift_code = $this->swiftCode ?? null;
            $wt->account_number = $this->accountNumber ?? null;
            $wt->save();

            // ✅ Update system balance safely
            $balance->decrement('current', $this->amount);
            $balance->increment('withdraw', $this->amount);
        });

        $this->dispatch('refresh');
        $this->dispatch('success', 'Withdraw resquest successfull');
        $this->dispatch('close-modal', 'withdrawModal1');
    }

    public function render() {
        $paymentMethod = [ 'Bkash', 'Nogod', 'Rocket', 'Bank' ];
        return view('livewire.system.store.coin-store', get_defined_vars());
    }
}
