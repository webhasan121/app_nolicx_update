<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Carbon;

class VendorOrdersIndexData
{
    public static function get(User $user, array $filters = []): array
    {
        $account = $user->account_type();
        $nav = $filters['nav'] ?? 'Pending';
        $delivery = $filters['delivery'] ?? 'all';
        $create = $filters['create'] ?? 'day';
        $startDate = $filters['start_date'] ?? now()->format('Y-m-d');
        $endDate = $filters['end_date'] ?? '';
        $area = $filters['area'] ?? 'all';
        $page = max((int) ($filters['page'] ?? 1), 1);

        $baseQuery = $user->orderToMe()->where(['belongs_to_type' => $account]);

        $summary = [
            'orders' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'Pending')->count(),
            'cancel' => (clone $baseQuery)->where('status', 'Cancel')->count(),
            'cancelled' => (clone $baseQuery)->where('status', 'Cancelled')->count(),
            'accept' => (clone $baseQuery)->where('status', 'Accept')->count(),
        ];

        $query = $user->orderToMe()->where(['belongs_to_type' => $account]);

        if ($nav === 'Trashed') {
            $query->onlyTrashed();
        } elseif ($nav !== 'All') {
            $query->where(['status' => $nav]);
        }

        if ($delivery !== 'all') {
            $query->where(['delevery' => $delivery]);
        }

        if ($create === 'day') {
            $query->whereDate('created_at', Carbon::parse($startDate)->endOfDay());
        } elseif ($create === 'between' && !empty($endDate)) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->endOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        }

        if ($area !== 'all') {
            $query->where('area_condition', $area);
        }

        $paginator = $query
            ->with(['user', 'comissionsInfo'])
            ->withCount('cartOrders')
            ->latest('id')
            ->paginate(20, ['*'], 'page', $page)
            ->withQueryString();

        return [
            'account' => $account,
            'filters' => [
                'nav' => $nav,
                'delivery' => $delivery,
                'create' => $create,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'area' => $area,
                'page' => $paginator->currentPage(),
            ],
            'summary' => $summary,
            'list' => [
                'data' => $paginator->getCollection()->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'total' => $item->total,
                        'shipping' => $item->shipping,
                        'status' => $item->status,
                        'created_at_human' => $item->created_at?->diffForHumans(),
                        'created_at_formatted' => $item->created_at?->toFormattedDateString(),
                        'delevery' => $item->delevery,
                        'location' => $item->location,
                        'number' => $item->number,
                        'user_name' => $item->user?->name,
                        'cart_orders_count' => $item->cart_orders_count,
                        'comission' => $item->comissionsInfo?->sum('take_comission'),
                    ];
                })->values()->all(),
                'total' => $paginator->total(),
                'sum_total' => $paginator->getCollection()->sum('total'),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'prev_page_url' => $paginator->previousPageUrl(),
                'next_page_url' => $paginator->nextPageUrl(),
            ],
        ];
    }
}