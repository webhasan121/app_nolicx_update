<?php

namespace App\Support;

use App\Models\Notice;
use App\Models\Order;

class OrderNotice
{
    private const ROLES = ['system', 'user', 'vendor', 'reseller', 'rider'];

    public static function statusChanged(Order $order, string $status, ?int $actorId = null): void
    {
        self::create(
            $order,
            "Order #{$order->id} moved to {$status}",
            "Order #{$order->id} status is now {$status}. Customer phone: " . ($order->number ?? 'N/A') . "."
        );
    }

    public static function riderAssigned(Order $order, string $riderName, ?int $actorId = null): void
    {
        self::create(
            $order,
            "Rider assigned for order #{$order->id}",
            "Order #{$order->id} has been assigned to {$riderName}."
        );
    }

    public static function riderStatusChanged(Order $order, string $status, ?int $actorId = null): void
    {
        self::create(
            $order,
            "Rider shipment update for order #{$order->id}",
            "Rider shipment status for order #{$order->id} is now {$status}."
        );
    }

    public static function customerReceived(Order $order, ?int $actorId = null): void
    {
        self::create(
            $order,
            "Customer received order #{$order->id}",
            "Customer marked order #{$order->id} as received. Seller can now finish the order."
        );
    }

    private static function create(Order $order, string $title, string $body): void
    {
        Notice::create([
            'created_by' => auth()->id(),
            'title' => $title,
            'body' => $body,
            'order_id' => $order->id,
            'target_roles' => self::ROLES,
            'is_active' => true,
            'published_at' => now(),
        ]);
    }
}
