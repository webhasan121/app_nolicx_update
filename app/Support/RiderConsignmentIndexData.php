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
        $createdAt = $filters['created_at'] ?? 'Today';
        $startTime = $filters['start_time'] ?? null;
        $endTime = $filters['end_time'] ?? null;
        $find = trim((string) ($filters['find'] ?? ''));
        $page = max((int) ($filters['page'] ?? 1), 1);

        $rider = $user->isRider()?->load('targetedArea');
        $areaTerms = RiderAreaMatcher::termsForRider($rider);

        $query = cod::query()
            ->with(['order.cartOrders.product'])
            ->where('rider_id', $user->id)
            ->whereHas('order', fn ($orderQuery) => RiderAreaMatcher::applyOrderAreaScope($orderQuery, $areaTerms));

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

        if ($find !== '') {
            $query->where(function ($builder) use ($find) {
                $builder
                    ->where('id', 'like', '%' . $find . '%')
                    ->orWhere('order_id', 'like', '%' . $find . '%')
                    ->orWhere('status', 'like', '%' . $find . '%')
                    ->orWhereHas('order', function ($orderQuery) use ($find) {
                        $orderQuery
                            ->where('id', 'like', '%' . $find . '%')
                            ->orWhere('number', 'like', '%' . $find . '%')
                            ->orWhere('location', 'like', '%' . $find . '%')
                            ->orWhere('district', 'like', '%' . $find . '%')
                            ->orWhere('upozila', 'like', '%' . $find . '%')
                            ->orWhere('target_area', 'like', '%' . $find . '%');
                    });
            });
        }

        $deliveryTotal = 0;
        $earnTotal = 0;

        (clone $query)->get()->each(function ($cod) use (&$deliveryTotal, &$earnTotal) {
            $totalForNotResel = 0;

            foreach ($cod->order?->cartOrders ?? [] as $item) {
                if (!$item->product?->isResel) {
                    $totalForNotResel += $item->total;
                }
            }

            $deliveryTotal += $totalForNotResel;
            $earnTotal += $cod->order->shipping ?? 0;
        });

        $paginator = $query
            ->orderBy('id', 'desc')
            ->paginate(18, ['*'], 'page', $page)
            ->withQueryString();

        $items = $paginator->getCollection()->map(function ($cod) {
            $totalForNotResel = 0;
            $images = [];

            foreach ($cod->order?->cartOrders ?? [] as $item) {
                if (!$item->product?->isResel) {
                    $totalForNotResel += $item->total;
                    $images[] = $item->product?->thumbnail;
                }
            }

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
                'find' => $find,
            ],
            'consignments' => $items,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
                'links' => $paginator->linkCollection()->values()->all(),
            ],
            'totals' => [
                'delivery' => $deliveryTotal,
                'earn' => $earnTotal,
            ],
        ];
    }
}
