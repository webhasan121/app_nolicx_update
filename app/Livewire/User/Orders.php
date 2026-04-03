<?php

namespace App\Livewire\User;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

#[layout('layouts.user.dash.userDash')]
class Orders extends Component
{
    use WithPagination;
    #[URL]
    public $nav = 'Pending';
    public $orders;

    #[On('refresh')]
    public function mount()
    {
        $this->getData();
    }

    public function remove($id)
    {
        Order::where(['user_id' => auth()->user()->id, 'user_type' => 'user', 'id' => $id])->delete();
        $this->dispatch('refresh');
    }

    public function cancelOrder($id)
    {
        Order::where(['user_id' => auth()->user()->id, 'user_type' => 'user', 'id' => $id])->update(
            [
                'status' => 'Cancelled',
            ]
        );
        $this->dispatch('refresh');
    }


    public function getData()
    {
        if ($this->nav) {
            $this->orders = Order::where(['user_id' => auth()->user()->id, 'user_type' => 'user'])->latest('id')->get();
        }
    }

    public function render()
    {
        return view('livewire.user.orders');
    }
}
