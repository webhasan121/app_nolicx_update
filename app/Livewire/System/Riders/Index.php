<?php

namespace App\Livewire\System\Riders;

use App\Models\rider;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Livewire\Attributes\Url;



#[layout('layouts.app')]
class Index extends Component
{
    use WithPagination, WithoutUrlPagination;

    #[URL]
    public $condition = 'Active', $search;

    public $tri, $ari, $pri, $dri, $sri;


    public function mount()
    {
        $this->tri = rider::count();
        $this->ari = rider::query()->active()->count();
        $this->pri = rider::query()->pending()->count();
        $this->dri = rider::query()->disabled()->count();
        $this->sri = rider::query()->suspended()->count();
    }


    public function render()
    {
        // $query = rider::query();

        $riders = rider::orderBy('created_at', 'desc')->where(['status' => $this->condition])->paginate(200);

        // if (!empty($this->search)) {
        //     if ($this->condition == 'all') {
        //         $vendors = rider::where('name', 'like', '%' . $this->search . '%')->paginate(20);
        //     } else {
        //         $vendors = rider::where('name', 'like', '%' . $this->search . '%')->where('status', $this->condition)->paginate(20);
        //     }
        // }

        return view('livewire.system.riders.index', compact('riders'));
    }
}
