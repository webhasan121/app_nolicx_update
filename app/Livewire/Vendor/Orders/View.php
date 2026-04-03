<?php

namespace App\Livewire\Vendor\Orders;

use App\Http\Controllers\ProductComissionController;
use App\Models\{CartOrder, cod, rider};
use App\Models\Order;
use App\Models\Product;
use App\Models\syncOrder;
use App\Models\TakeComissions;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;


#[layout('layouts.app')]
class View extends Component
{
    #[URL]
    public $order;

    public $orderStatus = 'Pending', $orders, $mainProduct, $isResell = false, $resellerProductId = '', $cartOrderId = '', $cartOrder;
    public $district, $upozila, $target_area, $location, $area_condition, $delevery, $quantity, $rprice, $attr, $name, $phone, $house_no, $road_no, $shipping;
    public $rider = [], $rider_id;

    public function mount()
    {
        $this->orders = Order::find($this->order);
        $this->shipping = $this->orders->shipping;
        $this->orderStatus = $this->orders->status;

        // get the rider those who are in the same location
        $this->rider = rider::where(function ($query) {
            $query->where('targeted_area', 'like', '%' . $this->orders->district . '%')
                ->orWhere('targeted_area', 'like', '%' . $this->orders->upozila . '%')
                ->orWhere('targeted_area', 'like', '%' . $this->orders->location . '%');
        })
            ->get();
    }

    // public function updated($property)
    // {
    //     // if orderStatus update
    //     $this->updateStatus($this->orders); // update status
    // }

    // public function updateOrderStatusTo($status)
    // {
    //     $this->updateStatus($status);
    // }

    public function acceptOrder()
    {
        $this->orders->update(['shipping' => $this->shipping]);
        $this->updateOrderStatusTo('Accept');
        $this->dispatch('close-modal', 'order-accept-modal');
    }

    public function updateOrderStatusTo($status)
    {

        if ($this->orders->status == 'Confirm') {
            $this->dispatch('error', 'Order Confirmed !');
            return;
        }

        // dd($status);
        $sysOr = syncOrder::where(['reseller_order_id' => $this->orders->id])->first();
        // dd($sysOr->userOrder);
        if (in_array($status, ['Cancel', 'Hold', 'Pending'])) {
            $this->orders->update(['status' => $status]);
            if ($sysOr) {
                # code...
                $sysOr->status = $status;
                $sysOr->save();
            }
        }

        $ensureBalance = $this->orders->comissionsInfo->sum('take_comission') + $this->orders->resellerProfit?->sum('profit');
        if ($this->orders->status == 'Pending' && auth()->user()->abailCoin() < $ensureBalance) {
            $this->dispatch('info', "You Don't have required balance to accept the order. You need ensure minimum" . $ensureBalance . " balance to procces the order ");
            return;
        }

        if (auth()->user()->abailCoin() > $this->orders->comissionsInfo->sum('take_comission')) {

            $this->orders->update(['status' => $status]);
            // $this->orders->save();

            if ($sysOr) {
                # code...
                $sysOr->status = $status;
                $sysOr->save();
            }

            if ($this->orders->status == 'Confirm') {
                $ct = new ProductComissionController(); // instance
                $ct->confirmTakeComissions($this->orders->id); // call to confirm comissions 
                // $ct->confirmTakeComissions($sysOr->user_order_id); // call to confirm comissions for user

                // $comisionForUser = TakeComissions::where([
                //     'order_id' => $this->cartOrder->order_id,
                //     'product_id' => $this->cartOrder->product_id,
                // ])->get();

                // if ($comisionForUser->count() > 0) {
                //     # code...
                //     $comisionForUser->each(function ($item) {
                //         $item->confirmed = true;
                //         // You could add more custom logic here
                //         $item->save();
                //     });
                // }
            }

            $this->dispatch('refresh');
            return;
        } else {
            $this->dispatch('warning', "You Don't have required balance to accept the order. You need ensure minimum" . $this->orders->comissionsInfo->sum('take_comission ') . " balance to procces the order ");
            return;
        }

        // if ($this->orders->status == 'Accept') {
        //     $pcc = new ProductComissionController();
        //     $pcc->confirmTakeComissions($this->order->id);
        // }
    }

    public function syncOrder($ci)
    {
        /**
         * check order satatus isn't Pending, ro Hold, or Rejected
         */
        if (in_array($this->orders->status, ['Pending', 'Hold', 'Cancelled', 'Cancel', 'Reject'])) {
            $this->dispatch('error', 'You can sync only accepted orders');
            return;
        };

        $this->cartOrder = CartOrder::findOrFail($ci);

        if ($this->cartOrder->product?->isResel()) {
            $this->isResell = true;
            $this->resellerProductId = $this->cartOrder->product_id;
            $this->mainProduct = $this->cartOrder->product?->isResel;
            $this->cartOrderId = $this->cartOrder->id;
        }

        // $comisionForUser = TakeComissions::where([
        //     'order_id' => $this->cartOrder->order_id,
        //     'product_id' => $this->cartOrder->product_id,
        // ])->get();

        // dd($comisionForUser);

        // dd($this->mainProduct);

        $this->district = $this->orders->district;
        $this->upozila = $this->orders->upozila;
        $this->location = $this->orders->location;
        $this->target_area = $this->orders->target_area;
        $this->area_condition = $this->orders->area_condition;
        $this->delevery = $this->orders->delevery;
        $this->rprice = $this->cartOrder->price;
        $this->quantity = $this->cartOrder->quantity;
        $this->attr = $this->cartOrder->size;
        $this->name = $this->orders->user?->name;
        $this->phone = $this->orders->number ?? $this->orders->user?->phone;
        $this->house_no = $this->orders->house_no;
        $this->road_no = $this->orders->road_no;
        $this->dispatch('open-modal', 'order-sync-modal');
    }

    public function confirmSyncOrder()
    {
        /**
         * check order satatus isn't Pending, ro Hold, or Rejected
         */
        if (in_array($this->orders->status, ['Pending', 'Hold', 'Cancelled', 'Cancel', 'Reject'])) {
            $this->dispatch('error', 'You can sync only accepted orders');
            return;
        };
        // dd($this->mainProduct?->id,);
        // $isExists = Order::where(
        //     [
        //         'order_id' => $this->orders->id,
        //         'user_id' => auth()->user()->id,
        //         'user_type' => 'reseller',
        //         'belongs_to' => $this->mainProduct->user_id,
        //         'belongs_to_type' => 'vendor',
        //     ]
        // )->exists();

        $order = order::create(
            [
                'user_id' => auth()->user()->id,
                'user_type' => 'reseller',
                'belongs_to' => $this->cartOrder?->product?->isResel?->belongs_to, // vendor id
                'belongs_to_type' => 'vendor',

                'quantity' => $this->quantity,
                'total' => $this->quantity * $this->rprice,
                'status' => 'Pending',

                'name' => 'Resel',
                'district' => $this->district,
                'upozila' => $this->upozila,
                'target_area' => $this->target_area,
                'location' => $this->location,
                'house_no' => $this->house_no,
                'road_no' => $this->road_no,
                'area_condition' => $this->area_condition,
                'delevery' => $this->delevery,
                'number' => $this->phone,
                'shipping' => $this->area_condition == 'Dhaka' ? 80 : 120,
            ]
        );

        $cor = CartOrder::create(
            [
                'user_id' => Auth::id(),
                'user_type' => 'reseller',
                'belongs_to' => intval($this->cartOrder?->product?->isResel?->belongs_to), // vendor id
                'belongs_to_type' => 'vendor',
                'order_id' => $order->id,
                'product_id' => $this->mainProduct?->parent_id,
                'quantity' => $this->quantity,
                'price' => $this->rprice,
                'size' => $this->attr,
                'total' => $this->quantity * $this->rprice,
                'buying_price' => Product::find($this->mainProduct?->product_id)->buying_price,
                'status' => 'Pending',
            ]
        );


        $this->dispatch('refresh');
        $this->dispatch('success', 'Order Done');

        if ($order->id && $cor->id) {
            # code...
            ProductComissionController::dispatchProductComissionsListeners($order->id);
        }

        $sync = new syncOrder();
        $sync->user_id = $this->orders->user_id;
        $sync->user_order_id = $this->orders->id;
        $sync->user_cart_order_id = $this->cartOrderId;
        $sync->reseller_product_id = $this->resellerProductId;
        $sync->reseller_order_id = $order->id;
        $sync->vendor_product_id = $this->mainProduct->id;
        $sync->reseller_id = auth()->user()->id;
        $sync->vendor_id = $this->cartOrder?->product?->isResel?->belongs_to;
        $sync->status = 'Pending';

        $sync->save();

        $this->dispatch('close-modal', 'order-sync-modal');
        $this->dispatch('refresh');
    }

    public function assignRiderToOrder()
    {
        // $this->orders->update(['rider_id' => $this->shipping]);
        $rdr = rider::find($this->rider_id);
        if (!$rdr) {
            $this->dispatch('error', 'Rider not found');
            return;
        }

        $cod = new cod();
        $cod->order_id = $this->orders->id;
        $cod->rider_id = $rdr->user_id;
        $cod->seller_id = $this->orders->belongs_to;
        $cod->user_id = $this->orders->user_id;
        $cod->amount = $this->orders->total;
        $cod->comission = $rdr->comission;
        $cod->rider_amount = $this->orders->shipping;
        $cod->system_comission = ($this->orders->shipping * $rdr->comission) / 100;
        $cod->total_amount = ($this->orders->total + (($this->orders->shipping * $rdr->comission) / 100));
        $cod->due_amount = $this->orders->total;
        $cod->status = 'Pending';
        $cod->save();

        $this->updateOrderStatusTo('Picked');
        $this->dispatch('close-modal', 'rider-assign-modal');
        $this->dispatch('success', 'Rider assigned successfully');
    }

    public function removeRider($id)
    {
        $cod = cod::findOrFail($id);
        if ($cod) {
            $cod->delete();
            $this->dispatch('success', 'Rider removed successfully. You can assign a new rider to this order.');
        } else {
            $this->dispatch('error', 'Order not found');
        }
    }



    public function render()
    {
        return view('livewire.vendor.orders.view');
    }
}
