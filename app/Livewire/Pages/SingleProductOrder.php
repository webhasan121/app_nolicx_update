<?php

namespace App\Livewire\Pages;

use App\Events\ProductComissions;
use App\Http\Controllers\ProductComissionController;
use App\Jobs\UpdateProductSalesIndex;
use App\Models\cart;
use App\Models\CartOrder;
use App\Models\Order;
use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use App\Models\city;
use App\Models\country;
use App\Models\state;
use App\Models\ta;

#[layout('layouts.user.app')]
class SingleProductOrder extends Component
{
    #[URL]
    public $slug, $id;

    public $product, $size, $total, $price;

    #[validate('required')]
    public $country_id;
    public $states = [];
    public $cities = [];
    public $areas = [];

    public $location, $phone, $quantity = 1, $house_no, $road_no, $area_condition = 'Dhaka', $district, $upozila, $shipping = 0, $delevery, $area_name;

    public function updated($property)
    {
        if ($property) {
            // if quantity is zero or less, set it to 1
            if ($this->quantity <= 0) {
                $this->quantity = 1;
            }
            $this->total = $this->price * $this->quantity;
            if ($this->delevery == 'hand') {
                $this->shipping = 0;
            } else {
                $this->shipping = $this->area_condition == 'Dhaka' ? $this->product->shipping_in_dhaka : $this->product->shipping_out_dhaka;
            }
        }
    }


    public function mount()
    {
        // dd($this->slug);
        $this->product = Product::where('id', $this->id)->active()->reseller()->firstOrFail();
        $this->price = $this->product->offer_type ? $this->product->discount : $this->product->price;
        $this->total = $this->price;

        $country = country::where('name', 'Bangladesh')->firstOrFail();
        $this->country_id = $country->id;

        $this->states = state::where('country_id', $this->country_id)->orderBy('name')->get();

        // if (!$this->product) {
        //     return redirect('/');
        // }
    }

    public function updatedDistrict()
{
    $this->upozila = null;
    $this->area_name = null;

    $this->cities = [];
    $this->areas = [];

    if ($this->district) {
        // now using id instead of name
        $state = state::find($this->district);

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
        // now using id instead of name
        $city = city::find($this->upozila);

        if ($city) {
            $this->areas = ta::where('city_id', $city->id)
                ->orderBy('name')
                ->get();
        }
    }
}




    public function confirm()
    {
        // ProductComissions::dispatch(6);
        $this->validate();

        if (auth()->user()->id !== $this->product->user_id) {

            try {
                //code...
                $order = Order::create(
                    [
                        'user_id' => auth()->user()->id,
                        'user_type' => 'user',
                        'belongs_to' => $this->product?->user_id,
                        'belongs_to_type' => 'reseller',
                        'status' => 'Pending',
                        // 'product_id' => $this->product?->id,
                        // 'size' => $this->size ?? 'Free Size',
                        // 'name' => $this->name,
                        // 'price' => $this->price,
                        'quantity' => $this->quantity,
                        'total' => $this->total,
                        'delevery' => $this->delevery,
                        'number' => $this->phone,
                        'area_condition' => $this->area_condition,
                        'district' => $this->district,
                        'upozila' => $this->upozila,
                        'location' => $this->location,
                        'phone' => $this->phone,
                        'road_no' => $this->road_no,
                        'house_no' => $this->house_no,
                        'shipping' => $this->shipping,
                        'target_area' => $this->upozila,
                        // 'target_area' => $this->area_name,
                        // 'buying_price' => $this->product?->buying_price,
                    ]
                );

                CartOrder::create(
                    [
                        'user_id' => auth()->user()->id,
                        'user_type' => 'user',
                        'belongs_to' => $this->product?->user_id,
                        'belongs_to_type' => 'reseller',
                        'order_id' => $order->id,
                        'product_id' => $this->product->id,
                        'size' => $this->size,
                        'price' => $this->price,
                        'total' => $this->total,
                        'quantity' => $this->quantity,
                        'buying_price' => $this->product?->buying_price ?? '0',
                    ]
                );
                $this->redirectRoute("user.orders.view");

                // dispatch comissions
                // ProductComissions::dispatch($order->id);

                ProductComissionController::dispatchProductComissionsListeners($order->id);
            } catch (\Throwable $th) {
                dd($th->getMessage());
                $this->dispatch('error', $th->getMessage());
            }
        } else {
            $this->dispatch('warning', "You can't purchase your own product");
        }
    }


    // public function render()
    // {
    //     $country = country::where('name', 'Bangladesh')->first();
    //     $city = [];
    //     $area = [];
    //     if ($this->district) {
    //         $city = city::where('state_id', state::where('name', $this->district)->first()?->id)->get();
    //     }
    //     if ($this->upozila) {
    //         $area = ta::where('city_id', city::where('name', $this->upozila)->first()?->id)->get();
    //     }

    //     return view('livewire.pages.single-product-order', [
    //         // 'states' => state::where('country_id', country::where('name', 'Bangladesh')->first()?->id)->get(),
    //         // 'states' => state::where('country_id', country::where('name', 'Bangladesh')->first()?->id)->get(),
    //         'states' => state::where('country_id', $country->id)->get(),
    //         'cities' => $city,
    //         'area' => $area,
    //         'country' => $country->id
    //     ]);
    // }

    public function render()
    {
        return view('livewire.pages.single-product-order', [
            'states' => $this->states,
            'cities' => $this->cities,
            'area'   => $this->areas,
            'country'=> $this->country_id,
        ]);
    }

}
