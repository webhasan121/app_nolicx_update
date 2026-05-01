<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductComissionController;
use App\Models\Cart;
use App\Models\CartOrder;
use App\Models\city;
use App\Models\country;
use App\Models\Order;
use App\Models\Product;
use App\Models\state;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserCartController extends Controller
{
    public function index(Request $request)
    {
        $carts = $request->user()
            ->myCarts()
            ->with('product.owner')
            ->get()
            ->map(fn (Cart $cart) => $this->cartListPayload($cart))
            ->values();

        return ApiResponse::success([
            'carts' => $carts,
            'cart_count' => $carts->count(),
            'total' => (float) $carts->sum('price'),
        ], 'Carts fetched');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $user = $request->user();
        $product = Product::findOrFail($validated['product_id']);

        if ($user->myCarts()->where('product_id', $product->id)->exists()) {
            return ApiResponse::success([
                'cart_count' => $user->myCarts()->count(),
            ], 'Product already in cart');
        }

        $cart = Cart::create([
            'product_id' => $product->id,
            'name' => $product->title,
            'image' => $product->thumbnail,
            'price' => $product->offer_type ? $product->discount : $product->price,
            'user_id' => $user->id,
            'user_type' => 'user',
            'belongs_to' => $product->user_id,
            'belongs_to_type' => 'reseller',
            'qty' => 1,
        ]);

        return ApiResponse::success([
            'cart' => $this->cartListPayload($cart->load('product.owner')),
            'cart_count' => $user->myCarts()->count(),
        ], 'Product added to cart', 201);
    }

    public function destroy(Request $request, int $id)
    {
        $deleted = $request->user()->myCarts()->where('id', $id)->delete();

        if (!$deleted) {
            return ApiResponse::notFound('Cart item not found');
        }

        return ApiResponse::success([
            'cart_count' => $request->user()->myCarts()->count(),
        ], 'Cart item deleted');
    }

    public function checkout(Request $request)
    {
        $user = $request->user();
        $carts = $user->myCarts()
            ->with('product.owner')
            ->get()
            ->map(fn (Cart $cart) => $this->checkoutCartPayload($cart))
            ->values();

        $country = country::where('name', 'Bangladesh')->first();
        $states = $country
            ? state::where('country_id', $country->id)->orderBy('name')->get(['id', 'name'])
            : collect();

        return ApiResponse::success([
            'carts' => $carts,
            'states' => $states,
            'cart_count' => $carts->count(),
            'subtotal' => (float) $carts->sum(fn ($cart) => $cart['price'] * $cart['qty']),
            'shipping' => [
                'inside_dhaka' => 80,
                'outside_dhaka' => 120,
                'hand_to_hand' => 0,
            ],
        ], 'Checkout fetched');
    }

    public function increase(Request $request, int $id)
    {
        $cart = $request->user()->myCarts()->where('id', $id)->firstOrFail();
        $cart->increment('qty');

        return ApiResponse::success($this->checkoutCartPayload($cart->fresh('product.owner')), 'Cart quantity increased');
    }

    public function decrease(Request $request, int $id)
    {
        $cart = $request->user()->myCarts()->where('id', $id)->firstOrFail();

        if ((int) $cart->qty === 1) {
            $cart->delete();

            return ApiResponse::success([
                'deleted' => true,
                'cart_count' => $request->user()->myCarts()->count(),
            ], 'Cart item deleted');
        }

        $cart->decrement('qty');

        return ApiResponse::success($this->checkoutCartPayload($cart->fresh('product.owner')), 'Cart quantity decreased');
    }

    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:25'],
            'delevery' => ['required', 'string'],
            'area_condition' => ['required', 'string'],
            'district' => ['required'],
            'upozila' => ['required'],
            'location' => ['required', 'string'],
            'road_no' => ['nullable', 'string', 'max:255'],
            'house_no' => ['nullable', 'string', 'max:255'],
            'carts' => ['nullable', 'array'],
            'carts.*.id' => ['required_with:carts', 'integer'],
            'carts.*.size' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();

        if (!$user->myCarts()->exists()) {
            return ApiResponse::error('Cart is empty', null, 422);
        }

        $orders = DB::transaction(function () use ($validated, $user) {
            $this->syncRequestedSizes($validated['carts'] ?? [], $user->id);

            $createdOrders = collect();
            $cartGroups = Cart::query()
                ->where('user_id', $user->id)
                ->with('product')
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
                    'delevery' => $validated['delevery'],
                    'number' => $validated['phone'],
                    'area_condition' => $validated['area_condition'],
                    'district' => $validated['district'],
                    'upozila' => $validated['upozila'],
                    'location' => $validated['location'],
                    'target_area' => $validated['upozila'],
                    'road_no' => $validated['road_no'] ?? null,
                    'house_no' => $validated['house_no'] ?? null,
                    'shipping' => $validated['area_condition'] === 'Dhaka' ? 80 : 120,
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
                $createdOrders->push($order->fresh('cartOrders.product'));
            }

            return $createdOrders;
        });

        return ApiResponse::success([
            'orders' => $orders->map(fn (Order $order) => $this->orderPayload($order))->values(),
            'order_count' => $orders->count(),
        ], 'Order confirmed', 201);
    }

    public function cities(int $state)
    {
        return ApiResponse::success(
            city::where('state_id', $state)->orderBy('name')->get(['id', 'name']),
            'Cities fetched'
        );
    }

    private function syncRequestedSizes(array $requestedCarts, int $userId): void
    {
        foreach ($requestedCarts as $item) {
            if (!array_key_exists('size', $item)) {
                continue;
            }

            Cart::query()
                ->where('user_id', $userId)
                ->where('id', $item['id'])
                ->update(['size' => $item['size']]);
        }
    }

    private function cartListPayload(Cart $cart): array
    {
        return [
            'id' => $cart->id,
            'price' => (float) ($cart->price ?? 0),
            'qty' => (int) ($cart->qty ?? 0),
            'size' => $cart->size,
            'created_at_human' => $cart->created_at?->diffForHumans(),
            'product' => [
                'id' => $cart->product?->id,
                'name' => $cart->product?->name,
                'slug' => $cart->product?->slug,
                'thumbnail' => $cart->product?->thumbnail,
                'shop_name' => $cart->product?->owner?->resellerShop()?->shop_name_en,
            ],
        ];
    }

    private function checkoutCartPayload(Cart $cart): array
    {
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
            'price' => (float) ($cart->price ?? 0),
            'qty' => (int) ($cart->qty ?? 0),
            'size' => $cart->size,
            'shop' => $cart->product?->owner?->resellerShop()?->shop_name_en ?? 'N/A',
            'attr_name' => $cart->product?->attr?->name,
            'attr_values' => $attrValues,
        ];
    }

    private function orderPayload(Order $order): array
    {
        return [
            'id' => $order->id,
            'status' => $order->status,
            'quantity' => (int) ($order->quantity ?? 0),
            'total' => (float) ($order->total ?? 0),
            'shipping' => (float) ($order->shipping ?? 0),
            'cart_orders_count' => $order->cartOrders?->count() ?? 0,
        ];
    }
}
