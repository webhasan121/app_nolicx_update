<?php

namespace App\Livewire\Vendor\Orders;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Order;

class Vprint extends Component
{
    #[URL]
    public $order;

    public $orders;

    public function mount()
    {
        $this->orders = Order::find($this->order);
    }

    public function render()
    {
        return view('livewire.vendor.orders.vprint');
    }
}
