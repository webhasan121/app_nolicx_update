<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductComissionController;
use App\Models\Cart;
use App\Models\Order;
use App\Models\CartOrder;
use App\Models\country;
use App\Models\state;
use App\Models\city;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartCheckoutController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->myCarts()->count() < 1) {
            return redirect('/');
        }

        $carts = $user->myCarts()->with('product.owner')->get()->map(function ($cart) {

            $attrValues = [];

            if ($cart->product?->attr?->value) {
                $attrValues = explode(',', $cart->product->attr->value);
            }

            return [
                'id' => $cart->id,
                'product_id' => $cart->product_id,
                'name' => $cart->product?->name,
                'slug' => Str::slug($cart->product?->name),
                'image' => $cart->product?->thumbnail,
                'price' => $cart->price,
                'qty' => $cart->qty,
                'shop' => $cart->product?->owner?->resellerShop()->shop_name_en ?? "N/A",

                'attr_name' => $cart->product?->attr?->name,
                'attr_values' => $attrValues,
            ];
        });

        $country = country::where('name', 'Bangladesh')->first();

        $states = state::where('country_id', $country->id)
            ->orderBy('name')
            ->get();



        return Inertia::render('User/CartCheckout', [
            'carts' => $carts,
            'states' => $states,
        ]);
    }


    public function loadCities($state)
    {

        $cities = city::where('state_id', $state)
            ->orderBy('name')
            ->get();

        return response()->json($cities);
    }


    public function increase($id)
    {
        $cart = Cart::findOrFail($id);

        $cart->increment('qty');

        return back();
    }


    public function decrease($id)
    {
        $cart = Cart::findOrFail($id);

        if ($cart->qty == 1) {
            $cart->delete();
        } else {
            $cart->decrement('qty');
        }

        return back();
    }


    public function confirm(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'delevery' => 'required',
            'area_condition' => 'required',
            'district' => 'required',
            'upozila' => 'required',
            'location' => 'required',
        ]);



        $user = $request->user();

        try {

            DB::beginTransaction();

            $cartGroups = Cart::where('user_id', $user->id)
                ->get()
                ->groupBy('belongs_to');






            foreach ($cartGroups as $reseller => $items) {

                $qty = 0;
                $total = 0;

                $order = Order::create([
                    'user_id' => $user->id,
                    'user_type' => 'user',
                    'belongs_to' => $reseller,
                    'belongs_to_type' => 'reseller',
                    'status' => 'Pending',
                    'size' => 'Details',
                    'name' => 'Cart Order',
                    'delevery' => $request->delevery,
                    'number' => $request->phone,
                    'area_condition' => $request->area_condition,
                    'district' => $request->district,
                    'upozila' => $request->upozila,
                    'location' => $request->location,
                    'target_area' => $request->upozila,
                    'road_no' => $request->road_no,
                    'house_no' => $request->house_no,
                    'shipping' => $request->area_condition == 'Dhaka' ? 80 : 120,
                ]);

                foreach ($items as $item) {

                    $qty += $item->qty;
                    $total += $item->price * $item->qty;

                    CartOrder::create([
                        'user_id' => $user->id,
                        'user_type' => 'user',
                        'belongs_to' => $item->product?->user_id,
                        'belongs_to_type' => 'reseller',
                        'order_id' => $order->id,
                        'product_id' => $item->product->id,
                        'size' => $item->size,
                        'price' => $item->price,
                        'total' => $item->price * $item->qty,
                        'quantity' => $item->qty,
                        'buying_price' => $item->product?->buying_price ?? 0,
                    ]);

                    $item->delete();
                }

                $order->update([
                    'quantity' => $qty,
                    'total' => $total,
                ]);

                ProductComissionController::dispatchProductComissionsListeners($order->id);
            }

            DB::commit();

            return redirect()->route('user.orders.view');
        } catch (\Throwable $th) {

            DB::rollBack();

            return back()->with('error', $th->getMessage());
        }
    }
}
