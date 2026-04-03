<?php

namespace App\Support;

use App\Models\cod;
use App\Models\User;
use Carbon\Carbon;

class RiderConsignmentIndexData
{
    public static function get(User $user, array $filters = []): array
    {
        $status = $filters['status'] ?? 'All';
        $createdAt = $filters['created_at'] ?? 'any';
        $startTime = $filters['start_time'] ?? null;
        $endTime = $filters['end_time'] ?? null;

        $query = cod::query()
            ->with(['order.cartOrders.product'])
            ->where('rider_id', $user->id);

        if ($status !== 'All') {
            $query->where('status', $status);
        }

        if ($createdAt !== 'any' && $createdAt !== null) {
            if ($createdAt === 'Today') {
                $query->whereDate('created_at', now());
            } elseif ($createdAt === 'Yesterday') {
                $query->whereDate('created_at', now()->yesterday());
            } elseif ($createdAt === 'Weak') {
                $query->whereBetween('created_at', [now()->subWeek(), today()]);
            } elseif ($createdAt === 'Month') {
                $query->whereBetween('created_at', [now()->startOfMonth(), today()]);
            } elseif ($createdAt === 'between' && $startTime && $endTime) {
                $query->whereBetween('created_at', [
                    $startTime,
                    Carbon::parse($endTime)->endOfDay(),
                ]);
            }
        }

        $consignments = $query->orderBy('id', 'desc')->get();

        $deliveryTotal = 0;
        $earnTotal = 0;

        $items = $consignments->map(function ($cod) use (&$deliveryTotal, &$earnTotal) {
            $totalForNotResel = 0;
            $images = [];

            foreach ($cod->order?->cartOrders ?? [] as $item) {
                if (!$item->product?->isResel) {
                    $totalForNotResel += $item->total;
                    $images[] = $item->product?->thumbnail;
                }
            }

            $deliveryTotal += $totalForNotResel;
            $earnTotal += $cod->order->shipping ?? 0;

            return [
                'id' => $cod->id,
                'order_id' => $cod->order?->id,
                'status' => $cod->status,
                'system_comission' => $cod->system_comission,
                'shipping' => $cod->order->shipping ?? 0,
                'location' => $cod->order?->location ?? 'N/A',
                'created_at_formatted' => $cod->created_at?->toFormattedDateString(),
                'total_for_not_resel' => $totalForNotResel,
                'display_total' => $totalForNotResel + ($cod->system_comission ?? 0),
                'images' => $images,
            ];
        })->values()->all();

        return [
            'filters' => [
                'status' => $status,
                'created_at' => $createdAt,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ],
            'consignments' => $items,
            'totals' => [
                'delivery' => $deliveryTotal,
                'earn' => $earnTotal,
            ],
        ];
    }
}