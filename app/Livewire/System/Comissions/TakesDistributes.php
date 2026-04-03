<?php

namespace App\Livewire\System\Comissions;

use App\Models\DistributeComissions;
use App\Models\TakeComissions;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;


#[layout('layouts.app')]
class TakesDistributes extends Component
{
    #[URL]
    public $id;
    public $takes, $distributes = [];

    public function getData()
    {
        $takes = TakeComissions::findOrFail($this->id);
        $this->takes = $takes->load('product', 'user');
        // dd($this->takes);
        $this->distributes = DistributeComissions::where(['parent_id' => $this->id])->get();
    }


    public function render()
    {
        return view('livewire.system.comissions.takes-distributes');
    }
}
