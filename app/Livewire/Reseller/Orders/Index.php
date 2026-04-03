<?php

namespace App\Livewire\Reseller\Orders;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Livewire\WithPagination;


#[layout('layouts.app')]
class Index extends Component
{

    use WithPagination;
    #[URL]
    public $nav = 'Pending';

    public $otme, $account;

    public function mount()
    {
        // $this->getData();
        $this->account = auth()->user()->account_type();
    }

    public function render()
    {
        $data = [];
        if ($this->nav == 'Trashed') {
            $data = auth()->user()->orderToMe()->where(['belongs_to_type' => $this->account])->latest('id')->onlyTrashed();
        } else {
            $data = auth()->user()->orderToMe()->where(['status' => $this->nav, 'belongs_to_type' => $this->account])->latest('id')->paginate(20);
        }
        return view('livewire.reseller.orders.index', compact('data'));
    }
}
