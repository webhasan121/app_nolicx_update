<?php

namespace App\Livewire\Reseller\Orders;

use Livewire\Component;
use App\Jobs\UpdateProductSalesIndex;
use App\Http\Controllers\ProductComissionController;
use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;


#[layout('layouts.app')]
class View extends Component
{
    #[URL]
    public $order;

    public $orders;

    public function mount()
    {
        $this->orders = Order::find($this->order);
    }

    public function computed() {}


    public function updateStatus($status)
    {
        // dd($this->orders);
        if ($this->orders->status == 'Pending') {

            if (auth()->user()->abailCoin() > $this->orders->comissionsInfo->sum('take_comission')) {

                $this->orders->status = $status;
                $this->orders->save();

                $ct = new ProductComissionController(); // instance to coomissions
                $ct->confirmTakeComissions($this->orders->id); // call to confirm comissions
                UpdateProductSalesIndex::dispatch(); // index product sales

                $this->dispatch('refresh');
            } else {
                $this->dispatch('warning', "You Don't have requried balance to accept the order. You need ensure minimum" . $this->orders->comissionsInfo->sum('take_comission') . " balance to procces the order ");
            }
        }

        // if ($this->orders->status == 'Accept') {
        //     $pcc = new ProductComissionController();
        //     $pcc->confirmTakeComissions($this->order->id);
        // }
    }

    public function render()
    {
        return view('livewire.reseller.orders.view');
    }
}
