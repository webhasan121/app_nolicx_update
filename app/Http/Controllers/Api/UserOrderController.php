<?php

namespace App\Http\Controllers\Api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductComissionController;
use App\Models\CartOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\syncOrder;
use App\Support\OrderNotice;
use App\Support\VendorResellOrderSync;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserOrderController extends Controller
{
    public function index(Request $request)
    {
        $find = trim((string) $request->query('find', ''));
        $perPage = max(1, min((int) $request->query('per_page', config('app.paginate')), 100));

        $query = Order::with('cartOrders.product')
            ->where([
                'user_id' => $request->user()->id,
                'user_type' => 'user',
            ])
            ->latest('id');

        $this->applySearch($query, $find);

        $orders = $query->paginate($perPage)->withQueryString();

        return ApiResponse::success([
            'filters' => [
                'find' => $find,
            ],
            'orders' => [
                'data' => $orders->getCollection()
                    ->map(fn (Order $order) => $this->listPayload($order))
                    ->values(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
                'total' => $orders->total(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
            ],
        ], 'Orders fetched');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'phone' => ['required', 'string', 'max:25'],
            'district' => ['required'],
            'upozila' => ['required'],
            'location' => ['required', 'string'],
            'delevery' => ['required', 'string'],
            'area_condition' => ['nullable', 'string'],
            'house_no' => ['nullable', 'string', 'max:255'],
            'road_no' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $product = Product::query()
            ->where('id', $validated['product_id'])
            ->active()
            ->reseller()
            ->firstOrFail();

        if ($user->id === $product->user_id) {
            return ApiResponse::error("You can't purchase your own product", null, 422);
        }

        if (!empty($product->attr?->value) && empty($validated['size'])) {
            return ApiResponse::error('The size field is required.', [
                'size' => ['The size field is required.'],
            ], 422);
        }

        $order = DB::transaction(function () use ($validated, $user, $product) {
            $price = $product->offer_type ? $product->discount : $product->price;
            $isHandDelivery = strtolower((string) $validated['delevery']) === 'hand';
            $orderStatus = $isHandDelivery ? 'Delivered' : 'Pending';
            $receivedAt = $isHandDelivery ? now() : null;
            $shipping = $isHandDelivery
                ? 0
                : (($validated['area_condition'] ?? 'Dhaka') === 'Dhaka'
                    ? $product->shipping_in_dhaka
                    : $product->shipping_out_dhaka);
            $total = $price * (int) $validated['quantity'];

            $order = Order::create([
                'user_id' => $user->id,
                'user_type' => 'user',
                'belongs_to' => $product->user_id,
                'belongs_to_type' => 'reseller',
                'status' => $orderStatus,
                'received_at' => $receivedAt,
                'quantity' => $validated['quantity'],
                'total' => $total,
                'delevery' => $validated['delevery'],
                'number' => $validated['phone'],
                'area_condition' => $validated['area_condition'] ?? 'Dhaka',
                'district' => $validated['district'],
                'upozila' => $validated['upozila'],
                'location' => $validated['location'],
                'road_no' => $validated['road_no'] ?? null,
                'house_no' => $validated['house_no'] ?? null,
                'shipping' => $shipping,
                'target_area' => $validated['upozila'],
            ]);

            $cartOrder = CartOrder::create([
                'user_id' => $user->id,
                'user_type' => 'user',
                'belongs_to' => $product->user_id,
                'belongs_to_type' => 'reseller',
                'order_id' => $order->id,
                'product_id' => $product->id,
                'size' => $validated['size'] ?? null,
                'price' => $price,
                'total' => $total,
                'quantity' => $validated['quantity'],
                'buying_price' => $product->buying_price ?? 0,
                'status' => $orderStatus,
            ]);

            ProductComissionController::dispatchProductComissionsListeners($order->id);
            VendorResellOrderSync::syncCartOrder($order, $cartOrder);
            OrderNotice::orderPlaced($order, $user->id);

            return $order->fresh(['cartOrders.product', 'hasRider.rider']);
        });

        return ApiResponse::success($this->detailsPayload($order), 'Order created', 201);
    }

    public function show(Request $request, int $id)
    {
        $order = Order::with(['cartOrders.product', 'hasRider.rider'])
            ->where([
                'user_id' => $request->user()->id,
                'user_type' => 'user',
            ])
            ->findOrFail($id);

        return ApiResponse::success($this->detailsPayload($order), 'Order fetched');
    }

    public function cancel(Request $request, Order $order)
    {
        if ((int) $order->user_id !== (int) $request->user()->id || $order->user_type !== 'user') {
            return ApiResponse::notFound('Order not found');
        }

        $order->update(['status' => 'Cancelled']);
        OrderNotice::statusChanged($order, 'Cancelled', $request->user()->id);

        return ApiResponse::success($this->listPayload($order->fresh('cartOrders.product')), 'Order cancelled');
    }

    public function destroy(Request $request, Order $order)
    {
        if ((int) $order->user_id !== (int) $request->user()->id || $order->user_type !== 'user') {
            return ApiResponse::notFound('Order not found');
        }

        $order->delete();

        return ApiResponse::success(null, 'Order deleted');
    }

    public function markReceived(Request $request, int $id)
    {
        $order = Order::where([
            'user_id' => $request->user()->id,
            'user_type' => 'user',
        ])->findOrFail($id);

        if (!$order->received_at) {
            $receivedAt = Carbon::now();
            $order->update(['received_at' => $receivedAt]);

            $synced = syncOrder::query()->where('user_order_id', $order->id)->first();
            if ($synced?->reseller_order_id) {
                Order::query()
                    ->where('id', $synced->reseller_order_id)
                    ->whereNull('received_at')
                    ->update(['received_at' => $receivedAt]);
            }

            OrderNotice::customerReceived($order, $request->user()->id);
        }

        return ApiResponse::success($this->detailsPayload($order->fresh(['cartOrders.product', 'hasRider.rider'])), 'Order marked as received');
    }

    private function applySearch($query, string $find): void
    {
        if ($find === '') {
            return;
        }

        $query->where(function ($subQuery) use ($find) {
            $subQuery
                ->where('id', 'like', '%' . $find . '%')
                ->orWhere('status', 'like', '%' . $find . '%')
                ->orWhere('total', 'like', '%' . $find . '%')
                ->orWhere('number', 'like', '%' . $find . '%')
                ->orWhereHas('cartOrders.product', function ($productQuery) use ($find) {
                    $productQuery
                        ->where('name', 'like', '%' . $find . '%')
                        ->orWhere('slug', 'like', '%' . $find . '%');
                });
        });
    }

    private function listPayload(Order $order): array
    {
        $shop = $order->shop()->first();

        return [
            'id' => $order->id,
            'status' => $order->status,
            'quantity' => (int) ($order->quantity ?? 0),
            'total' => (float) ($order->total ?? 0),
            'shipping' => (float) ($order->shipping ?? 0),
            'cart_orders_count' => $order->cartOrders?->count() ?? 0,
            'shop' => [
                'shop_name_en' => $shop?->shop_name_en,
                'shop_name_bn' => $shop?->shop_name_bn,
                'village' => $shop?->village ?? 'n/a',
                'upozila' => $shop?->upozila ?? 'n/a',
                'district' => $shop?->district ?? 'n/a',
            ],
            'created_at' => $order->created_at?->toDateTimeString(),
            'created_at_human' => $order->created_at?->diffForHumans(),
        ];
    }

    private function detailsPayload(Order $order): array
    {
        $riderAssignment = $order->hasRider()->latest()->first();

        return [
            'id' => $order->id,
            'status' => $order->status,
            'created_at' => Carbon::parse($order->created_at)->toFormattedDateString(),
            'created_time' => Carbon::parse($order->created_at)->format('H:i a'),
            'received_at' => $order->received_at,
            'total' => (float) ($order->total ?? 0),
            'shipping' => (float) ($order->shipping ?? 0),
            'delevery' => $order->delevery,
            'area_condition' => $order->area_condition,
            'location' => $order->location,
            'number' => $order->number,
            'cart_orders' => $order->cartOrders->map(fn (CartOrder $item) => [
                'id' => $item->id,
                'quantity' => (int) ($item->quantity ?? 0),
                'size' => $item->size,
                'price' => (float) ($item->price ?? 0),
                'total' => (float) ($item->total ?? 0),
                'product' => [
                    'id' => $item->product?->id,
                    'name' => $item->product?->name ?? 'N/A',
                    'slug' => $item->product?->slug ?? '',
                    'thumbnail' => $item->product?->thumbnail,
                ],
            ])->values(),
            'assigned_rider' => $riderAssignment ? [
                'name' => $riderAssignment?->rider?->name,
                'phone' => $riderAssignment?->phone ?? $riderAssignment?->rider?->phone,
            ] : null,
        ];
    }
}
