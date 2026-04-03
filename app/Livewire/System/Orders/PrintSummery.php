<?php

namespace App\Livewire\System\Orders;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Order as orderModel;
use Illuminate\Support\Carbon;

#[layout('layouts.print')]
class PrintSummery extends Component
{
    #[URL]
    public $search = '', $date, $sd = '', $ed = '', $qf = 'id', $type, $status;

    public function render()
    {
        $orders = [];

        $query = orderMOdel::query();
        if ($this->search) {
            $query->where([$this->qf => $this->search]);
        }

        if ($this->date) {
            switch ($this->date) {
                case 'today':
                    $this->sd = '';
                    $this->ed = '';;
                    $query->whereDate('created_at', today());
                    break;

                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;


                case 'between':
                    if ($this->sd && $this->ed) {
                        $query->whereBetween('created_at', [$this->sd, carbon::parse($this->ed)->endofDay()]);
                    }
                    break;
            }

            // $query->whereDate('created_at', $this->date);
        }

        if ($this->type) {
            $query->where(['user_type' => $this->type]);
        }

        if ($this->status) {
            $query->where(['status' => $this->status]);
        }
        $orders = $query->get();
        // $or = Order::all();

        return view('livewire.system.orders.print-summery', compact('orders'));
    }
}
