<?php

namespace App\Support;

use App\Models\cod;
use App\Models\Notice;

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

    private static function linkUrls(?int $orderId): array
    {
        if (! $orderId) {
            return [];
        }

        return [
            'system' => route('system.orders.details', ['id' => $orderId]),
            'user' => route('user.orders.details', ['id' => $orderId]),
            'vendor' => route('vendor.orders.view', ['order' => $orderId]),
            'reseller' => route('reseller.order.view', ['order' => $orderId]),
            'rider' => self::riderConsignmentUrl($orderId),
        ];
    }

    private static function riderConsignmentUrl(int $orderId): string
    {
        $riderId = auth()->id();
        $query = cod::query()
            ->where('order_id', $orderId);

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
            ->where('order_id', $orderId)
            ->pluck('rider_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
