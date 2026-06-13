<?php

namespace App\Support;

use App\Models\Notice;
use App\Models\Order;

class OrderNotice
{
    private const ROLES = ['system', 'user', 'vendor', 'reseller', 'rider'];

    public static function orderPlaced(Order $order, ?int $actorId = null): void
    {
        self::create(
            $order,
            "New order #{$order->id} placed",
            "Order #{$order->id} has been placed. Customer phone: " . ($order->number ?? 'N/A') . ".",
            $actorId
        );
    }

    public static function statusChanged(Order $order, string $status, ?int $actorId = null): void
    {
        self::create(
            $order,
            "Order #{$order->id} moved to {$status}",
            "Order #{$order->id} status is now {$status}. Customer phone: " . ($order->number ?? 'N/A') . ".",
            $actorId
        );
    }

    public static function riderAssigned(Order $order, string $riderName, ?int $actorId = null): void
    {
        self::create(
            $order,
            "Rider assigned for order #{$order->id}",
            "Order #{$order->id} has been assigned to {$riderName}.",
            $actorId
        );
    }

    public static function riderStatusChanged(Order $order, string $status, ?int $actorId = null): void
    {
        self::create(
            $order,
            "Rider shipment update for order #{$order->id}",
            "Rider shipment status for order #{$order->id} is now {$status}.",
            $actorId
        );
    }

    public static function customerReceived(Order $order, ?int $actorId = null): void
    {
        self::create(
            $order,
            "Customer received order #{$order->id}",
            "Customer marked order #{$order->id} as received. Seller can now finish the order.",
            $actorId
        );
    }

    private static function create(Order $order, string $title, string $body, ?int $actorId = null): void
    {
        Notice::create([
            'created_by' => $actorId ?? auth()->id(),
            'target_user_id' => $order->user_id ?: null,
            'title' => $title,
            'body' => $body,
            'order_id' => $order->id,
            'target_roles' => $order->user_id
                ? self::ROLES
                : array_values(array_diff(self::ROLES, ['user'])),
            'is_active' => true,
            'published_at' => now(),
        ]);
    }
}
