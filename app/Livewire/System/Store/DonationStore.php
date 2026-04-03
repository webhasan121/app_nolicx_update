<?php

namespace App\Livewire\System\Store;

use Livewire\Component;
use App\Models\Store;
use App\Models\Withdraw;

class DonationStore extends Component
{
    public $store;
    public function getDeta()
    {

        $this->store = Withdraw::where(['status' => true])->sum('server_fee');
    }

    public function render()
    {
        // $store = Store::query()->donation()->first();

        return view('livewire.system.store.donation-store');
    }
}
