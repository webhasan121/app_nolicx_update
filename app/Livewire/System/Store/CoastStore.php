<?php

namespace App\Livewire\System\Store;

use Livewire\Component;
use App\Models\Store;
use App\Models\Withdraw;

class CoastStore extends Component
{
    public $store;
    public function getDeta()
    {

        $this->store = Withdraw::where(['status' => true])->sum('maintenance_fee');
    }

    public function render()
    {
        // $store = Store::query()->cost()->first();
        return view('livewire.system.store.coast-store');
    }
}
