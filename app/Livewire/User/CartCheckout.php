<?php

namespace App\Livewire\User;

use App\Events\ProductComissions;
use App\Http\Controllers\ProductComissionController;
use App\Models\cart;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use App\Models\Order;
use App\Models\CartOrder;
use App\Models\city;
use App\Models\country;
use App\Models\state;
use App\Models\ta;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Validate;

#[layout('layouts.user.dash.userDash')]
class CartCheckout extends Component
{
    public $carts = [], $qty = [], $tp = 0, $q = 0, $isMultiple, $selectedCarts;

    #[validate('required')]

    public $country_id;
    public $state_id = null;
    public $city_id = null;
    public $area_id = null;

    public $states = [];
    public $cities = [];
    public $areas = [];

    public $phone, $house_no, $road_no, $location, $area_condition = 'Dhaka', $district, $upozila, $area_name, $shipping = 0, $delevery;

    
    // public function mount()
    // {
    //     $this->tp = 0;
    //     $this->q = 0;
    //     foreach ($this->carts as $key => $cart) {
    //         $p = $cart['qty'] * $cart['price'];
    //         $this->q += $cart['qty'];
    //         $this->tp += $p;
    //     }

    //     if (auth()->user()->myCarts->count() < 1) {
    //         $this->redirectIntended('/', true);
    //     }
    // }
    public function mount() {
        $this->tp = 0;
        $this->q = 0;

        foreach ($this->carts as $cart) {
            $p = $cart['qty'] * $cart['price'];
            $this->q += $cart['qty'];
            $this->tp += $p;
        }

        if (auth()->user()->myCarts->count() < 1) {
            $this->redirectIntended('/', true);
        }

        // 🔒 Static country: Bangladesh
        $country = country::where('name', 'Bangladesh')->firstOrFail();
        $this->country_id = $country->id;

        // Load districts (states)
        $this->states = state::where('country_id', $this->country_id)
            ->orderBy('name')
            ->get();
    }

    public function updatedDistrict()
    {
        $this->upozila = null;
        $this->area_name = null;

        $this->cities = [];
        $this->areas = [];

        if ($this->district) {
            $state = state::where('name', $this->district)->first();

            if ($state) {
                $this->cities = city::where('state_id', $state->id)
                    ->orderBy('name')
                    ->get();
            }
        }
    }

    public function updatedUpozila()
    {
        $this->area_name = null;
        $this->areas = [];

        if ($this->upozila) {
            $city = city::where('name', $this->upozila)->first();

            if ($city) {
                $this->areas = ta::where('city_id', $city->id)
                    ->orderBy('name')
                    ->get();
            }
        }
    }


    public function updated()
    {
        if ($this->delevery == 'hand') {
            $this->shipping = 0;
        }
    }

    public function changeSize($id)
    {
        // dd($id);
        auth()->user()->myCarts()->find($id)->size = $this->cart[$id]['size'];
    }


    public function increaseQuantity($cartId)
    {
        auth()->user()->myCarts()->find($cartId)->increment('qty');
        auth()->user()->myCarts()->find($cartId)->save();
        $this->dispatch('refresh');
    }
    public function decreaseQuantity($cartId)
    {
        if (auth()->user()->myCarts()->find($cartId)->qty == 1) {
            auth()->user()->myCarts()->find($cartId)->delete();
        } else {

            auth()->user()->myCarts()->find($cartId)->decrement('qty');
            auth()->user()->myCarts()->find($cartId)->save();
        }
        $this->dispatch('refresh');
    }


    public function confirm()
    {
        $this->validate();

        try {

            $ct = cart::where(['user_id' => auth()->user()->id])->get()->groupBy('belongs_to'); // get all cart group by belongs_to
            foreach ($ct as $reseller => $rp) {
                // iterated by single reseller
                $qty = 0;
                $total = 0;
                $order = 0;
                $order = new Order();
                $order->user_id = auth()->user()->id;
                $order->user_type = 'user';
                $order->belongs_to = $reseller;
                $order->belongs_to_type = 'reseller';
                $order->status = 'Pending';
                $order->size = 'Details';
                $order->name = 'Cart Order';
                $order->delevery = $this->delevery;
                $order->number = $this->phone;
                $order->area_condition = $this->area_condition;
                $order->district = $this->district;
                $order->upozila = $this->upozila;
                $order->location = $this->location;
                $order->target_area = $this->upozila;
                $order->road_no = $this->road_no;
                $order->house_no = $this->house_no;
                $order->shipping = $this->area_condition == 'Dhaka' ? 80 : 120;
                $order->save();


                foreach ($rp as $key => $item) {
                    $qty += $item->qty;
                    $total += $item->price * $item->qty;

                    CartOrder::create(
                        [
                            'user_id' => auth()->user()->id,
                            'user_type' => 'user',
                            'belongs_to' => $item->product?->user_id,
                            'belongs_to_type' => 'reseller',
                            'order_id' => $order->id,
                            'product_id' => $item->product->id,
                            'size' => $item->size,
                            'price' => $item->price,
                            'total' => $item->price * $item->qty,
                            'quantity' => $item->qty,
                            'buying_price' => $item->product?->buying_price ?? '0',
                        ]
                    );

                    auth()->user()->myCarts()->delete($item->id);
                }

                Order::find($order->id)->update(
                    [
                        'quantity' => $qty,
                        'total' => $total,
                    ]
                );

                // dispatch comissions
                ProductComissionController::dispatchProductComissionsListeners($order->id);
                $this->redirectRoute('user.orders.view');
                // $this->reset('name', 'phone', 'location', 'district');
                // $this->dispatch('success', "Product added to order list");
            }
        } catch (\Throwable $th) {
            $this->dispatch('error', $th->getMessage());
        }

        $this->dispatch('refresh');
    }



    // public function render()
    // {
    //     $city = [];
    //     $area = [];
    //     if ($this->district) {
    //         $city = city::where('state_id', state::where('name', $this->district)->first()?->id)->get();
    //     }
    //     if ($this->upozila) {
    //         $area = ta::where('city_id', city::where('name', $this->upozila)->first()?->id)->get();
    //     }

    //     return view(
    //         'livewire.user.cart-checkout',
    //         [
    //             'states' => state::where('country_id', country::where('name', 'Bangladesh')->first()?->id)->get(),
    //             'cities' => $city,
    //             'area' => $area,
    //         ]
    //     );
    // }

    public function render()
    {
        return view('livewire.user.cart-checkout', [
            'states' => $this->states,
            'cities' => $this->cities,
            'area'   => $this->areas,
        ]);
    }

}
