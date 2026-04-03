<?php

namespace App\Livewire\Rider\Consignment;

use App\Models\cod;
use App\Models\Order;
use Carbon\Carbon;
use App\Http\Controllers\ProductComissionController;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class Index extends Component
{

    #[URL]
    public $status = 'All', $created_at, $start_time, $end_time;
    public $viewConsignments = '';

    public function confirmOrder($order, $status)
    {
        $order = cod::findOrFail($order);
        // if the order status is not 'Delivery', cod status not change

        // if ($order->order->status != 'Delivery') {
        //     $this->dispatch('error', 'Order not shipped yet');
        //     return;
        // }

        if ($order && auth()->user()->abailCoin() >= $order->total_amount) {
            $order->status = $status;
            $order->save();

            if ($status == 'Delivered') {
                // cut due_amount from rider account, and add to seller account
                $rider = auth()->user();
                $seller = $order->order?->seller;

                if ($rider && $seller) {
                    $rider->coin -= $order->due_amount;
                    $seller->coin += $order->due_amount;

                    $rider->save();
                    $seller->save();
                }
            }
            $this->dispatch('success', "Shipment Updated");
        } else {
            $this->dispatch('warning', 'You do not have enough balance to process this request !');
        }
    }

    public function viewConsignment($id)
    {
        // redirect to rider.consignment.view route with $id
        // $this->viewConsignments = $id;
        // $this->dispatch('open-modal', 'view-consignment');

        $this->redirectRoute('rider.consignment.view', ['id' => $id], true, true);
    }

    public function render()
    {
        // dd(auth()->user()->abailCoin());
        $consignments = [];
        $query = cod::query()->with('order')->where('rider_id', auth()->user()->id);
        // get the consignments belongs to rider id
        if ($this->status != 'All') {
            $query->where(['status' => $this->status]);
        }


        if ($this->created_at != 'any') {
            if ($this->created_at == 'Today') {
                $query->whereDate('created_at', now());
            } elseif ($this->created_at == 'Yesterday') {
                $query->whereDate('created_at', now()->yesterday());
            } elseif ($this->created_at == 'Weak') {
                $query->whereBetween('created_at', [now()->subWeek(), today()]);
            } elseif ($this->created_at == 'Month') {
                $query->whereBetween('created_at', [now()->month(), today()]);
            } elseif ($this->created_at == 'between') {
                $query->whereBetween('created_at', [$this->start_time, Carbon::parse($this->end_time)->endOfDay()]);
            }
        }
        // $consignments = cod::where(['rider_id' => auth()->user()->id, 'status' => $this->status])->get();

        // $consignments = Order::whereHas('hasRider', function ($query) {
        //     $query->where('rider_id', auth()->user()->id)
        //         ->where('status', $this->status);
        // })->get();
        $consignments = $query->orderby('id', 'desc')->get();
        // dd($consignments);
        return view('livewire.rider.consignment.index', compact('consignments'));
    }
}
