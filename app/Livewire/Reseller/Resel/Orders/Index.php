<?php

namespace App\Livewire\Reseller\Resel\Orders;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Reseller_has_order;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;

#[layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    #[URL]
    public $nav = 'Pending', $delivery = 'all', $create = 'all', $type = 'All', $start_date = '', $end_date = '', $area = 'all';
    public function render()
    {
        // $data = [];
        $query = Order::where(['user_id' => auth()->user()->id, 'user_type' => 'reseller']);

        if ($this->nav == 'Trashed') {
            $query->onlyTrashed();
        } elseif (
            $this->nav != 'All'
        ) {
            $query->where(['status' => $this->nav]);
        }

        if ($this->type != "All") {
            $query->where('name', $this->type);
        }

        if ($this->create == 'day') {
            $query->whereDate('created_at', carbon::parse($this->start_date)->endOfDay());
        } elseif (
            $this->create == 'between'
        ) {
            $query->whereBetween('created_at', [carbon::parse($this->start_date)->endOfDay(), Carbon::parse($this->end_date)->endOfDay()]);
        }

        $data = $query->orderBy('id', 'desc')->paginate(100);

        // dd($data);
        return view('livewire.reseller.resel.orders.index', compact('data'));
    }
}
