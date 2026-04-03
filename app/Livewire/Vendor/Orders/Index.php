<?php

namespace App\Livewire\Vendor\Orders;

use Illuminate\Support\Carbon;
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
    public $nav = 'Pending', $delivery = 'all', $create = 'day', $start_date = '', $end_date = '', $area = 'all';

    public $otme, $account;

    public function mount()
    {
        $this->start_date = now();
        $this->getData();
        $this->account = auth()->user()->account_type();
    }


    public function getData()
    {
        // $this->otme = auth()->user()->orderToMe()->where(['belongs_to_type' => $this->account]);
    }


    public function render()
    {
        $query = auth()->user()->orderToMe()->where(['belongs_to_type' => $this->account]);
        if ($this->nav == 'Trashed') {
            $query->onlyTrashed();
        } elseif ($this->nav != 'All') {
            $query->where(['status' => $this->nav]);
        }

        if ($this->delivery != 'all') {
            $query->where(['delevery' => $this->delivery]);
        }

        if ($this->create == 'day') {
            $query->whereDate('created_at', carbon::parse($this->start_date)->endOfDay());
        } elseif ($this->create == 'between') {
            $query->whereBetween('created_at', [carbon::parse($this->start_date)->endOfDay(), Carbon::parse($this->end_date)->endOfDay()]);
        }

        if ($this->area != 'all') {
            $query->where('area_condition', $this->area);
        }

        // $data = $query->orderBy('id', 'desc')->paginate(20);
        $data = $query->latest('id')->paginate(20);
        return view('livewire.vendor.orders.index', compact('data'));
    }
}
