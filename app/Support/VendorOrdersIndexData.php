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
        $create = $filters['create'] ?? 'all';
        $startDate = $filters['start_date'] ?? '';
        $endDate = $filters['end_date'] ?? '';
        $area = $filters['area'] ?? 'all';
        $find = trim((string) ($filters['find'] ?? ''));
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

        if ($create === 'day' && !empty($startDate)) {
            $query->whereDate('created_at', Carbon::parse($startDate)->endOfDay());
        } elseif ($create === 'between' && !empty($startDate) && !empty($endDate)) {
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->endOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        }

        if ($area !== 'all') {
            $query->where('area_condition', $area);
        }

        if ($find !== '') {
            $query->where(function ($builder) use ($find) {
                $builder
                    ->where('id', 'like', '%' . $find . '%')
                    ->orWhere('number', 'like', '%' . $find . '%')
                    ->orWhere('location', 'like', '%' . $find . '%')
                    ->orWhere('status', 'like', '%' . $find . '%')
                    ->orWhereHas('user', function ($userQuery) use ($find) {
                        $userQuery
                            ->where('name', 'like', '%' . $find . '%')
                            ->orWhere('email', 'like', '%' . $find . '%');
                    })
                    ->orWhereHas('cartOrders.product', function ($productQuery) use ($find) {
                        $productQuery
                            ->where('title', 'like', '%' . $find . '%')
                            ->orWhere('name', 'like', '%' . $find . '%');
                    });
            });
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
                'find' => $find,
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
                'links' => $paginator->linkCollection()->map(function ($link) {
                    return [
                        'url' => $link['url'],
                        'label' => strip_tags((string) $link['label']),
                        'active' => (bool) $link['active'],
                    ];
                })->values()->all(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'prev_page_url' => $paginator->previousPageUrl(),
                'next_page_url' => $paginator->nextPageUrl(),
            ],
            'print_url' => route('vendor.orders.summary.print', [
                'nav' => $nav,
                'delivery' => $delivery,
                'create' => $create,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'area' => $area,
                'find' => $find,
            ]),
        ];
    }
}
