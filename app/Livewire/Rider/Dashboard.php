<?php

namespace App\Livewire\Rider;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Order;
use Livewire\Attributes\Url;
use App\Models\cod;
use Illuminate\Support\Facades\Auth;

#[layout('layouts.app')]
class Dashboard extends Component
{
    #[URL]
    public $search;
    public $orders = [], $riderInfo = [];

    public function mount()
    {
        if (auth()->user()?->requestsToBeRider() && auth()->user()?->isRider()) {
            $this->riderInfo = auth()->user()?->isRider();
            // dd($this->riderInfo?->targeted_area);
        }

        // get the order those are match with active user rider info
        if ($this->riderInfo) {
            $orq = Order::query();

            $this->orders = $orq->where(function ($itm) {
                $itm->where('target_area', 'like', '%' . $this->riderInfo?->targeted_area . '%')->whereIn('status', ['Accept']);
            })->whereDoesntHave('hasRider', function ($query) {
                $query->where('rider_id', auth()->id());
            })->get();
 
            // $this->orders = Order::query()->where(['delevery' => 'cash'])->where(function ($itm) {
            //     $itm->where('target_area', 'like', '%' . $this->riderInfo?->targeted_area . '%')
            //         ->whereIn('status', ['Accept', 'Picked', 'Delivery', 'Delivered']);
            // })->whereDoesntHave('hasRider', function ($query) {
            //     $query->where('rider_id', auth()->id());
            // })->get();
            // dd($this->orders);
        }
        // dd($this->orders[0]->hasRider()->first()->rider?->name);
    }

    public function confirmOrder($orderId)
    {
        $order = Order::find($orderId);
        if (!auth()?->user()?->isRider()) {
            $this->dispatch('error', 'Your are not a Rider !');
            return;
        };

        if (cod::where('order_id', '=', $orderId)->accept()->exists() || cod::where('order_id', '=', $orderId)->complete()->exists() || cod::where('order_id', '=', $orderId)->pending()->exists()) {
            $this->dispatch('error', 'Already Picked !');
            return;
        }

        if ($order && $order->delevery == 'cash' && $order->status == 'Accept') {
            $rider_cm_range = auth()->user()?->isRider()?->comission;
            $system_cm = ($order->shipping * $rider_cm_range) / 100;

            // assign necessary info to cod model
            $totalNotResel = $order->cartOrders->each(function ($item) {
                return !$item->product?->isResel;
            })->sum('total');
            cod::create(
                [
                    'order_id' => $order->id,
                    'seller_id' => $order->belongs_to,
                    'seller_type' => $order->belongs_to_type,
                    'user_id' => $order->user_id,
                    'rider_id' => Auth::id(),

                    'amount' => $totalNotResel,
                    'due_amount' => $totalNotResel,
                    'total_amount' => $totalNotResel + $system_cm,

                    'rider_amount' => $order->shipping,
                    'comission' => $rider_cm_range,

                    'system_comission' => $system_cm,
                ]
            );

            $order->update([
                'status' => 'Picked'
            ]);

            $this->dispatch('success', 'Order confirmed successfully.');
        } else {
            $this->dispatch('error', 'Order not found or already confirmed.');
        }
    }

    public function render()
    {
        return view('livewire.rider.dashboard');
    }
}
