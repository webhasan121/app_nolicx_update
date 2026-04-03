<?php

namespace App\Livewire\System\Orders;

use App\Http\Controllers\ProductComissionController;
use App\Models\Order;
use App\Models\ResellerResellProfits;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[layout('layouts.app')]
class Details extends Component
{
    #[URL]
    public $id, $nav = 'tab';
    public $order, $detais, $take, $resellerProfit;

    public function mount()
    {
        $this->order = Order::findOrFail($this->id);
        $this->resellerProfit = ResellerResellProfits::where(['order_id' => $this->id])->get();
    }

    public function confirmResellerProfit()
    {
        // 
        $pc = new ProductComissionController();
        $pc->transferResellerResellProfit($this->id);
        $this->dispatch('refresh');
        $this->dispatch('success', 'Profit Rounded !');
    }

    public function retundResellerProfit()
    {
        // 
        $pc = new ProductComissionController();
        $pc->refundResellerResellProfit($this->id);
        $this->dispatch('refresh');
        $this->dispatch('success', 'Profit Rounded !');
    }


    public function render()
    {
        return view('livewire.system.orders.details');
    }
}
