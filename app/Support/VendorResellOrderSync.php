<?php

namespace App\Support;

use App\Http\Controllers\ProductComissionController;
use App\Models\CartOrder;
use App\Models\Order;
use App\Models\Product;
use App\Models\syncOrder;
use Illuminate\Support\Facades\DB;

class VendorResellOrderSync
{
    public static function sync(Order $order): void
    {
        $order->loadMissing('cartOrders.product.isResel');

        foreach ($order->cartOrders as $cartOrder) {
            self::syncCartOrder($order, $cartOrder);
        }
    }

    public static function syncCartOrder(Order $order, CartOrder $cartOrder): ?Order
    {
        $cartOrder->loadMissing('product.isResel');

        $reselProduct = $cartOrder->product?->isResel;
        if (!$reselProduct) {
            return null;
        }

        $alreadySynced = syncOrder::query()->where([
            'user_order_id' => $order->id,
            'reseller_product_id' => $cartOrder->product_id,
        ])->first();

        if ($alreadySynced?->reseller_order_id) {
            return Order::query()->find($alreadySynced->reseller_order_id);
        }

        $mainProduct = Product::query()->find($reselProduct->parent_id);
        if (!$mainProduct) {
            return null;
        }

        return DB::transaction(function () use ($order, $cartOrder, $reselProduct, $mainProduct) {
            $vendorId = $mainProduct->user_id;
            $quantity = max(1, (int) ($cartOrder->quantity ?? 1));
            $price = (float) ($cartOrder->price ?? 0);
            $isHandDelivery = strtolower((string) $order->delevery) === 'hand';
            $orderStatus = $isHandDelivery ? 'Delivered' : ($order->status ?? 'Pending');
            $receivedAt = $isHandDelivery ? ($order->received_at ?? now()) : $order->received_at;
            $shipping = $isHandDelivery
                ? 0
                : ($order->area_condition === 'Dhaka' ? 80 : 120);

            $newOrder = Order::create([
                'user_id' => $order->belongs_to,
                'user_type' => 'reseller',
                'belongs_to' => $vendorId,
                'belongs_to_type' => 'vendor',
                'quantity' => $quantity,
                'total' => $quantity * $price,
                'status' => $orderStatus,
                'received_at' => $receivedAt,
                'name' => 'Resel',
                'district' => $order->district,
                'upozila' => $order->upozila,
                'target_area' => $order->target_area,
                'location' => $order->location,
                'house_no' => $order->house_no,
                'road_no' => $order->road_no,
                'area_condition' => $order->area_condition,
                'delevery' => $order->delevery,
                'number' => $order->number,
                'shipping' => $shipping,
            ]);

            CartOrder::create([
                'user_id' => $order->belongs_to,
                'user_type' => 'reseller',
                'belongs_to' => intval($vendorId),
                'belongs_to_type' => 'vendor',
                'order_id' => $newOrder->id,
                'product_id' => $reselProduct->parent_id,
                'quantity' => $quantity,
                'price' => $price,
                'size' => $cartOrder->size,
                'total' => $quantity * $price,
                'buying_price' => $mainProduct->buying_price,
                'status' => $orderStatus,
            ]);

            ProductComissionController::dispatchProductComissionsListeners($newOrder->id);

            syncOrder::create([
                'user_id' => $order->user_id,
                'user_order_id' => $order->id,
                'user_cart_order_id' => $cartOrder->id,
                'reseller_product_id' => $cartOrder->product_id,
                'reseller_order_id' => $newOrder->id,
                'vendor_product_id' => $mainProduct->id,
                'reseller_id' => $order->belongs_to,
                'vendor_id' => $vendorId,
                'status' => $newOrder->status,
            ]);

            return $newOrder;
        });
    }
}
