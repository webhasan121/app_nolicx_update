<?php

namespace App\Livewire\User\Order;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Order;

#[layout('layouts.user.dash.userDash')]
class Details extends Component
{
    #[URL]
    public $id;

    public $orders;

    public function mount()
    {
        $this->orders = Order::findOrFail($this->id);
        // dd($this->orders);
    }

    public function markAsReceived() {
        if (!$this->orders->received_at) {
            $this->orders->update([
                'received_at' => Carbon::now(),
            ]);

            // Refresh local model instance
            $this->orders->refresh();

            session()->flash('success', 'Order successfully marked as received.');
        }
    }

    public function render()
    {
        return view('livewire.user.order.details');
    }
}
