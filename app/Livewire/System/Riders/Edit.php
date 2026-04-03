<?php

namespace App\Livewire\System\Riders;

use App\Models\rider;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;

#[layout('layouts.app')]
class Edit extends Component
{
    #[URL]
    public $id, $nav = 'user';
    private $data;

    public $rider, $comission, $requestStatus;

    #[on('refresh')]
    public function mount()
    {
        $this->rider = rider::find($this->id);
        if ($this->rider) {
            $this->comission = $this->rider->comission;
            $this->requestStatus = $this->rider?->status;;
        }
    }

    public function updateStatus()
    {
        $this->rider->status = $this->requestStatus;
        $this->rider->comission = $this->comission;
        $this->rider->save();

        $this->dispatch('success', "Status Updated !");
        $this->dispatch('refresh');
    }


    public function render()
    {
        return view('livewire.system.riders.edit');
    }
}
