<?php

namespace App\Livewire\System\Store;

use App\Models\TakeComissions;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[layout('layouts.app')]
class Index extends Component
{

    #[URL]
    public $nav = 'donation', $type = 'withdraw';
    // $query = request('get') ?? "store";
    // $type = request('type') ?? 'withdraw';


    public function render()
    {
        $strack = [];
        // switch ($this->nav) {
        //     case 'donation':
        //         $track = $track->donation();
        //         break;

        //     case 'cost':
        //         $track = $track->cost();
        //         break;
        // }
        // $strack = $track->whereDate('created_at', today())->orderBy('created_at', 'desc')->get();
        return view('livewire.system.store.index', compact('strack'));
    }
}
