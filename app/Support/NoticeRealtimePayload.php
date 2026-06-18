<?php

namespace App\Support;

use App\Models\cod;
use App\Models\Notice;
use App\Models\Order;
use App\Models\syncOrder;

class NoticeRealtimePayload
{
    private const TARGET_ROLES = ['system', 'user', 'vendor', 'reseller', 'rider'];

    public static function rolesFor(Notice $notice): array
    {
        $roles = $notice->target_roles;

        return is_array($roles) && count($roles)
            ? array_values(array_intersect($roles, self::TARGET_ROLES))
            : self::TARGET_ROLES;
    }

    public static function serialize(Notice $notice): array
    {
        $orderId = self::orderId($notice);

        return [
            'id' => $notice->id,
            'title' => $notice->title,
            'body' => $notice->body,
            'target_user_id' => $notice->target_user_id,
            'order_id' => $orderId,
            'seller_id' => self::sellerId($notice, $orderId),
            'seller_type' => self::sellerType($notice, $orderId),
            'vendor_ids' => self::vendorIds($orderId),
            'rider_ids' => self::riderIds($orderId),
            'link_urls' => self::linkUrls($orderId),
            'is_read' => false,
            'target_roles' => $notice->target_roles ?? [],
            'is_active' => (bool) $notice->is_active,
            'published_at' => optional($notice->published_at)->format('Y-m-d'),
            'published_at_formatted' => $notice->published_at?->toFormattedDateString() ?? 'Immediately',
            'expires_at' => optional($notice->expires_at)->format('Y-m-d'),
            'expires_at_formatted' => $notice->expires_at?->toFormattedDateString() ?? 'No expiry',
            'created_at_formatted' => $notice->created_at?->timezone(config('app.timezone'))->format('D, M j, Y g:i A') ?? '',
            'creator_name' => $notice->creator?->name ?? 'System',
        ];
    }

    private static function orderId(Notice $notice): ?int
    {
        $orderId = $notice->order_id ?? null;

        if (! $orderId) {
            preg_match('/order\s*#?(\d+)|#(\d+)/i', "{$notice->title} {$notice->body}", $matches);
            $orderId = (int) ($matches[1] ?? $matches[2] ?? 0);
        }

        return $orderId ?: null;
    }

    private static function sellerId(Notice $notice, ?int $orderId): ?int
    {
        $sellerId = $notice->order?->belongs_to;

        if (! $sellerId && $orderId) {
            $sellerId = Order::query()->whereKey($orderId)->value('belongs_to');
        }

        return $sellerId ? (int) $sellerId : null;
    }

    private static function sellerType(Notice $notice, ?int $orderId): ?string
    {
        $sellerType = $notice->order?->belongs_to_type;

        if (! $sellerType && $orderId) {
            $sellerType = Order::query()->whereKey($orderId)->value('belongs_to_type');
        }

        return $sellerType ?: null;
    }

    private static function vendorIds(?int $orderId): array
    {
        if (! $orderId) {
            return [];
        }

        $order = Order::query()
            ->with('cartOrders.product.isResel')
            ->find($orderId);

        if (! $order) {
            return [];
        }

        $vendorIds = collect();

        if ($order->belongs_to_type === 'vendor' && $order->belongs_to) {
            $vendorIds->push((int) $order->belongs_to);
        }

        foreach ($order->cartOrders as $cartOrder) {
            $vendorId = $cartOrder->product?->isResel?->belongs_to;

            if ($vendorId) {
                $vendorIds->push((int) $vendorId);
            }
        }

        return $vendorIds->filter()->unique()->values()->all();
    }

    private static function linkUrls(?int $orderId): array
    {
        if (! $orderId) {
            return [];
        }

        $order = Order::query()->find($orderId);
        $vendorUrl = $order?->belongs_to_type === 'vendor'
            ? route('vendor.orders.view', ['order' => $orderId])
            : route('vendor.products.view');

        return [
            'system' => route('system.orders.details', ['id' => $orderId]),
            'user' => route('user.orders.details', ['id' => $orderId]),
            'vendor' => $vendorUrl,
            'reseller' => route('reseller.order.view', ['order' => $orderId]),
            'rider' => self::riderConsignmentUrl($orderId),
        ];
    }

    private static function riderConsignmentUrl(int $orderId): string
    {
        $riderId = auth()->id();
        $orderIds = self::riderOrderIds($orderId);
        $query = cod::query()
            ->whereIn('order_id', $orderIds);

        if ($riderId) {
            $query->where('rider_id', $riderId);
        }

        $consignmentId = $query
            ->latest('id')
            ->value('id');

        return $consignmentId
            ? route('rider.consignment.view', ['id' => $consignmentId])
            : route('rider.consignment');
    }

    private static function riderIds(?int $orderId): array
    {
        if (! $orderId) {
            return [];
        }

        return cod::query()
            ->whereIn('order_id', self::riderOrderIds($orderId))
            ->pluck('rider_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private static function riderOrderIds(int $orderId): array
    {
        $syncedOrderId = syncOrder::query()
            ->where('user_order_id', $orderId)
            ->value('reseller_order_id');

        return collect([$orderId, $syncedOrderId])
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
