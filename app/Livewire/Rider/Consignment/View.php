<?php

namespace App\Livewire\Rider\Consignment;

use App\Models\CartOrder;
use App\Models\cod;
use App\Models\Order;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class View extends Component
{

    #[Url]
    public $id;

    public function cancelShipment()
    {
        try {
            cod::destroy($this->id);
            $this->redirectIntended(route('rider.consignment'), true);
        } catch (\Throwable $th) {
            $this->dispatch('error', 'Have an error to Cancel the Shipment');
            //throw $th;
        }
    }

    public function render()
    {
        $dta = cod::findOrFail($this->id);
        return view('livewire.rider.consignment.view', [
            'cod' => $dta,
            'order' => $dta->order,
            'co' => CartOrder::where('order_id', '=', $dta->order_id)->get(),
            'seller' => User::findOrFail($dta->seller_id),
            'user' => User::findOrFail($dta->user_id)
        ]);
    }
}
