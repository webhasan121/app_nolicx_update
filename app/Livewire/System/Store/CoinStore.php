<?php

namespace App\Livewire\System\Store;

use App\Models\DistributeComissions;
use App\Models\Store;
use App\Models\TakeComissions;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CoinStore extends Component
{
    public $ammount, $store, $take, $give;

    public function addAmmountToStore()
    {
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

    public function getDeta()
    {
        $this->store = TakeComissions::where(['confirmed' => true])->sum('store');
        $this->take = TakeComissions::where(['confirmed' => true])->sum('take_comission');
        $this->give = TakeComissions::where(['confirmed' => true])->sum('distribute_comission');
    }



    public function render()
    {
        return view('livewire.system.store.coin-store');
    }
}
